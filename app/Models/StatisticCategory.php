<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function indicators()
    {
        return $this->hasMany(StatisticIndicator::class);
    }
}
