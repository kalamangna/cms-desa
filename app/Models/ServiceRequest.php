<?php

namespace App\Models;

use App\Services\ServiceAttachmentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    public const TTE_NONE = 'none';

    public const TTE_DRAFT = 'draft';

    public const TTE_SIGNED = 'signed';

    public const TTE_REVOKED = 'revoked';

    protected $fillable = [
        'ticket_number',
        'nik',
        'name',
        'phone',
        'service_id',
        'status',
        'admin_response',
        'form_data',
        'letter_payload',
        'letter_number',
        'letter_date',
        'draft_pdf_path',
        'signed_pdf_path',
        'signed_pdf_hash',
        'verification_token',
        'tte_status',
        'signer_official_id',
        'signer_name',
        'signer_position',
        'signer_nik_masked',
        'signed_at',
        'signed_by_user_id',
        'revoked_at',
        'revoked_by_user_id',
        'revocation_reason',
    ];

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'letter_payload' => 'array',
            'letter_date' => 'date',
            'signed_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ServiceRequestAttachment::class)->orderBy('id');
    }

    public function signerOfficial(): BelongsTo
    {
        return $this->belongsTo(Official::class, 'signer_official_id');
    }

    public function signedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by_user_id');
    }

    public function revokedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by_user_id');
    }

    public function isTteSigned(): bool
    {
        return $this->tte_status === self::TTE_SIGNED && ! empty($this->signed_pdf_path);
    }

    public function isTteRevoked(): bool
    {
        return $this->tte_status === self::TTE_REVOKED;
    }

    public function hasDraftPdf(): bool
    {
        return ! empty($this->draft_pdf_path);
    }

    public function hasSignedPdf(): bool
    {
        return ! empty($this->signed_pdf_path);
    }

    public function maskedNik(): string
    {
        $nik = (string) ($this->nik ?? '');
        if (strlen($nik) >= 16) {
            return substr($nik, 0, 6).'******'.substr($nik, -4);
        }

        return $nik !== '' ? substr($nik, 0, 4).'***' : '-';
    }

    /**
     * Membangun URL dan warna tombol notifikasi WhatsApp sesuai status permohonan dan TTE.
     *
     * @return array{url: string, color: string}|null
     */
    public function buildWhatsAppNotification(?string $statusOverride = null, ?string $adminResponseOverride = null): ?array
    {
        $waNumber = preg_replace('/[^0-9]/', '', (string) ($this->phone ?? ''));
        if ($waNumber === '') {
            return null;
        }

        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62'.substr($waNumber, 1);
        }

        $serviceName = $this->service?->title ?? 'layanan yang Anda ajukan';
        $status = $statusOverride ?? $this->status;
        $rawAdminResponse = $adminResponseOverride !== null ? $adminResponseOverride : $this->admin_response;
        $adminResponse = filled($rawAdminResponse) ? trim((string) $rawAdminResponse) : null;

        if ($this->tte_status === self::TTE_SIGNED) {
            $trackUrl = route('service-requests.track', ['ticket_number' => $this->ticket_number]);
            $message = "Halo {$this->name}, surat {$serviceName} Anda dengan Nomor Tiket {$this->ticket_number} telah selesai ditandatangani secara elektronik. Unduh surat PDF melalui tautan berikut dengan verifikasi NIK: {$trackUrl}";
            $color = '#25d366';
        } elseif ($this->tte_status === self::TTE_REVOKED) {
            $message = "Halo {$this->name}, surat {$serviceName} Anda dengan Nomor Tiket {$this->ticket_number} telah DICABUT keabsahannya oleh Pemerintah Desa.";
            if (filled($this->revocation_reason)) {
                $message .= ' Alasan: '.trim((string) $this->revocation_reason).'.';
            }
            $message .= ' Silakan hubungi Kantor Desa untuk informasi lebih lanjut.';
            $color = '#e11d48';
        } elseif ($status === 'Ditolak') {
            $message = "Halo {$this->name}, permohonan {$serviceName} Anda dengan Nomor Tiket {$this->ticket_number} ditolak.";
            if ($adminResponse !== null) {
                $message .= " Catatan petugas: {$adminResponse}.";
            }
            $message .= ' Silakan perbaiki data atau hubungi Kantor Desa.';
            $color = '#e11d48';
        } else {
            $statusLabel = match ($status) {
                'Diproses' => 'sedang DIPROSES',
                'Selesai' => 'telah SELESAI diproses. Silakan mengambil berkas fisik di Kantor Desa',
                default => 'masih MENUNGGU untuk diproses',
            };

            $message = "Halo {$this->name}, permohonan {$serviceName} Anda dengan Nomor Tiket {$this->ticket_number} {$statusLabel}.\n\nTerima kasih.";
            if ($adminResponse !== null) {
                $message .= "\n\nCatatan petugas: {$adminResponse}";
            }

            $color = match ($status) {
                'Selesai' => '#25d366',
                'Diproses' => '#f59e0b',
                default => '#64748b',
            };
        }

        return [
            'url' => "https://wa.me/{$waNumber}?text=".urlencode($message),
            'color' => $color,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceRequest $request): void {
            if (empty($request->ticket_number)) {
                $date = now()->format('Ymd');
                $random = strtoupper(bin2hex(random_bytes(3)));
                $request->ticket_number = 'SRV-'.$date.'-'.$random;
            }
        });

        // Hapus seluruh berkas fisik privat karena ON DELETE CASCADE melewati event model anak
        static::deleting(function (ServiceRequest $request): void {
            app(ServiceAttachmentService::class)->deleteRequestDirectory($request->id);
        });
    }
}
