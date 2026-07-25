<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasSlug;

class Dataset extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'year',
        'source',
        'source_table',
        'dusun_id',
        'selected_columns',
        'file_csv',
        'file_xlsx',
        'file_pdf',
    ];

    protected function casts(): array
    {
        return [
            'selected_columns' => 'array',
        ];
    }

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }

    protected static function booted()
    {
        static::saving(function ($dataset) {
            if (empty($dataset->file_csv)) {
                $dataset->file_csv = 'dynamic';
            }
            if (empty($dataset->file_xlsx)) {
                $dataset->file_xlsx = 'dynamic';
            }
            if (empty($dataset->file_pdf)) {
                $dataset->file_pdf = 'dynamic';
            }
        });
    }

    public function metadata()
    {
        return $this->hasOne(Metadata::class);
    }
}
