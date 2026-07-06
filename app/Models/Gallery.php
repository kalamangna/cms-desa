<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Gallery extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['title', 'slug', 'type', 'image', 'youtube_url', 'description'];

    public function getImageUrlAttribute()
    {
        if ($this->type === 'video' && $this->youtube_url) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $this->youtube_url, $matches);
            if (isset($matches[1])) {
                return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
            }
        }
        
        if ($this->image && !str_contains($this->image, 'gallery_dummy.jpg')) {
            return asset('storage/' . $this->image);
        }
        
        // Local meta.png image as placeholder fallback
        return asset('img/meta.png');
    }

    protected static function booted()
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_galleries'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_galleries'));
    }
}
