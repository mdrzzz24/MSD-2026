<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\Registrant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminImpersonateController extends Controller
{
    /**
     * Show the impersonation page (list of users & registrants).
     */
    public function index()
    {
        $current = Auth::user();
        if (!$current->isSuperAdmin()) {
            abort(403);
        }

        $users = User::where('id', '!=', $current->id)
            ->orderBy('name')
            ->get();

        $registrants = Registrant::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('admin.management.impersonate', compact('users', 'registrants'));
    }

    /**
     * Start impersonating a user (admin or client).
     */
    public function impersonate($userId, Request $request)
    {
        $target = User::findOrFail($userId);
        $current = Auth::user();

        if (!$current->isSuperAdmin()) {
            abort(403);
        }

        if ($target->id === $current->id) {
            return redirect()->route('admin.management.impersonate.index')
                ->with('error', 'Cannot impersonate yourself.');
        }

        // Store original admin in session
        session()->put('impersonator_id', $current->id);
        session()->put('impersonator_name', $current->name);
        session()->put('impersonating', true);

        // Log the impersonation BEFORE logout/login
        LoginLog::create([
            'user_type'       => 'admin',
            'user_id'         => $target->id,
            'name'            => $target->name,
            'email'           => $target->email,
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'login_at'        => now(),
            'session_id'      => $request->session()->getId(),
            'impersonated_by' => $current->id,
        ]);

        // Logout & login as target
        Auth::logout();
        Auth::login($target);

        return redirect()->route('admin.dashboard')
            ->with('success', ' Impersonating <strong>' . e($target->name) . '</strong>. Click "Return to Admin" to go back.');
    }

    /**
     * Start impersonating a registrant.
     */
    public function impersonateRegistrant($registrantId, Request $request)
    {
        $registrant = Registrant::findOrFail($registrantId);
        $current = Auth::user();

        if (!$current->isSuperAdmin()) {
            abort(403);
        }

        if (!$registrant->isApproved()) {
            return redirect()->route('admin.management.impersonate.index')
                ->with('error', 'Cannot impersonate an unapproved registrant.');
        }

        // Store original admin in session
        session()->put('impersonator_id', $current->id);
        session()->put('impersonator_name', $current->name);
        session()->put('impersonating', true);

        // Log the impersonation BEFORE logout/login
        LoginLog::create([
            'user_type'       => 'registrant',
            'user_id'         => $registrant->id,
            'name'            => $registrant->name,
            'email'           => $registrant->email,
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'login_at'        => now(),
            'session_id'      => $request->session()->getId(),
            'impersonated_by' => $current->id,
        ]);

        // Logout from admin guard & login as registrant via registrant guard
        Auth::logout();
        Auth::guard('registrant')->login($registrant);

        return redirect()->route('home1')
            ->with('success', ' Impersonating registrant <strong>' . e($registrant->name) . '</strong>.');
    }

    /**
     * Leave impersonation and return to the original admin account.
     */
    public function leave(Request $request)
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('admin.dashboard');
        }

        $originalId = session('impersonator_id');
        $original = User::find($originalId);

        if (!$original) {
            session()->forget(['impersonator_id', 'impersonator_name', 'impersonating']);
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->with('error', 'Original admin account not found. Please login again.');
        }

        // Update login_log for logout of impersonated session
        $sessionId = $request->session()->getId();
        LoginLog::where('session_id', $sessionId)
            ->whereNull('logout_at')
            ->whereNotNull('impersonated_by')
            ->update(['logout_at' => now()]);

        // Logout impersonated user
        Auth::logout();
        Auth::guard('registrant')->logout();

        // Clear impersonation session data
        session()->forget(['impersonator_id', 'impersonator_name', 'impersonating']);

        // Login back as original admin
        Auth::login($original);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', '⬅ Returned to your account.');
    }
}
