<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Announcement extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['title', 'slug', 'content', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
