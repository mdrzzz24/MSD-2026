<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Speaker extends Model
{
    protected $fillable = [
        'name', 'title', 'company', 'photo', 'bio', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function agendaItems()
    {
        return $this->belongsToMany(AgendaItem::class, 'agenda_item_speaker')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderByPivot('order');
    }

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'track_speaker')
                    ->withPivot('order')
                    ->withTimestamps()
                    ->orderByPivot('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
