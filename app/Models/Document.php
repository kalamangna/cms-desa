<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = ['title', 'slug', 'file', 'description'];
}
