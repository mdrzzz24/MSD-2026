<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminConfigController extends Controller
{
    /**
     * Show the Event Checker app config + QR code (super admin only).
     */
    public function show(Request $request)
    {
        $host = $request->getSchemeAndHttpHost();

        return view('admin.config.index', compact('host'));
    }
}
