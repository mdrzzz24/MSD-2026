<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtmLink extends Model
{
    protected $fillable = [
        'name', 'base_url', 'utm_source', 'utm_medium',
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
        return $this->base_url . '?' . http_build_query($params);
    }

    public function registrationsCount(): int
    {
        return \App\Models\Registrant::where('utm_source', $this->utm_source)
            ->where('utm_medium', $this->utm_medium)
            ->where('utm_campaign', $this->utm_campaign)
            ->count();
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
