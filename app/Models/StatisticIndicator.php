<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticIndicator extends Model
{
    protected $fillable = ['statistic_category_id', 'name', 'unit', 'mapping_column', 'mapping_operator', 'mapping_value'];

    public function category()
    {
        return $this->belongsTo(StatisticCategory::class, 'statistic_category_id');
    }

    public function data()
    {
        return $this->hasMany(StatisticData::class);
    }
}
