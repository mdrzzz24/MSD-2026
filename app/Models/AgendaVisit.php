<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_item_id',
        'registrant_id',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function agendaItem()
    {
        return $this->belongsTo(AgendaItem::class);
    }

    public function registrant()
    {
        return $this->belongsTo(Registrant::class);
    }
}
