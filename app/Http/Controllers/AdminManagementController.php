<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\Registrant;
use App\Models\Track;
use App\Models\User;
use App\Models\UtmLink;
use App\Models\Workshop;
use App\Models\WorkshopInvitation;
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

        // Mark which sources already have a UtmLink record
        $linkedSources = UtmLink::whereIn('utm_source', $sources->pluck('utm_source'))
            ->pluck('utm_source')
            ->unique()
            ->toArray();
        foreach ($sources as $src) {
            $src->has_link = in_array($src->utm_source, $linkedSources);
        }

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

        // Separate unlinked sources (no UtmLink record) into their own section
        $unlinkedSources = $sources->filter(fn($src) => $src->utm_source && !$src->has_link)->values();

        // Remove unlinked sources from main sources (keep only linked + Direct)
        $sources = $sources->filter(fn($src) => !$src->utm_source || $src->has_link)->values();

        $totals = [
            'all' => $sources->sum('total'),
            'checked' => $sources->sum('checked_in'),
        ];

        $untrackedTotals = [
            'all'     => $unlinkedSources->sum('total'),
            'checked' => $unlinkedSources->sum('checked_in'),
        ];

        // UTM Links — event registration only (workshop UTM is separate); scope by user unless super_admin
        $utmLinks = UtmLink::forEvent()->when($user->role !== 'super_admin', function ($q) use ($user) {
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

        return view('admin.management.utm', compact('sources', 'totals', 'utmLinks', 'clientUsers', 'unlinkedSources', 'untrackedTotals'));
    }

    // ── UTM Link CRUD ──

    public function storeUtmLink(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'utm_source'   => ['required', 'string', 'max:100'],
            'utm_medium'   => ['required', 'string', 'max:100'],
            'utm_campaign' => ['required', 'string', 'max:100'],
            'utm_content'  => ['nullable', 'string', 'max:100'],
        ]);

        $link = UtmLink::create(array_merge($request->all(), [
            'base_url'   => UtmLink::BASE_URL,
            'created_by' => Auth::id(),
        ]));
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
            'utm_source'   => ['required', 'string', 'max:100'],
            'utm_medium'   => ['required', 'string', 'max:100'],
            'utm_campaign' => ['required', 'string', 'max:100'],
            'utm_content'  => ['nullable', 'string', 'max:100'],
        ]);

        $utmLink->update(array_merge($request->all(), [
            'base_url' => UtmLink::BASE_URL,
        ]));
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

    // ── Workshop UTM Links (separate from event UTM, under Workshop menu) ──

    public function workshopUtmLinks()
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view workshop UTM links.');
        }

        $utmLinks = UtmLink::forWorkshop()->with('workshop')
            ->when($user->role !== 'super_admin', function ($q) use ($user) {
                // Own links + links created by any client (clients can see each other's links)
                $clientIds = User::where('role', 'client')->pluck('id');
                return $q->where(function ($sub) use ($user, $clientIds) {
                    $sub->where('created_by', $user->id)
                        ->orWhereIn('created_by', $clientIds);
                });
            })
            ->latest()->get();
        $workshops = Workshop::orderBy('name')->orderBy('title')->get();

        // For the create/edit modal: choose custom slug (invitation) + track
        $invitations = WorkshopInvitation::with(['workshop', 'track'])->get()->map(fn($i) => [
            'id'          => $i->id,
            'workshop_id' => $i->workshop_id,
            'track_id'    => $i->track_id,
            'slug'        => $i->slug,
            'token'       => $i->token,
            'is_active'   => $i->is_active,
            'track_name'  => $i->track?->name,
        ])->values();
        $tracks = Track::with('workshop')->get()->map(fn($t) => [
            'id'          => $t->id,
            'workshop_id' => $t->workshop_id,
            'name'        => $t->name,
        ])->values();

        return view('admin.workshops.utm-links', compact('utmLinks', 'workshops', 'invitations', 'tracks'));
    }

    public function exportWorkshopUtmLinks()
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to export workshop UTM links.');
        }

        $utmLinks = UtmLink::forWorkshop()->with('workshop')
            ->when($user->role !== 'super_admin', function ($q) use ($user) {
                // Same visibility as the page: own links + links created by any client
                $clientIds = User::where('role', 'client')->pluck('id');
                return $q->where(function ($sub) use ($user, $clientIds) {
                    $sub->where('created_by', $user->id)
                        ->orWhereIn('created_by', $clientIds);
                });
            })
            ->latest()->get();

        $headers = ['Name', 'Workshop', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content', 'Full URL', 'Created By', 'Registrations', 'Created At'];
        $rows = $utmLinks->map(fn($l) => [
            $l->name,
            $l->workshop ? ($l->workshop->name ?: $l->workshop->title) : '',
            $l->utm_source,
            $l->utm_medium,
            $l->utm_campaign,
            $l->utm_content ?? '',
            $l->full_url ?? $l->buildUrl(),
            $l->creator?->name ?? '',
            $l->workshopRegistrationsCount(),
            $l->created_at?->copy()->addHours(7)->format('Y-m-d H:i') ?? '',
        ])->toArray();

        return $this->csvDownload($headers, $rows, 'workshop-utm-links-' . now()->format('YmdHis') . '.csv');
    }

    public function storeWorkshopUtmLink(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage workshop UTM links.');
        }

        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'workshop_id'  => ['required', 'exists:workshops,id'],
            'utm_source'   => ['required', 'string', 'max:100'],
            'utm_medium'   => ['required', 'string', 'max:100'],
            'utm_campaign' => ['required', 'string', 'max:100'],
            'utm_content'  => ['nullable', 'string', 'max:100'],
            'workshop_invitation_id' => ['nullable', 'integer', 'exists:workshop_invitations,id'],
            'track_id'     => ['nullable', 'integer', 'exists:tracks,id'],
        ]);

        // Resolve invitation defensively: must belong to the chosen workshop, else fall back to auto-detect
        $workshopInvitationId = null;
        if ($request->input('workshop_invitation_id')) {
            $inv = WorkshopInvitation::find($request->input('workshop_invitation_id'));
            if ($inv && $inv->workshop_id == $request->input('workshop_id')) {
                $workshopInvitationId = $inv->id;
            }
        }

        // Resolve track defensively: must belong to the chosen workshop
        $trackId = null;
        if ($request->input('track_id')) {
            $tr = \App\Models\Track::find($request->input('track_id'));
            if ($tr && $tr->workshop_id == $request->input('workshop_id')) {
                $trackId = $tr->id;
            }
        }

        $link = UtmLink::create([
            'name'                    => $request->input('name'),
            'base_url'                => UtmLink::BASE_URL,
            'target_type'             => 'workshop',
            'workshop_id'             => $request->input('workshop_id'),
            'workshop_invitation_id'  => $workshopInvitationId,
            'track_id'                => $trackId,
            'utm_source'              => $request->input('utm_source'),
            'utm_medium'              => $request->input('utm_medium'),
            'utm_campaign'            => $request->input('utm_campaign'),
            'utm_content'             => $request->input('utm_content'),
            'created_by'              => Auth::id(),
        ]);
        $link->update(['full_url' => $link->buildUrl()]);

        return back()->with('success', "Workshop UTM Link <strong>{$link->name}</strong> created successfully.");
    }

    public function updateWorkshopUtmLink(Request $request, UtmLink $utmLink)
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage workshop UTM links.');
        }
        if ($user->role !== 'super_admin' && $utmLink->created_by !== $user->id) {
            return redirect()->route('admin.workshops.utm-links')->with('error', 'You can only edit your own workshop UTM links.');
        }

        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'workshop_id'  => ['required', 'exists:workshops,id'],
            'utm_source'   => ['required', 'string', 'max:100'],
            'utm_medium'   => ['required', 'string', 'max:100'],
            'utm_campaign' => ['required', 'string', 'max:100'],
            'utm_content'  => ['nullable', 'string', 'max:100'],
            'workshop_invitation_id' => ['nullable', 'integer', 'exists:workshop_invitations,id'],
            'track_id'     => ['nullable', 'integer', 'exists:tracks,id'],
        ]);

        // Resolve invitation defensively: must belong to the chosen workshop, else fall back to auto-detect
        $workshopInvitationId = null;
        if ($request->input('workshop_invitation_id')) {
            $inv = WorkshopInvitation::find($request->input('workshop_invitation_id'));
            if ($inv && $inv->workshop_id == $request->input('workshop_id')) {
                $workshopInvitationId = $inv->id;
            }
        }

        // Resolve track defensively: must belong to the chosen workshop
        $trackId = null;
        if ($request->input('track_id')) {
            $tr = \App\Models\Track::find($request->input('track_id'));
            if ($tr && $tr->workshop_id == $request->input('workshop_id')) {
                $trackId = $tr->id;
            }
        }

        $utmLink->update([
            'name'                    => $request->input('name'),
            'base_url'                => UtmLink::BASE_URL,
            'target_type'             => 'workshop',
            'workshop_id'             => $request->input('workshop_id'),
            'workshop_invitation_id'  => $workshopInvitationId,
            'track_id'                => $trackId,
            'utm_source'              => $request->input('utm_source'),
            'utm_medium'              => $request->input('utm_medium'),
            'utm_campaign'            => $request->input('utm_campaign'),
            'utm_content'             => $request->input('utm_content'),
        ]);
        $utmLink->update(['full_url' => $utmLink->buildUrl()]);

        return back()->with('success', "Workshop UTM Link <strong>{$utmLink->name}</strong> updated.");
    }

    public function destroyWorkshopUtmLink(UtmLink $utmLink)
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to manage workshop UTM links.');
        }
        if ($user->role !== 'super_admin' && $utmLink->created_by !== $user->id) {
            return redirect()->route('admin.workshops.utm-links')->with('error', 'You can only delete your own workshop UTM links.');
        }

        $name = $utmLink->name;
        $utmLink->delete();
        return back()->with('success', "Workshop UTM Link <strong>{$name}</strong> deleted.");
    }

    /**
     * View registrants who registered for the workshop via a specific UTM link.
     */
    public function utmLinkRegistrants(UtmLink $utmLink)
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to view workshop UTM link registrants.');
        }

        // Same visibility as the list: super_admin sees all; others only own/client-created links
        if ($user->role !== 'super_admin') {
            $clientIds = User::where('role', 'client')->pluck('id');
            $allowed = $utmLink->created_by === $user->id || $clientIds->contains($utmLink->created_by);
            if (!$allowed) {
                return redirect()->route('admin.workshops.utm-links')->with('error', 'You can only view registrants for your own workshop UTM links.');
            }
        }

        $utmLink->load(['workshop', 'track', 'workshopInvitation']);
        $workshop = $utmLink->workshop;

        // Pivot rows attributed to this link's UTM
        $pivotRows = \Illuminate\Support\Facades\DB::table('registrant_workshop')
            ->where('utm_source', $utmLink->utm_source)
            ->where('utm_medium', $utmLink->utm_medium)
            ->where('utm_campaign', $utmLink->utm_campaign)
            ->when($utmLink->utm_content, fn($q) => $q->where('utm_content', $utmLink->utm_content))
            ->orderByDesc('created_at')
            ->get();

        $registrantIds = $pivotRows->pluck('registrant_id')->unique()->values();
        $registrants = Registrant::whereIn('id', $registrantIds)->get();

        $trackLookup = $workshop ? $workshop->tracks()->get()->keyBy('id') : collect();
        $pivotByReg = $pivotRows->keyBy('registrant_id');

        foreach ($registrants as $r) {
            $p = $pivotByReg->get($r->id);
            $r->pivot_track_id    = $p?->track_id;
            $r->pivot_status      = $p?->status ?? 'pending';
            $r->pivot_admin_notes = $p?->admin_notes;
            $r->pivot_utm_source  = $p?->utm_source;
            $r->pivot_utm_medium  = $p?->utm_medium;
            $r->pivot_utm_campaign = $p?->utm_campaign;
            $r->pivot_utm_content = $p?->utm_content;
            $r->pivot_created_at  = $p?->created_at ? \Carbon\Carbon::parse($p->created_at) : null;
            $r->registered_track_name = ($p?->track_id && $trackLookup->has($p->track_id))
                ? $trackLookup[$p->track_id]->name
                : null;
        }

        // Keep newest-first by pivot created_at
        $registrants = $registrants->sortByDesc(fn($r) => $r->pivot_created_at?->timestamp ?? 0)->values();

        return view('admin.workshops.utm-link-registrants', compact('utmLink', 'registrants', 'workshop'));
    }

    /**
     * CSV export of registrants attributed to a specific workshop UTM link.
     */
    public function utmLinkRegistrantsCsv(UtmLink $utmLink)
    {
        $user = Auth::user();
        if (!$user->hasPermission('workshops') && !$user->hasPermission('workshop_registrants')) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to export workshop UTM link registrants.');
        }
        if ($user->role !== 'super_admin') {
            $clientIds = User::where('role', 'client')->pluck('id');
            $allowed = $utmLink->created_by === $user->id || $clientIds->contains($utmLink->created_by);
            if (!$allowed) {
                return redirect()->route('admin.workshops.utm-links')->with('error', 'You can only export registrants for your own workshop UTM links.');
            }
        }

        $utmLink->load(['workshop', 'track']);
        $workshop = $utmLink->workshop;

        $pivotRows = \Illuminate\Support\Facades\DB::table('registrant_workshop')
            ->where('utm_source', $utmLink->utm_source)
            ->where('utm_medium', $utmLink->utm_medium)
            ->where('utm_campaign', $utmLink->utm_campaign)
            ->when($utmLink->utm_content, fn($q) => $q->where('utm_content', $utmLink->utm_content))
            ->orderByDesc('created_at')
            ->get();
        $registrantIds = $pivotRows->pluck('registrant_id')->unique()->values();
        $registrants = Registrant::whereIn('id', $registrantIds)->get()->keyBy('id');
        $trackLookup = $workshop ? $workshop->tracks()->get()->keyBy('id') : collect();

        $headers = ['Name', 'Email', 'Phone', 'Company', 'Job Title', 'Job Role', 'Workshop', 'Track', 'WS Status', 'Reg Status', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'UTM Content', 'Joined Workshop', 'Check-in'];
        $rows = $pivotRows->map(function ($p) use ($registrants, $trackLookup, $workshop) {
            $r = $registrants->get($p->registrant_id);
            $trackName = ($p->track_id && $trackLookup->has($p->track_id)) ? $trackLookup[$p->track_id]->name : '';
            return [
                $r?->display_name ?? '',
                $r?->email ?? '',
                $r?->phone ?? '',
                $r?->company ?? '',
                $r?->job_title ?? '',
                $r?->job_role ?? '',
                $workshop ? ($workshop->name ?: $workshop->title) : '',
                $trackName,
                $p->status ?? '',
                $r?->status ?? '',
                $p->utm_source ?? '',
                $p->utm_medium ?? '',
                $p->utm_campaign ?? '',
                $p->utm_content ?? '',
                $p->created_at ? \Carbon\Carbon::parse($p->created_at)->copy()->addHours(7)->format('Y-m-d H:i') : '',
                $r?->checked_in_at ? \Carbon\Carbon::parse($r->checked_in_at)->copy()->addHours(7)->format('Y-m-d H:i') : '',
            ];
        })->toArray();

        $filename = 'utm-' . Str::slug($workshop ? ($workshop->name ?: $workshop->title) : 'workshop')
            . ($utmLink->track ? '-' . Str::slug($utmLink->track->name) : '')
            . '-registrants-' . now()->format('YmdHis') . '.csv';
        return $this->csvDownload($headers, $rows, $filename);
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
