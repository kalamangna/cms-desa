<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Official extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'position',
        'photo',
    ];

    protected $casts = [];
}
