<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'name',
        'email',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'session_id',
        'impersonated_by',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the user this log belongs to (polymorphic-like).
     */
    public function user()
    {
        return $this->morphTo();
    }

    /**
     * Scope: only admin/user (User model) logs.
     */
    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }

    /**
     * Scope: only registrant logs.
     */
    public function scopeRegistrants($query)
    {
        return $query->where('user_type', 'registrant');
    }

    /**
     * Scope: currently logged in (no logout_at).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('logout_at');
    }

    /**
     * Scope: logs from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('login_at', today());
    }

    /**
     * Check if this session is still active.
     */
    public function isActive(): bool
    {
        return is_null($this->logout_at);
    }

    /**
     * Mark this session as logged out.
     */
    public function markLoggedOut(): void
    {
        $this->update(['logout_at' => now()]);
    }
}
