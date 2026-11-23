<?php

namespace App\Models;

use App\Services\ServiceAttachmentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestAttachment extends Model
{
    protected $fillable = [
        'service_request_id',
        'service_requirement_id',
        'requirement_name',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function serviceRequirement(): BelongsTo
    {
        return $this->belongsTo(ServiceRequirement::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function formattedSize(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', '.').' MB';
        }

        return max(1, (int) round($bytes / 1024)).' KB';
    }

    protected static function booted(): void
    {
        static::deleting(function (ServiceRequestAttachment $attachment): void {
            app(ServiceAttachmentService::class)->deleteAttachmentFile($attachment);
        });
    }
}
