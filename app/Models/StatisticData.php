<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticData extends Model
{
    protected $table = 'statistic_data';

    protected $fillable = ['statistic_indicator_id', 'year', 'value'];

    public function indicator()
    {
        return $this->belongsTo(StatisticIndicator::class, 'statistic_indicator_id');
    }
}
