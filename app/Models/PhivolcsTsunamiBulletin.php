<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhivolcsTsunamiBulletin extends Model
{
    protected $table = 'phivolcs_tsunami_bulletins';

    protected $fillable = [
        'hash',
        'bulletin_no',
        'status',
        'issued_at',
        'summary_text',
        'full_text',
        'source_url',
        'fetched_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'fetched_at' => 'datetime',
    ];
}
