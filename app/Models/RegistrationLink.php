<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'registrant_id',
        'source_url',
        'landing_url',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the registrant that owns this link record.
     */
    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }
}
