<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait Auditable
{
    /**
     * Atribut internal/sensitif yang diabaikan dari pencatatan Audit Log.
     */
    protected static array $auditIgnoredAttributes = [
        'remember_token',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $attrs = static::filterAuditAttributes($model->getAttributes());
            if (! empty($attrs)) {
                static::logAudit('created', $model, null, $attrs);
            }
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            $old = array_intersect_key($original, $changes);
            $new = $changes;

            $old = static::filterAuditAttributes($old);
            $new = static::filterAuditAttributes($new);

            // Jangan catat jika hanya atribut internal (misal remember_token) yang berubah
            if (! empty($new)) {
                static::logAudit('updated', $model, $old, $new);
            }
        });

        static::deleted(function ($model) {
            $attrs = static::filterAuditAttributes($model->getAttributes());
            static::logAudit('deleted', $model, $attrs, null);
        });
    }

    /**
     * Filter atribut sensitif/internal dan ringkas konten teks panjang.
     */
    protected static function filterAuditAttributes(array $attributes): array
    {
        $filtered = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, static::$auditIgnoredAttributes, true)) {
                continue;
            }

            // Jika nilai berupa teks panjang atau HTML, bersihkan dan ringkas
            if (is_string($value) && strlen($value) > 120) {
                $clean = trim(preg_replace('/\s+/', ' ', strip_tags($value)));
                $value = Str::limit($clean, 120);
            }

            $filtered[$key] = $value;
        }

        return $filtered;
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

            if (isset($model->name) && $model->name) {
                $desc .= ": {$model->name}";
            } elseif (isset($model->title) && $model->title) {
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
