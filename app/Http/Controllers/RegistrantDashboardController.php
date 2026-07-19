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

        // Workshops the registrant has already signed up for (with pivot status)
        $myWorkshops = $registrant->workshops()
            ->with('agendaItems')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Agenda items (tracks/workshops) the registrant has registered for
        $myAgendaItems = $registrant->agendaItems()
            ->with('workshop')
            ->orderBy('start_time')
            ->get();

        // Deduplicate: exclude agenda items whose linked workshop already appears in $myWorkshops
        $workshopIds = $myWorkshops->pluck('id')->toArray();
        $myAgendaItems = $myAgendaItems->filter(function ($item) use ($workshopIds) {
            return !$item->workshop || !in_array($item->workshop->id, $workshopIds);
        });

        // All open workshops where registration is open and not already registered
        $registeredIds = $myWorkshops->pluck('id')->toArray();
        $availableWorkshops = Workshop::with('agendaItems')
            ->where('registration_open', true)
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->filter(fn ($w) => !in_array($w->id, $registeredIds));

        return view('registrant.dashboard', compact('registrant', 'myWorkshops', 'myAgendaItems', 'availableWorkshops'));
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

        if ($wsDate && $wsStart && $wsEnd) {
            // Check for time conflict with workshops (exclude rejected)
            $conflict = $registrant->workshops()
                ->where('workshops.id', '!=', $workshop->id)
                ->wherePivot('status', '!=', 'rejected')
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
                })->exists();

            // Also check time conflict with agenda item registrations on the same date (exclude rejected)
            if (!$conflict) {
                $conflict = $registrant->agendaItems()
                    ->wherePivot('status', '!=', 'rejected')
                    ->where(function ($q) use ($wsDate) {
                        $q->where('agenda_items.date', $wsDate)
                          ->orWhereNull('agenda_items.date');
                    })
                    ->where(function ($q) use ($wsStart, $wsEnd) {
                        $q->where('start_time', '<', $wsEnd)
                           ->where('end_time', '>', $wsStart);
                    })->exists();
            }

            if ($conflict) {
                return back()->with('error', 'You are already registered for another session at the same time.');
            }
        }

        $registrant->workshops()->attach($workshop->id, ['status' => 'pending']);

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

        $registrant->workshops()->detach($workshop->id);

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
            ->wherePivot('status', '!=', 'rejected')
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
                ->wherePivot('status', '!=', 'rejected')
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
                $registrant->workshops()->attach($workshopId, ['status' => 'pending']);
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

        $registrant->agendaItems()->detach($agendaItem->id);

        // Also unregister from linked workshop
        $workshopId = $agendaItem->workshop_id;
        if (!$workshopId && $agendaItem->agenda_type === 'workshop') {
            $matchingWorkshop = \App\Models\Workshop::where('title', $agendaItem->title)->first();
            if ($matchingWorkshop) $workshopId = $matchingWorkshop->id;
        }
        if ($workshopId) {
            $registrant->workshops()->detach($workshopId);
        }

        $displayName = $agendaItem->workshop ? ($agendaItem->workshop->name ?: $agendaItem->workshop->title) : $agendaItem->title;
        return back()->with('success', "Unregistered from <strong>{$displayName}</strong>.");
    }
}
