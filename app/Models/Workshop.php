<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
        'room',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'registration_open',
    ];

    protected $casts = [
        'date'              => 'date',
        'start_time'        => 'string',
        'end_time'          => 'string',
        'registration_open' => 'boolean',
    ];

    public function registrants()
    {
        return $this->belongsToMany(Registrant::class, 'registrant_workshop')
                    ->withTimestamps()
                    ->withPivot(['status', 'admin_notes', 'processed_by', 'processed_at', 'id']);
    }

    public function waitlist()
    {
        return $this->belongsToMany(Registrant::class, 'workshop_waitlist')
                    ->withTimestamps();
    }

    public function agendaItems()
    {
        return $this->hasMany(AgendaItem::class);
    }

    public function invitations()
    {
        return $this->hasMany(WorkshopInvitation::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('registration_open', true);
    }

    public function registrationsCount(): int
    {
        return $this->registrants()->wherePivot('status', 'approved')->count();
    }

    public function pendingCount(): int
    {
        return $this->registrants()->wherePivot('status', 'pending')->count();
    }

    public function rejectedCount(): int
    {
        return $this->registrants()->wherePivot('status', 'rejected')->count();
    }

    public function waitlistCount(): int
    {
        return $this->waitlist()->count();
    }

    public function isFull(): bool
    {
        return $this->capacity > 0 && $this->registrationsCount() >= $this->capacity;
    }

    public function canRegister(): bool
    {
        return $this->registration_open && !$this->isFull();
    }

    public function hasAvailability(): bool
    {
        return $this->registration_open && ($this->capacity === 0 || $this->registrationsCount() < $this->capacity);
    }

    public function timeRange(): string
    {
        if (!$this->start_time || !$this->end_time) return '—';
        return date('H:i', strtotime($this->start_time)) . ' – ' . date('H:i', strtotime($this->end_time));
    }

    /**
     * Get email placeholder data for this workshop.
     * Falls back to the first linked agenda item if workshop fields are empty.
     */
    public function emailData(): array
    {
        $agendaItem = $this->agendaItems()->first();

        $room     = $this->room ?? $agendaItem?->room ?? '';
        $date     = $this->date ?? $agendaItem?->date;
        $start    = $this->start_time ?? $agendaItem?->start_time;
        $end      = $this->end_time ?? $agendaItem?->end_time;
        $capacity = $this->capacity ?: ($agendaItem?->capacity ?: 0);

        $timeRange = '—';
        if ($start && $end) {
            $timeRange = date('H:i', strtotime($start)) . ' – ' . date('H:i', strtotime($end));
        }

        return [
            'workshop_name'     => $this->name ?: $this->title,
            'workshop_title'    => $this->title,
            'workshop_room'     => $room,
            'workshop_date'     => $date ? $date->format('l, d F Y') : '',
            'workshop_time'     => $timeRange,
            'workshop_capacity' => (string) $capacity,
            'venue_name'        => 'Shangri-La Hotel Jakarta',
        ];
    }
}
