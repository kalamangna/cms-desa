<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::saving(function ($model) {
            $sourceField = isset($model->title) ? 'title' : (isset($model->name) ? 'name' : null);
            if ($sourceField && empty($model->slug) && ! empty($model->$sourceField)) {
                $baseSlug = Str::slug($model->$sourceField);
                $slug = $baseSlug;
                $count = 1;

                $hasSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(static::class));

                while ((
                    $hasSoftDeletes ? static::withTrashed() : static::query()
                )
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
