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
        // Already authenticated as admin/client (session or remember-me cookie) — skip the form
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->is_admin || $user->role === 'client') {
                return redirect()->route('admin.dashboard');
            }

            Auth::logout();
        }

        // Already authenticated as an approved registrant — skip the form
        if (Auth::guard('registrant')->check()) {
            /** @var Registrant $registrant */
            $registrant = Auth::guard('registrant')->user();

            if ($registrant->isApproved()) {
                return redirect()->route('home1');
            }

            Auth::guard('registrant')->logout();
        }

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

                // Ensure remember token is set
                if ($remember && !$user->getRememberToken()) {
                    $user->setRememberToken(\Illuminate\Support\Str::random(60));
                    $user->save();
                }

                // Never land on a JSON/AJAX endpoint after login (e.g. the dashboard
                // polling endpoint /admin/dashboard/data). If the browser's last
                // "intended" URL is one of those, drop it so we always land on the
                // real dashboard page instead of showing raw JSON.
                $intended = session()->get('url.intended');
                if ($intended) {
                    $path = (string) parse_url($intended, PHP_URL_PATH);
                    $isJsonEndpoint = str_ends_with($path, '/data')
                        || str_ends_with($path, '/realtime')
                        || str_ends_with($path, '/rows')
                        || str_ends_with($path, '/search')
                        || str_contains($path, '/dashboard/daily/');
                    if ($isJsonEndpoint) {
                        session()->forget('url.intended');
                    }
                }

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

                // Ensure remember token is set for registrant
                if ($remember && !$registrant->getRememberToken()) {
                    $registrant->setRememberToken(\Illuminate\Support\Str::random(60));
                    $registrant->save();
                }

                // Allow the feedback login popup to send the user back to the page they were on.
                $redirectTo = $this->postLoginRedirect($request);
                return $redirectTo ? redirect()->to($redirectTo) : redirect()->intended(route('home1'));
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

                // Allow the feedback login popup to send the user back to the page they were on.
                $redirectTo = $this->postLoginRedirect($request);
                return $redirectTo ? redirect()->to($redirectTo) : redirect()->intended(route('home1'));
            }
        }

        // ── Both failed ──
        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    /**
     * Resolve an internal post-login redirect (used by the feedback login popup).
     * Only same-origin paths are allowed to prevent open redirects.
     */
    private function postLoginRedirect(Request $request): ?string
    {
        $next = (string) $request->input('redirect', '');

        if ($next !== '' && str_starts_with($next, '/') && !str_starts_with($next, '//')) {
            return $next;
        }

        return null;
    }

    public function logout(Request $request)
    {
        // If impersonating, redirect to leave impersonation instead
        if (session()->has('impersonating')) {
            return redirect()->route('admin.management.impersonate.leave');
        }

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
