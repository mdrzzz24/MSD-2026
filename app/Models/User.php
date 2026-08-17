<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role', 'permissions', 'setup_token', 'setup_token_expires_at', 'group_id', 'room_id', 'booth_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function assignedRegistrants()
    {
        return $this->hasMany(Registrant::class, 'assigned_to');
    }

    /**
     * The room this mobile-app account is bound to (organizational label).
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * The booth this mobile-app account is bound to (organizational label).
     */
    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    /**
     * Whether this is a mobile-app booth account (role = 'booth').
     */
    public function isBoothAccount(): bool
    {
        return $this->role === 'booth';
    }

    /**
     * The name of the booth this account is bound to.
     */
    public function boothName(): ?string
    {
        return $this->booth?->name;
    }

    /**
     * The booth IDs this account is allowed to manage / track via the mobile
     * API, or null when unrestricted. A booth account is scoped to its own
     * booth; unbound accounts are unrestricted (backward compatible).
     */
    public function scopedBoothIds(): ?array
    {
        if (!$this->isBoothAccount()) {
            return null;
        }

        return $this->booth_id ? [$this->booth_id] : null;
    }

    /**
     * Agenda sessions explicitly assigned to this account by the super admin.
     */
    public function managedAgendaItems()
    {
        return $this->belongsToMany(AgendaItem::class, 'agenda_item_room_account')->withTimestamps();
    }

    /**
     * Whether this is a mobile-app room account (role = 'room').
     */
    public function isRoomAccount(): bool
    {
        return $this->role === 'room';
    }

    /**
     * The name of the room this account is bound to.
     */
    public function roomName(): ?string
    {
        return $this->room?->name;
    }

    /**
     * The agenda item IDs this account is allowed to manage / track via the
     * mobile API, or null when unrestricted.
     *
     * - Non-room accounts            → null (unrestricted).
     * - Room account with NO session assignments → null (manages ALL sessions).
     * - Room account WITH assignments → only the assigned session IDs.
     */
    public function scopedAgendaItemIds(): ?array
    {
        if (!$this->isRoomAccount()) {
            return null;
        }

        $ids = $this->managedAgendaItems()->pluck('agenda_items.id')->all();

        return $ids === [] ? null : array_map('intval', $ids);
    }

    public function hasPermission(string $key): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Start with group permissions as baseline
        $effectivePerms = [];
        if ($this->group) {
            $groupPerms = $this->group->permissions ?? [];
            $effectivePerms = $groupPerms;
        }

        // Individual permissions can override group on a per-key basis
        $userPerms = $this->permissions ?? [];
        foreach ($userPerms as $k => $v) {
            $effectivePerms[$k] = filter_var($v, FILTER_VALIDATE_BOOLEAN);
        }

        return filter_var($effectivePerms[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'permissions' => 'array',
            'setup_token_expires_at' => 'datetime',
        ];
    }

    /**
     * All available permission keys with their display labels.
     */
    public static function allPermissions(): array
    {
        return [
            'registrants'         => 'Registrants',
            'workshops'           => 'Workshops',
            'workshop_registrants'=> 'Workshop Registrants',
            'tracks'              => 'Tracks',
            'agenda'              => 'Agenda',
            'speakers'            => 'Speakers',
            'time_slots'          => 'Time Slots',
            'rooms'               => 'Rooms & Floors',
            'email_templates'     => 'Email Templates',
            'utm_sources'         => 'UTM Sources',
            'qr_codes'            => 'QR Codes',
            'checkin_log'         => 'Check-in Log',
            'admin_users'         => 'Admin Users',
            'booths'              => 'Booths',
            'booth_visits'        => 'Booth Visits',
            'login_logs'          => 'Login Logs',
        ];
    }

    /**
     * Default permissions for a given role.
     */
    public static function defaultPermissions(string $role): array
    {
        $all = array_keys(self::allPermissions());
        return match ($role) {
            'super_admin' => array_combine($all, array_fill(0, count($all), true)),
            'admin' => [
                'registrants' => true, 'workshop_registrants' => true,
                'tracks' => true, 'utm_sources' => true, 'qr_codes' => true,
            ] + array_combine($all, array_fill(0, count($all), false)),
            'client' => [
                'registrants' => true, 'workshop_registrants' => true, 'utm_sources' => true, 'qr_codes' => true,
            ] + array_combine($all, array_fill(0, count($all), false)),
            // Mobile-app room accounts have no admin panel access at all.
            'room' => array_combine($all, array_fill(0, count($all), false)),
            // Mobile-app booth accounts have no admin panel access at all.
            'booth' => array_combine($all, array_fill(0, count($all), false)),
            default => [],
        };
    }

    /**
     * Normalize permission array: ensure all keys exist with boolean values.
     */
    public static function normalizePermissions(?array $perms): array
    {
        $all = array_keys(self::allPermissions());
        $result = [];
        foreach ($all as $key) {
            $result[$key] = filter_var($perms[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
        }
        return $result;
    }

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_admin && $this->role === 'super_admin';
    }

    /**
     * Check if the user is a regular admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin && $this->role === 'admin';
    }

    /**
     * Check if the user is a client (view-only with UTM generation).
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if the user can perform write operations (approve/reject/delete).
     */
    public function canWrite(): bool
    {
        return $this->is_admin && !$this->isClient();
    }
}
