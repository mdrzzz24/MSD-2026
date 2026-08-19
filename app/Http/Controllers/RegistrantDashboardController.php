<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\AgendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrantDashboardController extends Controller
{
    /**
     * Show registrant dashboard with their registered workshops
     * and available workshops to register.
     */
    public function dashboard()
    {
        /** @var \App\Models\Registrant $registrant */
        $registrant = Auth::guard('registrant')->user();

        // Workshops the registrant has signed up for (with pivot status). Cancelled
        // ones are KEPT so the user can see the "Cancelled" status on the dashboard.
        $myWorkshops = $registrant->workshops()
            ->with('agendaItems')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Agenda items (tracks/workshops) the registrant has registered for:
        //  - standalone sessions (no linked workshop) are shown as session rows;
        //  - sessions of a registered workshop are deduped (the workshop row shows it);
        //  - sessions whose workshop registration is missing (old-style cancel) are
        //    shown with a synthesized 'cancelled' status so the user sees they cancelled.
        $myAgendaItems = $registrant->agendaItems()
            ->with('workshop')
            ->orderBy('start_time')
            ->get()
            ->filter(function ($item) use ($myWorkshops) {
                if (!$item->workshop) {
                    return true; // standalone session
                }
                $ws = $myWorkshops->firstWhere('id', $item->workshop->id);
                if (!$ws) {
                    // Workshop pivot missing → the registration was cancelled (old system).
                    $item->pivot = (object) ['status' => 'cancelled'];
                    return true;
                }
                return false; // the workshop row already shows this registration
            });

        // All open workshops where registration is open and not already registered.
        // Cancelled workshops are treated as "not registered" so the user can re-register.
        $registeredIds = $myWorkshops
            ->filter(fn ($w) => ($w->pivot->status ?? '') !== 'cancelled')
            ->pluck('id')
            ->toArray();
        $availableWorkshops = Workshop::with('agendaItems')
            ->where('registration_open', true)
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->filter(fn ($w) => !in_array($w->id, $registeredIds));

        // Feedback the registrant has submitted — which sessions/tracks they filled it for.
        // Each row shows the session (title + company for tracks/workshops), type, room, time
        // and when the feedback was submitted.
        $myFeedbacks = $registrant->feedbacks()
            ->with(['agendaItem.workshop', 'agendaItem.track'])
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn ($fb) => $fb->agendaItem !== null);

        return view('registrant.dashboard', compact('registrant', 'myWorkshops', 'myAgendaItems', 'availableWorkshops', 'myFeedbacks'));
    }

    /**
     * Register for a workshop (pending approval).
     */
    public function registerWorkshop(Request $request, Workshop $workshop)
    {
        /** @var \App\Models\Registrant $registrant */
        $registrant = Auth::guard('registrant')->user();

        if (!$workshop->canRegister()) {
            return back()->with('error', 'Registration for this workshop is closed or full.');
        }

        // Check existing registration (any status)
        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();
        if ($existing) {
            $status = $existing->pivot->status;
            if ($status === 'approved') {
                return back()->with('error', 'You are already registered for this workshop.');
            } elseif ($status === 'pending') {
                return back()->with('error', 'Your registration for this workshop is pending approval.');
            }
            // If rejected, allow re-registration
            $registrant->workshops()->updateExistingPivot($workshop->id, ['status' => 'pending', 'admin_notes' => null, 'processed_by' => null, 'processed_at' => null]);
            return back()->with('success', "Re-registered for workshop <strong>" . ($workshop->name ?: $workshop->title) . "</strong>. Waiting for admin approval.");
        }

        // Resolve date/time from linked agenda items if workshop fields are empty
        $workshop->load('agendaItems');
        $agendaItem = $workshop->agendaItems->first();
        $wsDate = $workshop->date ?? $agendaItem?->date;
        $wsStart = $workshop->start_time ?? $agendaItem?->start_time;
        $wsEnd = $workshop->end_time ?? $agendaItem?->end_time;

        if ($registrant->hasTimeConflict($workshop->id, null, $wsDate, $wsStart, $wsEnd)) {
            return back()->with('error', 'You are already registered for another session at the same time.');
        }

        $registrant->workshops()->attach($workshop->id, $registrant->utmForPivot() + ['status' => 'pending']);

        return back()->with('success', "Successfully registered for workshop <strong>" . ($workshop->name ?: $workshop->title) . "</strong>. Waiting for admin approval.");
    }

    /**
     * Unregister from a workshop.
     */
    public function unregisterWorkshop(Request $request, Workshop $workshop)
    {
        /** @var \App\Models\Registrant $registrant */
        $registrant = Auth::guard('registrant')->user();

        // Cannot cancel if already approved by admin
        $existing = $registrant->workshops()->where('workshop_id', $workshop->id)->first();
        if ($existing && $existing->pivot->status === 'approved') {
            return back()->with('error', 'Your registration for this workshop has been approved. Please contact the organizer to cancel.');
        }

        // Mark as cancelled instead of deleting the pivot, so the admin can see
        // which registrants cancelled their workshop registration.
        $registrant->workshops()->updateExistingPivot($workshop->id, [
            'status'       => 'cancelled',
            'admin_notes'  => null,
            'processed_by' => null,
            'processed_at' => now(),
        ]);

        return back()->with('success', "Successfully unregistered from workshop <strong>{$workshop->title}</strong>.");
    }

    /**
     * Register for an agenda item (track/workshop).
     */
    public function registerAgenda(Request $request, AgendaItem $agendaItem)
    {
        /** @var \App\Models\Registrant $registrant */
        $registrant = Auth::guard('registrant')->user();
        $agendaItem->load('workshop');

        if (!$agendaItem->is_registrable) {
            return back()->with('error', 'This session is not open for registration.');
        }

        if (!$agendaItem->canRegister()) {
            return back()->with('error', 'Registration for this session is closed or full.');
        }

        $existing = $registrant->agendaItems()->where('agenda_item_id', $agendaItem->id)->first();
        if ($existing) {
            $status = $existing->pivot->status;
            if ($status === 'approved') {
                return back()->with('error', 'You are already registered for this session.');
            } elseif ($status === 'pending') {
                return back()->with('error', 'Your registration for this session is pending approval.');
            }
            // Re-register if rejected
            $registrant->agendaItems()->updateExistingPivot($agendaItem->id, ['status' => 'pending', 'admin_notes' => null, 'processed_by' => null, 'processed_at' => null]);
            $displayName = $agendaItem->workshop ? ($agendaItem->workshop->name ?: $agendaItem->workshop->title) : $agendaItem->title;
            return back()->with('success', "Re-registered for <strong>{$displayName}</strong>. Waiting for approval.");
        }

        // Check time conflict with agenda items on the same date (exclude rejected)
        $conflict = $registrant->agendaItems()
            ->where('agenda_items.id', '!=', $agendaItem->id)
            ->wherePivot('status', 'not in', ['rejected', 'cancelled'])
            ->where(function ($q) use ($agendaItem) {
                if ($agendaItem->date) {
                    $q->where('agenda_items.date', $agendaItem->date);
                } else {
                    $q->whereNull('agenda_items.date');
                }
            })
            ->where(function ($q) use ($agendaItem) {
                $q->where(function ($q2) use ($agendaItem) {
                    $q2->where('start_time', '<', $agendaItem->end_time)
                       ->where('end_time', '>', $agendaItem->start_time);
                });
            })->exists();

        // Also check time conflict with workshop registrations on the same date (exclude rejected)
        if (!$conflict && $agendaItem->date) {
            $conflict = $registrant->workshops()
                ->wherePivot('status', 'not in', ['rejected', 'cancelled'])
                ->where('date', $agendaItem->date)
                ->where(function ($q) use ($agendaItem) {
                    $q->where(function ($q2) use ($agendaItem) {
                        $q2->where('start_time', '<', $agendaItem->end_time)
                           ->where('end_time', '>', $agendaItem->start_time);
                    });
                })->exists();
        }

        if ($conflict) {
            return back()->with('error', 'You are already registered for another session at the same time.');
        }

        $registrant->agendaItems()->attach($agendaItem->id, ['status' => 'pending']);

        // Also register for linked workshop if exists
        $workshopId = $agendaItem->workshop_id;
        // Fallback: try to find workshop by title match
        if (!$workshopId && $agendaItem->agenda_type === 'workshop') {
            $matchingWorkshop = \App\Models\Workshop::where('title', $agendaItem->title)->first();
            if ($matchingWorkshop) {
                $workshopId = $matchingWorkshop->id;
                // Also backfill the agenda item
                $agendaItem->update(['workshop_id' => $workshopId]);
            }
        }
        if ($workshopId) {
            $existW = $registrant->workshops()->where('workshop_id', $workshopId)->first();
            if (!$existW) {
                $registrant->workshops()->attach($workshopId, $registrant->utmForPivot() + ['status' => 'pending']);
            }
        }

        $displayName = $agendaItem->workshop ? ($agendaItem->workshop->name ?: $agendaItem->workshop->title) : $agendaItem->title;
        return back()->with('success', "Registered for <strong>{$displayName}</strong>. Waiting for admin approval.");
    }

    /**
     * Unregister from an agenda item.
     */
    public function unregisterAgenda(Request $request, AgendaItem $agendaItem)
    {
        /** @var \App\Models\Registrant $registrant */
        $registrant = Auth::guard('registrant')->user();
        $agendaItem->load('workshop');

        // Cannot cancel if already approved by admin
        $existing = $registrant->agendaItems()->where('agenda_item_id', $agendaItem->id)->first();
        if ($existing && $existing->pivot->status === 'approved') {
            return back()->with('error', 'Your registration for this session has been approved. Please contact the organizer to cancel.');
        }

        // Mark as cancelled instead of deleting, so the admin can see who cancelled.
        $registrant->agendaItems()->updateExistingPivot($agendaItem->id, [
            'status'       => 'cancelled',
            'admin_notes'  => null,
            'processed_by' => null,
            'processed_at' => now(),
        ]);

        // Also unregister from linked workshop (mark cancelled, don't delete)
        $workshopId = $agendaItem->workshop_id;
        if (!$workshopId && $agendaItem->agenda_type === 'workshop') {
            $matchingWorkshop = \App\Models\Workshop::where('title', $agendaItem->title)->first();
            if ($matchingWorkshop) $workshopId = $matchingWorkshop->id;
        }
        if ($workshopId) {
            $existW = $registrant->workshops()->where('workshop_id', $workshopId)->first();
            if ($existW) {
                $registrant->workshops()->updateExistingPivot($workshopId, [
                    'status' => 'cancelled', 'admin_notes' => null, 'processed_by' => null, 'processed_at' => now(),
                ]);
            }
        }

        $displayName = $agendaItem->workshop ? ($agendaItem->workshop->name ?: $agendaItem->workshop->title) : $agendaItem->title;
        return back()->with('success', "Unregistered from <strong>{$displayName}</strong>.");
    }
}
