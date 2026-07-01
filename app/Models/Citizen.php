<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Citizen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nik',
        'kk_number',
        'dusun_id',
        'name',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'religion',
        'education',
        'job',
        'blood_type',
        'marital_status',
        'address',
        'rt',
        'rw',
        'status',
    ];

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }
}
