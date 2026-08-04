<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A client's decision selection (approve/reject/waitlist) that has NOT been
 * submitted yet. Persisted in the DB so it does not expire until submitted.
 */
class ClientPendingMark extends Model
{
    protected $fillable = ['user_id', 'registrant_id', 'action', 'reason'];

    protected $casts = [
        'user_id'       => 'integer',
        'registrant_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }
}
