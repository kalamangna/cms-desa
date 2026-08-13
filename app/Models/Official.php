<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Official extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'position',
        'photo',
        'level',
        'order',
    ];

    public function parent()
    {
        return $this->belongsTo(Official::class, 'parent_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Official::class, 'parent_id')->orderBy('order', 'asc');
    }

    protected $casts = [];

    protected static function booted()
    {
        static::saving(function ($official) {
            if ($official->isDirty('photo') && $official->photo) {
                $official->photo = ImageHelper::convertToWebp($official->photo, 'officials', 80, 500);
            }
        });

        static::saved(fn () => Cache::forget('home_village_head'));
        static::deleted(fn () => Cache::forget('home_village_head'));
    }
}
