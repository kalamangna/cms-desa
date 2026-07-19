<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupInfographic extends Model
{
    protected $fillable = ['title', 'image', 'sort_order', 'is_active'];
}
