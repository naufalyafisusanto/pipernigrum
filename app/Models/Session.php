<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory, HasRelationships;

    protected $table = 'sessions';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_station',
        'token',
        'start_at',
        'initial_mass',
        'eta',
        'end_at',
        'final_mass',
        'duration',
        'energy'
    ];

    protected $attributes = [
        'eta' => NULL,
        'end_at' => NULL,
        'final_mass' => NULL,
        'duration' => NULL,
        'energy' => NULL
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function data(): HasMany
    {
        return $this->hasMany(Data::class);
    }
}
