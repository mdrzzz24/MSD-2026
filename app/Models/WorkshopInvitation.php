<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkshopInvitation extends Model
{
    protected $fillable = [
        'workshop_id',
        'track_id',
        'token',
        'email',
        'max_uses',
        'use_count',
        'is_active',
    ];

    protected $casts = [
        'max_uses'  => 'integer',
        'use_count' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (WorkshopInvitation $invite) {
            if (empty($invite->token)) {
                $invite->token = Str::random(32);
            }
        });
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        // max_uses = 0 berarti unlimited
        return $this->max_uses === 0 || $this->use_count < $this->max_uses;
    }

    public function isUnlimited(): bool
    {
        return $this->max_uses === 0;
    }

    public function incrementUse(): void
    {
        $this->increment('use_count');
    }
}
