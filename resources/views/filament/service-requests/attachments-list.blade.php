@php
    $attachments = $record?->attachments ?? collect();
@endphp

@if($attachments->isEmpty())
    <p style="margin: 0; font-size: 13px; opacity: 0.7;">
        Belum ada berkas persyaratan terlampir pada permohonan ini.
    </p>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
        @foreach($attachments as $attachment)
            @php
                $previewUrl = route('admin.service-request-attachments.preview', $attachment);
                $downloadUrl = route('admin.service-request-attachments.download', $attachment);
                $isImage = $attachment->isImage();
            @endphp
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 12px; padding: 14px; border-radius: 12px; border: 1px solid rgba(148, 163, 184, 0.28);">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    @if($isImage)
                        <a href="{{ $previewUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="Lihat {{ $attachment->requirement_name }}"
                           style="display: block; width: 64px; height: 64px; flex-shrink: 0; overflow: hidden; border-radius: 8px; border: 1px solid rgba(148, 163, 184, 0.28);">
                            <img src="{{ $previewUrl }}"
                                 alt="{{ $attachment->requirement_name }}"
                                 loading="lazy"
                                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        </a>
                    @else
                        <div style="display: flex; width: 64px; height: 64px; flex-shrink: 0; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid rgba(244, 63, 94, 0.35); background-color: rgba(244, 63, 94, 0.1); font-size: 12px; font-weight: 700; color: #e11d48;">
                            PDF
                        </div>
                    @endif

                    <div style="min-width: 0; flex: 1;">
                        <p style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; line-height: 1.35;">
                            {{ $attachment->requirement_name }}
                        </p>
                        <p title="{{ $attachment->original_name }}"
                           style="margin: 0 0 6px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 12px; opacity: 0.78;">
                            {{ $attachment->original_name }}
                        </p>
                        <p style="margin: 0; font-size: 12px; font-weight: 500; opacity: 0.68;">
                            {{ $isImage ? 'Gambar' : 'PDF' }} &bull; {{ $attachment->formattedSize() }}
                        </p>
                    </div>
                </div>

                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding-top: 10px; border-top: 1px solid rgba(148, 163, 184, 0.2);">
                    <x-filament::button
                        tag="a"
                        :href="$previewUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        color="gray"
                        outlined
                        size="sm"
                        icon="heroicon-o-eye"
                    >
                        Lihat Berkas
                    </x-filament::button>
                    <x-filament::button
                        tag="a"
                        :href="$downloadUrl"
                        color="primary"
                        size="sm"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Unduh Berkas
                    </x-filament::button>
                </div>
            </div>
        @endforeach
    </div>
@endif
