<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kk_number',
        'head_name',
        'head_nik',
        'address',
        'dusun_id',
        'rt',
        'rw',
        'address_matches_kk',
        'assistance_type',
        'family_member_count',
        'building_type',
        'ownership_status',
        'ownership_proof',
        'floor_area',
        'floor_material',
        'wall_material',
        'roof_material',
        'floor_condition',
        'wall_condition',
        'roof_condition',
        'toilet_facility',
        'closet_type',
        'feces_disposal',
        'water_source',
        'lighting_source',
        'electricity_power',
        'electricity_id',
        'electricity_cost',
        'internet_cost',
        'photo_front',
        'photo_living_room',
        'photo_bathroom',
        'photo_kk',
        'gas_3kg_count',
        'gas_5kg_count',
        'refrigerator_count',
        'ac_count',
        'jewelry_count',
        'computer_count',
        'motorcycle_count',
        'motorcycle_value',
        'car_count',
        'car_value',
        'other_land_count',
        'other_land_value',
        'other_building_count',
        'other_building_value',
        'cow_count',
        'goat_count',
        'buffalo_count',
        'rental_estimate',
        'rental_free_estimate',
        'rental_contract_value',
        'notes',
    ];

    protected $casts = [
        'address_matches_kk' => 'integer',
        'family_member_count' => 'integer',
        'floor_area' => 'float',
        'electricity_cost' => 'integer',
        'internet_cost' => 'integer',
        'gas_3kg_count' => 'integer',
        'gas_5kg_count' => 'integer',
        'refrigerator_count' => 'integer',
        'ac_count' => 'integer',
        'jewelry_count' => 'integer',
        'computer_count' => 'integer',
        'motorcycle_count' => 'integer',
        'motorcycle_value' => 'integer',
        'car_count' => 'integer',
        'car_value' => 'integer',
        'other_land_count' => 'integer',
        'other_land_value' => 'integer',
        'other_building_count' => 'integer',
        'other_building_value' => 'integer',
        'cow_count' => 'integer',
        'goat_count' => 'integer',
        'buffalo_count' => 'integer',
        'rental_estimate' => 'integer',
        'rental_free_estimate' => 'integer',
        'rental_contract_value' => 'integer',
    ];

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }

    public function citizens()
    {
        return $this->hasMany(Citizen::class);
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('home_total_penduduk_real');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home_total_penduduk_real');
        });
    }
}
