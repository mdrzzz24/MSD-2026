<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtmLink extends Model
{
    protected $fillable = [
        'name', 'base_url', 'target_type', 'workshop_id', 'workshop_invitation_id', 'track_id', 'utm_source', 'utm_medium',
        'utm_campaign', 'utm_content', 'full_url', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'utm_link_user');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function workshopInvitation()
    {
        return $this->belongsTo(WorkshopInvitation::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Fixed base URL for all UTM links. Cannot be changed.
     */
    public const BASE_URL = 'https://metrodatasolutionday.com/2026/';

    /**
     * Scope: event-registration UTM links.
     */
    public function scopeForEvent($query)
    {
        return $query->where('target_type', 'event');
    }

    /**
     * Scope: workshop-invitation UTM links.
     */
    public function scopeForWorkshop($query)
    {
        return $query->where('target_type', 'workshop');
    }

    /**
     * Base URL of the target page — workshop invitation when targeted, otherwise home page.
     */
    public function targetBaseUrl(): string
    {
        if ($this->target_type === 'workshop' && $this->workshop_id) {
            // Prefer the explicitly chosen invitation (custom slug / track-specific), if any
            if ($this->workshop_invitation_id) {
                $chosen = WorkshopInvitation::find($this->workshop_invitation_id);
                if ($chosen) {
                    return rtrim(self::BASE_URL, '/') . '/invitation/workshop/' . ($chosen->slug ?: $chosen->token);
                }
            }
            // Fallback: prefer a workshop-level (no track) custom-slug invitation, so a single
            // custom slug can serve multiple tracks (the track is resolved from the UTM link later)
            $invitation = WorkshopInvitation::where('workshop_id', $this->workshop_id)
                ->whereNotNull('slug')
                ->whereNull('track_id')
                ->first();
            if (!$invitation) {
                $invitation = WorkshopInvitation::where('workshop_id', $this->workshop_id)
                    ->whereNotNull('slug')
                    ->first();
            }
            if (!$invitation) {
                $invitation = WorkshopInvitation::where('workshop_id', $this->workshop_id)
                    ->where('is_active', true)
                    ->first();
            }
            if ($invitation) {
                return rtrim(self::BASE_URL, '/') . '/invitation/workshop/' . ($invitation->slug ?: $invitation->token);
            }
        }
        return self::BASE_URL;
    }

    public function buildUrl(): string
    {
        $params = [
            'utm_source'   => $this->utm_source,
            'utm_medium'   => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
        ];
        if ($this->utm_content) {
            $params['utm_content'] = $this->utm_content;
        }

        $base = rtrim($this->targetBaseUrl(), '/');
        // Event keeps the trailing slash before '?'; workshop invitation does not
        $separator = $this->target_type === 'workshop' ? '?' : '/?';

        return $base . $separator . http_build_query($params);
    }

    /**
     * Resolve a track for a workshop-level (master custom-slug) invitation from a matching
     * workshop UTM link that carries a track_id. Returns null when no link/track matches.
     */
    public static function resolveTrackForWorkshop(?int $workshopId, array $utm): ?Track
    {
        if (!$workshopId || empty($utm['utm_source']) || empty($utm['utm_campaign'])) {
            return null;
        }

        $q = static::forWorkshop()
            ->where('workshop_id', $workshopId)
            ->where('utm_source', $utm['utm_source'] ?? '')
            ->where('utm_medium', $utm['utm_medium'] ?? '')
            ->where('utm_campaign', $utm['utm_campaign'] ?? '')
            ->whereNotNull('track_id');
        if (!empty($utm['utm_content'])) {
            $q->where('utm_content', $utm['utm_content']);
        }

        $link = $q->latest()->first();

        return $link?->track;
    }

    public function registrationsCount(): int
    {
        return \App\Models\Registrant::where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign)
            ->count();
    }

    /**
     * Count workshop registrations (registrant_workshop pivot) attributed to this link.
     */
    public function workshopRegistrationsCount(): int
    {
        $q = \Illuminate\Support\Facades\DB::table('registrant_workshop')
            ->where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign);
        if ($this->utm_content) {
            $q->where('utm_content', $this->utm_content);
        }
        return $q->count();
    }

    public function checkedInCount(): int
    {
        return \App\Models\Registrant::where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign)
            ->whereNotNull('checked_in_at')
            ->count();
    }

    public function approvedCount(): int
    {
        return \App\Models\Registrant::where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign)
            ->where('status', 'approved')
            ->count();
    }

    public function pendingCount(): int
    {
        return \App\Models\Registrant::where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign)
            ->where('status', 'pending')
            ->count();
    }

    public function rejectedCount(): int
    {
        return \App\Models\Registrant::where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign)
            ->where('status', 'rejected')
            ->count();
    }
}
