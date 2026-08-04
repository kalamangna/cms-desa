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
                $baseSlug = Str::slug($model->$sourceField);
                $slug = $baseSlug;
                $count = 1;

                while (static::withTrashed()
                    ->where('slug', $slug)
                    ->where('id', '!=', $model->id ?? null)
                    ->exists()) {
                    $slug = "{$baseSlug}-{$count}";
                    $count++;
                }

                $model->slug = $slug;
            }
        });
    }
}
