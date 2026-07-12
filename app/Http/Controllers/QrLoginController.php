<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrLoginController extends Controller
{
    public function showForm()
    {
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

        return response()->json([
            'success'  => true,
            'redirect' => route('home1'),
        ]);
    }
}
