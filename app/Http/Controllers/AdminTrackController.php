<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Models\Track;
use App\Models\AgendaItem;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminTrackController extends Controller
{
    public function index()
    {
        $tracks = Track::withCount('agendaItems')->with('agendaItems')->orderBy('title')->get();
        return view('admin.tracks.index', compact('tracks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
        ]);
        Track::create($validated + ['is_active' => true]);
        return back()->with('success', 'Track created.');
    }

    public function update(Request $request, Track $track)
    {
        $validated = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
        ]);
        $track->update($validated);

        // Sync title to linked agenda items
        if ($track->wasChanged('title')) {
            $track->agendaItems()->update(['title' => $track->title]);
        }

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
        if (!Auth::user()->hasPermission('tracks')) {
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
        if (!Auth::user()->hasPermission('tracks')) {
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
                'status' => 'approved', 'processed_by' => Auth::id(), 'processed_at' => now(),
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
                        'status' => 'approved', 'processed_by' => Auth::id(), 'processed_at' => now(),
                    ]);
                } else {
                    $registrant->workshops()->attach($workshopId, [
                        'status' => 'approved', 'processed_by' => Auth::id(), 'processed_at' => now(),
                    ]);
                }

                // Auto-reject other pending workshops at the same time
                $workshop = \App\Models\Workshop::find($workshopId);
                if ($workshop) {
                    $wsDate  = $workshop->date ?? $agendaItem->date;
                    $wsStart = $workshop->start_time ?? $agendaItem->start_time;
                    $wsEnd   = $workshop->end_time ?? $agendaItem->end_time;
                    if ($wsDate && $wsStart && $wsEnd) {
                        $otherPending = $registrant->workshops()
                            ->where('workshops.id', '!=', $workshopId)
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
                }
            }
        }

        // Send track approval email (with fallback)
        $sessionName = $agendaItem ? $agendaItem->title : $track->title;

        // Pass track-specific time data for email template placeholders
        $trackEmailData = $this->getTrackEmailExtraData($track, $agendaItem, $workshop ?? null);
        $trackEmailData['track_name'] = $sessionName;

        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_TRACK_APPROVAL);
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, $trackEmailData);
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
        if (!Auth::user()->hasPermission('tracks')) {
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
                'status' => 'rejected', 'admin_notes' => $adminNotes, 'processed_by' => Auth::id(), 'processed_at' => now(),
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
                        'status' => 'rejected', 'admin_notes' => $adminNotes, 'processed_by' => Auth::id(), 'processed_at' => now(),
                    ]);
                } else {
                    $registrant->workshops()->attach($workshopId, [
                        'status' => 'rejected', 'admin_notes' => $adminNotes, 'processed_by' => Auth::id(), 'processed_at' => now(),
                    ]);
                }
            }
        }

        // Send track rejection email (with fallback)
        $sessionName = $agendaItem ? $agendaItem->title : $track->title;

        // Pass track-specific time data for email template placeholders
        $trackEmailData = $this->getTrackEmailExtraData($track, $agendaItem, $workshop ?? null);
        $trackEmailData['track_name'] = $sessionName;
        $trackEmailData['admin_notes'] = $adminNotes;

        $tmpl = EmailTemplate::activeOfType(EmailTemplate::TYPE_TRACK_REJECTION);
        if ($tmpl) {
            EmailService::send($registrant, $tmpl, $trackEmailData);
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

    /**
     * Build email extra data using track's agenda item time/date/room,
     * falling back to workshop data if available.
     */
    private function getTrackEmailExtraData($track, $agendaItem, $workshop = null): array
    {
        $ai = $agendaItem ?? $track->agendaItems()->first();
        $ws = $workshop ?? $track->workshop;
        $wsAi = $ws?->agendaItems()->first(); // workshop's first agenda item as fallback

        // Priority: track's own time > track's agenda item > workshop's agenda item > workshop
        $start    = $track->start_time ?? $ai?->start_time ?? $wsAi?->start_time ?? $ws?->start_time;
        $end      = $track->end_time ?? $ai?->end_time ?? $wsAi?->end_time ?? $ws?->end_time;
        $room     = $ai?->room ?? $wsAi?->room ?? $ws?->room ?? '';
        $date     = $ai?->date ?? $wsAi?->date ?? $ws?->date;
        $capacity = $ai?->capacity ?? $wsAi?->capacity ?? $ws?->capacity ?? 0;

        $timeRange = '—';
        if ($start && $end) {
            $timeRange = date('H:i', strtotime($start)) . ' – ' . date('H:i', strtotime($end));
        }

        return [
            'track_name'        => $track->name,
            'track_title'       => $track->title,
            'workshop_name'     => $ws?->name ?: $ws?->title ?: ($ai?->title ?? $wsAi?->title ?? ''),
            'workshop_title'    => $ws?->title ?? ($ai?->title ?? $wsAi?->title ?? ''),
            'workshop_room'     => $room,
            'workshop_date'     => $date ? $date->format('l, d F Y') : '',
            'workshop_time'     => $timeRange,
            'workshop_capacity' => (string) $capacity,
            'venue_name'        => 'Shangri-La Hotel Jakarta',
        ];
    }
}
