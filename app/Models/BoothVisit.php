<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoothVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'booth_id',
        'registrant_id',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * The booth that was visited.
     */
    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    /**
     * The registrant who visited.
     */
    public function registrant()
    {
        return $this->belongsTo(Registrant::class);
    }
}
