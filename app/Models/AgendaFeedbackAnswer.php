<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaFeedbackAnswer extends Model
{
    protected $fillable = [
        'agenda_feedback_id',
        'agenda_item_question_id',
        'answer_value',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(AgendaFeedback::class, 'agenda_feedback_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AgendaItemQuestion::class, 'agenda_item_question_id');
    }
}
