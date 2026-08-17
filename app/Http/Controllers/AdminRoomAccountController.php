<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\Booth;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Mobile-app account management (super admin only): room accounts & booth
 * accounts — login credentials for the mobile apps.
 *
 * Room accounts (role = 'room') are bound to a room; super admins assign which
 * agenda sessions each account can track via `agenda_item_room_account`.
 * Booth accounts (role = 'booth') are bound to a booth; they can only scan
 * their own booth in the mobile app.
 */
class AdminRoomAccountController extends Controller
{
    /**
     * List all mobile-app room & booth accounts.
     */
    public function index()
    {
        $roomAccounts = User::where('role', 'room')
            ->with('room')
            ->withCount('managedAgendaItems')
            ->orderBy('name')
            ->get();

        $boothAccounts = User::where('role', 'booth')
            ->with('booth')
            ->orderBy('name')
            ->get();

        $rooms  = Room::ordered()->get();
        $booths = Booth::ordered()->get();

        return view('admin.room-accounts.index', compact('roomAccounts', 'boothAccounts', 'rooms', 'booths'));
    }

    /**
     * Create a new room or booth account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'role'     => ['required', 'in:room,booth'],
            'room_id'  => ['nullable', 'required_if:role,room', 'exists:rooms,id'],
            'booth_id' => ['nullable', 'required_if:role,booth', 'exists:booths,id'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'is_admin'    => false,
            'role'        => $request->role,
            'room_id'     => $request->role === 'room' ? $request->room_id : null,
            'booth_id'    => $request->role === 'booth' ? $request->booth_id : null,
            'permissions' => User::defaultPermissions($request->role),
        ]);

        $type = $request->role === 'room' ? 'Room account' : 'Booth account';

        return redirect()->route('admin.room-accounts.index')
            ->with('success', "<strong>{$type}</strong> <strong>{$request->name}</strong> created successfully.");
    }

    /**
     * Show the session-assignment form for one room account.
     */
    public function sessions(User $user)
    {
        abort_unless($user->isRoomAccount(), 404);

        // Time slots for rowspan-aware display end times (e.g. Confluent 13:00–14:30).
        $timeSlots = \App\Models\TimeSlot::ordered()->get();

        // Only grid-anchored workshop items (whose start–end exactly matches a
        // real time slot) appear on the public agenda. Redundant duplicates — the
        // same workshop again with an extended end_time (e.g. 13:00–14:30) that
        // matches no slot — are dropped so each workshop is listed only once.
        // Non-workshop sessions (general / track / break) are always kept.
        $slotKeys = $timeSlots->map(fn ($s) => $s->start_time . '-' . $s->end_time);

        // Workshops that already have registered attendees. Workshops with no
        // registrations are not listed (non-workshop sessions are always shown).
        $workshopsWithRegs = \App\Models\Workshop::whereHas('registrants')->pluck('id');

        $items = AgendaItem::ordered()
            ->with(['workshop', 'track'])
            ->get()
            ->filter(fn ($i) => ! $i->workshop_id || $slotKeys->contains($i->start_time . '-' . $i->end_time))
            ->filter(fn ($i) => ! $i->workshop_id || $workshopsWithRegs->contains($i->workshop_id))
            ->groupBy(fn ($i) => $i->room ?: '(No room)');

        $assigned = $user->managedAgendaItems()->pluck('agenda_items.id')->all();

        return view('admin.room-accounts.sessions', compact('user', 'items', 'assigned', 'timeSlots'));
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
