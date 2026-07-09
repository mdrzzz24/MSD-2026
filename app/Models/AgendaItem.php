<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
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
        'capacity',
        'registration_open',
    ];

    protected $casts = [
        'date'              => 'date',
        'start_time'        => 'string',
        'end_time'          => 'string',
        'is_registrable'     => 'boolean',
        'registration_open' => 'boolean',
        'capacity'          => 'integer',
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

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    // ── Helpers ──

    public function isFullRow(): bool
    {
        return is_null($this->room) || $this->room === '';
    }

    public function isFull(): bool
    {
        return $this->capacity > 0 && $this->registrants()->wherePivot('status', 'approved')->count() >= $this->capacity;
    }

    public function canRegister(): bool
    {
        return $this->is_registrable && $this->registration_open && !$this->isFull();
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
