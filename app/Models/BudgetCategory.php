<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug'];

    public function realizations()
    {
        return $this->hasMany(BudgetRealization::class);
    }
}
