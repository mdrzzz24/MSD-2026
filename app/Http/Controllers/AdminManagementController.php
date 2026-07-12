<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\Registrant;
use App\Models\User;
use App\Models\UtmLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminManagementController extends Controller
{
    use Exportable;
    // ── UTM Sources ──

    public function utmSources()
    {
        $user = Auth::user();

        $sources = Registrant::whereNotNull('utm_source')
            ->selectRaw("utm_source, COUNT(*) as total,
                COUNT(CASE WHEN checked_in_at IS NOT NULL THEN 1 END) as checked_in,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count")
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->get();

        // Add "Direct" row for registrants without UTM
        $directTotal = Registrant::whereNull('utm_source')->count();
        $directChecked = Registrant::whereNull('utm_source')->whereNotNull('checked_in_at')->count();
        $directApproved = Registrant::whereNull('utm_source')->where('status', 'approved')->count();
        $directPending = Registrant::whereNull('utm_source')->where('status', 'pending')->count();
        $directRejected = Registrant::whereNull('utm_source')->where('status', 'rejected')->count();
        if ($directTotal > 0) {
            $sources->push((object) [
                'utm_source'      => null,
                'total'           => $directTotal,
                'checked_in'      => $directChecked,
                'approved_count'  => $directApproved,
                'pending_count'   => $directPending,
                'rejected_count'  => $directRejected,
            ]);
        }

        $totals = [
            'all' => $sources->sum('total'),
            'checked' => $sources->sum('checked_in'),
        ];

        // UTM Links — scope by user unless super_admin
        $utmLinks = UtmLink::when($user->role !== 'super_admin', function ($q) use ($user) {
                // If user belongs to a group, show all UTM links from the group
                if ($user->group_id) {
                    $groupUserIds = User::where('group_id', $user->group_id)->pluck('id')->toArray();
                    return $q->whereIn('created_by', $groupUserIds)
                        ->orWhereHas('sharedWith', fn($sq) => $sq->whereIn('user_id', $groupUserIds));
                }
                return $q->where('created_by', $user->id)
                    ->orWhereHas('sharedWith', fn($sq) => $sq->where('user_id', $user->id));
            })
            ->with('sharedWith')
            ->orderBy('created_at', 'desc')
            ->get();

        $clientUsers = User::where('role', 'client')->orderBy('name')->get();

        return view('admin.management.utm', compact('sources', 'totals', 'utmLinks', 'clientUsers'));
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

        $link = UtmLink::create(array_merge($request->all(), ['created_by' => Auth::id()]));
        $link->update(['full_url' => $link->buildUrl()]);

        return redirect()->route('admin.management.utm')
            ->with('success', "UTM Link <strong>{$link->name}</strong> created successfully.");
    }

    public function updateUtmLink(Request $request, UtmLink $utmLink)
    {
        $user = Auth::user();
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
        $user = Auth::user();
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

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->role !== 'client',
            'role'     => $request->role,
            'group_id' => $request->group_id ?: null,
        ];

        // Set permissions from request, or default for role
        if ($request->has('permissions') && is_array($request->permissions) && isset($request->permissions['_enabled'])) {
            $perms = $request->permissions;
            unset($perms['_enabled']);
            $data['permissions'] = User::normalizePermissions($perms);
        } else {
            $data['permissions'] = User::defaultPermissions($request->role);
        }

        User::create($data);

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
            'group_id' => $request->group_id ?: null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Save permissions
        if ($request->has('permissions') && is_array($request->permissions)) {
            $perms = $request->permissions;
            // _enabled flag = permissions section was shown → save even if all unchecked
            if (isset($perms['_enabled'])) {
                unset($perms['_enabled']);
                $data['permissions'] = User::normalizePermissions($perms);
            }
        } elseif ($request->role === 'super_admin') {
            $data['permissions'] = User::defaultPermissions('super_admin');
        }

        $user->update($data);

        return redirect()->route('admin.management.users')
            ->with('success', "User <strong>{$user->name}</strong> updated successfully.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
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

    /**
     * Export UTM sources detail to CSV — lists all registrants per UTM link.
     */
    public function exportUtmCsv()
    {
        $utmLinks = UtmLink::with('creator')->latest()->get();

        $headers = ['UTM Name', 'Source', 'Medium', 'Campaign', 'URL',
                     'Registrant Name', 'Email', 'Phone', 'Company', 'Job Title',
                     'Status', 'Checked In', 'Registered At'];

        $rows = [];
        foreach ($utmLinks as $u) {
            $registrants = \App\Models\Registrant::where('utm_source', $u->utm_source)
                ->where('utm_medium', $u->utm_medium)
                ->where('utm_campaign', $u->utm_campaign)
                ->latest()
                ->get();

            foreach ($registrants as $r) {
                $rows[] = [
                    $u->name,
                    $u->utm_source,
                    $u->utm_medium,
                    $u->utm_campaign,
                    $u->full_url ?? $u->buildUrl(),
                    $r->display_name ?: $r->name,
                    $r->email,
                    $r->phone ?? '-',
                    $r->company ?? '-',
                    $r->job_title ?? '-',
                    $r->status ?? '-',
                    $r->checked_in_at ? 'Yes' : 'No',
                    $r->created_at->format('Y-m-d H:i'),
                ];
            }
        }

        // Also include registrants without UTM as "Direct"
        $direct = \App\Models\Registrant::whereNull('utm_source')->latest()->get();
        foreach ($direct as $r) {
            $rows[] = [
                '(Direct)',
                '', '', '', '',
                $r->display_name ?: $r->name,
                $r->email,
                $r->phone ?? '-',
                $r->company ?? '-',
                $r->job_title ?? '-',
                $r->status ?? '-',
                $r->checked_in_at ? 'Yes' : 'No',
                $r->created_at->format('Y-m-d H:i'),
            ];
        }

        return $this->csvDownload($headers, $rows, 'utm-detail-' . now()->format('YmdHis') . '.csv');
    }

    /**
     * Export QR codes list to CSV.
     */
    public function exportQrCsv()
    {
        $rows = Registrant::approved()
            ->whereNotNull('qr_token')
            ->latest()
            ->get()
            ->map(fn($r) => [
                $r->display_name ?: $r->name,
                $r->email,
                $r->unique_code ?? '',
                $r->qr_token ?? '',
                $r->company ?? '',
                $r->checked_in_at ? $r->checked_in_at->format('Y-m-d H:i:s') : 'Not checked in',
            ])->toArray();

        return $this->csvDownload(
            ['Name', 'Email', 'Unique Code', 'QR Token', 'Company', 'Check-in Status'],
            $rows,
            'qr-codes-' . now()->format('YmdHis') . '.csv'
        );
    }

    /**
     * Export check-in log to CSV.
     */
    public function exportCheckinCsv()
    {
        $rows = Registrant::approved()
            ->whereNotNull('checked_in_at')
            ->orderByDesc('checked_in_at')
            ->get()
            ->map(fn($r) => [
                $r->display_name ?: $r->name,
                $r->email,
                $r->unique_code ?? '',
                $r->company ?? '',
                $r->checked_in_at->format('Y-m-d H:i:s'),
            ])->toArray();

        return $this->csvDownload(
            ['Name', 'Email', 'Unique Code', 'Company', 'Checked In At'],
            $rows,
            'checkin-log-' . now()->format('YmdHis') . '.csv'
        );
    }
}
