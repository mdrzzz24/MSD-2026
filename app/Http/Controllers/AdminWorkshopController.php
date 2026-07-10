<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Services\EmailService;
use Illuminate\Http\Request;

class AdminWorkshopController extends Controller
{
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
        if (!auth()->user()->hasPermission('workshop_registrants')) {
            return back()->with('error', 'You do not have permission to approve workshop registrations.');
        }

        $workshop->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Also sync to linked agenda items
        foreach ($workshop->agendaItems as $item) {
            $registrant = \App\Models\Registrant::find($registrantId);
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'approved', 'processed_by' => auth()->id(), 'processed_at' => now(),
                ]);
            }
        }

        // Send workshop approval email
        EmailService::sendByType($registrant, \App\Models\EmailTemplate::TYPE_WORKSHOP_APPROVAL, [
            'workshop_name' => $workshop->title,
        ]);

        return back()->with('success', 'Workshop registration approved.');
    }

    /**
     * Reject a registrant's workshop registration.
     */
    public function rejectRegistrant(Request $request, Workshop $workshop, $registrantId)
    {
        if (!auth()->user()->hasPermission('workshop_registrants')) {
            return back()->with('error', 'You do not have permission to reject workshop registrations.');
        }

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ], [
            'admin_notes.required' => 'Rejection reason is required.',
        ]);

        $workshop->registrants()->updateExistingPivot($registrantId, [
            'status'       => 'rejected',
            'admin_notes'  => $request->input('admin_notes'),
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Also sync to linked agenda items
        foreach ($workshop->agendaItems as $item) {
            $registrant = \App\Models\Registrant::find($registrantId);
            $existA = $registrant->agendaItems()->where('agenda_item_id', $item->id)->first();
            if ($existA) {
                $registrant->agendaItems()->updateExistingPivot($item->id, [
                    'status' => 'rejected', 'admin_notes' => $request->input('admin_notes'),
                    'processed_by' => auth()->id(), 'processed_at' => now(),
                ]);
            }
        }

        // Send workshop rejection email
        EmailService::sendByType($registrant, \App\Models\EmailTemplate::TYPE_WORKSHOP_REJECTION, [
            'workshop_name' => $workshop->title,
            'admin_notes'   => $request->input('admin_notes'),
        ]);

        return back()->with('success', 'Workshop registration rejected.');
    }
}
