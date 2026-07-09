<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\TimeSlot;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminAgendaController extends Controller
{
    public function index()
    {
        $items = AgendaItem::ordered()->get();
        $rooms = Room::ordered()->get();
        $timeSlots = TimeSlot::ordered()->get();

        // Group items by time slot key
        $itemMap = [];
        foreach ($items as $item) {
            $key = $item->start_time . '-' . $item->end_time;
            $itemMap[$key][] = $item;
        }

        return view('admin.agenda.index', compact('items', 'rooms', 'timeSlots', 'itemMap'));
    }

    public function create()
    {
        $rooms = Room::ordered()->get();
        return view('admin.agenda.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:2000'],
            'key_highlights'    => ['nullable', 'string', 'max:3000'],
            'category'          => ['nullable', 'string', 'max:50'],
            'agenda_type'       => ['nullable', 'string', 'max:50'],
            'room'              => ['nullable', 'string', 'max:100'],
            'start_time'        => ['required'],
            'end_time'          => ['required', 'after:start_time'],
            'date'              => ['nullable', 'date'],
            'order'             => ['nullable', 'integer', 'min:0'],
            'rowspan'           => ['nullable', 'integer', 'min:1', 'max:12'],
            'colspan'           => ['nullable', 'integer', 'min:1', 'max:8'],
            'is_registrable'     => ['boolean'],
            'capacity'          => ['nullable', 'integer', 'min:0'],
            'registration_open' => ['boolean'],
            'workshop_id'       => ['nullable', 'string'],
            'track_id'          => ['nullable', 'string'],
            'new_workshop_title' => ['nullable', 'string', 'max:255'],
            'new_workshop_desc'  => ['nullable', 'string', 'max:2000'],
            'new_track_title'   => ['nullable', 'string', 'max:255'],
            'new_track_desc'    => ['nullable', 'string', 'max:2000'],
            'speaker_ids'       => ['nullable', 'array'],
            'speaker_ids.*'     => ['exists:speakers,id'],
            'speaker_highlights' => ['nullable', 'array'],
            'speaker_presentation_title' => ['nullable', 'array'],
            'speaker_presentation_desc'  => ['nullable', 'array'],
        ]);

        // If full row, room is null
        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $validated['is_registrable'] = $request->boolean('is_registrable');
        $validated['registration_open'] = $request->boolean('registration_open', true);

        // Don't pass __new__ as workshop_id or track_id
        if (($validated['workshop_id'] ?? '') === '__new__') $validated['workshop_id'] = null;
        if (($validated['track_id'] ?? '') === '__new__') $validated['track_id'] = null;

        $agendaItem = AgendaItem::create($validated);

        // Handle inline workshop creation
        if ($request->input('workshop_id') === '__new__' && $request->input('new_workshop_title')) {
            $workshop = \App\Models\Workshop::create([
                'title' => $request->input('new_workshop_title'),
                'description' => $request->input('new_workshop_desc'),
                'registration_open' => true,
            ]);
            $agendaItem->update(['workshop_id' => $workshop->id]);
        }

        // Handle inline track creation
        if ($request->input('track_id') === '__new__' && $request->input('new_track_title')) {
            $track = \App\Models\Track::create([
                'title' => $request->input('new_track_title'),
                'description' => $request->input('new_track_desc'),
                'is_active' => true,
            ]);
            $agendaItem->update(['track_id' => $track->id]);
        }

        // Sync speakers with all pivot fields
        if ($speakerIds = $request->input('speaker_ids')) {
            $syncData = [];
            $highlights = $request->input('speaker_highlights', []);
            $presTitles = $request->input('speaker_presentation_title', []);
            $presDescs = $request->input('speaker_presentation_desc', []);
            foreach ($speakerIds as $i => $id) {
                $syncData[$id] = [
                    'order' => $i,
                    'key_highlights' => $highlights[$id] ?? null,
                    'presentation_title' => $presTitles[$id] ?? null,
                    'presentation_description' => $presDescs[$id] ?? null,
                ];
            }
            $agendaItem->speakers()->sync($syncData);
        }

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda item <strong>' . e($validated['title']) . '</strong> created successfully.');
    }

    public function edit(AgendaItem $agendum)
    {
        $rooms = Room::ordered()->get();
        return view('admin.agenda.edit', compact('agendum', 'rooms'));
    }

    public function update(Request $request, AgendaItem $agendum)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:2000'],
            'key_highlights'    => ['nullable', 'string', 'max:3000'],
            'category'          => ['nullable', 'string', 'max:50'],
            'agenda_type'       => ['nullable', 'string', 'max:50'],
            'room'              => ['nullable', 'string', 'max:100'],
            'start_time'        => ['required'],
            'end_time'          => ['required', 'after:start_time'],
            'date'              => ['nullable', 'date'],
            'order'             => ['nullable', 'integer', 'min:0'],
            'rowspan'           => ['nullable', 'integer', 'min:1', 'max:12'],
            'colspan'           => ['nullable', 'integer', 'min:1', 'max:8'],
            'is_registrable'     => ['boolean'],
            'capacity'          => ['nullable', 'integer', 'min:0'],
            'registration_open' => ['boolean'],
            'workshop_id'       => ['nullable', 'string'],
            'new_workshop_title' => ['nullable', 'string', 'max:255'],
            'new_workshop_desc'  => ['nullable', 'string', 'max:2000'],
            'speaker_ids'       => ['nullable', 'array'],
            'speaker_ids.*'     => ['exists:speakers,id'],
            'speaker_highlights' => ['nullable', 'array'],
            'speaker_presentation_title' => ['nullable', 'array'],
            'speaker_presentation_desc'  => ['nullable', 'array'],
        ]);

        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $validated['is_registrable'] = $request->boolean('is_registrable');
        $validated['registration_open'] = $request->boolean('registration_open', true);

        // Don't pass __new__ as workshop_id or track_id
        if (($validated['workshop_id'] ?? '') === '__new__') $validated['workshop_id'] = null;
        if (($validated['track_id'] ?? '') === '__new__') $validated['track_id'] = null;

        $agendum->update($validated);

        // Handle inline workshop creation
        if ($request->input('workshop_id') === '__new__' && $request->input('new_workshop_title')) {
            $workshop = \App\Models\Workshop::create([
                'title' => $request->input('new_workshop_title'),
                'description' => $request->input('new_workshop_desc'),
                'registration_open' => true,
            ]);
            $agendum->update(['workshop_id' => $workshop->id]);
        }

        // Handle inline track creation
        if ($request->input('track_id') === '__new__' && $request->input('new_track_title')) {
            $track = \App\Models\Track::create([
                'title' => $request->input('new_track_title'),
                'description' => $request->input('new_track_desc'),
                'is_active' => true,
            ]);
            $agendum->update(['track_id' => $track->id]);
        }

        // Sync speakers with all pivot fields
        if ($request->has('speaker_ids')) {
            $syncData = [];
            $highlights = $request->input('speaker_highlights', []);
            $presTitles = $request->input('speaker_presentation_title', []);
            $presDescs = $request->input('speaker_presentation_desc', []);
            foreach ($request->input('speaker_ids', []) as $i => $id) {
                $syncData[$id] = [
                    'order' => $i,
                    'key_highlights' => $highlights[$id] ?? null,
                    'presentation_title' => $presTitles[$id] ?? null,
                    'presentation_description' => $presDescs[$id] ?? null,
                ];
            }
            $agendum->speakers()->sync($syncData);
        } else {
            $agendum->speakers()->detach();
        }

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda item <strong>' . e($agendum->title) . '</strong> updated successfully.');
    }

    public function destroy(AgendaItem $agendum)
    {
        $title = $agendum->title;
        $agendum->delete();

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda item <strong>' . e($title) . '</strong> deleted successfully.');
    }

    /**
     * Quick merge: increase colspan (→) or rowspan (↓) by 1.
     */
    public function merge(Request $request, AgendaItem $agendum)
    {
        $direction = $request->input('dir'); // 'right' or 'down'

        if ($direction === 'right') {
            $rooms = Room::ordered()->pluck('name')->toArray();
            $roomIdx = array_search($agendum->room, $rooms);
            $max = $roomIdx !== false ? count($rooms) - $roomIdx : 1;
            $agendum->increment('colspan');
            if ($agendum->colspan > $max) $agendum->update(['colspan' => $max]);
        } elseif ($direction === 'down') {
            $agendum->increment('rowspan');
            if ($agendum->rowspan > 12) $agendum->update(['rowspan' => 12]);
        } elseif ($direction === 'unright') {
            $agendum->decrement('colspan');
            if ($agendum->colspan < 1) $agendum->update(['colspan' => 1]);
        } elseif ($direction === 'undown') {
            $agendum->decrement('rowspan');
            if ($agendum->rowspan < 1) $agendum->update(['rowspan' => 1]);
        }

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Merge updated for <strong>' . e($agendum->title) . '</strong>.');
    }

    /**
     * Toggle registration open/close for an agenda item.
     */
    public function toggleRegistration(AgendaItem $agendum)
    {
        $agendum->update(['registration_open' => !$agendum->registration_open]);
        $status = $agendum->registration_open ? 'opened' : 'closed';
        return back()->with('success', 'Registration ' . $status . ' for <strong>' . e($agendum->title) . '</strong>.');
    }

    // ── Agenda Registrants Management ──

    /**
     * List all registrable agenda items with registrant counts.
     */
    public function registrantsIndex(Request $request)
    {
        $query = AgendaItem::where('is_registrable', true)
            ->withCount(['registrants as approved_count' => function ($q) {
                $q->where('agenda_item_registrant.status', 'approved');
            }])
            ->withCount(['registrants as pending_count' => function ($q) {
                $q->where('agenda_item_registrant.status', 'pending');
            }]);

        // Filter by track
        if ($trackId = $request->get('track_id')) {
            $query->where('track_id', $trackId);
        }

        $items = $query->orderBy('start_time')->get();
        $track = $trackId ? \App\Models\Track::find($trackId) : null;

        return view('admin.agenda-registrants.index', compact('items', 'track'));
    }

    /**
     * View registrants of a specific agenda item.
     */
    public function registrantsDetail(AgendaItem $agendum)
    {
        $registrants = $agendum->registrants()
            ->orderBy('name')
            ->get();

        return view('admin.agenda-registrants.detail', compact('agendum', 'registrants'));
    }

    /**
     * Approve a registrant's agenda item registration.
     */
    public function registrantsApprove(AgendaItem $agendum, $registrantId)
    {
        $agendum->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Sync with linked workshop
        if ($agendum->workshop_id) {
            $exists = \DB::table('registrant_workshop')
                ->where('workshop_id', $agendum->workshop_id)
                ->where('registrant_id', $registrantId)
                ->exists();
            if ($exists) {
                \DB::table('registrant_workshop')
                    ->where('workshop_id', $agendum->workshop_id)
                    ->where('registrant_id', $registrantId)
                    ->update(['status' => 'approved', 'processed_by' => auth()->id(), 'processed_at' => now()]);
            }
        }

        return back()->with('success', 'Registration approved.');
    }

    /**
     * Reject a registrant's agenda item registration.
     */
    public function registrantsReject(Request $request, AgendaItem $agendum, $registrantId)
    {
        $request->validate(['admin_notes' => ['required', 'string', 'max:500']]);

        $agendum->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'rejected',
            'admin_notes'  => $request->input('admin_notes'),
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Sync with linked workshop
        if ($agendum->workshop_id) {
            $exists = \DB::table('registrant_workshop')
                ->where('workshop_id', $agendum->workshop_id)
                ->where('registrant_id', $registrantId)
                ->exists();
            if ($exists) {
                \DB::table('registrant_workshop')
                    ->where('workshop_id', $agendum->workshop_id)
                    ->where('registrant_id', $registrantId)
                    ->update(['status' => 'rejected', 'admin_notes' => $request->input('admin_notes'), 'processed_by' => auth()->id(), 'processed_at' => now()]);
            }
        }

        return back()->with('success', 'Registration rejected.');
    }
}
