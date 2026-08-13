<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use App\Traits\Auditable;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    use Auditable, HasSlug, SoftDeletes;

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

            if ($post->isDirty('featured_image') && $post->featured_image) {
                $post->featured_image = ImageHelper::convertToWebp($post->featured_image, 'posts');
            }
        });

        static::saved(fn () => Cache::forget('home_posts'));
        static::deleted(fn () => Cache::forget('home_posts'));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
