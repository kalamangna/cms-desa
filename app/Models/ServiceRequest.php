<?php

namespace App\Models;

use App\Services\ServiceAttachmentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    protected $fillable = ['ticket_number', 'nik', 'name', 'phone', 'service_id', 'status', 'admin_response'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ServiceRequestAttachment::class)->orderBy('id');
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceRequest $request): void {
            $date = now()->format('Ymd');
            $random = strtoupper(bin2hex(random_bytes(3)));
            $request->ticket_number = 'SRV-'.$date.'-'.$random;
        });

        // Hapus seluruh berkas fisik privat karena ON DELETE CASCADE melewati event model anak
        static::deleting(function (ServiceRequest $request): void {
            app(ServiceAttachmentService::class)->deleteRequestDirectory($request->id);
        });
    }
}
