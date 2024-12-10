<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Player extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'team_id',
        'name',
        'height',
        'weight',
        'position',
        'number',
        'status',
        'created_by',
        'updated_by'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
