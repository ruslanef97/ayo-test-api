<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Team extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'logo',
        'year_of_est',
        'address',
        'city_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    protected function logo(): Attribute
    {
        return Attribute::make(
            get: fn ($logo) => url('/storage/teams/' . $logo),
        );
    }
}
