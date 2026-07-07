<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\Request;

class AdminTimeSlotController extends Controller
{
    public function index()
    {
        $slots = TimeSlot::ordered()->get();
        return view('admin.time-slots.index', compact('slots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_time' => ['required'],
            'end_time'   => ['required', 'after:start_time'],
            'order'      => ['nullable', 'integer', 'min:0'],
        ]);

        TimeSlot::create($validated);

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Time slot created successfully.');
    }

    public function update(Request $request, TimeSlot $timeSlot)
    {
        $validated = $request->validate([
            'start_time' => ['required'],
            'end_time'   => ['required', 'after:start_time'],
            'order'      => ['nullable', 'integer', 'min:0'],
        ]);

        $timeSlot->update($validated);

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Time slot updated successfully.');
    }

    public function destroy(TimeSlot $timeSlot)
    {
        $timeSlot->delete();

        return redirect()->route('admin.time-slots.index')
            ->with('success', 'Time slot deleted successfully.');
    }
}
