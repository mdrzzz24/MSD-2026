<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrLoginController extends Controller
{
    public function showForm(Request $request)
    {
        // Remember where the visitor came from (e.g. the feedback login popup)
        // so they are sent back there after a successful QR login.
        $redirect = $this->validatedRedirect($request->input('redirect'));
        if ($redirect) {
            session(['qr_login_redirect' => $redirect]);
        }

        return view('auth.qr-login');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $registrant = Registrant::where('email', $request->email)->first();

        if (!$registrant) {
            return response()->json(['success' => false, 'message' => 'No account found with this email address.'], 422);
        }
        if (!$registrant->isApproved()) {
            return response()->json(['success' => false, 'message' => 'Your account has not been approved yet.'], 422);
        }
        if (!$registrant->unique_code) {
            return response()->json(['success' => false, 'message' => 'No QR code assigned to this account.'], 422);
        }

        session(['qr_login_email' => $registrant->email]);

        return response()->json([
            'success' => true,
            'name'    => $registrant->display_name ?: $registrant->name,
            'initial' => strtoupper(substr($registrant->display_name ?? $registrant->name, 0, 1)),
        ]);
    }

    public function authenticate(Request $request)
    {
        $request->validate(['scanned_code' => ['required', 'string']]);

        $email = session('qr_login_email');
        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please refresh and try again.'], 422);
        }

        $registrant = Registrant::where('email', $email)->first();
        if (!$registrant || !$registrant->isApproved()) {
            return response()->json(['success' => false, 'message' => 'Invalid registrant.'], 422);
        }

        if (trim($request->scanned_code) !== $registrant->unique_code) {
            return response()->json(['success' => false, 'message' => 'Invalid QR code. Please try again.'], 422);
        }

        Auth::guard('registrant')->login($registrant);
        session()->forget('qr_login_email');

        // Send the user back to the page they were on (e.g. the feedback page).
        $redirect = session('qr_login_redirect');
        session()->forget('qr_login_redirect');

        return response()->json([
            'success'  => true,
            'redirect' => $redirect ?: route('home1'),
        ]);
    }

    /**
     * Validate a same-origin redirect path (prevents open redirects).
     */
    private function validatedRedirect(?string $redirect): ?string
    {
        if ($redirect !== null && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) {
            return $redirect;
        }

        return null;
    }
}
