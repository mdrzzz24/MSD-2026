<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Models\Track;
use App\Models\AgendaItem;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminTrackController extends Controller
{
    public function index()
    {
        $tracks = Track::withCount('agendaItems')->orderBy('title')->get();
        return view('admin.tracks.index', compact('tracks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
        Track::create($validated + ['is_active' => true]);
        return back()->with('success', 'Track created.');
    }

    public function update(Request $request, Track $track)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
        $track->update($validated);
        return back()->with('success', 'Track updated.');
    }

    public function destroy(Track $track)
    {
        $track->delete();
        return back()->with('success', 'Track deleted.');
    }

    public function toggle(Track $track)
    {
        $track->update(['is_active' => !$track->is_active]);
        return back()->with('success', 'Track status toggled.');
    }

    /**
     * View registrants for all agenda items linked to this track.
     */
    public function registrants(Track $track)
    {
        if (!auth()->user()->hasPermission('tracks')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view track registrants.');
        }

        $agendaItems = $track->agendaItems()->with(['registrants'])->get();
        $allRegistrants = collect();
        foreach ($agendaItems as $item) {
            foreach ($item->registrants as $r) {
                if (!$allRegistrants->has($r->id)) {
                    $r->agenda_item_title = $item->title;
                    $r->agenda_item_id = $item->id;
                    $allRegistrants->put($r->id, $r);
                }
            }
        }

        return view('admin.tracks.registrants', compact('track', 'allRegistrants'));
    }

    /**
     * Approve a registrant for an agenda item linked to this track.
     */
    public function approveRegistrant(Request $request, Track $track, $registrantId)
    {
        if (!auth()->user()->hasPermission('tracks')) {
            return back()->with('error', 'You do not have permission to approve track registrations.');
        }

        // Ensure $registrant is always defined
        $registrant = Registrant::find($registrantId);
        if (!$registrant) {
            return back()->with('error', 'Registrant not found.');
        }

        $agendaItemId = $request->input('agenda_item_id');
        $agendaItem = null;
        if ($agendaItemId) {
            $agendaItem = AgendaItem::findOrFail($agendaItemId);
            $agendaItem->registrants()->updateExistingPivot($registrantId, [
                'status' => 'approved', 'processed_by' => auth()->id(), 'processed_at' => now(),
            ]);

            // Also sync to workshop pivot if linked
            $workshopId = $agendaItem->workshop_id;
            if (!$workshopId && $agendaItem->agenda_type === 'workshop') {
                $mw = \App\Models\Workshop::where('title', $agendaItem->title)->first();
                if ($mw) { $workshopId = $mw->id; $agendaItem->update(['workshop_id' => $mw->id]); }
            }
            if ($workshopId) {
                $existW = $registrant->workshops()->where('workshop_id', $workshopId)->first();
                if ($existW) {
                    $registrant->workshops()->updateExistingPivot($workshopId, [
                        'status' => 'approved', 'processed_by' => auth()->id(), 'processed_at' => now(),
                    ]);
                } else {
                    $registrant->workshops()->attach($workshopId, [
                        'status' => 'approved', 'processed_by' => auth()->id(), 'processed_at' => now(),
                    ]);
                }
            }
        }

        // Send track approval email (with fallback)
        $sessionName = $agendaItem ? $agendaItem->title : $track->title;
        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_TRACK_APPROVAL);
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, ['track_name' => $sessionName]);
        } else {
            try {
                Mail::send('emails.track-approved', [
                    'registrant'  => $registrant,
                    'sessionName' => $sessionName,
                ], function ($msg) use ($registrant, $sessionName) {
                    $msg->to($registrant->email)->subject("Session Approved: {$sessionName}");
                });
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'track_approval',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Session Approved: {$sessionName}",
                    'status'          => 'sent',
                    'sent_at'         => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'track_approval',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Session Approved: {$sessionName}",
                    'status'          => 'failed',
                    'error_message'   => $e->getMessage(),
                    'sent_at'         => now(),
                ]);
            }
        }

        return back()->with('success', 'Registration approved.');
    }

    /**
     * Reject a registrant for an agenda item linked to this track.
     */
    public function rejectRegistrant(Request $request, Track $track, $registrantId)
    {
        if (!auth()->user()->hasPermission('tracks')) {
            return back()->with('error', 'You do not have permission to reject track registrations.');
        }

        $request->validate(['admin_notes' => ['nullable', 'string', 'max:500']]);

        // Ensure $registrant is always defined
        $registrant = Registrant::find($registrantId);
        if (!$registrant) {
            return back()->with('error', 'Registrant not found.');
        }

        $adminNotes = $request->input('admin_notes', '');

        $agendaItemId = $request->input('agenda_item_id');
        $agendaItem = null;
        if ($agendaItemId) {
            $agendaItem = AgendaItem::findOrFail($agendaItemId);
            $agendaItem->registrants()->updateExistingPivot($registrantId, [
                'status' => 'rejected', 'admin_notes' => $adminNotes, 'processed_by' => auth()->id(), 'processed_at' => now(),
            ]);

            // Also sync to workshop pivot if linked
            $workshopId = $agendaItem->workshop_id;
            if (!$workshopId && $agendaItem->agenda_type === 'workshop') {
                $mw = \App\Models\Workshop::where('title', $agendaItem->title)->first();
                if ($mw) { $workshopId = $mw->id; $agendaItem->update(['workshop_id' => $mw->id]); }
            }
            if ($workshopId) {
                $existW = $registrant->workshops()->where('workshop_id', $workshopId)->first();
                if ($existW) {
                    $registrant->workshops()->updateExistingPivot($workshopId, [
                        'status' => 'rejected', 'admin_notes' => $adminNotes, 'processed_by' => auth()->id(), 'processed_at' => now(),
                    ]);
                } else {
                    $registrant->workshops()->attach($workshopId, [
                        'status' => 'rejected', 'admin_notes' => $adminNotes, 'processed_by' => auth()->id(), 'processed_at' => now(),
                    ]);
                }
            }
        }

        // Send track rejection email (with fallback)
        $sessionName = $agendaItem ? $agendaItem->title : $track->title;
        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_TRACK_REJECTION);
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, [
                'track_name'  => $sessionName,
                'admin_notes' => $adminNotes,
            ]);
        } else {
            try {
                Mail::send('emails.track-rejected', [
                    'registrant'  => $registrant,
                    'sessionName' => $sessionName,
                    'adminNotes'  => $adminNotes,
                ], function ($msg) use ($registrant, $sessionName) {
                    $msg->to($registrant->email)->subject("Session Registration Rejected: {$sessionName}");
                });
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'track_rejection',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Session Registration Rejected: {$sessionName}",
                    'status'          => 'sent',
                    'sent_at'         => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'registrant_id'   => $registrant->id,
                    'template_type'   => 'track_rejection',
                    'recipient_email' => $registrant->email,
                    'recipient_name'  => $registrant->display_name,
                    'subject'         => "Session Registration Rejected: {$sessionName}",
                    'status'          => 'failed',
                    'error_message'   => $e->getMessage(),
                    'sent_at'         => now(),
                ]);
            }
        }

        return back()->with('success', 'Registration rejected.');
    }
}
