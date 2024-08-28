<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Data extends Model
{
    use HasFactory, HasRelationships;

    protected $table = 'data';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_session',
        'timestamp',
        'voltage',
        'current',
        'power',
        'frequency',
        'power_factor',
        'temp',
        'humidity'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function getcurrentAttribute($value)
    {
        return round($value, 2);
    }

    public function getpowerAttribute($value)
    {
        return round($value, 1);
    }

    public function getpowerfactorAttribute($value)
    {
        return round($value, 2);
    }

    public function gettempAttribute($value)
    {
        return round($value, 1);
    }

    public function gethumidityAttribute($value)
    {
        return round($value, 1);
    }
}
