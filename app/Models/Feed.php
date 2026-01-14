<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // これを忘れずに

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // ユーザーIDも保存可能にする
        'source_platform',
        'external_author',
        'content',
        'original_url',
    ];

    /**
     * リレーションを再定義
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}