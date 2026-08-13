<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Announcement extends Model
{
    use Auditable, HasSlug, SoftDeletes;

    protected $fillable = ['title', 'slug', 'photo', 'content', 'published_at'];

    protected static function booted()
    {
        static::saving(function ($announcement) {
            if (empty($announcement->published_at)) {
                $announcement->published_at = now();
            }
        });

        static::saved(fn () => Cache::forget('home_announcements'));
        static::deleted(fn () => Cache::forget('home_announcements'));
    }

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
