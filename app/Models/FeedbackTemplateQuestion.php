<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackTemplateQuestion extends Model
{
    protected $fillable = [
        'template_id',
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

    public function template(): BelongsTo
    {
        return $this->belongsTo(FeedbackTemplate::class, 'template_id');
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
