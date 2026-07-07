<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'room',
        'start_time',
        'end_time',
        'date',
        'order',
        'rowspan',
        'colspan',
    ];

    protected $casts = [
        'date'       => 'date',
        'start_time' => 'string',
        'end_time'   => 'string',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('start_time')->orderBy('order');
    }

    public function isFullRow(): bool
    {
        return is_null($this->room) || $this->room === '';
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
     * Category CSS class mapping.
     */
    public static function categoryClass(?string $cat): string
    {
        return match ($cat) {
            'general'  => 'tag-general',
            'workshop' => 'ws',
            'platinum' => 'plat',
            'gold'     => 'gold',
            'break'    => 'tag-break',
            default    => '',
        };
    }
}
