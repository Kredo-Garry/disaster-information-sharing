<?php

namespace App\Models;

// ログイン機能を使うためにこれが必要
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * シリアル化から隠す属性
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}