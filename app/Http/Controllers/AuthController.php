<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                return redirect()->intended(route('home1'));
            }

            // Not approved yet
            Auth::guard('registrant')->logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has not been approved by admin yet.',
            ]);
        }

        // ── Both failed ──
        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    public function logout(Request $request)
    {
        // Log out from both guards (admin & registrant)
        Auth::logout();
        Auth::guard('registrant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
