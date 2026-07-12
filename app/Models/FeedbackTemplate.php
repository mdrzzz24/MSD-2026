<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(FeedbackTemplateQuestion::class, 'template_id')->orderBy('order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Apply this template to an agenda item (copy all questions).
     */
    public function applyToAgendaItem(AgendaItem $agendaItem): void
    {
        $idMap = []; // maps old template question ID → new agenda item question ID

        // First pass: create all questions without parent
        $questions = $this->questions()->orderBy('order')->get();
        foreach ($questions as $q) {
            $newQ = AgendaItemQuestion::create([
                'agenda_item_id'              => $agendaItem->id,
                'source_template_id'          => $this->id,
                'source_template_question_id' => $q->id,
                'question_text'               => $q->question_text,
                'question_type'               => $q->question_type,
                'options'                     => $q->options,
                'order'                       => $q->order,
                'required'                    => $q->required,
                'parent_question_id'          => null,
                'trigger_value'               => $q->trigger_value,
            ]);
            $idMap[$q->id] = $newQ->id;
        }

        // Second pass: update parent references using the map
        foreach ($questions as $q) {
            if ($q->parent_question_id !== null && isset($idMap[$q->parent_question_id])) {
                $newId = $idMap[$q->id];
                AgendaItemQuestion::where('id', $newId)
                    ->update(['parent_question_id' => $idMap[$q->parent_question_id]]);
            }
        }
    }
}
