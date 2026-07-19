<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticData extends Model
{
    protected $table = 'statistic_data';

    protected $fillable = ['statistic_indicator_id', 'year', 'value', 'value_male', 'value_female'];

    public function getValMaleAttribute()
    {
        return $this->attributes['value_male'] ?? 0;
    }

    public function getValFemaleAttribute()
    {
        return $this->attributes['value_female'] ?? 0;
    }

    public function indicator()
    {
        return $this->belongsTo(StatisticIndicator::class, 'statistic_indicator_id');
    }
}
