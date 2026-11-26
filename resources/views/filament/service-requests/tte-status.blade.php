@php
    use App\Models\ServiceRequest;

    $status = $record?->tte_status ?? ServiceRequest::TTE_NONE;
    $statusLabel = match ($status) {
        ServiceRequest::TTE_DRAFT => 'Draf',
        ServiceRequest::TTE_SIGNED => 'Sudah TTE',
        ServiceRequest::TTE_REVOKED => 'Dicabut',
        default => 'Belum Dibuat',
    };

    $badgeClass = match ($status) {
        ServiceRequest::TTE_DRAFT => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        ServiceRequest::TTE_SIGNED => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        ServiceRequest::TTE_REVOKED => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
        default => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
    };
@endphp

<div class="space-y-3 border-b border-gray-200 pb-4 dark:border-gray-700">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $badgeClass }}">
                {{ $statusLabel }}
            </span>

            @if($record?->signer_name)
                <span class="text-xs text-gray-600 dark:text-gray-300">
                    {{ $record->signer_name }}
                    @if($record->signed_at)
                        &bull; {{ $record->signed_at->translatedFormat('d M Y, H:i') }}
                    @endif
                </span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($record?->hasDraftPdf() && ! $record->isTteSigned())
                <a href="{{ route('admin.service-request-letters.preview', ['serviceRequest' => $record->id, 'type' => 'draft']) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    Lihat Draf PDF
                </a>
            @endif

            @if($record?->hasSignedPdf())
                <a href="{{ route('admin.service-request-letters.preview', ['serviceRequest' => $record->id, 'type' => 'signed']) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex h-10 items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 px-4 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    Lihat Surat TTE
                </a>
                <a href="{{ route('admin.service-request-letters.download', ['serviceRequest' => $record->id, 'type' => 'signed']) }}"
                   class="inline-flex h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 text-xs font-semibold text-white transition hover:bg-emerald-700">
                    Unduh Surat TTE
                </a>
            @endif

            @if($record?->verification_token && ($record->isTteSigned() || $record->isTteRevoked()))
                <a href="{{ route('letters.verify', $record->verification_token) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex h-10 items-center justify-center rounded-lg border border-sky-300 bg-sky-50 px-4 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 dark:border-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                    Buka Verifikasi
                </a>
            @endif
        </div>
    </div>

    @if(($record?->service?->is_tte_enabled ?? false) && ! ($isGlobalTteEnabled ?? false))
        <p class="text-xs font-medium text-amber-700 dark:text-amber-300">
            Fitur TTE BSrE belum diaktifkan pada menu Pengaturan.
        </p>
    @endif

    @if($status === ServiceRequest::TTE_REVOKED && $record?->revocation_reason)
        <p class="text-xs font-medium text-rose-700 dark:text-rose-300">
            Alasan pencabutan: {{ $record->revocation_reason }}
        </p>
    @endif

    @if($record?->signed_pdf_hash && $record->isTteSigned())
        <p class="break-all font-mono text-[11px] text-gray-500 dark:text-gray-400">
            SHA-256: {{ $record->signed_pdf_hash }}
        </p>
    @endif
</div>
