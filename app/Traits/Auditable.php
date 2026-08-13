<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $old = array_intersect_key($model->getOriginal(), $model->getChanges());
            $new = $model->getChanges();
            unset($old['updated_at'], $new['updated_at']);

            if (! empty($new)) {
                static::logAudit('updated', $model, $old, $new);
            }
        });

        static::deleted(function ($model) {
            static::logAudit('deleted', $model, $model->getAttributes(), null);
        });
    }

    protected static function logAudit(string $event, $model, ?array $old, ?array $new): void
    {
        try {
            $user = Auth::user();
            $modelName = class_basename($model);

            $desc = match ($event) {
                'created' => "Menambahkan {$modelName} baru",
                'updated' => "Mengubah data {$modelName}",
                'deleted' => "Menghapus {$modelName}",
                default => "Melakukan {$event} pada {$modelName}"
            };

            if (isset($model->name)) {
                $desc .= ": {$model->name}";
            } elseif (isset($model->title)) {
                $desc .= ": {$model->title}";
            }

            AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'Sistem',
                'event' => $event,
                'auditable_type' => get_class($model),
                'auditable_id' => (string) $model->getKey(),
                'description' => $desc,
                'old_values' => $old,
                'new_values' => $new,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore audit logging errors to prevent breaking main operations
        }
    }
}
