<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Public walk-in registration — a shareable link so on-site attendees can
 * register themselves. Submissions always land in "pending" and are approved by
 * the admin later (e.g. when their badge is printed on the Onsite Event page).
 */
class WalkinRegisterController extends Controller
{
    /**
     * Show the public walk-in registration form (event-styled).
     */
    public function create()
    {
        return view('walkin.register');
    }

    /**
     * Process a public walk-in registration → status pending.
     */
    public function store(Request $request)
    {
        $wantsJson = $request->wantsJson() || $request->ajax();

        // Honeypot — if a bot fills the hidden field, silently ignore the request.
        if (filled($request->input('company_website'))) {
            return $wantsJson
                ? response()->json(['success' => true])
                : redirect()->route('walkin.public.create');
        }

        $validated = $request->validate([
            'firstName'      => ['required', 'string', 'max:255'],
            'lastName'       => ['required', 'string', 'max:255'],
            'job_role'       => ['required', 'string', 'max:255'],
            'job_title'      => ['required', 'string', 'max:255'],
            'company'        => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:registrants,email'],
            'phone'          => ['required', 'string', 'max:50'],
            'industry'       => ['required', 'string', 'max:255'],
            'employees'      => ['required', 'string', 'max:50'],
            'gdpr'           => ['accepted'],
            'referral_source'=> ['required', 'string', 'max:255'],
        ]);

        // Normalize phone number: ensure +62 prefix, remove leading 0 (same as general registration).
        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (substr($phone, 0, 2) === '62') {
            $phone = '+62' . substr($phone, 2);
        } elseif (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) !== '+') {
            $phone = '+62' . $phone;
        }

        $plainPassword = Str::random(8);

        $registrant = Registrant::create([
            'first_name'     => $validated['firstName'],
            'last_name'      => $validated['lastName'],
            'name'           => $validated['firstName'] . ' ' . $validated['lastName'],
            'job_role'       => $validated['job_role'],
            'job_title'      => $validated['job_title'],
            'company'        => $validated['company'],
            'email'          => $validated['email'],
            'phone'          => $phone,
            'industry'       => $validated['industry'],
            'employees'      => $validated['employees'],
            'gdpr'           => true,
            'referral_source'=> $validated['referral_source'],
            'status'         => 'pending',
            'password'       => $plainPassword,
            'plain_password' => $plainPassword,
            'qr_token'       => Registrant::generateQrToken(),
            'utm_source'     => 'walkin-link',
        ]);

        // AJAX/JSON request (walk-in card) — return the created registrant so the
        // page can show a success popup without navigating away.
        if ($wantsJson) {
            return response()->json([
                'success'    => true,
                'registrant' => [
                    'name'       => $registrant->name,
                    'company'    => $registrant->company,
                    'job_title'  => $registrant->job_title,
                    'job_role'   => $registrant->job_role,
                    'email'      => $registrant->email,
                    'phone'      => $registrant->phone,
                ],
            ]);
        }

        return view('walkin.success', compact('registrant'));
    }
}
