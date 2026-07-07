<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;

class QrScanController extends Controller
{
    /**
     * Handle QR code scan — show registrant info & allow check-in.
     */
    public function scan($token)
    {
        $registrant = Registrant::where('qr_token', $token)->firstOrFail();

        return view('admin.qr-scan', compact('registrant'));
    }

    /**
     * Process check-in (mark as arrived).
     */
    public function checkin(Request $request, $token)
    {
        $registrant = Registrant::where('qr_token', $token)->firstOrFail();

        if (!$registrant->isApproved()) {
            return back()->with('error', 'Registrant is not approved.');
        }

        $registrant->update([
            'checked_in_at' => now(),
        ]);

        return back()->with('success', "Check-in successful for <strong>{$registrant->name}</strong>!");
    }
}
