@php
    use App\Models\ServiceRequest;

    $status = $record?->tte_status ?? ServiceRequest::TTE_NONE;
    $statusLabel = match ($status) {
        ServiceRequest::TTE_DRAFT => 'Draf',
        ServiceRequest::TTE_SIGNED => 'Sudah TTE',
        ServiceRequest::TTE_REVOKED => 'Dicabut',
        default => 'Belum Dibuat',
    };

    $badgeStyle = match ($status) {
        ServiceRequest::TTE_DRAFT => 'background-color: #fef3c7; color: #b45309; border: 1px solid #fcd34d;',
        ServiceRequest::TTE_SIGNED => 'background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7;',
        ServiceRequest::TTE_REVOKED => 'background-color: #ffe4e6; color: #be123c; border: 1px solid #fda4af;',
        default => 'background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
    };
@endphp

<div style="display: flex; flex-direction: column; gap: 12px; padding: 4px 2px 16px 2px; border-bottom: 1px solid rgba(148, 163, 184, 0.25);">
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
            <span style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700; line-height: 1.4; {{ $badgeStyle }}">
                {{ $statusLabel }}
            </span>

            @if($record?->signer_name)
                <span style="font-size: 13px; font-weight: 500; opacity: 0.85;">
                    {{ $record->signer_name }}
                    @if($record->signed_at)
                        &bull; {{ $record->signed_at->translatedFormat('d M Y, H:i') }}
                    @endif
                </span>
            @endif
        </div>

        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
            @if($record?->hasDraftPdf() && ! $record->isTteSigned())
                <x-filament::button
                    tag="a"
                    :href="route('admin.service-request-letters.preview', ['serviceRequest' => $record->id, 'type' => 'draft'])"
                    target="_blank"
                    rel="noopener noreferrer"
                    color="gray"
                    outlined
                    size="sm"
                    icon="heroicon-o-eye"
                >
                    Lihat Draf
                </x-filament::button>
                <x-filament::button
                    tag="a"
                    :href="route('admin.service-request-letters.download', ['serviceRequest' => $record->id, 'type' => 'draft'])"
                    color="primary"
                    size="sm"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Unduh Draf
                </x-filament::button>
            @endif

            @if($record?->isTteSigned())
                <x-filament::button
                    tag="a"
                    :href="route('admin.service-request-letters.preview', ['serviceRequest' => $record->id, 'type' => 'signed'])"
                    target="_blank"
                    rel="noopener noreferrer"
                    color="success"
                    outlined
                    size="sm"
                    icon="heroicon-o-eye"
                >
                    Lihat Surat
                </x-filament::button>
                <x-filament::button
                    tag="a"
                    :href="route('admin.service-request-letters.download', ['serviceRequest' => $record->id, 'type' => 'signed'])"
                    color="success"
                    size="sm"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Unduh Surat
                </x-filament::button>
            @endif
        </div>
    </div>

    @if(! ($record?->service?->is_tte_enabled ?? false))
        <p style="margin: 0; font-size: 12px; font-weight: 500; opacity: 0.75;">
            Layanan ini belum mengaktifkan fitur pembuatan surat TTE.
        </p>
    @elseif(! ($isGlobalTteEnabled ?? false))
        <p style="margin: 0; font-size: 12px; font-weight: 500; color: #b45309;">
            Fitur TTE BSrE belum diaktifkan pada menu Pengaturan.
        </p>
    @elseif(($isGlobalTteEnabled ?? false) && config('bsre.mock'))
        <p style="margin: 0; font-size: 12px; font-weight: 500; color: #b45309;">
            Mode simulasi BSrE sedang aktif. Surat yang diterbitkan tidak menggunakan sertifikat elektronik nyata.
        </p>
    @endif

    @if($status === ServiceRequest::TTE_REVOKED && $record?->revocation_reason)
        <p style="margin: 0; font-size: 12px; font-weight: 500; color: #be123c;">
            Alasan pencabutan: {{ $record->revocation_reason }}
        </p>
    @endif

    @if($record?->signed_pdf_hash && $record->isTteSigned())
        <p style="margin: 0; word-break: break-all; font-family: monospace; font-size: 11px; opacity: 0.7;">
            SHA-256: {{ $record->signed_pdf_hash }}
        </p>
    @endif
</div>
