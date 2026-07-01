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

    protected static function booted()
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_village_head'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_village_head'));
    }
}
