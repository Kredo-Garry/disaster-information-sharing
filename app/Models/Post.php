<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    /**
     * テーブル名（明示）
     */
    protected $table = 'posts';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'title',
        'body',
        'user_id',
        'category_id',
        'lat',
        'lng',
    ];

    /**
     * 投稿者
     * users.id ← posts.user_id
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * カテゴリ
     * categories.id ← posts.category_id
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
