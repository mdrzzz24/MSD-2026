<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Models\Workshop;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminWorkshopController extends Controller
{
    use Exportable;
    /**
     * List all workshops.
     */
    public function index()
    {
        $workshops = Workshop::with('agendaItems')->withCount(['registrants as approved_count' => function ($q) {
                $q->where('registrant_workshop.status', 'approved');
            }])
            ->withCount(['registrants as pending_count' => function ($q) {
                $q->where('registrant_workshop.status', 'pending');
            }])
            ->withCount(['registrants as rejected_count' => function ($q) {
                $q->where('registrant_workshop.status', 'rejected');
            }])
            ->orderBy('title')
            ->get();
        return view('admin.workshops.index', compact('workshops'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.workshops.create');
    }

    /**
     * Store a new workshop.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        Workshop::create($validated + ['registration_open' => true]);

        return redirect()->route('admin.workshops.index')
            ->with('success', "Workshop <strong>{$validated['title']}</strong> created. Link it to an agenda slot to set date, time & room.");
    }

    /**
     * Show edit form.
     */
    public function edit(Workshop $workshop)
    {
        return view('admin.workshops.edit', compact('workshop'));
    }

    /**
     * Update a workshop.
     */
    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $workshop->update($validated);

        // Sync title to linked agenda items
        if ($workshop->wasChanged('title')) {
            $workshop->agendaItems()->update(['title' => $workshop->title]);
        }

        return redirect()->route('admin.workshops.index')
            ->with('success', "Workshop <strong>{$workshop->title}</strong> updated.");
    }

    /**
     * Delete a workshop.
     */
    public function destroy(Workshop $workshop)
    {
        $title = $workshop->title;
        $workshop->registrants()->detach();
        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', "Workshop <strong>{$title}</strong> deleted successfully.");
    }

    /**
     * Toggle registration open/close.
     */
    public function toggleRegistration(Workshop $workshop)
    {
        $workshop->update(['registration_open' => !$workshop->registration_open]);

        $status = $workshop->registration_open ? 'opened' : 'closed';
        return redirect()->route('admin.workshops.index')
            ->with('success', "Registration for workshop <strong>{$workshop->title}</strong> has been {$status}.");
    }

    /**
     * List all workshops with registrant counts (accessible by all admin roles).
     */
    public function workshopRegistrants()
    {
        $workshops = Workshop::withCount(['registrants as approved_count' => function ($q) {
                $q->where('registrant_workshop.status', 'approved');
            }])
            ->withCount(['registrants as pending_count' => function ($q) {
                $q->where('registrant_workshop.status', 'pending');
            }])
            ->withCount('waitlist')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('admin.workshop-registrants.index', compact('workshops'));
    }

    /**
     * View registrants of a workshop with full details.
     */
    public function registrants(Workshop $workshop)
    {
        $registrants = $workshop->registrants()
            ->with(['workshops', 'workshopWaitlists'])
            ->orderBy('name')
            ->get();

        return view('admin.workshop-registrants.detail', compact('workshop', 'registrants'));
    }

    /**
     * Approve a registrant's workshop registration.
     */
    public function approveRegistrant(Workshop $workshop, $registrantId)
    {
        if (!Auth::user()->hasPermission('workshop_registrants')) {
            return back()->with('error', 'You do not have permission to approve workshop registrations.');
        }

        // Load registrant
        $registrant = Registrant::find($registrantId);
        if (!$registrant) {
            return back()->with('error', 'Registrant not found.');
        }

        $workshop->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Also sync to linked agenda items
        foreach ($workshop->agendaItems as $item) {
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'approved', 'processed_by' => Auth::id(), 'processed_at' => now(),
                ]);
            }
        }

        // Send workshop approval email (with fallback)
        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_WORKSHOP_APPROVAL);
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, ['workshop_name' => $workshop->title]);
        } else {
            try {
                Mail::send('emails.workshop-approved', [
                    'registrant'   => $registrant,
                    'workshopName' => $workshop->title,
                ], function ($msg) use ($registrant, $workshop) {
                    $msg->to($registrant->email)->subject("Workshop Approved: {$workshop->title}");
                });
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_approval',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Workshop Approved: {$workshop->title}",
                    'status'          => 'sent',
                    'sent_at'         => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_approval',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Workshop Approved: {$workshop->title}",
                    'status'          => 'failed',
                    'error_message'   => $e->getMessage(),
                    'sent_at'         => now(),
                ]);
            }
        }

        return back()->with('success', 'Workshop registration approved.');
    }

    /**
     * Reject a registrant's workshop registration.
     */
    public function rejectRegistrant(Request $request, Workshop $workshop, $registrantId)
    {
        if (!Auth::user()->hasPermission('workshop_registrants')) {
            return back()->with('error', 'You do not have permission to reject workshop registrations.');
        }

        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Load registrant
        $registrant = Registrant::find($registrantId);
        if (!$registrant) {
            return back()->with('error', 'Registrant not found.');
        }

        // Only super admin can cancel an already-approved registration
        $pivot = $workshop->registrants()->wherePivot('registrant_id', $registrantId)->first();
        $currentStatus = $pivot?->pivot?->status;
        if ($currentStatus === 'approved' && !Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'Only a super admin can cancel an approved workshop registration.');
        }

        $adminNotes = $request->input('admin_notes', '');

        $workshop->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'rejected',
            'admin_notes'  => $adminNotes,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Also sync to linked agenda items
        foreach ($workshop->agendaItems as $item) {
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'rejected', 'admin_notes' => $adminNotes,
                    'processed_by' => Auth::id(), 'processed_at' => now(),
                ]);
            }
        }

        // Send workshop rejection email (with fallback)
        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_WORKSHOP_REJECTION);
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, [
                'workshop_name' => $workshop->title,
                'admin_notes'   => $adminNotes,
            ]);
        } else {
            try {
                Mail::send('emails.workshop-rejected', [
                    'registrant'   => $registrant,
                    'workshopName' => $workshop->title,
                    'adminNotes'   => $adminNotes,
                ], function ($msg) use ($registrant, $workshop) {
                    $msg->to($registrant->email)->subject("Workshop Registration Rejected: {$workshop->title}");
                });
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_rejection',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Workshop Registration Rejected: {$workshop->title}",
                    'status'          => 'sent',
                    'sent_at'         => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_rejection',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Workshop Registration Rejected: {$workshop->title}",
                    'status'          => 'failed',
                    'error_message'   => $e->getMessage(),
                    'sent_at'         => now(),
                ]);
            }
        }

        return back()->with('success', 'Workshop registration rejected.');
    }

    /**
     * Export all workshops registrants.
     */
    public function exportCsv()
    {
        $workshops = Workshop::with('registrants')->orderBy('title')->get();

        $headers = ['Workshop', 'Date', 'Time', 'Registrant Name', 'Email', 'Phone', 'Company', 'Job Title', 'Status', 'Registered At', 'UTM Source', 'UTM Medium', 'UTM Campaign'];
        $rows = [];

        foreach ($workshops as $w) {
            foreach ($w->registrants as $r) {
                $rows[] = [
                    $w->title,
                    $w->date ? $w->date->format('Y-m-d') : '-',
                    ($w->start_time ? substr($w->start_time, 0, 5) : '') . ' - ' . ($w->end_time ? substr($w->end_time, 0, 5) : ''),
                    $r->display_name ?: $r->name,
                    $r->email,
                    $r->phone ?? '-',
                    $r->company ?? '-',
                    $r->job_title ?? '-',
                    $r->pivot->status ?? '-',
                    $r->created_at->format('Y-m-d H:i'),
                    $r->utm_source ?? '',
                    $r->utm_medium ?? '',
                    $r->utm_campaign ?? '',
                ];
            }
        }

        return $this->csvDownload($headers, $rows, 'workshops-all-' . now()->format('YmdHis') . '.csv');
    }

    /**
     * Export single workshop registrants.
     */
    public function exportWorkshopCsv(Workshop $workshop)
    {
        $registrants = $workshop->registrants()->orderBy('name')->get();

        $headers = ['Registrant Name', 'Email', 'Phone', 'Company', 'Job Title', 'Status', 'Registered At', 'UTM Source', 'UTM Medium', 'UTM Campaign'];
        $rows = $registrants->map(fn($r) => [
            $r->display_name ?: $r->name,
            $r->email,
            $r->phone ?? '-',
            $r->company ?? '-',
            $r->job_title ?? '-',
            $r->pivot->status ?? '-',
            $r->created_at->format('Y-m-d H:i'),
            $r->utm_source ?? '',
            $r->utm_medium ?? '',
            $r->utm_campaign ?? '',
        ])->toArray();

        return $this->csvDownload($headers, $rows, 'workshop-' . $workshop->id . '-' . now()->format('YmdHis') . '.csv');
    }
}
