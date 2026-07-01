<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Publication extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'year',
        'cover',
        'pdf_file',
    ];

    protected static function booted()
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_publications'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_publications'));
    }
}
