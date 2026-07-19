<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('home_village_head'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('home_village_head'));
    }
}
