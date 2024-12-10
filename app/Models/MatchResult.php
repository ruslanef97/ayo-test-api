<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MatchResult extends Model
{
    
    protected $fillable = [
        'match_id',
        'home_score',
        'away_score',
        'result',
        'created_by',
        'updated_by'
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(MatchResultHistory::class, 'match_result_id', 'id');
    }

    protected function result(): Attribute
    {
        return Attribute::make(
            get: fn ($result) => matchResult($result),
        );
    }
}
