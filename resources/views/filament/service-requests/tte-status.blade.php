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

<div style="display: flex; flex-direction: column; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid rgba(148, 163, 184, 0.25);">
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
                <a href="{{ route('admin.service-request-letters.preview', ['serviceRequest' => $record->id, 'type' => 'draft']) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; background-color: #ffffff; color: #334155; border: 1px solid #cbd5e1;">
                    Lihat Draf PDF
                </a>
            @endif

            @if($record?->isTteSigned())
                <a href="{{ route('admin.service-request-letters.preview', ['serviceRequest' => $record->id, 'type' => 'signed']) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                    Lihat Surat TTE
                </a>
                <a href="{{ route('admin.service-request-letters.download', ['serviceRequest' => $record->id, 'type' => 'signed']) }}"
                   style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; background-color: #059669; color: #ffffff;">
                    Unduh Surat TTE
                </a>
            @endif
        </div>
    </div>

    @if(($record?->service?->is_tte_enabled ?? false) && ! ($isGlobalTteEnabled ?? false))
        <p style="margin: 0; font-size: 12px; font-weight: 500; color: #b45309;">
            Fitur TTE BSrE belum diaktifkan pada menu Pengaturan.
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
