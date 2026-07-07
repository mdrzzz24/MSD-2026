<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrantAuthController extends Controller
{
    /**
     * Handle registration form submission (public).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstName' => ['required', 'string', 'max:255'],
            'lastName'  => ['required', 'string', 'max:255'],
            'title'     => ['required', 'string', 'max:255'],
            'company'   => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:registrants,email'],
            'phone'     => ['required', 'string', 'max:50'],
            'industry'  => ['required', 'string', 'max:255'],
            'employees' => ['required', 'string', 'max:50'],
            'gdpr'      => ['accepted'],
        ]);

        Registrant::create([
            'first_name' => $validated['firstName'],
            'last_name'  => $validated['lastName'],
            'name'       => $validated['firstName'] . ' ' . $validated['lastName'],
            'job_title'  => $validated['title'],
            'company'    => $validated['company'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'industry'   => $validated['industry'],
            'employees'  => $validated['employees'],
            'gdpr'       => true,
            'status'     => 'pending',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('register.success'),
            ]);
        }

        return redirect()->route('register.success')
            ->with('success', 'Registration successful! Please wait for admin confirmation.');
    }

    /**
     * Show registration success page.
     */
    public function success()
    {
        return view('registrant.success');
    }

    /**
     * Redirect registrant login to the unified login page.
     */
    public function showLoginForm()
    {
        return redirect()->route('login');
    }

    /**
     * Handle registrant logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('registrant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
