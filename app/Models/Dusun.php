<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dusun extends Model
{
    protected $fillable = [
        'name',
        'head_name',
        'geojson',
    ];

    public function citizens()
    {
        return $this->hasMany(Citizen::class);
    }

    public function families()
    {
        return $this->hasMany(Family::class);
    }
}
