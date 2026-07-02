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
        
        if ($this->image && strpos($this->image, 'gallery_dummy.jpg') === false) {
            return asset('storage/' . $this->image);
        }
        
        // Unsplash beautiful village image as placeholder fallback
        return "https://images.unsplash.com/photo-1500382017468-9049fee74a62?w=800&q=80";
    }

    protected static function booted()
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_galleries'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_galleries'));
    }
}
