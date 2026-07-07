<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
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
        $registrant = Auth::guard('registrant')->user();

        // Workshops the registrant has already signed up for
        $myWorkshops = $registrant->workshops()->orderBy('date')->orderBy('start_time')->get();

        // All open workshops where registration is open
        $availableWorkshops = Workshop::where('registration_open', true)
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->filter(fn ($w) => !$registrant->workshops->contains($w->id));

        return view('registrant.dashboard', compact('registrant', 'myWorkshops', 'availableWorkshops'));
    }

    /**
     * Register for a workshop.
     */
    public function registerWorkshop(Request $request, Workshop $workshop)
    {
        $registrant = Auth::guard('registrant')->user();

        if (!$workshop->canRegister()) {
            return back()->with('error', 'Registration for this workshop is closed or full.');
        }

        if ($registrant->workshops()->where('workshop_id', $workshop->id)->exists()) {
            return back()->with('error', 'You are already registered for this workshop.');
        }

        // Check for time conflict
        $conflict = $registrant->workshops()->where(function ($q) use ($workshop) {
            $q->where('date', $workshop->date)
              ->where(function ($q2) use ($workshop) {
                  $q2->whereBetween('start_time', [$workshop->start_time, $workshop->end_time])
                     ->orWhereBetween('end_time', [$workshop->start_time, $workshop->end_time])
                     ->orWhere(function ($q3) use ($workshop) {
                         $q3->where('start_time', '<=', $workshop->start_time)
                            ->where('end_time', '>=', $workshop->end_time);
                     });
              });
        })->exists();

        if ($conflict) {
            return back()->with('error', 'You are already registered for another workshop at the same time.');
        }

        $registrant->workshops()->attach($workshop->id);

        return back()->with('success', "Successfully registered for workshop <strong>{$workshop->title}</strong>.");
    }

    /**
     * Unregister from a workshop.
     */
    public function unregisterWorkshop(Request $request, Workshop $workshop)
    {
        $registrant = Auth::guard('registrant')->user();
        $registrant->workshops()->detach($workshop->id);

        return back()->with('success', "Successfully unregistered from workshop <strong>{$workshop->title}</strong>.");
    }
}
