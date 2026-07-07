<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $fillable = ['name', 'order'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
