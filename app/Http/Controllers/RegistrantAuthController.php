<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use App\Models\Registrant;
use App\Models\RegistrationLink;
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
                'job_role'      => ['required', 'string', 'max:255'],
                'job_title'     => ['required', 'string', 'max:255'],
                'company'       => ['required', 'string', 'max:255'],
                'email'         => ['required', 'email', 'max:255', 'unique:registrants,email',
                    function ($attribute, $value, $fail) {
                        $freeDomains = [
                            'gmail.com', 'yahoo.com', 'yahoo.co.id', 'ymail.com',
                            'hotmail.com', 'outlook.com', 'live.com',
                            'aol.com', 'icloud.com', 'me.com', 'mac.com',
                            'protonmail.com', 'proton.me', 'mail.com',
                            'gmx.com', 'gmx.net', 'zoho.com', 'yandex.com',
                            'tutanota.com', 'fastmail.com', 'rocketmail.com',
                        ];
                        $domain = strtolower(substr(strrchr($value, '@'), 1));
                        if (in_array($domain, $freeDomains)) {
                            $fail('Please use your company email address. Free email providers (Gmail, Yahoo, etc.) are not accepted.');
                        }
                    },
                ],
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

        // Normalize phone number: ensure +62 prefix, remove leading 0
        $phone = $validated['phone'];
        $phone = preg_replace('/[^0-9]/', '', $phone);          // strip non-digits
        if (substr($phone, 0, 2) === '62') {
            $phone = '+62' . substr($phone, 2);                // 628xx → +628xx
        } elseif (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);                // 08xx → +628xx
        } elseif (substr($phone, 0, 1) !== '+') {
            $phone = '+62' . $phone;                            // raw digits → +62...
        }

        $registrant = Registrant::create([
            'first_name'      => $validated['firstName'],
            'last_name'       => $validated['lastName'],
            'name'            => $validated['firstName'] . ' ' . $validated['lastName'],
            'job_role'        => $validated['job_role'],
            'job_title'       => $validated['job_title'],
            'company'         => $validated['company'],
            'email'           => $validated['email'],
            'phone'           => $phone,
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

        // ── Capture registration link source ──
        RegistrationLink::create([
            'registrant_id' => $registrant->id,
            'source_url'    => $request->headers->get('referer'),
            'landing_url'   => $request->fullUrl(),
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        // ── Send auto-reply email using Registration template (if toggle is ON) ──
        if (\Illuminate\Support\Facades\Cache::get('auto_registration_email', true)) {
            $sent = \App\Services\EmailService::sendByType($registrant, \App\Models\EmailTemplate::TYPE_REGISTRATION);
            if (!$sent) {
                // Fallback: send via blade view
                try {
                    \Illuminate\Support\Facades\Mail::send('emails.registration-clean', [
                        'name' => $registrant->display_name,
                        'registrant' => $registrant,
                    ], function ($msg) use ($registrant) {
                        $msg->to($registrant->email)->subject('Thank you for your interest in MSD 2026!');
                    });
                    \App\Models\EmailLog::create([
                        'email_template_id' => null,
                        'registrant_id'     => $registrant->id,
                        'template_type'     => 'registration',
                        'recipient_email'   => $registrant->email,
                        'recipient_name'    => $registrant->display_name,
                        'subject'           => 'Thank you for your interest in MSD 2026!',
                        'html_content'      => null,
                        'status'            => 'sent',
                        'sent_at'           => now(),
                    ]);
                } catch (\Throwable $e) {
                    \App\Models\EmailLog::create([
                        'email_template_id' => null,
                        'registrant_id'     => $registrant->id,
                        'template_type'     => 'registration',
                        'recipient_email'   => $registrant->email,
                        'recipient_name'    => $registrant->display_name,
                        'subject'           => 'Thank you for your interest in MSD 2026!',
                        'html_content'      => null,
                        'status'            => 'failed',
                        'error_message'     => $e->getMessage(),
                        'sent_at'           => now(),
                    ]);
                }
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
