<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    /**
     * Consolidated Floors & Rooms management page.
     */
    public function index()
    {
        $floors = Floor::with('rooms')->ordered()->get();
        $roomsWithoutFloor = Room::whereNull('floor_id')->ordered()->get();
        return view('admin.rooms.index', compact('floors', 'roomsWithoutFloor'));
    }

    // ────────── Floor actions ──────────

    public function storeFloor(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        Floor::create($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Floor created successfully.');
    }

    public function updateFloor(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        $floor->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Floor updated successfully.');
    }

    public function destroyFloor(Floor $floor)
    {
        $floor->rooms()->update(['floor_id' => null]);
        $floor->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Floor deleted successfully.');
    }

    // ────────── Room actions ──────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'floor_id' => ['nullable', 'exists:floors,id'],
            'order'    => ['nullable', 'integer', 'min:0'],
        ]);

        Room::create($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'floor_id' => ['nullable', 'exists:floors,id'],
            'order'    => ['nullable', 'integer', 'min:0'],
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
