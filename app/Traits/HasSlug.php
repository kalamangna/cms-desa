<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::saving(function ($model) {
            $sourceField = isset($model->title) ? 'title' : (isset($model->name) ? 'name' : null);
            if ($sourceField && empty($model->slug) && !empty($model->$sourceField)) {
                $model->slug = Str::slug($model->$sourceField);
            }
        });
    }
}
