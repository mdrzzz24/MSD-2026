<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BounceCheckController extends Controller
{
    /**
     * Run the bounce check artisan command and return output.
     */
    public function run()
    {
        try {
            Artisan::call('email:check-bounces');
            $output = Artisan::output();

            return back()->with('info', nl2br(e($output)));
        } catch (\Exception $e) {
            return back()->with('error', 'Bounce check failed: ' . $e->getMessage());
        }
    }
}
