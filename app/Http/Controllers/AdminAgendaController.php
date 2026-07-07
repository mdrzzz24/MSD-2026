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
            'title'      => ['required', 'string', 'max:255'],
            'category'   => ['nullable', 'string', 'max:50'],
            'room'       => ['nullable', 'string', 'max:100'],
            'start_time' => ['required'],
            'end_time'   => ['required', 'after:start_time'],
            'date'       => ['nullable', 'date'],
            'order'      => ['nullable', 'integer', 'min:0'],            'rowspan'    => ['nullable', 'integer', 'min:1', 'max:12'],            'colspan'    => ['nullable', 'integer', 'min:1', 'max:8'],        ]);

        // If full row, room is null
        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        AgendaItem::create($validated);

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
            'title'      => ['required', 'string', 'max:255'],
            'category'   => ['nullable', 'string', 'max:50'],
            'room'       => ['nullable', 'string', 'max:100'],
            'start_time' => ['required'],
            'end_time'   => ['required', 'after:start_time'],
            'date'       => ['nullable', 'date'],
            'order'      => ['nullable', 'integer', 'min:0'],            'rowspan'    => ['nullable', 'integer', 'min:1', 'max:12'],            'colspan'    => ['nullable', 'integer', 'min:1', 'max:8'],        ]);

        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $agendum->update($validated);

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
}
