<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgendaItemQuestion extends Model
{
    protected $fillable = [
        'agenda_item_id',
        'source_template_id',
        'source_template_question_id',
        'question_text',
        'question_type',
        'options',
        'order',
        'required',
        'parent_question_id',
        'trigger_value',
    ];

    protected $casts = [
        'options'  => 'array',
        'required' => 'boolean',
        'order'    => 'integer',
    ];

    public function agendaItem(): BelongsTo
    {
        return $this->belongsTo(AgendaItem::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AgendaFeedbackAnswer::class, 'agenda_item_question_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_question_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_question_id');
    }
}
