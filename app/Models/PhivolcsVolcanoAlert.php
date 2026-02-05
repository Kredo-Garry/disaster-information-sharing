<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhivolcsVolcanoAlert extends Model
{
    protected $table = 'phivolcs_volcano_alerts';

    protected $fillable = [
        'hash',
        'volcano_name',   // ✅ これが必須
        'volcano',        // 既にあるなら残してOK（任意）
        'alert_level',
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
