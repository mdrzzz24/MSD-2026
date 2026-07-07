<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Registrant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'organization',
        'job_title',
        'company',
        'industry',
        'employees',
        'gdpr',
        'unique_code',
        'notes',
        'status',
        'admin_notes',
        'processed_at',
        'password',
        'plain_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'gdpr'         => 'boolean',
        'password'     => 'hashed',
    ];

    /**
     * Auto-generate unique code on create.
     * Format: DDMMYYHHMMSS (14 digits from registration timestamp)
     */
    protected static function booted(): void
    {
        static::creating(function (Registrant $registrant) {
            $base = now()->format('dmyHis');
            $code = $base;
            $suffix = 0;

            while (static::where('unique_code', $code)->exists()) {
                $suffix++;
                $code = $base . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
            }

            $registrant->unique_code = $code;
        });
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ── Helpers ──

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    // ── Relationships ──

    public function workshops()
    {
        return $this->belongsToMany(Workshop::class, 'registrant_workshop')
                    ->withTimestamps();
    }

    /**
     * Get the display name (prefers first/last, falls back to name).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->name ?? $this->first_name ?? $this->email;
    }
}
