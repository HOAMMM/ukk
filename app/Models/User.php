<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tb_users';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'username',
        'namleng',
        'email',
        'password',
        'id_level',
        'user_code',
        'user_phone',
        'user_passtext',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // User
    public function level()
    {
        return $this->belongsTo(Level::class, 'id_level', 'level_id');
    }

    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    //     'password' => 'hashed',
    // ];
}
