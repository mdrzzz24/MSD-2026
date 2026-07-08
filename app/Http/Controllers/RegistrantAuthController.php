<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegistrantAuthController extends Controller
{
    /**
     * Handle registration form submission (public).
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'firstName'     => ['required', 'string', 'max:255'],
                'lastName'      => ['required', 'string', 'max:255'],
                'title'         => ['required', 'string', 'max:255'],
                'company'       => ['required', 'string', 'max:255'],
                'email'         => ['required', 'email', 'max:255', 'unique:registrants,email'],
                'phone'         => ['required', 'string', 'max:50'],
                'industry'      => ['required', 'string', 'max:255'],
                'employees'     => ['required', 'string', 'max:50'],
                'gdpr'           => ['accepted'],
                'referral_source'=> ['required', 'string', 'max:255'],
                'referral_code'  => ['nullable', 'string', 'max:20'],
                'attended_before' => ['nullable', 'boolean'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $registrant = Registrant::create([
            'first_name'      => $validated['firstName'],
            'last_name'       => $validated['lastName'],
            'name'            => $validated['firstName'] . ' ' . $validated['lastName'],
            'job_title'       => $validated['title'],
            'company'         => $validated['company'],
            'email'           => $validated['email'],
            'phone'           => $validated['phone'],
            'industry'        => $validated['industry'],
            'employees'       => $validated['employees'],
            'gdpr'             => true,
            'referral_source'  => $validated['referral_source'],
            'referral_code'    => $request->input('referral_code'),
            'attended_before' => $request->boolean('attended_before'),
            'utm_source'      => $request->input('utm_source'),
            'utm_medium'      => $request->input('utm_medium'),
            'utm_campaign'    => $request->input('utm_campaign'),
            'utm_content'     => $request->input('utm_content'),
            'status'          => 'pending',
        ]);

        // Link to managed referral code if it exists and is active
        if ($referralCode = $request->input('referral_code')) {
            $rc = ReferralCode::where('code', $referralCode)
                ->where('is_active', true)
                ->first();
            if ($rc && $rc->canBeUsed()) {
                $registrant->update(['referral_code_id' => $rc->id]);
                $rc->incrementUses();
            }
        }

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
