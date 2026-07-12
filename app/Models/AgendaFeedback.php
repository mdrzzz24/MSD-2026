<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgendaFeedback extends Model
{
    protected $fillable = [
        'agenda_item_id',
        'registrant_id',
        'name',
        'email',
    ];

    public function agendaItem(): BelongsTo
    {
        return $this->belongsTo(AgendaItem::class);
    }

    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AgendaFeedbackAnswer::class, 'agenda_feedback_id');
    }
}
