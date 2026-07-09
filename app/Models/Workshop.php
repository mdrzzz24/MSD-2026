<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
