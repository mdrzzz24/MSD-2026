<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get a specific permission value from this group.
     */
    public function hasPermission(string $key): bool
    {
        $perms = $this->permissions ?? [];
        return isset($perms[$key]) && filter_var($perms[$key], FILTER_VALIDATE_BOOLEAN);
    }
}
