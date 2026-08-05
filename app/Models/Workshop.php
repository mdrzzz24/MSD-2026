<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
        'room',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'registration_open',
        'invitation_bypass_approval',
    ];

    protected $casts = [
        'date'                       => 'date',
        'start_time'                 => 'string',
        'end_time'                   => 'string',
        'registration_open'          => 'boolean',
        'invitation_bypass_approval' => 'boolean',
    ];

    public function registrants()
    {
        return $this->belongsToMany(Registrant::class, 'registrant_workshop')
                    ->withTimestamps()
                    ->withPivot(['status', 'admin_notes', 'processed_by', 'processed_at', 'id', 'track_id',
                                 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content']);
    }

    public function waitlist()
    {
        return $this->belongsToMany(Registrant::class, 'workshop_waitlist')
                    ->withTimestamps();
    }

    public function agendaItems()
    {
        return $this->hasMany(AgendaItem::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function invitations()
    {
        return $this->hasMany(WorkshopInvitation::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('registration_open', true);
    }

    public function registrationsCount(): int
    {
        return $this->registrants()->wherePivot('status', 'approved')->count();
    }

    public function pendingCount(): int
    {
        return $this->registrants()->wherePivot('status', 'pending')->count();
    }

    public function rejectedCount(): int
    {
        return $this->registrants()->wherePivot('status', 'rejected')->count();
    }

    public function waitlistCount(): int
    {
        return $this->waitlist()->count();
    }

    public function isFull(): bool
    {
        return $this->capacity > 0 && $this->registrationsCount() >= $this->capacity;
    }

    public function canRegister(): bool
    {
        return $this->registration_open && !$this->isFull();
    }

    public function hasAvailability(): bool
    {
        return $this->registration_open && ($this->capacity === 0 || $this->registrationsCount() < $this->capacity);
    }

    public function timeRange(): string
    {
        if (!$this->start_time || !$this->end_time) return '—';
        return date('H:i', strtotime($this->start_time)) . ' – ' . date('H:i', strtotime($this->end_time));
    }

    /**
     * Get email placeholder data for this workshop.
     * Falls back to the first linked agenda item if workshop fields are empty.
     */
    public function emailData(): array
    {
        $agendaItem = $this->agendaItems()->first();

        $room     = $agendaItem?->room ?? $this->room ?? '';
        $date     = $agendaItem?->date ?? $this->date;
        $start    = $agendaItem?->start_time ?? $this->start_time;
        $end      = $agendaItem?->end_time ?? $this->end_time;
        $capacity = $agendaItem?->capacity ?: ($this->capacity ?: 0);

        $timeRange = '—';
        if ($start && $end) {
            $timeRange = date('H:i', strtotime($start)) . ' – ' . date('H:i', strtotime($end));
        }

        return [
            'workshop_name'     => $this->name ?: $this->title,
            'workshop_title'    => $this->title,
            'workshop_room'     => $room,
            'workshop_date'     => $date ? $date->format('l, d F Y') : '',
            'workshop_time'     => $timeRange,
            'workshop_capacity' => (string) $capacity,
            'venue_name'        => 'Shangri-La Hotel Jakarta',
        ];
    }

    /**
     * Ids of the agenda items belonging to this workshop (its own agenda items
     * plus the agenda items of its tracks). Used to detect cancelled registrations.
     */
    private function workshopAgendaItemIds(): \Illuminate\Support\Collection
    {
        return $this->agendaItems()->pluck('agenda_items.id')
            ->merge(\Illuminate\Support\Facades\DB::table('agenda_items')
                ->whereIn('track_id', $this->tracks()->pluck('tracks.id'))
                ->pluck('id'))
            ->unique()
            ->values();
    }

    /**
     * Best-effort detection of registrants who cancelled this workshop registration.
     *
     * Older cancels DELETED the registrant_workshop row entirely, so they can't be
     * read from there. We recover them by comparing agenda_item_registrant (the
     * session-level pivot, which was not always removed) against registrant_workshop:
     * a registrant who still has an agenda_item_registrant row for one of this
     * workshop's sessions but has NO registrant_workshop row for the workshop is
     * treated as cancelled.
     */
    public function cancelledRegistrantIds(): \Illuminate\Support\Collection
    {
        $agendaItemIds = $this->workshopAgendaItemIds();
        if ($agendaItemIds->isEmpty()) {
            return collect();
        }

        $workshopRegistrantIds = $this->registrants()->pluck('registrants.id');

        return \Illuminate\Support\Facades\DB::table('agenda_item_registrant')
            ->whereIn('agenda_item_id', $agendaItemIds)
            ->whereNotIn('status', ['rejected'])
            ->whereNotIn('registrant_id', $workshopRegistrantIds)
            ->pluck('registrant_id')
            ->unique()
            ->values();
    }

    /**
     * Full cancelled registrants (with optional profile/source/date filters).
     *
     * Each returned registrant carries a synthetic $pivot (status='cancelled' and
     * created_at from the agenda registration) so it renders exactly like a normal
     * cancelled row in the admin views.
     */
    public function cancelledRegistrants(array $filters = []): \Illuminate\Support\Collection
    {
        $agendaItemIds = $this->workshopAgendaItemIds();
        if ($agendaItemIds->isEmpty()) {
            return collect();
        }

        $workshopRegistrantIds = $this->registrants()->pluck('registrants.id');

        $airRows = \Illuminate\Support\Facades\DB::table('agenda_item_registrant')
            ->whereIn('agenda_item_id', $agendaItemIds)
            ->whereNotIn('status', ['rejected'])
            ->whereNotIn('registrant_id', $workshopRegistrantIds)
            ->orderBy('created_at')
            ->get()
            ->keyBy('registrant_id');

        if ($airRows->isEmpty()) {
            return collect();
        }

        $query = \App\Models\Registrant::whereIn('id', $airRows->keys());
        if (!empty($filters['profile'])) {
            $query->whereIn('registrants.job_title', $filters['profile']);
        }
        $query->filterBySources($filters['source'] ?? [], 'registrants.utm_source');
        if (!empty($filters['date_from'])) {
            $query->whereDate('registrants.created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('registrants.created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('name')->get()->map(function ($r) use ($airRows) {
            $row = $airRows[$r->id] ?? null;
            $r->cancelled = true;
            $r->pivot = (object) [
                'status'       => 'cancelled',
                'admin_notes'  => null,
                'processed_by' => null,
                'processed_at' => $row->updated_at ?? null,
                'track_id'     => null,
                'utm_source'   => null,
                'utm_medium'   => null,
                'utm_campaign' => null,
                'utm_content'  => null,
                'created_at'   => $row->created_at ?? null,
            ];
            return $r;
        });
    }
}
