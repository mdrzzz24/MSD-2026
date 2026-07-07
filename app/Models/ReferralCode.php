<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $fillable = [
        'code',
        'owner_name',
        'description',
        'max_uses',
        'uses_count',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'max_uses'   => 'integer',
        'uses_count' => 'integer',
        'is_active'  => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrants()
    {
        return $this->hasMany(Registrant::class);
    }

    /**
     * Check if this code can still be used.
     */
    public function canBeUsed(): bool
    {
        if (!$this->is_active) return false;
        if ($this->max_uses > 0 && $this->uses_count >= $this->max_uses) return false;
        return true;
    }

    /**
     * Increment usage count.
     */
    public function incrementUses(): void
    {
        $this->increment('uses_count');
    }
}
