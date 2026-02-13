<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_platform',
        'external_author',
        'content',
        'original_url',
        'tags',
        'published_at',
        'is_visible',
        'sort_weight',
        'embed_html',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_visible' => 'boolean',
    ];

    /**
     * ユーザー投稿用リレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
