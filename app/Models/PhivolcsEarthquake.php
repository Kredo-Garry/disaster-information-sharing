<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhivolcsEarthquake extends Model
{
    // テーブル名（Laravelの自動推測と違うので明示）
    protected $table = 'phivolcs_earthquakes';

    // 一括代入を許可するカラム
    protected $fillable = [
        'occurred_at',
        'lat',
        'lng',
        'magnitude',
        'depth_km',
        'location_text',
        'source_url',
        'issued_at',
        'fetched_at',
        'hash',
    ];

    // 型変換（Carbonで扱えるようにする）
    protected $casts = [
        'occurred_at' => 'datetime',
        'issued_at'   => 'datetime',
        'fetched_at'  => 'datetime',
    ];
}
