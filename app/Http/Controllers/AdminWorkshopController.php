<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;

class AdminWorkshopController extends Controller
{
    /**
     * List all workshops.
     */
    public function index()
    {
        $workshops = Workshop::orderBy('date')->orderBy('start_time')->get();
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
            'description' => ['nullable', 'string', 'max:1000'],
            'room'        => ['nullable', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'start_time'  => ['required'],
            'end_time'    => ['required', 'after:start_time'],
            'capacity'    => ['required', 'integer', 'min:0'],
        ]);

        Workshop::create($validated + ['registration_open' => true]);

        return redirect()->route('admin.workshops.index')
            ->with('success', "Workshop <strong>{$validated['title']}</strong> created successfully.");
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
            'description' => ['nullable', 'string', 'max:1000'],
            'room'        => ['nullable', 'string', 'max:255'],
            'date'        => ['required', 'date'],
            'start_time'  => ['required'],
            'end_time'    => ['required', 'after:start_time'],
            'capacity'    => ['required', 'integer', 'min:0'],
        ]);

        $workshop->update($validated);

        return redirect()->route('admin.workshops.index')
            ->with('success', "Workshop <strong>{$workshop->title}</strong> updated successfully.");
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
     * View registrants of a workshop.
     */
    public function registrants(Workshop $workshop)
    {
        $registrants = $workshop->registrants()->orderBy('name')->get();
        return view('admin.workshops.registrants', compact('workshop', 'registrants'));
    }
}
