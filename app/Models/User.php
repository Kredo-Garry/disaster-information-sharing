<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account_name',
        'phone',
        'birth_date',
        'is_admin',

        // ✅ MyPage
        'family_id',
        'status',
        'status_message',
        'status_updated_at',
        'status_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'birth_date' => 'date',

        // ✅ MyPage
        'status_updated_at' => 'datetime',
        'status_expires_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }
}
