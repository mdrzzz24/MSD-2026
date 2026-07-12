<?php

namespace App\Http\Controllers;

use App\Mail\ClientInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ClientInviteController extends Controller
{
    /**
     * Show invite form.
     */
    public function showInviteForm()
    {
        return view('admin.users.invite');
    }

    /**
     * Send invitation email.
     */
    public function sendInvite(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $token = Str::random(40);

        $user = User::create([
            'name'                   => $request->name,
            'email'                  => $request->email,
            'password'               => Hash::make(Str::random(20)), // temp placeholder
            'is_admin'               => false,
            'role'                   => 'client',
            'permissions'            => User::defaultPermissions('client'),
            'setup_token'            => $token,
            'setup_token_expires_at' => now()->addHours(48),
        ]);

        $setupUrl = route('client.setup-password', ['token' => $token]);

        try {
            Mail::to($user->email)->send(new ClientInvitation($user, $setupUrl));
        } catch (\Exception $e) {
            $user->delete();
            return back()->with('error', 'Failed to send invitation email: ' . $e->getMessage())->withInput();
        }

        return back()->with('success', 'Invitation sent to <strong>' . e($user->email) . '</strong>. They have 48 hours to set up their password.');
    }

    /**
     * Show set-password form.
     */
    public function showSetupForm(string $token)
    {
        $user = User::where('setup_token', $token)
            ->where('setup_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return view('auth.setup-password', ['user' => null, 'token' => $token]);
        }

        return view('auth.setup-password', [
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Save password and log in.
     */
    public function savePassword(Request $request, string $token)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::where('setup_token', $token)
            ->where('setup_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return back()->with('error', 'Invalid or expired invitation link.');
        }

        $user->update([
            'password'               => Hash::make($request->password),
            'setup_token'            => null,
            'setup_token_expires_at' => null,
        ]);

        // Log the user in
        auth()->login($user);

        return redirect()->intended(route('admin.dashboard'));
    }
}
