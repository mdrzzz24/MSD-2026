<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\AgendaItem;
use Illuminate\Http\Request;

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
                $registrant = \App\Models\Registrant::find($registrantId);
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

        // Send track approval email
        \App\Services\EmailService::sendByType($registrant, \App\Models\EmailTemplate::TYPE_TRACK_APPROVAL, [
            'track_name' => $agendaItem->title,
        ]);

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

        $request->validate(['admin_notes' => ['required', 'string', 'max:500']]);
        $agendaItemId = $request->input('agenda_item_id');
        $agendaItem = null;
        if ($agendaItemId) {
            $agendaItem = AgendaItem::findOrFail($agendaItemId);
            $agendaItem->registrants()->updateExistingPivot($registrantId, [
                'status' => 'rejected', 'admin_notes' => $request->admin_notes, 'processed_by' => auth()->id(), 'processed_at' => now(),
            ]);

            // Also sync to workshop pivot if linked
            $workshopId = $agendaItem->workshop_id;
            if (!$workshopId && $agendaItem->agenda_type === 'workshop') {
                $mw = \App\Models\Workshop::where('title', $agendaItem->title)->first();
                if ($mw) { $workshopId = $mw->id; $agendaItem->update(['workshop_id' => $mw->id]); }
            }
            if ($workshopId) {
                $registrant = \App\Models\Registrant::find($registrantId);
                $existW = $registrant->workshops()->where('workshop_id', $workshopId)->first();
                if ($existW) {
                    $registrant->workshops()->updateExistingPivot($workshopId, [
                        'status' => 'rejected', 'admin_notes' => $request->admin_notes, 'processed_by' => auth()->id(), 'processed_at' => now(),
                    ]);
                } else {
                    $registrant->workshops()->attach($workshopId, [
                        'status' => 'rejected', 'admin_notes' => $request->admin_notes, 'processed_by' => auth()->id(), 'processed_at' => now(),
                    ]);
                }
            }
        }

        // Send track rejection email
        \App\Services\EmailService::sendByType($registrant, \App\Models\EmailTemplate::TYPE_TRACK_REJECTION, [
            'track_name'  => $agendaItem->title,
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Registration rejected.');
    }
}
