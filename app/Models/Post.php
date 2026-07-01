<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Post extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'featured_image',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($post) {
            if (empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_posts'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_posts'));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
