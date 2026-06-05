<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dataset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'year',
        'source',
        'file_csv',
        'file_xlsx',
        'file_pdf',
    ];

    public function metadata()
    {
        return $this->hasOne(Metadata::class);
    }
}
