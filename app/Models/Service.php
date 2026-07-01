<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSlug;

class Service extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'icon',
        'description',
        'requirements',
    ];
}
