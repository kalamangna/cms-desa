<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Publication extends Model
{
    use HasSlug, SoftDeletes;

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
        static::saving(function ($publication) {
            if ($publication->isDirty('cover') && $publication->cover) {
                $publication->cover = ImageHelper::convertToWebp($publication->cover, 'publications');
            }
        });

        static::saved(fn () => Cache::forget('home_publications'));
        static::deleted(fn () => Cache::forget('home_publications'));
    }
}
