<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Mobile-app "room account" management (super admin only).
 *
 * Each room account is a user with role = 'room' bound to a room (room_id).
 * Super admins assign specific agenda sessions to each account via
 * `agenda_item_room_account`. An account with NO assignments manages ALL
 * sessions (default); once sessions are assigned, it can only track those.
 */
class AdminRoomAccountController extends Controller
{
    /**
     * List all mobile-app room accounts.
     */
    public function index()
    {
        $accounts = User::where('role', 'room')
            ->with('room')
            ->withCount('managedAgendaItems')
            ->orderBy('name')
            ->get();

        $rooms = Room::ordered()->get();

        return view('admin.room-accounts.index', compact('accounts', 'rooms'));
    }

    /**
     * Create a new room account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'room_id'  => ['required', 'exists:rooms,id'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'is_admin'    => false,
            'role'        => 'room',
            'room_id'     => $request->room_id,
            'permissions' => User::defaultPermissions('room'),
        ]);

        return redirect()->route('admin.room-accounts.index')
            ->with('success', "Room account <strong>{$request->name}</strong> created successfully.");
    }

    /**
     * Show the session-assignment form for one room account.
     */
    public function sessions(User $user)
    {
        abort_unless($user->isRoomAccount(), 404);

        $items = AgendaItem::ordered()
            ->with(['workshop', 'track'])
            ->get()
            ->groupBy(fn ($i) => $i->room ?: '(No room)');

        $assigned = $user->managedAgendaItems()->pluck('agenda_items.id')->all();

        return view('admin.room-accounts.sessions', compact('user', 'items', 'assigned'));
    }

    /**
     * Save the session assignments for one room account.
     */
    public function saveSessions(Request $request, User $user)
    {
        abort_unless($user->isRoomAccount(), 404);

        $validated = $request->validate([
            'agenda_item_ids'   => ['nullable', 'array'],
            'agenda_item_ids.*' => ['integer'],
        ]);

        $ids = array_values(array_map('intval', $validated['agenda_item_ids'] ?? []));

        $user->managedAgendaItems()->sync($ids);

        $count = $user->managedAgendaItems()->count();

        return redirect()->route('admin.room-accounts.index')
            ->with('success', 'Assignments for <strong>' . e($user->name) . '</strong> saved. '
                . ($count === 0
                    ? 'This account now manages <strong>ALL sessions</strong> (default).'
                    : "This account can now track <strong>{$count} session(s)</strong>."));
    }
}
