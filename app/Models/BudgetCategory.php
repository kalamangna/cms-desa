<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class BudgetCategory extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['name', 'slug'];

    public function realizations()
    {
        return $this->hasMany(BudgetRealization::class);
    }
}
