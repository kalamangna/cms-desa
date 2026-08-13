<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Model;

class PopupInfographic extends Model
{
    protected $fillable = ['title', 'image', 'sort_order', 'is_active'];

    protected static function booted()
    {
        static::saving(function ($infographic) {
            if ($infographic->isDirty('image') && $infographic->image) {
                $infographic->image = ImageHelper::convertToWebp($infographic->image, 'popup-infographics', 82, 700);
            }
        });
    }
}
