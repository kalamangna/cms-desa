<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Gallery extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = ['title', 'slug', 'type', 'image', 'youtube_url', 'description'];

    public function getImageUrlAttribute()
    {
        if ($this->type === 'video' && $this->youtube_url) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $this->youtube_url, $matches);
            if (isset($matches[1])) {
                return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
            }
        }

        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        // Local meta.webp image as placeholder fallback
        return asset('img/meta.webp');
    }

    protected static function booted()
    {
        static::saving(function ($gallery) {
            if ($gallery->isDirty('image') && $gallery->image) {
                $gallery->image = ImageHelper::convertToWebp($gallery->image, 'galleries');
            }
        });

        static::saved(fn () => Cache::forget('home_galleries'));
        static::deleted(fn () => Cache::forget('home_galleries'));
    }
}
