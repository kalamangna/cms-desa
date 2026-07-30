@extends('layouts.app')

@section('title', 'Aparatur | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Susunan jajaran aparatur dan perangkat desa yang bertugas dalam penyelenggaraan urusan pemerintahan di bawah Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
<script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@graph": [{
            "@@type": "GovernmentOrganization",
            "@@id": "{{ url('/aparatur') }}#organization",
            "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('img/sinjai.webp') }}",
            "employee": [
                @foreach($officials as $idx => $official) {
                    "@@type": "Person",
                    "name": "{{ $official->name }}",
                    "jobTitle": "{{ $official->position }}",
                    "image": "{{ $official->photo ? asset('storage/' . $official->photo) : asset('img/meta.webp') }}"
                }{{ $idx < count($officials) - 1 ? ',' : '' }}
                @endforeach
            ]
        }]
    }
</script>

<style id="sotk-custom-css">
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap');

    /* ─── SOTK Modal ─── */
    .sotk-modal-backdrop { position: fixed; inset: 0; z-index: 9999; background: rgba(2,6,23,0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; padding: 16px; }
    .sotk-modal { background: #fff; border-radius: 28px; box-shadow: 0 40px 100px rgba(15,23,42,0.3); width: 100%; max-width: 1240px; height: 90vh; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
    .sotk-modal:fullscreen, .sotk-modal:-webkit-full-screen { border-radius: 0; max-height: 100vh; height: 100vh; }
    .sotk-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 22px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; background: #fff; border-radius: 28px 28px 0 0; }
    .sotk-modal:fullscreen .sotk-modal-header, .sotk-modal:-webkit-full-screen .sotk-modal-header { border-radius: 0; }
    .sotk-modal-actions { display: flex; align-items: center; gap: 5px; }
    .sotk-modal-btn { width: 36px; height: 36px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; font-size: 12px; flex-shrink: 0; transition: background 0.2s, color 0.2s, box-shadow 0.2s, border-color 0.2s; }
    .sotk-modal-btn:hover { background: #ecfdf5; color: #059669; border-color: #6ee7b7; box-shadow: 0 4px 12px rgba(5,150,105,0.15); }
    .sotk-modal-btn.btn-close:hover { background: #fef2f2; color: #dc2626; border-color: #fca5a5; }
    .sotk-modal-body { flex: 1; min-height: 0; padding: 16px 22px 22px; display: flex; flex-direction: column; gap: 10px; }
    .sotk-hint { font-size: 11px; color: #94a3b8; font-weight: 500; display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
    .sotk-zoom-wrapper { flex: 1; min-height: 0; overflow: auto; border: 1px solid #e2e8f0; border-radius: 16px; background: #f8fafc; display: flex; align-items: flex-start; justify-content: center; padding: 20px 16px; cursor: grab; }

    .sotk-zoom-wrapper:active { cursor: grabbing; }
    .sotk-zoom-content { transform-origin: top center; transition: transform 0.15s ease; }
    .sotk-trigger-btn { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #059669; font-weight: 700; font-size: 13px; padding: 10px 22px; border-radius: 999px; border: 1.5px solid #059669; cursor: pointer; transition: background 0.2s, color 0.2s, box-shadow 0.2s; }
    .sotk-trigger-btn:hover { background: #059669; color: #fff; box-shadow: 0 4px 16px rgba(5,150,105,0.25); }
    .sotk-trigger-btn:active { transform: scale(0.97); }
    @media (max-width: 767px) {
        .sotk-trigger-btn { display: none !important; }
    }
    body.sotk-modal-open { overflow: hidden; }


    /* ─── Org Chart Tree ─── */
    /* ─── Tree layout ─── */
    .oc-tree { display: flex; min-width: max-content; justify-content: center; margin: 0 auto; }
    .oc-tree ul { display: flex; align-items: flex-start; position: relative; padding: 0; margin: 0; list-style: none; }

    /* ─── Connector Lines ─── */

    /*
     * Diagram vertikal (bukan skala):
     *
     *   ┌──────────┐
     *   │  parent  │  ← .oc-card
     *   └──────────┘
     *        │  ← height:32px (.oc-item.has-children > .oc-card::after)
     *  ──────┼──────  ← .oc-children > .oc-item::before (top:0 of .oc-item)
     *        │  ← height:32px (.oc-children > .oc-item > .oc-card::before)
     *   ┌──────────┐
     *   │  child   │  ← .oc-card, top:32px dalam .oc-item (karena padding-top)
     *   └──────────┘
     */

    /* Jarak parent card → bar = 32px */
    /* Margin negatif menetralkan padding horizontal .oc-item parent agar lebar kolom simetris */
    .oc-tree ul.oc-children { padding-top: 32px; margin-left: -20px; margin-right: -20px; }

    /* Jarak bar → child card = 32px (padding-top pada .oc-item) */
    .oc-item { display: flex; flex-direction: column; align-items: center; position: relative; padding: 32px 20px 0; }

    /* Garis vertikal TURUN dari parent card ke bar (dengan overlap 2px) */
    .oc-item.has-children > .oc-card::after {
        content: "" !important;
        display: block !important;
        position: absolute !important;
        left: 50% !important;
        top: 100% !important;
        width: 2px !important;
        height: 34px !important;
        background-color: #cbd5e1 !important;
        transform: translateX(-50%) !important;
        z-index: 2 !important;
    }

    /* Garis HORIZONTAL — bar di top:0 tiap .oc-item dalam .oc-children (overlap 1px kiri-kanan) */
    .oc-children > .oc-item::before {
        content: "" !important;
        display: block !important;
        position: absolute !important;
        top: 0 !important;
        left: -1px !important;
        right: -1px !important;
        height: 2px !important;
        background-color: #cbd5e1 !important;
        z-index: 1 !important;
    }
    .oc-children > .oc-item:first-child::before { left: 50% !important; }
    .oc-children > .oc-item:last-child::before  { right: 50% !important; }
    .oc-children > .oc-item:only-child::before  { display: none !important; }

    /* Garis vertikal NAIK dari child card ke bar (dengan overlap 2px) */
    .oc-children > .oc-item > .oc-card::before {
        content: "" !important;
        display: block !important;
        position: absolute !important;
        left: 50% !important;
        bottom: 100% !important;
        width: 2px !important;
        height: 34px !important;
        background-color: #cbd5e1 !important;
        transform: translateX(-50%) !important;
        z-index: 0 !important;
    }



    /* ─── Node Card ─── */
    .oc-card { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; background: #fff; border: 2px solid #e2e8f0; border-radius: 18px; padding: 0 0 12px 0; width: 160px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: box-shadow 0.2s, transform 0.2s; cursor: default; }
    .oc-card:hover { box-shadow: 0 10px 28px rgba(0,0,0,0.12); transform: translateY(-2px); }
    .oc-photo { width: 100%; height: 170px; border-radius: 16px 16px 0 0; overflow: hidden; border: none; border-bottom: 2px solid #e2e8f0; flex-shrink: 0; background-size: cover; background-position: top center; background-repeat: no-repeat; }

    .oc-name { font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.15; font-family: 'Poppins', sans-serif; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word; }
    .oc-pos { font-size: 11px; font-weight: 600; color: #475569; line-height: 1.15; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word; }





    /* Level colors */
    .oc-lv1 { border-color: #059669; background: linear-gradient(160deg, #ecfdf5 0%, #fff 55%); }
    .oc-lv1 .oc-name { color: #059669; }
    .oc-lv1 .oc-photo { border-color: #6ee7b7; }
    .oc-lv2 { border-color: #0ea5e9; background: linear-gradient(160deg, #f0f9ff 0%, #fff 55%); }
    .oc-lv2 .oc-name { color: #0369a1; }
    .oc-lv2 .oc-photo { border-color: #bae6fd; }
    .oc-lv3 { border-color: #8b5cf6; background: linear-gradient(160deg, #f5f3ff 0%, #fff 55%); }
    .oc-lv3 .oc-name { color: #6d28d9; }
    .oc-lv3 .oc-photo { border-color: #ddd6fe; }
    .oc-lv4 { border-color: #f59e0b; background: linear-gradient(160deg, #fffbeb 0%, #fff 55%); }
    .oc-lv4 .oc-name { color: #b45309; }
    .oc-lv4 .oc-photo { border-color: #fde68a; }
    .oc-lv5 { border-color: #94a3b8; }
</style>
@endpush

@section('content')
<div x-data="sotkModal()">

<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-emerald-600/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li><a href="/" class="hover:text-emerald-400 transition-colors duration-200 flex items-center gap-1.5"><i class="fa-solid fa-house text-[10px]"></i> Beranda</a></li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[9px] text-emerald-500/40"></i><span class="text-white">Aparatur Desa</span></li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">Aparatur <span class="text-emerald-500 italic">Desa</span></h1>
            <p class="text-slate-300 text-lg mt-2 mb-6">Jajaran pelayan masyarakat Desa {{ $site_settings['village_name'] ?? '' }}.</p>
            @if($officials->isNotEmpty())
            <button type="button" @click="open()" class="sotk-trigger-btn" aria-haspopup="dialog">
                Struktur Organisasi
                @if(!empty($site_settings['village_period_start']) || !empty($site_settings['village_period_end']))
                    <span class="opacity-75 font-normal ml-1">| Periode {{ $site_settings['village_period_start'] ?? '...' }} - {{ $site_settings['village_period_end'] ?? '...' }}</span>
                @endif
            </button>
            @endif
        </div>
    </div>
</div>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

    @if($officials->isEmpty())
    <x-empty-state
        icon="fa-solid fa-users"
        title="Data Aparatur Belum Diisi"
        description="Belum ada data aparatur yang diinput."
    />
    @else

    @php
        $officialItems = $officials->map(fn($o) => [
            'id' => $o->id,
            'name' => $o->name,
            'position' => $o->position,
            'photo' => $o->photo ? asset('storage/' . $o->photo) : asset('img/meta.webp')
        ])->values()->toArray();
    @endphp

    <div x-data="{
        officialItems: @js($officialItems),
        currentIndex: 0,
        previewOpen: false,
        get currentOfficial() {
            return this.officialItems[this.currentIndex] || {};
        },
        openPreviewByIndex(index) {
            this.currentIndex = index;
            this.previewOpen = true;
            document.body.classList.add('sotk-modal-open');
        },
        closePreview() {
            this.previewOpen = false;
            document.body.classList.remove('sotk-modal-open');
        },
        nextSlide() {
            if (this.officialItems.length === 0) return;
            this.currentIndex = (this.currentIndex + 1) % this.officialItems.length;
        },
        prevSlide() {
            if (this.officialItems.length === 0) return;
            this.currentIndex = (this.currentIndex - 1 + this.officialItems.length) % this.officialItems.length;
        }
    }">

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 md:gap-8">
        @foreach($officials as $idx => $official)
        @php
            $photoUrl = $official->photo ? asset('storage/' . $official->photo) : asset('img/meta.webp');
        @endphp
        <div class="group flex flex-col bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:border-emerald-200 hover:-translate-y-1.5 transition-all duration-300">
            <div class="relative w-full aspect-[4/5] overflow-hidden bg-slate-100">
                <button type="button"
                        @click="openPreviewByIndex({{ $idx }})"
                        class="w-full h-full block cursor-pointer relative group/btn text-left"
                        title="Klik untuk memperbesar foto">
                    <img src="{{ $photoUrl }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-700" alt="{{ $official->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300 flex items-end justify-center p-3 sm:p-4 text-white">
                        <span class="inline-flex items-center gap-1.5 bg-slate-900/80 backdrop-blur-md px-2.5 sm:px-3.5 py-1.5 rounded-full text-[10px] sm:text-xs font-bold shadow-lg">
                            <i class="fa-solid fa-expand text-[10px]"></i> <span class="hidden sm:inline">Perbesar Foto</span><span class="sm:hidden">Perbesar</span>
                        </span>
                    </div>
                </button>
            </div>
            <div class="p-3.5 sm:p-5 md:p-6 text-center flex-1 flex flex-col items-center justify-between bg-white">
                <h3 class="text-sm sm:text-base md:text-lg font-heading font-extrabold text-slate-900 mb-2 sm:mb-3 leading-snug group-hover:text-emerald-600 transition-colors">{{ $official->name }}</h3>
                <span class="inline-flex items-center bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full px-2.5 sm:px-3.5 py-1 text-[10px] sm:text-xs font-black uppercase tracking-wide">
                    {{ $official->position }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Modal Preview Foto Fullscreen With Slider --}}
    <div x-show="previewOpen" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closePreview()"
         @keydown.arrow-left.window="prevSlide()"
         @keydown.arrow-right.window="nextSlide()"
         @click="closePreview()"
         class="fixed inset-0 z-[9999] bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 md:p-10 cursor-pointer select-none"
         role="dialog" aria-modal="true">
        
        {{-- Counter Slide (Di Luar Modal, Kiri Atas Layar) --}}
        <template x-if="officialItems.length > 1">
            <div class="fixed top-5 left-5 sm:top-8 sm:left-8 z-50 bg-slate-900/80 backdrop-blur-md border border-white/20 text-white text-xs font-black uppercase tracking-wider px-4 py-2 rounded-full shadow-2xl">
                <span x-text="(currentIndex + 1) + ' / ' + officialItems.length"></span>
            </div>
        </template>

        {{-- Tombol Tutup (Di Luar Modal, Kanan Atas Layar) --}}
        <button
            type="button"
            @click.stop="closePreview()"
            class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110"
            title="Tutup (Esc)"
        >
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        {{-- Tombol Navigasi Panah Kiri (Di Luar Modal, Kiri Layar) --}}
        <template x-if="officialItems.length > 1">
            <button type="button" @click.stop="prevSlide()" 
                    class="fixed left-2 sm:left-6 md:left-10 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-emerald-600 text-white flex items-center justify-center transition duration-300 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110"
                    title="Sebelumnya (Tombol Panah Kiri)">
                <i class="fa-solid fa-chevron-left text-base sm:text-lg"></i>
            </button>
        </template>

        {{-- Tombol Navigasi Panah Kanan (Di Luar Modal, Kanan Layar) --}}
        <template x-if="officialItems.length > 1">
            <button type="button" @click.stop="nextSlide()" 
                    class="fixed right-2 sm:right-6 md:right-10 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-emerald-600 text-white flex items-center justify-center transition duration-300 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110"
                    title="Selanjutnya (Tombol Panah Kanan)">
                <i class="fa-solid fa-chevron-right text-base sm:text-lg"></i>
            </button>
        </template>

        <div class="relative bg-slate-900 rounded-[28px] max-w-xs sm:max-w-sm w-full flex flex-col overflow-hidden border border-slate-700/80 shadow-2xl cursor-default" @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            <div class="w-full aspect-[4/5] bg-slate-800 relative overflow-hidden flex items-center justify-center">
                <img :src="currentOfficial.photo" :alt="currentOfficial.name" class="w-full h-full object-cover object-top transition-all duration-300">
            </div>
            
            <div class="p-5 text-center relative z-10 bg-white border-t border-slate-100 flex flex-col items-center justify-center">
                <h3 class="text-lg sm:text-xl font-heading font-extrabold text-slate-900 mb-1.5" x-text="currentOfficial.name"></h3>
                <div class="flex items-center justify-center gap-3 w-full">
                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-full px-3.5 py-1 text-xs font-black uppercase tracking-wide" x-text="currentOfficial.position"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-12 text-center">

        <div x-show="isOpen" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="close()"
             @click="close()"
             class="sotk-modal-backdrop cursor-pointer"
             role="dialog" aria-modal="true" aria-labelledby="sotk-modal-title">
            <div x-ref="modal" class="sotk-modal cursor-default" @click.stop
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                {{-- Header --}}

                <div class="sotk-modal-header" @mousedown="isDragging = false">
                    <h3 class="font-heading font-bold text-lg text-slate-800">
                        Struktur Organisasi
                        @if(!empty($site_settings['village_period_start']) || !empty($site_settings['village_period_end']))
                            <span class="text-slate-500 font-normal ml-2 text-sm">| Periode {{ $site_settings['village_period_start'] ?? '...' }} - {{ $site_settings['village_period_end'] ?? '...' }}</span>
                        @endif
                    </h3>
                    <div class="sotk-modal-actions">
                        <span class="text-xs font-bold text-slate-400 tabular-nums mr-1" x-text="Math.round(scale * 100) + '%'"></span>
                        <button type="button" class="sotk-modal-btn" @click="zoomOut()" title="Perkecil"><i class="fa-solid fa-minus"></i></button>
                        <button type="button" class="sotk-modal-btn" @click="zoomIn()" title="Perbesar"><i class="fa-solid fa-plus"></i></button>
                        <button type="button" class="sotk-modal-btn" @click="resetZoom()" title="Reset ukuran"><i class="fa-solid fa-arrows-to-circle"></i></button>
                        <button type="button" class="sotk-modal-btn" @click="toggleFullscreen()" title="Layar penuh">
                            <i :class="isFullscreen ? 'fa-solid fa-compress' : 'fa-solid fa-expand'"></i>
                        </button>
                        <button type="button" class="sotk-modal-btn text-rose-600 hover:text-rose-700 hover:bg-rose-50 hover:border-rose-300" @click="downloadChart()" title="Download PDF Struktur Organisasi">
                            <i class="fa-solid fa-file-pdf text-sm"></i>
                        </button>
                        <button type="button" class="sotk-modal-btn btn-close" @click="close()" title="Tutup">
                            <i class="fa-solid fa-xmark"></i>
                        </button>



                    </div>
                </div>

                {{-- Body --}}
                <div class="sotk-modal-body">
                    <p class="sotk-hint">
                        <i class="fa-solid fa-hand-pointer"></i>
                        Scroll untuk zoom &bull; Tombol +/&minus; untuk mengatur ukuran
                    </p>
                    <div class="sotk-zoom-wrapper" @wheel.prevent="onWheel($event)">
                        <div class="sotk-zoom-content" :style="'transform: scale(' + scale + ')'">
                            <div class="oc-tree">
                                <ul id="oc-container"></ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @endif


</div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>

document.addEventListener('alpine:init', function () {

    Alpine.data('sotkModal', function () {
        return {
            searchQuery: '',
            isFullscreen: false,
            scale: 1,
            isDragging: false,
            startX: 0,
            startY: 0,
            scrollLeft: 0,
            scrollTop: 0,
            villageName: '{{ $site_settings["village_name"] ?? "" }}',
            villagePeriodStart: '{{ $site_settings["village_period_start"] ?? "" }}',
            villagePeriodEnd: '{{ $site_settings["village_period_end"] ?? "" }}',
            districtName: '{{ $site_settings["district_name"] ?? "" }}',
            regencyName: '{{ $site_settings["regency_name"] ?? "" }}',
            villageAddress: '{{ $site_settings["village_address"] ?? "" }}',
            villagePhone: '{{ $site_settings["village_phone"] ?? "" }}',
            villageEmail: '{{ $site_settings["village_email"] ?? "" }}',
            villageWebsite: '{{ url("/") }}',

            open: function () {
                this.isOpen = true;
                document.body.classList.add('sotk-modal-open');
                var self = this;
                this.$nextTick(function () {
                    if (!window._ocRendered) {
                        window.renderOcTree();
                        window._ocRendered = true;
                    }
                    self.$nextTick(function () {
                        self.recalcFitScale();
                    });
                });
            },
            close: function () {
                this.isOpen = false;
                document.body.classList.remove('sotk-modal-open');
                if (this.isFullscreen) this.exitFullscreen();
            },
            zoomIn: function () { this.scale = Math.min(+(this.scale + 0.15).toFixed(2), 3); },
            zoomOut: function () { this.scale = Math.max(+(this.scale - 0.15).toFixed(2), 0.2); },
            resetZoom: function () { this.scale = window._ocFitScale || 1; },
            onWheel: function (e) {
                var delta = e.deltaY > 0 ? -0.1 : 0.1;
                this.scale = Math.min(Math.max(+(this.scale + delta).toFixed(2), 0.2), 3);
            },
            recalcFitScale: function () {
                var wrapper = document.querySelector('.sotk-zoom-wrapper');
                var content = document.querySelector('.sotk-zoom-content');
                if (!wrapper || !content) return;
                var wW = wrapper.clientWidth - 32;
                var wH = wrapper.clientHeight - 40;
                var cW = content.scrollWidth;
                var cH = content.scrollHeight;
                if (cW <= 0 || cH <= 0) return;
                var fit = Math.min(wW / cW, wH / cH, 1);
                window._ocFitScale = Math.max(+(fit.toFixed(2)), 0.2);
                this.scale = window._ocFitScale;
            },
            downloadChart: function () {
                var content = document.querySelector('.sotk-zoom-content');
                if (!content) return;

                var pdfWindow = window.open('', '_blank');
                if (pdfWindow) {
                    pdfWindow.document.write('<title>Generating PDF...</title><body style="margin:0;display:flex;align-items:center;justify-content:center;height:100vh;background:#f8fafc;font-family:sans-serif;color:#64748b;"><div style="text-align:center;"><div style="border: 4px solid #e2e8f0; border-top: 4px solid #f43f5e; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div><p style="font-size:14px;font-weight:600;">Membuat dokumen PDF...</p></div><style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style></body>');
                }

                // Kita menggunakan dom-to-image-more, yang sangat stabil dan tidak crash pada oklch.
                // Tidak perlu lagi mencabut stylesheet!

                // Cabut semua stylesheet eksternal untuk mencegah html2canvas crash karena fungsi warna 'oklch' bawaan Tailwind v4
                var detachedSheets = [];
                var head = document.head;
                var sheets = Array.from(head.querySelectorAll('link[rel="stylesheet"], style'));
                sheets.forEach(function (sheet) {
                    var href = sheet.href || '';
                    var isFont = href.indexOf('fonts.googleapis.com') > -1 || href.indexOf('cdnjs.cloudflare.com') > -1;
                    if (sheet.id !== 'sotk-custom-css' && !isFont) {
                        detachedSheets.push({ element: sheet, nextSibling: sheet.nextSibling });
                        sheet.remove();
                    }
                });

                // Buat offscreen container yang aman
                var offscreenContainer = document.createElement('div');
                offscreenContainer.style.position = 'fixed';
                offscreenContainer.style.left = '0px';
                offscreenContainer.style.top = '0px';
                offscreenContainer.style.zIndex = '-9999';
                offscreenContainer.style.opacity = '0';
                offscreenContainer.style.width = content.scrollWidth + 'px';
                offscreenContainer.style.height = content.scrollHeight + 'px';
                offscreenContainer.style.background = '#ffffff';
                document.body.appendChild(offscreenContainer);

                // Kloning elemen secara rahasia untuk diproses
                var clone = content.cloneNode(true);
                
                // Bersihkan atribut Alpine.js (:style, x-data, dll) dari klon
                // agar MutationObserver Alpine tidak error saat klon dimasukkan ke DOM
                var allEls = [clone].concat(Array.from(clone.querySelectorAll('*')));
                allEls.forEach(function(el) {
                    Array.from(el.attributes).forEach(function(attr) {
                        if (attr.name.startsWith('x-') || attr.name.startsWith(':') || attr.name.startsWith('@')) {
                            el.removeAttribute(attr.name);
                        }
                    });
                });

                clone.style.transition = 'none';
                clone.style.transform = 'scale(1)';
                clone.style.transformOrigin = 'top center';
                
                // Injeksi Kop Surat Resmi ke dalam DOM clone
                var kopSurat = document.createElement('div');
                kopSurat.style.display = 'flex';
                kopSurat.style.alignItems = 'center';
                kopSurat.style.justifyContent = 'space-between';
                kopSurat.style.borderBottom = '3px solid #000';
                kopSurat.style.paddingBottom = '15px';
                kopSurat.style.marginBottom = '25px';
                
                var logoLeftWrap = document.createElement('div');
                logoLeftWrap.style.width = '100px';
                logoLeftWrap.style.flexShrink = '0';
                
                var logoLeft = document.createElement('div');
                logoLeft.className = 'kop-logo';
                logoLeft.style.width = '100px';
                logoLeft.style.height = '110px';
                logoLeft.style.backgroundImage = 'url("/img/sinjai.webp")';
                logoLeft.style.backgroundSize = 'contain';
                logoLeft.style.backgroundPosition = 'center';
                logoLeft.style.backgroundRepeat = 'no-repeat';
                logoLeftWrap.appendChild(logoLeft);
                
                var textWrap = document.createElement('div');
                textWrap.style.flexGrow = '1';
                textWrap.style.textAlign = 'center';
                textWrap.style.padding = '0 20px';
                
                var t1 = document.createElement('div');
                t1.textContent = 'PEMERINTAH KABUPATEN ' + (this.regencyName || 'SINJAI').toUpperCase();
                t1.style.fontSize = '26px';
                t1.style.fontWeight = 'bold';
                t1.style.color = '#000';
                
                var t2 = document.createElement('div');
                t2.textContent = 'KECAMATAN ' + (this.districtName || '...').toUpperCase();
                t2.style.fontSize = '26px';
                t2.style.fontWeight = 'bold';
                t2.style.color = '#000';
                
                var t3 = document.createElement('div');
                t3.textContent = 'DESA ' + (this.villageName || '...').toUpperCase();
                t3.style.fontSize = '34px';
                t3.style.fontWeight = 'bold';
                t3.style.color = '#000';
                
                var t4 = document.createElement('div');
                t4.textContent = (this.villageAddress || '') + ' - Telp: ' + (this.villagePhone || '-') + ' - Email: ' + (this.villageEmail || '-');
                t4.style.fontSize = '14px';
                t4.style.fontStyle = 'italic';
                t4.style.color = '#000';
                t4.style.marginTop = '8px';
                
                textWrap.appendChild(t1);
                textWrap.appendChild(t2);
                textWrap.appendChild(t3);
                textWrap.appendChild(t4);
                
                var logoRightWrap = document.createElement('div');
                logoRightWrap.style.width = '100px';
                logoRightWrap.style.flexShrink = '0';
                // Biarkan kosong untuk keseimbangan simetris
                
                kopSurat.appendChild(logoLeftWrap);
                kopSurat.appendChild(textWrap);
                kopSurat.appendChild(logoRightWrap);
                
                // Tambahkan judul SOTK di bawah Kop
                var titleContent = document.createElement('div');
                titleContent.style.textAlign = 'center';
                titleContent.style.marginBottom = '60px';
                
                var st1 = document.createElement('div');
                st1.textContent = 'STRUKTUR ORGANISASI';
                st1.style.fontSize = '24px';
                st1.style.fontWeight = 'bold';
                st1.style.textDecoration = 'underline';
                st1.style.color = '#000';
                st1.style.marginBottom = '8px';
                
                var st2 = document.createElement('div');
                st2.textContent = 'PEMERINTAH DESA ' + (this.villageName || '').toUpperCase();
                st2.style.fontSize = '20px';
                st2.style.fontWeight = 'bold';
                st2.style.color = '#000';
                
                titleContent.appendChild(st1);
                titleContent.appendChild(st2);
                
                if (this.villagePeriodStart || this.villagePeriodEnd) {
                    var periodText = (this.villagePeriodStart || '...') + ' - ' + (this.villagePeriodEnd || '...');
                    var st3 = document.createElement('div');
                    st3.textContent = 'PERIODE ' + periodText;
                    st3.style.fontSize = '18px';
                    st3.style.fontWeight = 'bold';
                    st3.style.color = '#000';
                    st3.style.marginTop = '8px';
                    titleContent.appendChild(st3);
                }
                
                var headerContainer = document.createElement('div');
                headerContainer.appendChild(kopSurat);
                headerContainer.appendChild(titleContent);
                
                clone.insertBefore(headerContainer, clone.firstChild);
                
                // Paksa penggunaan web-safe font standar (Arial/Helvetica) pada seluruh elemen klon
                // Hal ini menjamin html2canvas bisa merender font dengan sempurna tanpa tergantung koneksi atau CORS Google Fonts
                var allNodes = [clone].concat(Array.from(clone.querySelectorAll('*')));
                allNodes.forEach(function(el) {
                    el.style.fontFamily = "Arial, Helvetica, sans-serif";
                });
                
                offscreenContainer.appendChild(clone);
                
                // Konversi semua background-image (termasuk foto aparatur & logo kop surat) di dalam klon menjadi Base64 menggunakan Fetch API
                var photoNodes = clone.querySelectorAll('.oc-photo, .kop-logo');
                var convertPromises = Array.from(photoNodes).map(function(node) {
                    var bgImage = node.style.backgroundImage;
                    if (!bgImage || bgImage === 'none') return Promise.resolve();
                    
                    var match = bgImage.match(/url\(["']?(.*?)["']?\)/);
                    if (!match || !match[1]) return Promise.resolve();
                    
                    var url = match[1];
                    if (url.startsWith('data:')) return Promise.resolve();
                    
                    return fetch(url)
                        .then(res => res.blob())
                        .then(blob => new Promise((resolve) => {
                            var reader = new FileReader();
                            reader.onloadend = () => {
                                node.style.backgroundImage = 'url("' + reader.result + '")';
                                resolve();
                            };
                            reader.onerror = resolve; // Abaikan jika error
                            reader.readAsDataURL(blob);
                        }))
                        .catch(() => Promise.resolve()); // Abaikan error fetch
                });

                Promise.all(convertPromises).then(function() {
                    html2canvas(clone, {
                        backgroundColor: '#ffffff',
                        scale: 1.5,
                        logging: false,
                        useCORS: true
                    }).then(function (canvas) {
                        // Kembalikan semua stylesheet
                        detachedSheets.forEach(function (item) {
                            head.insertBefore(item.element, item.nextSibling);
                        });
                        offscreenContainer.remove();

                        var imgData = canvas.toDataURL('image/jpeg', 0.95);
                        var scale = 1.5;
                        var imgWidth = canvas.width;
                        var imgHeight = canvas.height;
                    
                    var padding = 40;
                    var pdfWidth = (imgWidth / scale) + (padding * 2);
                    var pdfHeight = (imgHeight / scale) + (padding * 2);

                    var { jsPDF } = window.jspdf;
                    var doc = new jsPDF('l', 'px', [pdfWidth, pdfHeight]);
                    
                    doc.addImage(imgData, 'JPEG', padding, padding, imgWidth / scale, imgHeight / scale, undefined, 'FAST');
                    
                    var fileName = 'Struktur_Organisasi_' + (this.villageName ? this.villageName.replace(/\s+/g, '_') : 'Desa');
                    if (this.villagePeriodStart || this.villagePeriodEnd) {
                        fileName += '_Periode_' + (this.villagePeriodStart || '') + '-' + (this.villagePeriodEnd || '');
                    }
                    
                    doc.save(fileName + '.pdf');
                    
                    if (pdfWindow) pdfWindow.close();
                }.bind(this)).catch(function(err) {
                    detachedSheets.forEach(function (item) {
                        head.insertBefore(item.element, item.nextSibling);
                    });
                    if (offscreenContainer.parentNode) offscreenContainer.remove();
                    if (pdfWindow) pdfWindow.close();
                    
                    console.error('Gagal membuat PDF:', err);
                    alert('Gagal mendownload PDF bagan SOTK. ' + (err.message || err));
                });
                }.bind(this)).catch(function(err) {
                    if (pdfWindow) pdfWindow.close();
                    console.error('Promise.all error:', err);
                });
            },





            toggleFullscreen: function () {

                var el = this.$refs.modal;
                if (this.isFullscreen) {
                    this.exitFullscreen();
                } else {
                    if (el.requestFullscreen) el.requestFullscreen();
                    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                }
            },
            exitFullscreen: function () {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            },
            init: function () {
                var self = this;
                var onFsChange = function () {
                    self.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
                    self.$nextTick(function () {
                        // Beri jeda kecil agar browser menyelesaikan animasi/transisi resize ke fullscreen
                        setTimeout(function () {
                            self.recalcFitScale();
                        }, 150);
                    });
                };
                document.addEventListener('fullscreenchange', onFsChange);
                document.addEventListener('webkitfullscreenchange', onFsChange);
            }

        };
    });
});
</script>

<script>
(function () {
    var treeData    = {!! json_encode($tree) !!};
    var storageBase = '/storage';
    var defaultPhoto = '/img/meta.webp';


    function buildNode(node) {
        var li = document.createElement('li');
        var hasChildren = Array.isArray(node.children) && node.children.length > 0;
        li.className = 'oc-item' + (hasChildren ? ' has-children' : '');

        var lv = parseInt(node.level) || 5;
        var lvClass = lv >= 1 && lv <= 4 ? 'oc-lv' + lv : 'oc-lv5';
        var photo = node.photo ? storageBase + '/' + node.photo : defaultPhoto;

        var card = document.createElement('div');
        card.className = 'oc-card ' + lvClass;

        var imgWrap = document.createElement('div');
        imgWrap.className = 'oc-photo';
        imgWrap.style.backgroundImage = 'url("' + photo + '")';
        imgWrap.style.backgroundSize = 'cover';
        imgWrap.style.backgroundPosition = 'center';
        imgWrap.style.backgroundColor = '#f1f5f9';
        var nameEl = document.createElement('div');
        nameEl.className = 'oc-name';
        nameEl.title = node.name;
        nameEl.textContent = node.name;

        var posEl = document.createElement('div');
        posEl.className = 'oc-pos';
        posEl.title = node.position || '';
        posEl.textContent = node.position || '';

        card.appendChild(imgWrap);
        card.appendChild(nameEl);
        card.appendChild(posEl);
        li.appendChild(card);

        if (hasChildren) {
            var ul = document.createElement('ul');
            ul.className = 'oc-children';
            node.children.forEach(function (child) {
                ul.appendChild(buildNode(child));
            });
            li.appendChild(ul);
        }

        return li;
    }

    window._ocRendered = false;
    window.renderOcTree = function () {
        var container = document.getElementById('oc-container');
        if (!container || !treeData || treeData.length === 0) return;
        container.innerHTML = '';
        treeData.forEach(function (rootNode) {
            container.appendChild(buildNode(rootNode));
        });
    };
}());
</script>
@endpush

