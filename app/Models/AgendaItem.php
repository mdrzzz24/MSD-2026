<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaItem extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (AgendaItem $item) {
            $item->slug = static::uniqueSlug($item->title, $item->id);
            if (empty($item->short_code)) {
                $item->short_code = static::uniqueShortCode();
            }
        });
    }

    protected static function uniqueSlug(?string $title, $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug((string) $title) ?: 'session';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    protected static function uniqueShortCode(int $length = 6): string
    {
        do {
            $code = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random($length));
        } while (static::where('short_code', $code)->exists());
        return $code;
    }

    protected $fillable = [
        'title',
        'slug',
        'short_code',
        'topic_headline',
        'description',
        'speaker_name',
        'speaker_title',
        'speaker_photo',
        'speaker2_name',
        'speaker2_title',
        'speaker2_photo',
        'key_highlights',
        'category',
        'agenda_type',
        'workshop_id',
        'track_id',
        'room',
        'start_time',
        'end_time',
        'date',
        'order',
        'rowspan',
        'colspan',
        'is_registrable',
        'registration_closed',
        'capacity',
        'feedback_enabled',
    ];

    protected $casts = [
        'date'              => 'date',
        'start_time'        => 'string',
        'end_time'          => 'string',
        'is_registrable'     => 'boolean',
        'registration_closed' => 'boolean',
        'capacity'          => 'integer',
        'feedback_enabled'   => 'boolean',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('start_time')->orderBy('order');
    }

    // ── Relationships ──

    public function registrants()
    {
        return $this->belongsToMany(Registrant::class, 'agenda_item_registrant')
                    ->withTimestamps()
                    ->withPivot(['status', 'admin_notes', 'processed_by', 'processed_at', 'id']);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'agenda_item_speaker')
                    ->withPivot(['order', 'key_highlights', 'presentation_title', 'presentation_description'])
                    ->withTimestamps()
                    ->orderByPivot('order');
    }

    /**
     * Speakers in display order. If no explicit order was ever stored for the
     * pivot (all orders are 0), fall back to alphabetical by name so the agenda
     * shows them alphabetically by default.
     */
    public function orderedSpeakers()
    {
        $spk = $this->speakers; // already ordered by pivot 'order'

        if ($spk->isEmpty()) {
            return $spk;
        }

        $allZero = $spk->every(fn ($s) => (int) $s->pivot->order === 0);

        if ($allZero) {
            return $spk->sortBy(fn ($s) => mb_strtolower($s->name))->values();
        }

        return $spk->values();
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function feedback()
    {
        return $this->hasMany(AgendaFeedback::class, 'agenda_item_id');
    }

    public function feedbackQuestions()
    {
        return $this->hasMany(AgendaItemQuestion::class, 'agenda_item_id')->orderBy('order');
    }

    /**
     * Compact URL that redirects to this session's feedback page.
     */
    public function shortUrl(): string
    {
        return route('feedback.short', $this->short_code);
    }

    public function visits()
    {
        return $this->hasMany(AgendaVisit::class);
    }

    public function visitorRegistrants()
    {
        return $this->belongsToMany(Registrant::class, 'agenda_visits')
            ->withTimestamps()
            ->withPivot(['visited_at', 'id']);
    }

    // ── Helpers ──

    public function isFullRow(): bool
    {
        return is_null($this->room) || $this->room === '';
    }

    public function isFull(): bool
    {
        if ($this->capacity <= 0) {
            return false;
        }
        // Use the eager-loaded count when available (home page), otherwise query.
        $approved = isset($this->approved_count)
            ? (int) $this->approved_count
            : $this->registrants()->wherePivot('status', 'approved')->count();
        return $approved >= $this->capacity;
    }

    public function canRegister(): bool
    {
        return $this->is_registrable && !$this->registration_closed && !$this->isFull();
    }

    public function approvedCount(): int
    {
        return $this->registrants()->wherePivot('status', 'approved')->count();
    }

    public function pendingCount(): int
    {
        return $this->registrants()->wherePivot('status', 'pending')->count();
    }

    public function timeLabel(): string
    {
        return date('H.i', strtotime($this->start_time)) . ' – ' . date('H.i', strtotime($this->end_time));
    }

    /**
     * Effective end time for sessions rendered as multi-slot blocks (rowspan > 1).
     * When a session's cell spans several time-slot rows, the visible end time is
     * the last spanned slot's end (e.g. rowspan=3 from 13:00-13:30 ends at 14:30),
     * not the raw end_time column. Mirrors the home page modal logic.
     */
    public function displayEndTime(?\Illuminate\Support\Collection $timeSlots = null): ?string
    {
        if ($this->rowspan > 1) {
            $timeSlots = $timeSlots ?? \App\Models\TimeSlot::ordered()->get();
            if ($timeSlots->isNotEmpty()) {
                $slotIndex = $timeSlots->search(fn ($ts) => $ts->start_time === $this->start_time && $ts->end_time === $this->end_time);
                if ($slotIndex !== false) {
                    $lastSlotIndex = min($slotIndex + $this->rowspan - 1, $timeSlots->count() - 1);
                    $lastSlot = $timeSlots->get($lastSlotIndex);
                    if ($lastSlot) {
                        return $lastSlot->end_time;
                    }
                }
            }
        }

        return $this->end_time;
    }

    /**
     * Time label using the rowspan-aware display end time.
     */
    public function timeLabelWith(?\Illuminate\Support\Collection $timeSlots = null): string
    {
        return date('H.i', strtotime($this->start_time)) . ' – ' . date('H.i', strtotime($this->displayEndTime($timeSlots)));
    }

    /**
     * All available rooms in display order.
     */
    public static function rooms(): array
    {
        return [
            'Ballroom A',
            'Ballroom B',
            'Ballroom C',
            'Sumatra',
            'Java',
            'Sulawesi',
            'Kalimantan',
            'Maluku',
        ];
    }

    /**
     * Category CSS class mapping — auto-detects from category or agenda_type.
     */
    public static function categoryClass(?string $cat, ?string $agendaType = null): string
    {
        // Explicit category takes priority
        if ($cat) {
            return match ($cat) {
                'general'  => 'tag-general',
                'workshop' => 'ws',
                'platinum' => 'plat',
                'gold'     => 'gold',
                'break'    => 'tag-break',
                default    => '',
            };
        }
        // Fallback to agenda_type auto-color
        return match ($agendaType) {
            'workshop' => 'ws',
            'track'    => 'tag-track',
            'keynote'  => 'tag-keynote',
            default    => 'tag-general',
        };
    }
}
