<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = [
        'district_id',
        'name',
        'code',
        'slug',
        'website',
        'email',
        'phone',
        'address',
        'logo',
        'latitude',
        'longitude',
        'village_cantik_year',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
