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
            'name'        => ['nullable', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
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
            'name'        => ['nullable', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
        ]);

        $workshop->update($validated);

        // Sync name to linked agenda items (workshop name appears in agenda)
        if ($workshop->wasChanged('name') && $workshop->name) {
            $workshop->agendaItems()->update(['title' => $workshop->name]);
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

        // Get the latest workshop reminder log for this workshop's registrants
        $registrantIds = $registrants->pluck('id');
        $lastReminderLog = EmailLog::where('template_type', EmailTemplate::TYPE_WORKSHOP_REMINDER)
            ->whereIn('registrant_id', $registrantIds)
            ->latest('sent_at')
            ->first();

        return view('admin.workshop-registrants.detail', compact('workshop', 'registrants', 'lastReminderLog'));
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

        // Auto-reject other pending workshops at the same time
        $workshop->load('agendaItems');
        $agendaItem = $workshop->agendaItems->first();
        $wsDate  = $workshop->date ?? $agendaItem?->date;
        $wsStart = $workshop->start_time ?? $agendaItem?->start_time;
        $wsEnd   = $workshop->end_time ?? $agendaItem?->end_time;
        if ($wsDate && $wsStart && $wsEnd) {
            $otherPending = $registrant->workshops()
                ->where('workshops.id', '!=', $workshop->id)
                ->wherePivot('status', 'pending')
                ->where(function ($q) use ($wsDate, $wsStart, $wsEnd) {
                    $q->where('date', $wsDate)
                      ->where(function ($q2) use ($wsStart, $wsEnd) {
                          $q2->whereBetween('start_time', [$wsStart, $wsEnd])
                             ->orWhereBetween('end_time', [$wsStart, $wsEnd])
                             ->orWhere(function ($q3) use ($wsStart, $wsEnd) {
                                 $q3->where('start_time', '<=', $wsStart)
                                    ->where('end_time', '>=', $wsEnd);
                             });
                      });
                })->get();

            foreach ($otherPending as $other) {
                $registrant->workshops()->updateExistingPivot($other->id, [
                    'status' => 'rejected',
                    'admin_notes' => 'Auto-rejected: another workshop at the same time was approved.',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);
            }
        }

        // Send workshop approval email (with fallback)
        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_WORKSHOP_APPROVAL);
        $extraData = $workshop->emailData();
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, $extraData);
        } else {
            $subject = '[CONFIRMATION] Thank you for your Registration : MSD 2026 | ' . ($workshop->name ?: $workshop->title)
                . ' | ' . ($extraData['workshop_date'] ?: '')
                . ' | Shangri-La Hotel Jakarta | ' . ($extraData['workshop_room'] ?: '');
            try {
                Mail::send('emails.workshop-approved', array_merge([
                    'registrant'   => $registrant,
                    'workshopName' => $workshop->title,
                ], $extraData), function ($msg) use ($registrant, $subject) {
                    $msg->to($registrant->email)->subject($subject);
                });
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_approval',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => $subject,
                    'status'          => 'sent',
                    'sent_at'         => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_approval',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => $subject,
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

        $workshop->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'rejected',
            'admin_notes'  => null,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Also sync to linked agenda items
        foreach ($workshop->agendaItems as $item) {
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'rejected', 'admin_notes' => null,
                    'processed_by' => Auth::id(), 'processed_at' => now(),
                ]);
            }
        }

        // Send workshop rejection email (with fallback)
        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_WORKSHOP_REJECTION);
        $extraData = $workshop->emailData();
        $extraData['admin_notes'] = '';
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, $extraData);
        } else {
            $subject = '[CONFIRMATION] Thank you for your Registration : MSD 2026 | ' . ($workshop->name ?: $workshop->title)
                . ' | ' . ($extraData['workshop_date'] ?: '')
                . ' | Shangri-La Hotel Jakarta | ' . ($extraData['workshop_room'] ?: '');
            try {
                Mail::send('emails.workshop-rejected', array_merge([
                    'registrant'   => $registrant,
                    'workshopName' => $workshop->title,
                    'adminNotes'   => '',
                ], $extraData), function ($msg) use ($registrant, $subject) {
                    $msg->to($registrant->email)->subject($subject);
                });
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_rejection',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => $subject,
                    'status'          => 'sent',
                    'sent_at'         => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'workshop_rejection',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => $subject,
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
                    $r->created_at->copy()->addHours(7)->format('Y-m-d H:i'),
                    $r->utm_source ?? '',
                    $r->utm_medium ?? '',
                    $r->utm_campaign ?? '',
                ];
            }
        }

        return $this->csvDownload($headers, $rows, 'workshops-all-' . now()->format('YmdHis') . '.csv');
    }

    /**
     * Send workshop gentle reminder to all approved registrants of a workshop.
     */
    public function sendReminder(Request $request, Workshop $workshop)
    {
        if (!Auth::user()->hasPermission('email_templates')) {
            return redirect()->back()->with('error', 'You do not have permission to send reminders.');
        }

        $template = EmailTemplate::activeOfType(EmailTemplate::TYPE_WORKSHOP_REMINDER);
        if (!$template) {
            return redirect()->back()->with('error', 'No active template for Workshop Gentle Reminder. Create one first.');
        }

        $extraData = $workshop->emailData();

        // If specific registrant IDs are provided, send only to those
        if ($request->filled('registrant_ids')) {
            $registrantIds = $request->input('registrant_ids', []);
            $registrants = $workshop->registrants()
                ->wherePivot('status', 'approved')
                ->whereIn('registrants.id', $registrantIds)
                ->get();
        } else {
            $registrants = $workshop->registrants()->wherePivot('status', 'approved')->get();
        }

        if ($registrants->isEmpty()) {
            return redirect()->back()->with('error', 'No approved registrants selected for this workshop.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($registrants as $registrant) {
            try {
                $result = EmailService::send($registrant, $template, $extraData);
                if ($result && $result->status === 'sent') {
                    $successCount++;
                } else {
                    $errors[] = $registrant->email . ': ' . ($result->error_message ?? 'failed');
                }
            } catch (\Exception $e) {
                $errors[] = $registrant->email . ': ' . $e->getMessage();
            }
        }

        $msg = "Workshop reminder sent to <strong>{$successCount}</strong> registrant(s).";
        if (!empty($errors)) {
            $msg .= ' Errors: ' . implode('; ', $errors);
        }

        return redirect()->back()->with($successCount > 0 ? 'success' : 'error', $msg);
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
            $r->created_at->copy()->addHours(7)->format('Y-m-d H:i'),
            $r->utm_source ?? '',
            $r->utm_medium ?? '',
            $r->utm_campaign ?? '',
        ])->toArray();

        return $this->csvDownload($headers, $rows, 'workshop-' . $workshop->id . '-' . now()->format('YmdHis') . '.csv');
    }
}
