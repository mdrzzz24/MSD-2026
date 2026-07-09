<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use App\Models\User;
use App\Models\UtmLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminManagementController extends Controller
{
    // ── UTM Sources ──

    public function utmSources()
    {
        $user = auth()->user();

        $sources = Registrant::whereNotNull('utm_source')
            ->selectRaw('utm_source, COUNT(*) as total, COUNT(CASE WHEN checked_in_at IS NOT NULL THEN 1 END) as checked_in')
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->get();

        // Add "Direct" row for registrants without UTM
        $directTotal = Registrant::whereNull('utm_source')->count();
        $directChecked = Registrant::whereNull('utm_source')->whereNotNull('checked_in_at')->count();
        if ($directTotal > 0) {
            $sources->push((object) [
                'utm_source' => null,
                'total'      => $directTotal,
                'checked_in' => $directChecked,
            ]);
        }

        $totals = [
            'all' => $sources->sum('total'),
            'checked' => $sources->sum('checked_in'),
        ];

        // UTM Links — scope by admin unless super_admin
        $utmLinks = UtmLink::when($user->role !== 'super_admin', fn($q) => $q->where('created_by', $user->id))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.management.utm', compact('sources', 'totals', 'utmLinks'));
    }

    // ── UTM Link CRUD ──

    public function storeUtmLink(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'base_url'     => ['required', 'url', 'max:500'],
            'utm_source'   => ['required', 'string', 'max:100'],
            'utm_medium'   => ['required', 'string', 'max:100'],
            'utm_campaign' => ['required', 'string', 'max:100'],
            'utm_content'  => ['nullable', 'string', 'max:100'],
        ]);

        $link = UtmLink::create(array_merge($request->all(), ['created_by' => auth()->id()]));
        $link->update(['full_url' => $link->buildUrl()]);

        return redirect()->route('admin.management.utm')
            ->with('success', "UTM Link <strong>{$link->name}</strong> created successfully.");
    }

    public function updateUtmLink(Request $request, UtmLink $utmLink)
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin' && $utmLink->created_by !== $user->id) {
            return redirect()->route('admin.management.utm')->with('error', 'You can only edit your own UTM links.');
        }

        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'base_url'     => ['required', 'url', 'max:500'],
            'utm_source'   => ['required', 'string', 'max:100'],
            'utm_medium'   => ['required', 'string', 'max:100'],
            'utm_campaign' => ['required', 'string', 'max:100'],
            'utm_content'  => ['nullable', 'string', 'max:100'],
        ]);

        $utmLink->update($request->all());
        $utmLink->update(['full_url' => $utmLink->buildUrl()]);

        return redirect()->route('admin.management.utm')
            ->with('success', "UTM Link <strong>{$utmLink->name}</strong> updated.");
    }

    public function destroyUtmLink(UtmLink $utmLink)
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin' && $utmLink->created_by !== $user->id) {
            return redirect()->route('admin.management.utm')->with('error', 'You can only delete your own UTM links.');
        }

        $name = $utmLink->name;
        $utmLink->delete();
        return redirect()->route('admin.management.utm')
            ->with('success', "UTM Link <strong>{$name}</strong> deleted.");
    }

    // ── QR Codes (list all approved with QR) ──

    public function qrCodes()
    {
        if (auth()->user()->isClient()) {
            return redirect()->route('admin.dashboard')->with('error', 'Clients do not have access to QR codes.');
        }

        $registrants = Registrant::approved()
            ->whereNotNull('qr_token')
            ->latest()
            ->paginate(20);

        return view('admin.management.qr-codes', compact('registrants'));
    }

    // ── User Management (Super Admin only) ──

    public function users()
    {
        $users = User::orderBy('created_at')->get();
        return view('admin.management.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', 'in:admin,super_admin,client'],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->role !== 'client',
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.management.users')
            ->with('success', "User <strong>{$request->name}</strong> created successfully.");
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:admin,super_admin,client'],
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'is_admin' => $request->role !== 'client',
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.management.users')
            ->with('success', "User <strong>{$user->name}</strong> updated successfully.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.management.users')
            ->with('success', "User <strong>{$name}</strong> deleted successfully.");
    }

    // ── Check-in Log ──

    public function checkinLog()
    {
        $checkedIn = Registrant::approved()
            ->whereNotNull('checked_in_at')
            ->orderByDesc('checked_in_at')
            ->paginate(30);

        return view('admin.management.checkin-log', compact('checkedIn'));
    }
}
