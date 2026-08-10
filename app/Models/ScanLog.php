<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $fillable = [
        'action',
        'registrant_id',
        'registrant_name',
        'qr_token',
        'item_id',
        'item_type',
        'item_label',
        'source',
        'client_id',
        'admin_id',
        'success',
        'printed',
        'message',
        'ip_address',
    ];

    protected $casts = [
        'registrant_id' => 'integer',
        'item_id'       => 'integer',
        'admin_id'      => 'integer',
        'success'       => 'boolean',
        'printed'       => 'boolean',
    ];
}
