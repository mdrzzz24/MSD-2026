<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'floor', 'floor_id', 'order'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function floorRelation()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function isSecondFloor(): bool
    {
        return $this->floorRelation?->name === 'Second Floor';
    }
}
