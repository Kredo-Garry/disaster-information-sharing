<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // テーブル名（省略可だが明示するなら）
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'body',
        'user_id',
        'category_id',
        'lat',
        'lng',
    ];
}
