<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Document extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['title', 'slug', 'file', 'description'];
}
