<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory, HasRelationships;

    protected $table = 'stations';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'token',
        'name',
        'ip_address',
        'mac_address',
        'added_at',
        'active',
        'running',
        'rotation',
        'mass',
        'duration',
        'energy'
    ];

    protected $attributes = [
        'active' => true,
        'running' => false,
        'rotation' => 0,
        'mass' => NULL,
        'duration' => NULL,
        'energy' => NULL
    ];

    protected $hidden = [
        'token'
    ];

    public function session(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function log(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
