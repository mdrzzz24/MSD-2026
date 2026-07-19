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
        'job_role',
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
        'qr_token',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'referral_code',
        'referral_code_id',
        'attended_before',
        'referral_source',
        'checked_in_at',
        'approved_by',
        'rejected_by',
        'assigned_to',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'processed_at'  => 'datetime',
        'checked_in_at' => 'datetime',
        'gdpr'          => 'boolean',
        'attended_before' => 'boolean',
        'password'      => 'hashed',
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

    /**
     * Generate a unique QR token.
     */
    public static function generateQrToken(): string
    {
        return strtolower(
            substr(md5(uniqid((string) mt_rand(), true)), 0, 16)
        );
    }

    /**
     * Get the QR code URL (via API) encoding the unique code.
     */
    public function getQrCodeUrlAttribute(): string
    {
        $data = $this->unique_code ?? $this->qr_token;
        return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($data);
    }

    /**
     * Get the QR check-in URL.
     */
    public function getQrCheckinUrlAttribute(): string
    {
        return $this->qr_token
            ? route('registrant.qr-scan', $this->qr_token)
            : '';
    }

    /**
     * Get the shareable QR view URL.
     */
    public function getQrShareUrlAttribute(): string
    {
        return $this->qr_token
            ? route('registrant.qr-share', $this->qr_token)
            : '';
    }

    // ── Relationships ──

    public function workshops()
    {
        return $this->belongsToMany(Workshop::class, 'registrant_workshop')
                    ->withPivot(['status', 'admin_notes', 'processed_by', 'processed_at'])
                    ->withTimestamps();
    }

    /**
     * Workshops waiting list.
     */
    public function workshopWaitlists()
    {
        return $this->belongsToMany(Workshop::class, 'workshop_waitlist')
                    ->withTimestamps();
    }

    /**
     * Admin who approved this registrant.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Admin who rejected this registrant.
     */
    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Admin or Client this registrant is assigned to.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Email logs for this registrant.
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'registrant_id');
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

    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class);
    }

    /**
     * Registration link source for this registrant.
     */
    public function registrationLink()
    {
        return $this->hasOne(RegistrationLink::class);
    }

    /**
     * Agenda items the registrant has signed up for.
     */
    public function agendaItems()
    {
        return $this->belongsToMany(AgendaItem::class, 'agenda_item_registrant')
                    ->withTimestamps()
                    ->withPivot(['status', 'admin_notes', 'processed_by', 'processed_at', 'id']);
    }
}
