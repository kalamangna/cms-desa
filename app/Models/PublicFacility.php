<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicFacility extends Model
{
    protected $fillable = ['name', 'type', 'latitude', 'longitude', 'address', 'description'];
}
