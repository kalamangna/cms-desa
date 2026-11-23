@php
    $attachments = $record?->attachments ?? collect();
@endphp

@if($attachments->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Belum ada berkas persyaratan terlampir pada permohonan ini.
    </p>
@else
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach($attachments as $attachment)
            @php
                $previewUrl = route('admin.service-request-attachments.preview', $attachment);
                $downloadUrl = route('admin.service-request-attachments.download', $attachment);
            @endphp
            <div class="flex flex-col justify-between gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-start gap-3">
                    @if($attachment->isImage())
                        <a href="{{ $previewUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="Lihat {{ $attachment->requirement_name }}"
                           class="block h-16 w-16 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                            <img src="{{ $previewUrl }}"
                                 alt="{{ $attachment->requirement_name }}"
                                 loading="lazy"
                                 class="h-full w-full object-cover">
                        </a>
                    @else
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-xs font-bold uppercase text-rose-600 dark:border-rose-900 dark:bg-rose-950/60 dark:text-rose-400">
                            PDF
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $attachment->requirement_name }}
                        </p>
                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                            {{ $attachment->original_name }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ $attachment->formattedSize() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <a href="{{ $previewUrl }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex h-10 flex-1 items-center justify-center rounded-lg border border-gray-300 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Lihat Berkas
                    </a>
                    <a href="{{ $downloadUrl }}"
                       class="inline-flex h-10 flex-1 items-center justify-center rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white transition hover:bg-emerald-700">
                        Unduh Berkas
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
