<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VillagePotential extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($potential) {
            if (empty($potential->slug)) {
                $potential->slug = Str::slug($potential->title);
            }
        });

        static::updating(function ($potential) {
            if (empty($potential->slug)) {
                $potential->slug = Str::slug($potential->title);
            }
        });
    }
}
