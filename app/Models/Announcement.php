<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Announcement extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['title', 'slug', 'content', 'published_at'];

    protected static function booted()
    {
        static::saving(function ($announcement) {
            if (empty($announcement->published_at)) {
                $announcement->published_at = now();
            }
        });

        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_announcements'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_announcements'));
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
