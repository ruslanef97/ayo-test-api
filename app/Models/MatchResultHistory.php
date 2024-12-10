<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchResultHistory extends Model
{
    
    protected $fillable = [
        'match_result_id',
        'type',
        'half',
        'time',
        'player_id',
        'sec_player_id',
        'is_penalty',
        'information'
    ];

    public function match_result(): BelongsTo
    {
        return $this->belongsTo(MatchResult::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function sec_player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'sec_player_id');
    }
}
