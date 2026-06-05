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

    public function category()
    {
        return $this->belongsTo(BudgetCategory::class, 'budget_category_id');
    }

    public function getPercentageAttribute()
    {
        if ($this->budget_amount <= 0) {
            return 0;
        }

        return ($this->realization_amount / $this->budget_amount) * 100;
    }
}
