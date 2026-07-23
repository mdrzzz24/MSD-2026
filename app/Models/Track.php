<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = ['workshop_id', 'name', 'title', 'description', 'is_active', 'start_time', 'end_time'];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_time' => 'string',
        'end_time'   => 'string',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function agendaItems()
    {
        return $this->hasMany(AgendaItem::class);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'track_speaker')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderByPivot('order');
    }

    public function invitations()
    {
        return $this->hasMany(WorkshopInvitation::class);
    }

    public function registrantsCount(): int
    {
        return $this->countByStatus('approved');
    }

    public function pendingCount(): int
    {
        return $this->countByStatus('pending');
    }

    public function rejectedCount(): int
    {
        return $this->countByStatus('rejected');
    }

    private function countByStatus(string $status): int
    {
        return \App\Models\Registrant::whereIn('id', function ($q) use ($status) {
            $q->select('registrant_id')
              ->from('agenda_item_registrant')
              ->whereIn('agenda_item_id', $this->agendaItems()->select('id'))
              ->where('status', $status);
        })->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
