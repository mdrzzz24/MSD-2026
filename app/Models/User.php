<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
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
