<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role', 'permissions', 'setup_token', 'setup_token_expires_at', 'group_id'])]
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

    public function hasPermission(string $key): bool
    {
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check group permissions first
        if ($this->group) {
            $groupPerms = $this->group->permissions ?? [];
            if (isset($groupPerms[$key])) {
                return filter_var($groupPerms[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }

        // Fall back to individual permissions
        $perms = $this->permissions ?? [];
        return filter_var($perms[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
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
                'registrants' => true, 'workshops' => true, 'workshop_registrants' => true,
                'tracks' => true, 'utm_sources' => true, 'qr_codes' => true,
            ] + array_combine($all, array_fill(0, count($all), false)),
            'client' => [
                'workshops' => true, 'utm_sources' => true, 'qr_codes' => true,
            ] + array_combine($all, array_fill(0, count($all), false)),
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
