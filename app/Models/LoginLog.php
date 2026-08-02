<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'name',
        'email',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'session_id',
        'impersonated_by',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the user this log belongs to (polymorphic-like).
     */
    public function user()
    {
        return $this->morphTo();
    }

    /**
     * Scope: only admin/user (User model) logs.
     */
    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }

    /**
     * Scope: only registrant logs.
     */
    public function scopeRegistrants($query)
    {
        return $query->where('user_type', 'registrant');
    }

    /**
     * Session lifetime (minutes) used to decide whether a session is still live.
     */
    public static function sessionLifetimeMinutes(): int
    {
        return (int) config('session.lifetime', 120);
    }

    /**
     * Whether the current session driver lets us inspect live sessions
     * (database or file). Other drivers fall back to logout_at only.
     */
    public static function supportsLiveDetection(): bool
    {
        return in_array(config('session.driver'), ['database', 'file']);
    }

    /**
     * IDs of sessions that are currently live (still present and recently active).
     * Works with both the database and file session drivers.
     */
    public static function liveSessionIds(): \Illuminate\Support\Collection
    {
        $cutoff = now()->subMinutes(static::sessionLifetimeMinutes())->getTimestamp();

        if (config('session.driver') === 'database') {
            return \Illuminate\Support\Facades\DB::table('sessions')
                ->where('last_activity', '>=', $cutoff)
                ->pluck('id');
        }

        // File driver: session files live in storage/framework/sessions
        $ids = collect();
        $path = storage_path('framework/sessions');
        if (is_dir($path)) {
            foreach (glob($path . '/*') as $file) {
                if (is_file($file) && basename($file) !== '.gitignore' && filemtime($file) >= $cutoff) {
                    $ids->push(basename($file));
                }
            }
        }

        return $ids;
    }

    /**
     * Scope: logged in AND the underlying session is still live (realtime).
     */
    public function scopeActive($query)
    {
        if (!static::supportsLiveDetection()) {
            return $query->whereNull('logout_at');
        }

        $live = static::liveSessionIds();

        return $query->whereNull('logout_at')
            ->when($live->isNotEmpty(), fn ($q) => $q->whereIn('session_id', $live))
            ->when($live->isEmpty(), fn ($q) => $q->whereRaw('1 = 0'));
    }

    /**
     * Scope: logs from today.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('login_at', today());
    }

    /**
     * Realtime login status: 'active' | 'expired' | 'logged_out'.
     */
    public function status($liveSessionIds = null): string
    {
        if ($this->logout_at) {
            return 'logged_out';
        }

        if (!static::supportsLiveDetection()) {
            return 'active';
        }

        $live = $liveSessionIds ?? static::liveSessionIds();

        return $live->contains($this->session_id) ? 'active' : 'expired';
    }

    /**
     * Check if this session is still active in realtime.
     */
    public function isActive(): bool
    {
        return $this->status() === 'active';
    }

    /**
     * Mark this session as logged out.
     */
    public function markLoggedOut(): void
    {
        $this->update(['logout_at' => now()]);
    }
}
