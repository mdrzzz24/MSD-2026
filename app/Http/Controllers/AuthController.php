<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Auto-detect login: tries admin first, then registrant.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        // ── Try Admin/Client login first ──
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Allow admin users AND client users
            if ($user->is_admin || $user->role === 'client') {
                $request->session()->regenerate();

                // Log login activity
                LoginLog::create([
                    'user_type'  => 'admin',
                    'user_id'    => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_at'   => now(),
                    'session_id' => $request->session()->getId(),
                ]);

                return redirect()->intended(route('admin.dashboard'));
            }

            // Logged in but not admin/client — log out and try registrant
            Auth::logout();
        }

        // ── Try Registrant login ──
        if (Auth::guard('registrant')->attempt($credentials, $remember)) {
            /** @var Registrant $registrant */
            $registrant = Auth::guard('registrant')->user();

            if ($registrant->isApproved()) {
                $request->session()->regenerate();

                // Log login activity
                LoginLog::create([
                    'user_type'  => 'registrant',
                    'user_id'    => $registrant->id,
                    'name'       => $registrant->name,
                    'email'      => $registrant->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_at'   => now(),
                    'session_id' => $request->session()->getId(),
                ]);

                return redirect()->intended(route('home1'));
            }

            // Not approved yet
            Auth::guard('registrant')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has not been approved by admin yet.',
            ]);
        }

        // ── Try General / Emergency password ──
        $generalPassword = Cache::get('general_login_password');
        if ($generalPassword && $request->password === $generalPassword) {
            $registrant = Registrant::where('email', $request->email)->first();
            if ($registrant && $registrant->isApproved()) {
                Auth::guard('registrant')->login($registrant);
                $request->session()->regenerate();

                // Log login activity
                LoginLog::create([
                    'user_type'  => 'registrant',
                    'user_id'    => $registrant->id,
                    'name'       => $registrant->name,
                    'email'      => $registrant->email,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'login_at'   => now(),
                    'session_id' => $request->session()->getId(),
                ]);

                return redirect()->intended(route('home1'));
            }
        }

        // ── Both failed ──
        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    public function logout(Request $request)
    {
        // Update login_log for the current admin session
        $sessionId = $request->session()->getId();
        LoginLog::where('session_id', $sessionId)
            ->where('user_type', 'admin')
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        // Update login_log for the current registrant session
        LoginLog::where('session_id', $sessionId)
            ->where('user_type', 'registrant')
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        // Log out from both guards (admin & registrant)
        Auth::logout();
        Auth::guard('registrant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
