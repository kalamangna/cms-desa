<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetRealization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'budget_category_id',
        'title',
        'year',
        'budget_amount',
        'realization_amount',
    ];

    protected $casts = [
        'budget_amount'      => 'float',
        'realization_amount' => 'float',
        'year'               => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }

    public function getPercentageAttribute()
    {
        if ($this->budget_amount <= 0) {
            return 0;
        }

        return min(($this->realization_amount / $this->budget_amount) * 100, 100);
    }

    protected static function booted()
    {
        $clearCache = function ($realization) {
            $year = $realization->year ?? date('Y');
            \Illuminate\Support\Facades\Cache::forget("home_budget_summary_{$year}");
            \Illuminate\Support\Facades\Cache::forget("home_belanja_details_{$year}");
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
