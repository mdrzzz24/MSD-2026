<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkshopInvitation extends Model
{
    protected $fillable = [
        'workshop_id',
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

    public function isValid(): bool
    {
        return $this->is_active && $this->use_count < $this->max_uses;
    }

    public function incrementUse(): void
    {
        $this->increment('use_count');
    }
}
