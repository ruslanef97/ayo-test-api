<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class City extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by'
    ];
}
