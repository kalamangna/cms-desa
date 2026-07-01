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
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
