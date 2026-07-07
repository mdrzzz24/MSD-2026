<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = ['start_time', 'end_time', 'order'];

    protected $casts = [
        'start_time' => 'string',
        'end_time'   => 'string',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('start_time');
    }

    public function label(): string
    {
        return date('H.i', strtotime($this->start_time)) . ' – ' . date('H.i', strtotime($this->end_time));
    }
}
