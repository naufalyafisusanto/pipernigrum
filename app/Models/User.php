<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'username',
        'password',
        'admin',
        'date_created',
        'last_login'
    ];

    protected $attributes = [
        'admin' => 0
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    public function role()
    {
        if ($this->admin == true)
            return "Administrator";
        else
            return "Operator";
    }

    public function log(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
