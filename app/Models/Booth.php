<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booth extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    /**
     * Registrants who visited this booth.
     */
    public function visitors()
    {
        return $this->belongsToMany(Registrant::class, 'booth_visits')
            ->withTimestamps()
            ->withPivot(['visited_at', 'id']);
    }

    /**
     * Booth visit records.
     */
    public function visits()
    {
        return $this->hasMany(BoothVisit::class);
    }

    /**
     * Total unique visitors for this booth.
     */
    public function visitorCount(): int
    {
        return $this->visitors()->count();
    }

    /**
     * Scope active booths.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
