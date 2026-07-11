<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    // ── Template type constants ──
    public const TYPE_REGISTRATION        = 'registration';
    public const TYPE_APPROVAL            = 'approval';
    public const TYPE_REJECTION           = 'rejection';
    public const TYPE_WORKSHOP_APPROVAL   = 'workshop_approval';
    public const TYPE_WORKSHOP_REJECTION  = 'workshop_rejection';
    public const TYPE_TRACK_APPROVAL      = 'track_approval';
    public const TYPE_TRACK_REJECTION     = 'track_rejection';
    public const TYPE_REMINDER            = 'reminder';

    /**
     * All available template types with labels and descriptions.
     */
    public static function types(): array
    {
        return [
            self::TYPE_REGISTRATION => [
                'label'       => 'Registration Auto-Reply',
                'description' => 'Dikirim otomatis setelah registrant mendaftar.',
                'color'       => 'blue',
            ],
            self::TYPE_APPROVAL => [
                'label'       => 'Registrant Approval',
                'description' => 'Dikirim saat admin menyetujui pendaftaran (termasuk kredensial login).',
                'color'       => 'emerald',
            ],
            self::TYPE_REJECTION => [
                'label'       => 'Registrant Rejection',
                'description' => 'Dikirim saat admin menolak pendaftaran.',
                'color'       => 'red',
            ],
            self::TYPE_WORKSHOP_APPROVAL => [
                'label'       => 'Workshop Approval',
                'description' => 'Dikirim saat admin menyetujui pendaftaran workshop.',
                'color'       => 'sky',
            ],
            self::TYPE_WORKSHOP_REJECTION => [
                'label'       => 'Workshop Rejection',
                'description' => 'Dikirim saat admin menolak pendaftaran workshop.',
                'color'       => 'rose',
            ],
            self::TYPE_TRACK_APPROVAL => [
                'label'       => 'Track / Session Approval',
                'description' => 'Dikirim saat admin menyetujui pendaftaran track/sesi.',
                'color'       => 'teal',
            ],
            self::TYPE_TRACK_REJECTION => [
                'label'       => 'Track / Session Rejection',
                'description' => 'Dikirim saat admin menolak pendaftaran track/sesi.',
                'color'       => 'orange',
            ],
            self::TYPE_REMINDER => [
                'label'       => 'Gentle Reminder',
                'description' => 'Pengingat untuk menghadiri acara (dikirim manual oleh admin).',
                'color'       => 'violet',
            ],
        ];
    }

    /**
     * Get label for a given type.
     */
    public static function typeLabel(string $type): string
    {
        return self::types()[$type]['label'] ?? ucfirst($type);
    }

    /**
     * Get color for a given type.
     */
    public static function typeColor(string $type): string
    {
        return self::types()[$type]['color'] ?? 'gray';
    }

    protected $fillable = [
        'name',
        'type',
        'description',
        'subject',
        'html_content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Scopes ──

    public function scopeApproval($query)
    {
        return $query->where('type', self::TYPE_APPROVAL);
    }

    public function scopeRejection($query)
    {
        return $query->where('type', self::TYPE_REJECTION);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Helpers ──

    /**
     * Get the active template for a given type.
     */
    public static function activeOfType(string $type): ?self
    {
        return static::ofType($type)->active()->first();
    }

    /**
     * Replace placeholders with actual data.
     *
     * Supported placeholders:
     *   {{ name }}, {{ email }}, {{ status }}, {{ password }},
     *   {{ unique_code }}, {{ admin_notes }}, {{ workshop_name }},
     *   {{ track_name }}, {{ event_date }}, {{ login_url }}
     */
    public function render(array $data = []): string
    {
        $content = $this->html_content;

        // Merge defaults
        $defaults = [
            'event_date' => '12 Agustus 2026',
            'login_url'  => route('registrant.login'),
        ];
        $data = array_merge($defaults, $data);

        foreach ($data as $key => $value) {
            $content = str_replace('{{ ' . $key . ' }}', (string) $value, $content);
            $content = str_replace('{{' . $key . '}}', (string) $value, $content);
        }

        // Convert relative storage paths (e.g. /storage/email-templates/xxx/img.jpg)
        // to absolute URLs so images render in email clients
        $baseUrl = rtrim(config('app.url'), '/');
        $content = preg_replace(
            '/src="\/storage\//i',
            'src="' . $baseUrl . '/storage/',
            $content
        );

        return $content;
    }
}
