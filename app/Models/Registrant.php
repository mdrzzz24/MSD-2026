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
        'client_remark',
        'client_remark_action',
        'client_remarked_by',
        'client_remarked_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'processed_at'       => 'datetime',
        'checked_in_at'      => 'datetime',
        'client_remarked_at' => 'datetime',
        'gdpr'               => 'boolean',
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
                    ->withPivot(['status', 'admin_notes', 'processed_by', 'processed_at', 'track_id',
                                 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content'])
                    ->withTimestamps();
    }

    /**
     * UTM values to store on the workshop pivot when a registration is created.
     * $override wins when provided (e.g. from a workshop invitation link).
     */
    public function utmForPivot(array $override = []): array
    {
        return array_merge([
            'utm_source'   => $this->utm_source,
            'utm_medium'   => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content'  => $this->utm_content,
        ], $override);
    }

    /**
     * Determine whether this registrant already has a non-rejected workshop or
     * agenda-item registration whose time window overlaps [\$start, \$end].
     *
     * Workshop times are resolved from the workshop's first linked agenda item
     * when the workshop fields are empty, and a null date is treated as the
     * same event day (the event is a single-day event). Rejected registrations
     * and the workshop/agenda item being registered are excluded.
     */
    public function hasTimeConflict(int $excludeWorkshopId, ?int $excludeAgendaItemId, $date, $start, $end): bool
    {
        if (!$start || !$end) {
            return false;
        }

        $newStart = strtotime($start);
        $newEnd   = strtotime($end);

        $overlaps = function ($s, $e) use ($newStart, $newEnd): bool {
            if (!$s || !$e) {
                return false;
            }
            return strtotime($s) < $newEnd && strtotime($e) > $newStart;
        };

        $sameDay = function ($a, $b): bool {
            if (!$a || !$b) {
                return true; // unknown date = same event day
            }
            return $a->format('Y-m-d') === $b->format('Y-m-d');
        };

        // 1) Existing workshop-level registrations (exclude rejected + the workshop being registered)
        foreach ($this->workshops()->wherePivot('status', '!=', 'rejected')->with('agendaItems')->get() as $w) {
            if ($w->id === $excludeWorkshopId) {
                continue;
            }
            $ai = $w->agendaItems->first();
            $wsDate  = $w->date ?? $ai?->date;
            $wsStart = $w->start_time ?? $ai?->start_time;
            $wsEnd   = $w->end_time ?? $ai?->end_time;

            if ($sameDay($date, $wsDate) && $overlaps($wsStart, $wsEnd)) {
                return true;
            }
        }

        // 2) Existing agenda-item registrations (exclude rejected + the agenda item being registered)
        foreach ($this->agendaItems()->wherePivot('status', '!=', 'rejected')->get() as $a) {
            if ($excludeAgendaItemId && $a->id === $excludeAgendaItemId) {
                continue;
            }
            if ($sameDay($date, $a->date) && $overlaps($a->start_time, $a->end_time)) {
                return true;
            }
        }

        return false;
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

    /**
     * Client user who submitted a remark on this registrant.
     */
    public function clientRemarkedBy()
    {
        return $this->belongsTo(User::class, 'client_remarked_by');
    }

    /**
     * Check if this registrant has a client remark.
     */
    public function hasClientRemark(): bool
    {
        return !is_null($this->client_remark_action);
    }
}
