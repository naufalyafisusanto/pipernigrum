<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory, HasRelationships;

    protected $table = 'logs';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'id_user',
        'id_station',
        'host',
        'entity',
        'activity'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function entity()
    {
        if ($this->entity == true)
            return "Operation";
        else
            return "Maintenance";
    }
}
