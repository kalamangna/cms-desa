<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metadata extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dataset_id',
        'source',
        'definition',
        'update_frequency',
        'responsible_person',
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}
