@extends('layouts.app')

@section('title', 'Layanan Mandiri | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Panduan prosedur, persyaratan berkas, dan pengajuan layanan administrasi pada Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
@if(!$services->isEmpty())
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        @foreach($services as $idx => $service)
        {
            "@@type": "GovernmentService",
            "@@id": "{{ url('/layanan') }}#service-{{ $service->id }}",
            "name": {!! json_encode($service->title) !!},
            "description": {!! json_encode(strip_tags($service->description)) !!},
            "serviceOperator": {
                "@@type": "GovernmentOrganization",
                "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}",
                "url": "{{ url('/') }}"
            },
            "provider": {
                "@@type": "GovernmentOffice",
                "name": "Kantor Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}",
                "url": "{{ url('/kontak') }}"
            }
        }{{ $idx < count($services) - 1 ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
@endpush

@php
    $servicesMap = $services->mapWithKeys(fn ($service) => [
        $service->id => [
            'title' => $service->title,
            'requirements' => $service->requirementItems->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'is_required' => (bool) $item->is_required,
            ])->values()->all(),
        ],
    ])->all();

    $oldServiceId = old('service_id') ? (int) old('service_id') : null;
    $oldServiceTitle = $oldServiceId && isset($servicesMap[$oldServiceId])
        ? $servicesMap[$oldServiceId]['title']
        : '';

    $attachmentErrors = [];
    foreach ($errors->getMessages() as $key => $messages) {
        if (str_starts_with($key, 'attachments.')) {
            $reqId = substr($key, strlen('attachments.'));
            $attachmentErrors[$reqId] = $messages[0] ?? '';
        }
    }
@endphp

@section('content')
{{-- Wrapper with Alpine JS state --}}
<div x-data="{
        servicesMap: @js($servicesMap),
        serverAttachmentErrors: @js($attachmentErrors),
        fileStates: {},
        submitting: false,
        showApplyModal: {{ $errors->any() && $oldServiceId ? 'true' : 'false' }},
        applyServiceId: {{ $oldServiceId ?: 'null' }},
        applyServiceTitle: @js($oldServiceTitle),
        showLacak: {{ isset($serviceRequest) || isset($searched_ticket) ? 'true' : 'false' }},
        ticket: @js($searched_ticket ?? ''),
        loading: false,
        searched: {{ isset($searched_ticket) ? 'true' : 'false' }},
        result: {{ isset($serviceRequest) ? json_encode([
            'found' => true,
            'ticket_number' => $serviceRequest->ticket_number,
            'name' => $serviceRequest->name,
            'nik_masked' => substr($serviceRequest->nik, 0, 4) . '**********',
            'service_title' => $serviceRequest->service?->title ?? 'Layanan Umum',
            'status' => $serviceRequest->status,
            'created_at' => $serviceRequest->created_at->translatedFormat('d M Y, H:i'),
            'attachments' => $serviceRequest->attachments->pluck('requirement_name')->values()->all(),
        ]) : 'null' }},
        get activeRequirements() {
            if (!this.applyServiceId || !this.servicesMap[this.applyServiceId]) {
                return [];
            }
            return this.servicesMap[this.applyServiceId].requirements || [];
        },
        openApplyModal(serviceId) {
            this.applyServiceId = serviceId;
            this.applyServiceTitle = this.servicesMap[serviceId]?.title || '';
            this.fileStates = {};
            this.serverAttachmentErrors = {};
            this.submitting = false;
            this.showApplyModal = true;
        },
        handleFileSelect(reqId, event) {
            const input = event.target;
            const file = input.files && input.files[0];
            delete this.serverAttachmentErrors[reqId];

            if (!file) {
                delete this.fileStates[reqId];
                return;
            }

            const allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const maxBytes = 2 * 1024 * 1024;

            if (!allowedExt.includes(ext)) {
                input.value = '';
                this.fileStates[reqId] = { name: '', sizeText: '', error: 'Format berkas harus JPG, PNG, WebP, atau PDF.' };
                return;
            }

            if (file.size > maxBytes) {
                input.value = '';
                this.fileStates[reqId] = { name: '', sizeText: '', error: 'Ukuran berkas melebihi batas maksimal 2 MB.' };
                return;
            }

            const sizeText = file.size >= 1048576
                ? (file.size / 1048576).toFixed(1).replace('.', ',') + ' MB'
                : Math.max(1, Math.round(file.size / 1024)) + ' KB';

            this.fileStates[reqId] = { name: file.name, sizeText, error: '' };
        },
        getAttachmentError(reqId) {
            return this.fileStates[reqId]?.error || this.serverAttachmentErrors[reqId] || '';
        },
        async fetchStatus(ticketNum = null) {
            if (ticketNum) this.ticket = ticketNum;
            if (!this.ticket.trim()) return;
            this.loading = true;
            this.searched = true;
            try {
                const res = await fetch('{{ route('service-requests.track') }}?ticket_number=' + encodeURIComponent(this.ticket.trim()), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    this.result = await res.json();
                } else {
                    this.result = null;
                }
            } catch (e) {
                this.result = null;
            } finally {
                this.loading = false;
            }
        }
     }"
     class="relative"
>

    {{-- ===================== HERO ===================== --}}
    <div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900"></div>
            <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
                <ol class="inline-flex items-center gap-2">
                    <li>
                        <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                            <i class="fa-solid fa-house text-[10px]"></i> Beranda
                        </a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                        <span class="text-white">Layanan</span>
                    </li>
                </ol>
            </nav>
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-4 sm:mb-6 drop-shadow-2xl">
                    Layanan <span class="text-primary-500 italic">Mandiri</span>
                </h1>
                <p class="text-slate-300 text-base sm:text-lg mt-2 leading-relaxed">
                    Panduan persyaratan dan pengajuan surat administrasi warga.
                </p>
            </div>
        </div>
    </div>

    {{-- ===================== SERVICES GRID ===================== --}}
    <div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

            {{-- Success Modal Popup & Ticket Display --}}
            @if(session('success') && session('ticket_number'))
            <div x-data="{ showSuccessModal: true, copied: false }"
                 x-show="showSuccessModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md cursor-pointer select-none"
                 style="display: none;"
                 @click.self="showSuccessModal = false">

                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-6 sm:p-8 md:px-10 w-full max-w-md mx-auto border border-slate-100 dark:border-slate-800 relative text-center cursor-default">
                    <button
                        type="button"
                        @click.stop="showSuccessModal = false"
                        aria-label="Tutup dialog"
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 w-10 h-10 rounded-full flex items-center justify-center transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>

                    <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-5">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <h3 class="font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 text-xl sm:text-2xl mb-2">{{ session('success') }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Simpan nomor tiket di bawah untuk melacak status permohonan.</p>

                    <div class="mb-6 sm:mb-8">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-1">Nomor Tiket</span>
                        <div class="flex items-center justify-center gap-2">
                            <h4 class="text-lg sm:text-xl font-mono font-black text-slate-800 dark:text-slate-100 select-all break-all">{{ session('ticket_number') }}</h4>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ session('ticket_number') }}'); copied = true; setTimeout(() => copied = false, 2000);"
                                    aria-label="Salin nomor tiket"
                                    class="shrink-0 w-10 h-10 inline-flex items-center justify-center rounded-xl text-slate-400 dark:text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button" @click="showSuccessModal = false" class="w-full h-11 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 text-white rounded-2xl font-bold text-sm transition-all duration-200 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
            @endif

            {{-- Tabs Navigation --}}
            <div class="flex justify-center mb-8 sm:mb-12 md:mb-16">
                <div class="flex space-x-6 sm:space-x-8 border-b border-slate-200 dark:border-slate-800 overflow-x-auto max-w-full">
                    <button
                        type="button"
                        @click="showLacak = false"
                        :class="!showLacak ? 'border-primary-600 text-primary-700 dark:text-primary-400 font-extrabold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 font-semibold'"
                        class="py-3 px-2 text-sm whitespace-nowrap border-b-2 transition-all duration-200 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 cursor-pointer"
                    >
                        <i class="fa-solid fa-list-check"></i> Daftar Layanan
                    </button>
                    <button
                        type="button"
                        @click="showLacak = true"
                        :class="showLacak ? 'border-primary-600 text-primary-700 dark:text-primary-400 font-extrabold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 font-semibold'"
                        class="py-3 px-2 text-sm whitespace-nowrap border-b-2 transition-all duration-200 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 cursor-pointer"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i> Lacak Pengajuan
                    </button>
                </div>
            </div>

            {{-- PANEL: DAFTAR LAYANAN --}}
            <div x-show="!showLacak" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 xl:gap-8">
                @forelse($services as $service)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 flex flex-col overflow-hidden">

                    {{-- Card Top --}}
                    <div class="p-6 xl:p-8 flex-1">
                        {{-- Icon --}}
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/50 text-primary-600 dark:text-primary-400 flex items-center justify-center text-lg sm:text-xl mb-5 sm:mb-8 shadow-xs">
                            <i class="fa-solid {{ $service->icon }}"></i>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-xl sm:text-2xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-3">{{ $service->title }}</h3>

                        {{-- Description --}}
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed font-medium mb-6">
                            {{ Str::limit($service->description, 120) }}
                        </p>

                        {{-- Collapsible Requirements Preview --}}
                        <div x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hover:text-primary-600 dark:hover:text-primary-400 transition mb-0 cursor-pointer">
                                <i class="fa-solid fa-list-ul text-[10px]"></i>
                                Persyaratan
                                <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="mt-3 space-y-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 rounded-2xl p-4 text-xs leading-relaxed"
                                x-cloak>
                                @if($service->requirements)
                                    <div class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                                        {!! $service->requirements !!}
                                    </div>
                                @endif

                                @if($service->requirementItems->isNotEmpty())
                                    <div class="{{ $service->requirements ? 'pt-3 border-t border-slate-200/80 dark:border-slate-700' : '' }}">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">
                                            Berkas Unggahan
                                        </p>
                                        <ul class="space-y-1.5">
                                            @foreach($service->requirementItems as $item)
                                                <li class="flex items-center justify-between gap-2 text-slate-700 dark:text-slate-200">
                                                    <span class="font-medium">{{ $item->name }}</span>
                                                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold {{ $item->is_required ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' : 'bg-slate-200/70 text-slate-600 dark:bg-slate-700 dark:text-slate-300' }}">
                                                        {{ $item->is_required ? 'Wajib' : 'Opsional' }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(! $service->requirements && $service->requirementItems->isEmpty())
                                    <p class="text-slate-400 dark:text-slate-500 italic">Persyaratan belum diisi.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 px-6 xl:px-8 py-4 sm:py-5 flex items-center justify-center bg-slate-50/50 dark:bg-slate-950/40">
                        <button
                            type="button"
                            @click="openApplyModal({{ $service->id }})"
                            class="w-full h-11 inline-flex items-center justify-center gap-2 bg-primary-600 text-white px-5 rounded-full font-bold text-sm hover:bg-primary-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            Ajukan Layanan
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-8">
                    <x-empty-state
                        icon="fa-solid fa-clipboard-list"
                        title="Belum ada layanan tersedia"
                        description="Hubungi petugas desa untuk informasi layanan."
                        action-label="Hubungi Kami"
                        action-href="/kontak"
                    />
                </div>
                @endforelse
            </div>

            {{-- PANEL: LACAK PERMOHONAN --}}
            <div x-show="showLacak" class="space-y-6 sm:space-y-8 animate-in fade-in duration-300" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-8 md:p-12 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 mb-6 sm:mb-8 max-w-4xl mx-auto">
                    <div class="mb-6 sm:mb-8">
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Lacak Status Permohonan</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Periksa progres permohonan menggunakan nomor tiket.</p>
                    </div>
                    <form @submit.prevent="fetchStatus()" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        <div class="flex-grow relative">
                            <input type="text" x-model="ticket" placeholder="SRV-20261123-A1B2C3" required
                                   class="w-full h-12 sm:h-14 px-4 sm:px-5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono font-bold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-base sm:text-sm">
                        </div>
                        <button type="submit" :disabled="loading" class="h-12 sm:h-14 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white px-6 sm:px-8 rounded-2xl font-bold transition flex items-center justify-center gap-2 text-sm cursor-pointer active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 shadow-xs">
                            <template x-if="loading">
                                <i class="fa-solid fa-spinner animate-spin"></i>
                            </template>
                            <template x-if="!loading">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </template>
                            <span x-text="loading ? 'Memuat...' : 'Lacak Tiket'"></span>
                        </button>
                    </form>
                </div>

                {{-- Tracking Results (Real-time Rendered) --}}
                <div x-show="searched" x-transition:enter="transition ease-out duration-300">
                    <template x-if="result && result.found">
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 p-5 sm:p-8 md:p-12 space-y-5 sm:space-y-6 max-w-4xl mx-auto">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 sm:pb-6 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Nomor Tiket</span>
                                    <h4 class="text-base sm:text-lg font-mono font-black text-slate-900 dark:text-slate-100 mt-0.5 break-all" x-text="result.ticket_number"></h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 dark:text-slate-400 text-xs font-semibold">Status:</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider"
                                          :class="{
                                              'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700': result.status === 'Menunggu',
                                              'bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900 text-amber-700 dark:text-amber-300': result.status === 'Diproses',
                                              'bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900 text-emerald-700 dark:text-emerald-300': result.status === 'Selesai'
                                          }"
                                    >
                                        <span x-text="result.status"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-medium">
                                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-0.5">Nama Pemohon</span>
                                    <span class="text-slate-800 dark:text-slate-100 font-bold" x-text="result.name"></span>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-0.5">NIK Pemohon</span>
                                    <span class="text-slate-800 dark:text-slate-100 font-mono font-bold" x-text="result.nik_masked"></span>
                                </div>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-1">Layanan Pengajuan</span>
                                <h4 class="font-extrabold text-slate-900 dark:text-slate-100 text-base" x-text="result.service_title"></h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-2">
                                    <i class="fa-regular fa-clock mr-1"></i> Diajukan pada <span x-text="result.created_at"></span>
                                </p>
                            </div>

                            <template x-if="result.attachments && result.attachments.length > 0">
                                <div class="bg-slate-50 dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-100 dark:border-slate-700">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2.5">Berkas Terlampir</span>
                                    <ul class="flex flex-wrap gap-2">
                                        <template x-for="(itemName, idx) in result.attachments" :key="idx">
                                            <li class="inline-flex items-center gap-1.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 px-3 py-1 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                <i class="fa-solid fa-file-circle-check text-emerald-500 text-[11px]"></i>
                                                <span x-text="itemName"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>

                            <template x-if="result.status === 'Selesai'">
                                <div class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-900 p-5 rounded-2xl text-slate-700 dark:text-slate-200 text-sm">
                                    <h5 class="font-extrabold text-emerald-800 dark:text-emerald-300 mb-1"><i class="fa-solid fa-circle-check"></i> Pengajuan Selesai Diproses</h5>
                                    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 font-medium">Berkas surat Anda telah selesai diproses. Silakan ambil dokumen fisik di <strong>Kantor Desa</strong> pada jam kerja dengan membawa dokumen asli terkait.</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!loading && searched && (!result || !result.found)">
                        <div class="text-center py-16 animate-in fade-in duration-300">
                            <i class="fa-solid fa-circle-xmark text-rose-400 text-3xl mb-3 block"></i>
                            <h3 class="text-slate-500 dark:text-slate-400 font-bold text-sm">Nomor tiket tidak ditemukan</h3>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- ===================== APPLY LAYANAN MODAL ===================== --}}
    <div x-show="showApplyModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md cursor-pointer select-none"
         style="display: none;"
         @keydown.escape.window="if (!submitting) showApplyModal = false"
         @click="if (!submitting) showApplyModal = false">

        <div x-show="showApplyModal"
             @click.stop
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-5 sm:p-8 md:p-10 max-w-lg w-full max-h-[90vh] overflow-y-auto border border-slate-100 dark:border-slate-800 relative cursor-default">

            <div class="flex items-start justify-between gap-4 mb-6">
                <div class="min-w-0">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Pengajuan Layanan</span>
                    <h3 class="text-lg sm:text-xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mt-1 break-words" x-text="applyServiceTitle"></h3>
                </div>
                <button
                    type="button"
                    @click.stop="if (!submitting) showApplyModal = false"
                    aria-label="Tutup formulir pengajuan"
                    class="shrink-0 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 w-10 h-10 rounded-full flex items-center justify-center transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            @error('form_error')
                <div class="mb-5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-2xl p-4 flex gap-3 items-start text-rose-700 dark:text-rose-300 text-xs font-medium">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <form action="{{ route('service-requests.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  @submit="submitting = true"
                  class="space-y-5">
                @csrf
                <x-honeypot />
                <input type="hidden" name="service_id" :value="applyServiceId">

                <div>
                    <label for="apply-nik" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1">NIK Pemohon</label>
                    <input type="text"
                           id="apply-nik"
                           name="nik"
                           value="{{ old('nik') }}"
                           maxlength="16"
                           inputmode="numeric"
                           placeholder="16 digit NIK"
                           required
                           class="w-full h-12 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border {{ $errors->has('nik') ? 'border-rose-500 dark:border-rose-500' : 'border-slate-200 dark:border-slate-700' }} focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-base sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition">
                    @error('nik')
                        <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apply-name" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1">Nama Lengkap</label>
                    <input type="text"
                           id="apply-name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Budi Santoso"
                           required
                           class="w-full h-12 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-rose-500 dark:border-rose-500' : 'border-slate-200 dark:border-slate-700' }} focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-base sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition">
                    @error('name')
                        <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="apply-phone" class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2 ml-1">Nomor WhatsApp</label>
                    <input type="tel"
                           id="apply-phone"
                           name="phone"
                           value="{{ old('phone') }}"
                           placeholder="081234567890"
                           required
                           class="w-full h-12 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border {{ $errors->has('phone') ? 'border-rose-500 dark:border-rose-500' : 'border-slate-200 dark:border-slate-700' }} focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-base sm:text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition">
                    @error('phone')
                        <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dynamic Requirement File Uploads --}}
                <template x-if="activeRequirements.length > 0">
                    <div class="space-y-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">Berkas Persyaratan</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Format JPG, PNG, WebP, atau PDF maksimal 2 MB.</p>
                        </div>

                        <template x-for="req in activeRequirements" :key="req.id">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <label :for="'attachment-' + req.id" class="text-xs font-bold text-slate-700 dark:text-slate-200" x-text="req.name"></label>
                                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                          :class="req.is_required ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                          x-text="req.is_required ? 'Wajib' : 'Opsional'">
                                    </span>
                                </div>

                                <template x-if="req.description">
                                    <p class="text-xs text-slate-500 dark:text-slate-400" x-text="req.description"></p>
                                </template>

                                <input type="file"
                                       :id="'attachment-' + req.id"
                                       :name="'attachments[' + req.id + ']'"
                                       :required="req.is_required"
                                       accept=".jpg,.jpeg,.png,.webp,.pdf"
                                       @change="handleFileSelect(req.id, $event)"
                                       :class="getAttachmentError(req.id) ? 'border-rose-500 dark:border-rose-500' : 'border-slate-200 dark:border-slate-700'"
                                       class="block w-full max-w-full overflow-hidden min-h-11 rounded-xl border bg-slate-50 dark:bg-slate-800 px-3 py-2 text-xs text-slate-700 dark:text-slate-200 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 dark:file:bg-primary-950/60 file:px-3 file:py-2 file:text-xs file:font-bold file:text-primary-700 dark:file:text-primary-300 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/60 focus:outline-none focus:ring-2 focus:ring-primary-500 transition cursor-pointer">

                                <template x-if="fileStates[req.id] && fileStates[req.id].name && !getAttachmentError(req.id)">
                                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span class="truncate" x-text="fileStates[req.id].name"></span>
                                        <span class="shrink-0 text-slate-400 dark:text-slate-500" x-text="fileStates[req.id].sizeText"></span>
                                    </p>
                                </template>

                                <template x-if="getAttachmentError(req.id)">
                                    <p class="text-xs font-medium text-rose-600 dark:text-rose-400" x-text="getAttachmentError(req.id)"></p>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button"
                            @click="showApplyModal = false"
                            :disabled="submitting"
                            class="w-full sm:flex-1 min-h-12 flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-4 py-3 rounded-full font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-700 disabled:opacity-60 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="submitting"
                            class="w-full sm:flex-1 min-h-12 flex items-center justify-center gap-2 bg-primary-600 text-white px-4 py-3 rounded-full font-bold text-sm hover:bg-primary-700 disabled:opacity-60 disabled:cursor-not-allowed active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-all duration-200 shadow-xs cursor-pointer">
                        <template x-if="submitting">
                            <i class="fa-solid fa-spinner animate-spin text-xs"></i>
                        </template>
                        <template x-if="!submitting">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                        </template>
                        <span x-text="submitting ? 'Mengirim...' : 'Kirim Pengajuan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
