<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GameMatch extends Model
{

    use SoftDeletes;

    protected $table = 'matches';
    
    protected $fillable = [
        'date',
        'duration',
        'home_team',
        'away_team',
        'created_by',
        'updated_by'
    ];

    public function home(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team');
    }

    public function away(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team');
    }

    public function match_result(): HasOne
    {
        return $this->hasOne(MatchResult::class, 'match_id', 'id');
    }
}
