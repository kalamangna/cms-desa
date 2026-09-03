@extends('layouts.app')

@section('title', 'Desa ' . ($site_settings['village_name'] ?? '') . ' | Portal Informasi & Layanan Digital')
@section('meta_description', 'Portal resmi Desa ' . ($site_settings['village_name'] ?? '') . ', Kec. ' . \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? '')) . ', Kab. ' . \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? '')) . '. Layanan mandiri warga, transparansi APBDes, dan informasi pembangunan.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
    <link rel="preload" as="image" href="{{ ($villageHead && $villageHead->photo) ? asset('storage/' . $villageHead->photo) : asset('img/meta.webp') }}" fetchpriority="high">
@endpush

@section('content')

@php
    $popups = \App\Models\PopupInfographic::where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->get(['image', 'title'])
        ->toArray();
@endphp

@if(!empty($popups))
@push('head')
    <link rel="preload" as="image" href="{{ asset('storage/' . $popups[0]['image']) }}" fetchpriority="high">
@endpush

<div x-data="{ 
        isOpen: false,
        activeSlide: 0,
        direction: 1,
        popups: @js($popups),
        get activeSlideTitle() {
            return this.popups[this.activeSlide]?.title || '';
        },
        totalSlides: {{ count($popups) }},
        touchStartX: 0,
        touchEndX: 0,
        init() {
            const hasShown = sessionStorage.getItem('home_popup_shown_session');
            if (!hasShown) {
                this.isOpen = true;
                document.body.classList.add('overflow-hidden');
            }
        },
        closePopup() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
            sessionStorage.setItem('home_popup_shown_session', 'true');
        },
        nextSlide() {
            this.direction = 1;
            this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
        },
        prevSlide() {
            this.direction = -1;
            this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        handleTouchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },
        handleTouchEnd(e) {
            this.touchEndX = e.changedTouches[0].screenX;
            const diff = this.touchStartX - this.touchEndX;
            if (Math.abs(diff) > 40) {
                if (diff > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            }
        }
     }"
     x-show="isOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="closePopup()"
     @keydown.arrow-left.window="if(isOpen) prevSlide()"
     @keydown.arrow-right.window="if(isOpen) nextSlide()"
     @touchstart.passive="handleTouchStart($event)"
     @touchend.passive="handleTouchEnd($event)"
     @click="closePopup()"
     class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 cursor-pointer select-none"
     role="dialog" aria-modal="true" aria-labelledby="popup-infographic-title">
    
    {{-- Counter Slide (Di Luar Modal, Kiri Atas Layar) --}}
    @if(count($popups) > 1)
        <div class="fixed top-4 left-4 sm:top-6 sm:left-6 md:top-8 md:left-8 z-50 bg-slate-900/80 backdrop-blur-md border border-white/20 text-white text-[11px] sm:text-xs font-black uppercase tracking-wider px-3 py-1.5 sm:px-4 sm:py-2 rounded-full shadow-2xl pointer-events-none">
            <span x-text="(activeSlide + 1) + ' / {{ count($popups) }}'"></span>
        </div>
    @endif

    {{-- Tombol Tutup (Di Luar Modal, Kanan Atas Layar) --}}
    <button
        type="button"
        @click.stop="closePopup()"
        class="fixed top-4 right-4 sm:top-6 sm:right-6 md:top-8 md:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
        title="Tutup (Esc)"
    >
        <i class="fa-solid fa-xmark text-base sm:text-lg md:text-xl"></i>
    </button>

    {{-- Tombol Navigasi Panah Kiri (Di Luar Modal, Kiri Layar) --}}
    @if(count($popups) > 1)
        <button type="button" @click.stop="prevSlide()" 
                class="fixed left-3 sm:left-6 md:left-8 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                title="Sebelumnya (Tombol Panah Kiri)">
            <i class="fa-solid fa-chevron-left text-xs sm:text-sm md:text-base"></i>
        </button>

        {{-- Tombol Navigasi Panah Kanan (Di Luar Modal, Kanan Layar) --}}
        <button type="button" @click.stop="nextSlide()" 
                class="fixed right-3 sm:right-6 md:right-8 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                title="Selanjutnya (Tombol Panah Kanan)">
            <i class="fa-solid fa-chevron-right text-xs sm:text-sm md:text-base"></i>
        </button>
    @endif

    {{-- Container Modal Konten --}}
    <div class="relative w-full h-full overflow-hidden flex items-center justify-center cursor-default" @click.stop>
        <div class="relative flex transition-transform duration-500 ease-out h-full w-full"
             :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
            @foreach($popups as $index => $popup)
            <div class="w-full h-full flex-shrink-0 flex items-center justify-center px-12 pt-16 pb-20 sm:px-20 sm:pt-20 sm:pb-28 md:px-24 md:pt-20 md:pb-32 relative">
                <img src="{{ asset('storage/' . $popup['image']) }}" 
                     class="max-w-full max-h-full w-auto h-auto object-contain rounded-2xl shadow-2xl transition-all duration-300 select-none"
                     alt="{{ $popup['title'] ?? 'Infografis Beranda' }}"
                     onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'"
                     @if($loop->first) fetchpriority="high" decoding="async" @else loading="lazy" @endif>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Footer Info (Floating at viewport bottom) --}}
    <div class="fixed bottom-0 inset-x-0 p-6 sm:p-8 md:p-12 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent flex flex-col items-center text-center pointer-events-none z-10">
        <span class="text-[10px] md:text-xs font-black uppercase tracking-widest text-primary-400 drop-shadow-md">Infografis Desa {{ $site_settings['village_name'] ?? '' }}</span>
        <h3 id="popup-infographic-title" class="text-base md:text-2xl font-heading font-black tracking-tight text-white leading-snug line-clamp-2 mt-2 drop-shadow-xl max-w-3xl" x-show="activeSlideTitle" x-text="activeSlideTitle"></h3>
    </div>
</div>
@endif

{{-- 1. HERO --}}
<div class="relative bg-slate-900 dark:bg-slate-950 min-h-screen flex items-center overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-500"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 dark:bg-primary-600/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-28 md:pt-28 md:pb-32 lg:pb-36 lg:pt-32 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center justify-between">

            {{-- Kolom Kiri: Teks --}}
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-px w-12 bg-primary-500"></div>
                    <span class="text-primary-400 text-xs font-black uppercase tracking-widest">Portal Informasi & Layanan Digital</span>
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-heading font-black text-white leading-[1.1] tracking-tight mb-6 drop-shadow-2xl">
                    <span class="block">Desa</span><span class="block text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-primary-600">{{ $site_settings['village_name'] ?? '' }}</span>
                </h1>
                <div class="flex flex-wrap items-center gap-2 text-slate-400 text-sm md:text-base font-medium tracking-wide mb-10">
                    Kec. {{ \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? '...')) }} 
                    <span class="text-slate-600 px-1.5">•</span> 
                    Kab. {{ \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? '...')) }}
                    <span class="text-slate-600 px-1.5">•</span> 
                    Prov. {{ \Illuminate\Support\Str::title(preg_replace('/^Provinsi\s+/i', '', $site_settings['province_name'] ?? '...')) }}
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/statistik" class="group inline-flex items-center justify-center gap-3 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 text-white font-bold text-base px-8 py-4 rounded-2xl shadow-xl shadow-primary-600/30 hover:shadow-2xl hover:shadow-primary-600/40 hover:-translate-y-1 transition-all duration-300" aria-label="Lihat Dashboard Statistik Desa">
                        <i class="fa-solid fa-chart-pie group-hover:rotate-12 transition-transform"></i>
                        Dashboard Statistik
                    </a>
                    <a href="/layanan" class="group inline-flex items-center justify-center gap-3 bg-white/5 backdrop-blur-xl hover:bg-white/10 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 text-white font-bold text-base px-8 py-4 rounded-2xl border border-white/10 hover:border-white/20 hover:shadow-2xl hover:shadow-white/10 hover:-translate-y-1 transition-all duration-300">
                        <i class="fa-solid fa-file-signature group-hover:scale-110 transition-transform"></i>
                        Layanan Mandiri
                    </a>
                </div>
            </div>

            {{-- Kolom Kanan: Foto Kepala Desa --}}
            <div class="flex items-center justify-center mt-16 lg:mt-0">
                <div class="relative">
                    {{-- Glow background --}}
                    <div class="absolute inset-0 bg-primary-500/20 rounded-3xl blur-3xl scale-110"></div>

                    {{-- Frame foto --}}
                    <div class="relative w-64 h-72 sm:w-72 sm:h-80 xl:w-80 xl:h-[400px] rounded-3xl overflow-hidden border-2 border-white/10 shadow-2xl">
                        <img src="{{ ($villageHead && $villageHead->photo) ? asset('storage/' . $villageHead->photo) : asset('img/meta.webp') }}"
                             class="w-full h-full object-cover object-top"
                             alt="Foto {{ $villageHead?->name ?? 'Kepala Desa' }}"
                             width="384"
                             height="480"
                             loading="eager"
                             fetchpriority="high"
                             onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                        {{-- Overlay gradient bawah --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
                    </div>

                    {{-- Badge nama kepala desa --}}
                    <div class="absolute -bottom-5 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-5 py-4 text-center shadow-xl">
                        <p class="text-white font-heading font-bold text-base leading-tight">
                            {{ $villageHead?->name ?? 'Kepala Desa' }}
                        </p>
                        <p class="text-primary-400 text-xs font-bold uppercase tracking-widest mt-1">
                            Kepala Desa {{ $site_settings['village_name'] ?? '' }}
                        </p>
                    </div>

                    {{-- Dekorasi sudut --}}
                    <div class="absolute -top-4 -right-4 w-20 h-20 border-2 border-primary-500/30 rounded-3xl"></div>
                    <div class="absolute -bottom-4 -left-4 w-14 h-14 bg-primary-500/10 rounded-2xl"></div>
                </div>
            </div>

        </div>
    </div>


</div>

{{-- 2. STAT CARDS --}}
<div class="relative z-20 mt-10 lg:-mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

        {{-- Penduduk --}}
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center text-center sm:text-left gap-3 sm:gap-4 group cursor-default">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 flex items-center justify-center text-primary-700 dark:text-primary-400 flex-shrink-0 mx-auto sm:mx-0 shadow-lg shadow-primary-500/20">
                <i class="fa-solid fa-users text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 w-full">
                <p class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-0.5">Penduduk</p>
                <p class="text-2xl md:text-3xl font-heading font-black text-slate-900 dark:text-slate-100 leading-none tracking-tight">{{ number_format($totalPenduduk, 0, ',', '.') }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Jiwa</p>
            </div>
        </div>

        {{-- Keluarga --}}
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center text-center sm:text-left gap-3 sm:gap-4 group cursor-default">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 flex items-center justify-center text-primary-700 dark:text-primary-400 flex-shrink-0 mx-auto sm:mx-0 shadow-lg shadow-primary-500/20">
                <i class="fa-solid fa-house-chimney text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 w-full">
                <p class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-0.5">Keluarga</p>
                <p class="text-2xl md:text-3xl font-heading font-black text-slate-900 dark:text-slate-100 leading-none tracking-tight">{{ number_format($totalKeluarga, 0, ',', '.') }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Kepala Keluarga</p>
            </div>
        </div>

        {{-- Dusun --}}
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center text-center sm:text-left gap-3 sm:gap-4 group cursor-default">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 flex items-center justify-center text-primary-700 dark:text-primary-400 flex-shrink-0 mx-auto sm:mx-0 shadow-lg shadow-primary-500/20">
                <i class="fa-solid fa-map-location-dot text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 w-full">
                <p class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-0.5">Dusun</p>
                <p class="text-2xl md:text-3xl font-heading font-black text-slate-900 dark:text-slate-100 leading-none tracking-tight">{{ number_format($totalDusun, 0, ',', '.') }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ $totalRT }} RT / {{ $totalRW }} RW</p>
            </div>
        </div>

        {{-- Luas Wilayah --}}
        <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center text-center sm:text-left gap-3 sm:gap-4 group cursor-default">
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 flex items-center justify-center text-primary-700 dark:text-primary-400 flex-shrink-0 mx-auto sm:mx-0 shadow-lg shadow-primary-500/20">
                <i class="fa-solid fa-ruler-combined text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0 w-full">
                <p class="text-slate-500 dark:text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-0.5">Luas Wilayah</p>
                <p class="text-2xl md:text-3xl font-heading font-black text-slate-900 dark:text-slate-100 leading-none tracking-tight">{{ $site_settings['village_area'] ?? '—' }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ $site_settings['village_area_unit'] ?? 'km²' }}</p>
            </div>
        </div>

    </div>
</div>

{{-- 2.5 MENU LAYANAN CEPAT --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 md:mt-20">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 dark:text-slate-100">Akses <span class="text-primary-700 dark:text-primary-400">Layanan Cepat</span></h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-medium">Pilih layanan yang Anda butuhkan di bawah ini</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <a href="/layanan" class="group bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 hover:shadow-2xl hover:shadow-primary-500/10 hover:border-primary-300 dark:hover:border-primary-900/50 hover:-translate-y-1.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 flex flex-col items-center text-center relative overflow-hidden cursor-pointer">
            <div class="relative z-10 w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-primary-50 dark:bg-primary-950/40 border border-primary-100/80 dark:border-primary-900/50 flex items-center justify-center mb-5 group-hover:scale-105 group-hover:bg-primary-600 group-hover:shadow-lg group-hover:shadow-primary-600/30 transition-all duration-300 shadow-sm">
                <i class="fa-solid fa-file-signature text-2xl md:text-3xl text-primary-700 dark:text-primary-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="relative z-10 font-heading font-black text-slate-900 dark:text-slate-100 text-base md:text-lg tracking-tight mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Layanan Mandiri</h3>
            <p class="relative z-10 text-xs md:text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">Panduan dan pengajuan administrasi kependudukan</p>
        </a>
        
        <a href="/pengaduan" class="group bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 hover:shadow-2xl hover:shadow-primary-500/10 hover:border-primary-300 dark:hover:border-primary-900/50 hover:-translate-y-1.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 flex flex-col items-center text-center relative overflow-hidden cursor-pointer">
            <div class="relative z-10 w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-primary-50 dark:bg-primary-950/40 border border-primary-100/80 dark:border-primary-900/50 flex items-center justify-center mb-5 group-hover:scale-105 group-hover:bg-primary-600 group-hover:shadow-lg group-hover:shadow-primary-600/30 transition-all duration-300 shadow-sm">
                <i class="fa-solid fa-bullhorn text-2xl md:text-3xl text-primary-700 dark:text-primary-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="relative z-10 font-heading font-black text-slate-900 dark:text-slate-100 text-base md:text-lg tracking-tight mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Lapor & Pengaduan</h3>
            <p class="relative z-10 text-xs md:text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">Sampaikan aspirasi atau laporan masalah warga</p>
        </a>
        
        <a href="/buku-tamu" class="group bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 hover:shadow-2xl hover:shadow-primary-500/10 hover:border-primary-300 dark:hover:border-primary-900/50 hover:-translate-y-1.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 flex flex-col items-center text-center relative overflow-hidden cursor-pointer">
            <div class="relative z-10 w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-primary-50 dark:bg-primary-950/40 border border-primary-100/80 dark:border-primary-900/50 flex items-center justify-center mb-5 group-hover:scale-105 group-hover:bg-primary-600 group-hover:shadow-lg group-hover:shadow-primary-600/30 transition-all duration-300 shadow-sm">
                <i class="fa-solid fa-address-book text-2xl md:text-3xl text-primary-700 dark:text-primary-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="relative z-10 font-heading font-black text-slate-900 dark:text-slate-100 text-base md:text-lg tracking-tight mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Buku Tamu</h3>
            <p class="relative z-10 text-xs md:text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">Tinggalkan pesan, kesan, atau saran untuk desa</p>
        </a>
        
        <a href="/potensi" class="group bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 hover:shadow-2xl hover:shadow-primary-500/10 hover:border-primary-300 dark:hover:border-primary-900/50 hover:-translate-y-1.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 flex flex-col items-center text-center relative overflow-hidden cursor-pointer">
            <div class="relative z-10 w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-primary-50 dark:bg-primary-950/40 border border-primary-100/80 dark:border-primary-900/50 flex items-center justify-center mb-5 group-hover:scale-105 group-hover:bg-primary-600 group-hover:shadow-lg group-hover:shadow-primary-600/30 transition-all duration-300 shadow-sm">
                <i class="fa-solid fa-map-marked-alt text-2xl md:text-3xl text-primary-700 dark:text-primary-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="relative z-10 font-heading font-black text-slate-900 dark:text-slate-100 text-base md:text-lg tracking-tight mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Potensi Desa</h3>
            <p class="relative z-10 text-xs md:text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">Eksplorasi pariwisata, budaya, dan komoditas lokal</p>
        </a>
    </div>
</div>

{{-- 3. SAMBUTAN KEPALA DESA --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">
    <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl rounded-[2rem] shadow-2xl shadow-slate-200/50 dark:shadow-slate-950/50 overflow-hidden border border-white/40 dark:border-slate-800 relative">
        {{-- Decorative Quote Mark in Background --}}
        <div class="absolute -top-12 -right-12 text-primary-500/10 pointer-events-none z-0">
            <i class="fa-solid fa-quote-right text-9xl md:text-[12rem]"></i>
        </div>

        <div class="p-8 md:p-16 lg:p-20 relative z-10 max-w-4xl mx-auto text-center flex flex-col items-center">
            <div class="flex items-center gap-3 mb-10 justify-center">
                <div class="h-px w-8 bg-primary-600"></div>
                <h2 class="text-primary-700 dark:text-primary-400 font-black text-xs uppercase tracking-widest">Sambutan Kepala Desa</h2>
                <div class="h-px w-8 bg-primary-600"></div>
            </div>
            <div class="prose prose-slate prose-p:mb-6 prose-p:last:mb-0 text-slate-600 dark:text-slate-300 text-base md:text-lg leading-relaxed italic mb-12 max-w-3xl text-center mx-auto font-medium">
                {!! $site_settings['village_head_greeting'] ?? 'Selamat datang di portal resmi Desa ' . ($site_settings['village_name'] ?? '') . '.' !!}
            </div>
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-primary-950/40 flex items-center justify-center text-primary-700 dark:text-primary-400 shadow-xs">
                    <i class="fa-solid fa-signature text-lg"></i>
                </div>
                <div>
                    <p class="font-heading font-extrabold text-lg text-slate-900 dark:text-slate-100">{{ $villageHead?->name ?? 'Nama Kepala Desa' }}</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Kepala Desa {{ $site_settings['village_name'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. DATA DEMOGRAFI --}}
<div class="bg-slate-50 dark:bg-slate-950 py-16 md:py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 lg:mb-16 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-primary-600"></div>
                    <span class="text-primary-700 dark:text-primary-400 font-black text-xs uppercase tracking-widest">Transparansi Data</span>
                </div>
                <h2 class="text-4xl md:text-6xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">Statistik & <span class="text-primary-700 dark:text-primary-400">APBDes</span></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Grafik Demografi Penduduk --}}
            <div class="lg:col-span-7 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-slate-950/50 overflow-hidden">
                <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-heading font-black tracking-tight text-2xl text-slate-900 dark:text-slate-100" id="chartCardTitle">Demografi Penduduk</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 font-medium" id="chartCardSubtitle">Perbandingan jumlah laki-laki dan perempuan aktif</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <label for="homeChartType" class="sr-only">Tipe Grafik Demografi</label>
                            <select id="homeChartType" aria-label="Tipe Grafik Demografi" class="appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2 pr-8 text-xs font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all cursor-pointer">
                                <option value="gender">Jenis Kelamin</option>
                                <option value="job">Status Pekerjaan</option>
                                <option value="education">Pendidikan</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-chevron-down text-[8px]"></i>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-950/40 px-3 py-1.5 rounded-full border border-primary-100 dark:border-primary-900/50 shadow-xs">{{ date('Y') }}</span>
                    </div>
                </div>
                <div class="p-8">
                    @if($lakiLakiCount == 0 && $perempuanCount == 0)
                        <div class="h-72 flex flex-col items-center justify-center text-center p-4">
                            <i class="fa-solid fa-users-slash text-slate-300 dark:text-slate-700 text-4xl mb-3"></i>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-semibold">Belum ada data demografi aktif.</p>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Impor data warga melalui panel admin untuk melihat visualisasi.</p>
                        </div>
                    @else
                        <div class="h-72"><div id="populationChart"></div></div>
                    @endif
                    <a href="/statistik" class="mt-8 flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-primary-600 hover:text-white hover:border-primary-600 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 cursor-pointer">
                        <i class="fa-solid fa-chart-line text-xs"></i> Statistik Lengkap
                    </a>
                </div>
            </div>

            {{-- APBDes --}}
            <div class="lg:col-span-5 bg-slate-900 rounded-3xl text-white overflow-hidden flex flex-col shadow-2xl shadow-slate-900/40 border border-slate-800">
                <div class="p-8 border-b border-white/10 flex justify-between items-center relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary-500/20 blur-2xl rounded-full pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="font-heading font-black tracking-tight text-2xl text-white">APBDes {{ date('Y') }}</h3>
                        <p class="text-slate-400 text-sm mt-1 font-medium">Realisasi anggaran desa berjalan</p>
                    </div>
                </div>
                <div class="p-8 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-emerald-400 text-sm">Total Pendapatan</span>
                                <span class="text-sm font-extrabold text-white">{{ number_format($pendapatanPct, 1, ',', '.') }}%</span>
                            </div>
                            <div class="w-full h-4 bg-white/10 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-emerald-500 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.8)]" style="width: {{ $pendapatanPct }}%"></div>
                            </div>
                            <p class="text-xs text-slate-300 font-medium mt-3">Target: Rp {{ number_format($budgetSummary['pendapatan']['budget'], 0, ',', '.') }}</p>
                        </div>
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-sky-400 text-sm">Total Belanja</span>
                                <span class="text-sm font-extrabold text-white">{{ number_format($belanjaPct, 1, ',', '.') }}%</span>
                            </div>
                            <div class="w-full h-4 bg-white/10 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-sky-500 rounded-full shadow-[0_0_10px_rgba(14,165,233,0.8)]" style="width: {{ $belanjaPct }}%"></div>
                            </div>
                            <p class="text-xs text-slate-300 font-medium mt-3">Target: Rp {{ number_format($budgetSummary['belanja']['budget'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <a href="/apbdes" class="mt-8 flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-white/10 backdrop-blur-md hover:bg-primary-600 border border-white/10 hover:border-primary-600 text-sm font-bold text-white shadow-lg active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 hover:shadow-primary-600/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                        <i class="fa-solid fa-file-invoice-dollar text-xs"></i> Detail Transparansi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 5. BERITA & PENGUMUMAN --}}
<div class="bg-white dark:bg-slate-950 py-16 md:py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 lg:mb-16 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-primary-600"></div>
                    <span class="text-primary-700 dark:text-primary-400 font-black text-xs uppercase tracking-widest">Informasi Terbaru</span>
                </div>
                <h2 class="text-4xl md:text-6xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">Berita & <span class="text-primary-700 dark:text-primary-400">Pengumuman</span></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
                @if($featuredPost)
                <a href="/berita/{{ $featuredPost->slug }}" class="block group focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-3xl cursor-pointer">
                    <div class="relative rounded-3xl overflow-hidden aspect-video mb-6 shadow-xl shadow-slate-200/60 dark:shadow-slate-950/60 border border-slate-200/80 dark:border-slate-800 group-hover:shadow-2xl group-hover:shadow-slate-300/50 dark:group-hover:shadow-slate-950 group-hover:-translate-y-1 transition-all duration-300">
                        <img src="{{ $featuredPost->featured_image ? asset('storage/' . $featuredPost->featured_image) : asset('img/meta.webp') }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                             alt="{{ $featuredPost->title }}"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/30 to-transparent"></div>
                        <div class="absolute top-6 left-6">
                            <span class="bg-primary-600/90 backdrop-blur-md text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-md border border-primary-500">Berita Utama</span>
                        </div>
                        <div class="absolute bottom-8 left-8 right-8">
                            <p class="text-slate-300 text-xs font-bold uppercase tracking-widest mb-3 drop-shadow-md">{{ $featuredPost->published_at->translatedFormat('d M Y') }}</p>
                            <h3 class="text-white font-heading font-black tracking-tight text-2xl md:text-4xl leading-tight line-clamp-2 group-hover:text-primary-300 transition-colors mb-3 drop-shadow-lg">{{ $featuredPost->title }}</h3>
                            <p class="text-white/80 text-sm md:text-base line-clamp-2 hidden md:block font-medium drop-shadow-md">{!! Str::limit(strip_tags($featuredPost->content), 120) !!}</p>
                        </div>
                    </div>
                </a>
                @endif

                <div class="space-y-4">
                    @forelse($recentPosts as $post)
                    <a href="/berita/{{ $post->slug }}" class="flex gap-5 group items-center p-4 rounded-2xl hover:bg-white dark:hover:bg-slate-900 hover:shadow-xl hover:shadow-slate-200/40 dark:hover:shadow-slate-950/40 hover:-translate-y-1 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 -mx-4 cursor-pointer">
                        <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-inner">
                            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp') }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 alt="{{ $post->title }}"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-primary-700 dark:text-primary-400 text-[10px] font-bold uppercase tracking-widest mb-1.5">{{ $post->published_at->translatedFormat('d M Y') }}</p>
                            <h4 class="font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 text-base md:text-lg leading-snug group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2 mb-1.5">{{ $post->title }}</h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs md:text-sm line-clamp-2 font-medium">{!! Str::limit(strip_tags($post->content), 100) !!}</p>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-300 dark:text-slate-600 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:translate-x-1 transition-all flex-shrink-0 hidden md:block"></i>
                    </a>
                    @empty
                        @if(!$featuredPost)
                        <x-empty-state
                            icon="fa-solid fa-newspaper"
                            title="Belum Ada Berita"
                            :compact="true"
                        />
                        @endif
                    @endforelse
                </div>

                <a href="/berita" class="mt-8 flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-primary-600 hover:text-white hover:border-primary-600 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 cursor-pointer">
                    Semua Berita <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="lg:col-span-4">
                <div class="sticky top-24 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-slate-800 rounded-[2rem] p-7 flex flex-col shadow-xl shadow-slate-200/40 dark:shadow-slate-950/40">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-heading font-black tracking-tight text-2xl text-slate-900 dark:text-slate-100">Pengumuman</h3>
                    </div>
                    <div class="space-y-4 flex-1">
                        @forelse($announcements as $ann)
                        <a href="/pengumuman" class="block bg-white dark:bg-slate-800/60 rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:shadow-slate-200/50 dark:hover:shadow-slate-950/50 hover:border-primary-200 dark:hover:border-primary-900/50 hover:-translate-y-1 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 cursor-pointer">
                            <div class="flex items-start gap-4">
                                <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-400 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-xs">
                                    <i class="fa-solid fa-bullhorn text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ $ann->published_at->translatedFormat('d M Y') }}</p>
                                    <h4 class="font-extrabold text-slate-900 dark:text-slate-100 text-sm leading-snug line-clamp-2">{{ $ann->title }}</h4>
                                </div>
                            </div>
                        </a>
                        @empty
                        <x-empty-state
                            icon="fa-solid fa-bullhorn"
                            title="Belum Ada Pengumuman"
                            :compact="true"
                        />
                        @endforelse
                    </div>

                    <a href="/pengumuman" class="mt-8 flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-primary-600 hover:text-white hover:border-primary-600 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 cursor-pointer">
                        Semua Pengumuman <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $homeGalleryItems = $galleries->map(function($g) {
        $embedUrl = '';
        if ($g->type === 'video' && $g->youtube_url) {
            if (preg_match('/(?:youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]{11})/', $g->youtube_url, $matches)) {
                $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
            }
        }
        return [
            'id' => $g->id,
            'type' => $g->type === 'video' ? 'video' : 'photo',
            'image_url' => $g->image_url ? $g->image_url : asset('img/meta.webp'),
            'title' => $g->title,
            'youtube_url' => $g->type === 'video' ? $g->youtube_url : '',
            'youtube_embed' => $embedUrl,
            'created_at' => $g->created_at->translatedFormat('d M Y')
        ];
    })->values()->toArray();
@endphp

{{-- 6. GALERI & PUBLIKASI --}}
<div 
    class="bg-slate-50 dark:bg-slate-950 py-16 md:py-20 lg:py-28"
    x-data="{
        galleryItems: @js($homeGalleryItems),
        currentIndex: 0,
        lightboxOpen: false,
        get currentItem() {
            return this.galleryItems[this.currentIndex] || {};
        },
        openLightboxByIndex(index) {
            this.currentIndex = index;
            this.lightboxOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeLightbox() {
            this.lightboxOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        nextSlide() {
            if (this.galleryItems.length === 0) return;
            this.currentIndex = (this.currentIndex + 1) % this.galleryItems.length;
        },
        prevSlide() {
            if (this.galleryItems.length === 0) return;
            this.currentIndex = (this.currentIndex - 1 + this.galleryItems.length) % this.galleryItems.length;
        },
        getYoutubeEmbed(url) {
            if (!url) return '';
            const match = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/);
            return (match && match[2].length === 11) ? 'https://www.youtube-nocookie.com/embed/' + match[2] : '';
        },
        touchStartX: 0,
        touchEndX: 0,
        handleTouchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },
        handleTouchEnd(e) {
            this.touchEndX = e.changedTouches[0].screenX;
            const diff = this.touchStartX - this.touchEndX;
            if (Math.abs(diff) > 40) {
                if (diff > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
            }
        }
    }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-primary-600"></div>
                    <span class="text-primary-700 dark:text-primary-400 font-black text-xs uppercase tracking-widest">Dokumentasi Desa</span>
                </div>
                <h2 class="text-4xl md:text-6xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">Galeri <span class="text-primary-700 dark:text-primary-400">Foto & Video</span></h2>
            </div>
        </div>

        {{-- Galeri Full Width Grid (4 Kolom Layout Rapi) --}}
        @if($galleries->isEmpty())
        <x-empty-state
            icon="fa-solid fa-images"
            title="Belum Ada Dokumentasi"
            :compact="true"
        />
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($galleries as $idx => $gallery)
            <div class="group relative bg-slate-900 rounded-3xl overflow-hidden shadow-md aspect-video cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-400/40 dark:hover:shadow-slate-950/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500" tabindex="0"
                 @click="openLightboxByIndex({{ $idx }})">
                <img src="{{ $gallery->image_url }}"
                     class="w-full h-full object-cover object-center group-hover:scale-110 transition duration-700"
                     alt="{{ $gallery->title }}"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                
                @if($gallery->type === 'video')

                    <div class="absolute inset-0 bg-slate-950/30 flex items-center justify-center group-hover:opacity-0 transition-opacity duration-300 pointer-events-none">
                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center shadow-xl shadow-red-950/40 text-white text-sm">
                            <i class="fa-solid fa-play ml-0.5"></i>
                        </div>
                    </div>
                @endif

                {{-- Hover Overlay Gradient & Info --}}
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/60 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-4 sm:p-5 text-white">
                    <span class="text-[9px] font-black uppercase tracking-widest text-primary-400 mb-0.5">
                        {{ $gallery->created_at->translatedFormat('d M Y') }}
                    </span>
                    <h3 class="text-xs sm:text-sm font-heading font-extrabold text-white leading-snug line-clamp-2">
                        {{ $gallery->title }}
                    </h3>
                </div>
            </div>
            @endforeach
        </div>

        <a href="/galeri" class="mt-10 flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-primary-600 hover:text-white hover:border-primary-600 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-300 shadow-sm hover:shadow-lg hover:shadow-primary-600/30 cursor-pointer">
            Semua Galeri <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- LIGHTBOX MODAL WITH SLIDER --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="lightboxOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeLightbox()"
        @keydown.arrow-left.window="if(lightboxOpen) prevSlide()"
        @keydown.arrow-right.window="if(lightboxOpen) nextSlide()"
        @touchstart.passive="handleTouchStart($event)"
        @touchend.passive="handleTouchEnd($event)"
        @click="closeLightbox()"
        class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 cursor-pointer select-none"
        role="dialog" aria-modal="true" aria-labelledby="home-gallery-lightbox-title"
    >
        {{-- Counter Slide (Di Luar Modal, Kiri Atas Layar) --}}
        <template x-if="galleryItems.length > 1">
            <div class="fixed top-4 left-4 sm:top-6 sm:left-6 md:top-8 md:left-8 z-50 bg-slate-900/80 backdrop-blur-md border border-white/20 text-white text-[11px] sm:text-xs font-black uppercase tracking-wider px-3 py-1.5 sm:px-4 sm:py-2 rounded-full shadow-2xl pointer-events-none">
                <span x-text="(currentIndex + 1) + ' / ' + galleryItems.length"></span>
            </div>
        </template>

        {{-- Tombol Tutup (Di Luar Modal, Kanan Atas Layar) --}}
        <button
            type="button"
            @click.stop="closeLightbox()"
            class="fixed top-4 right-4 sm:top-6 sm:right-6 md:top-8 md:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
            title="Tutup (Esc)"
        >
            <i class="fa-solid fa-xmark text-base sm:text-lg md:text-xl"></i>
        </button>

        {{-- Tombol Navigasi Panah Kiri (Di Luar Modal, Kiri Layar) --}}
        <template x-if="galleryItems.length > 1">
            <button type="button" @click.stop="prevSlide()" 
                    class="fixed left-3 sm:left-6 md:left-8 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    title="Sebelumnya (Tombol Panah Kiri)">
                <i class="fa-solid fa-chevron-left text-xs sm:text-sm md:text-base"></i>
            </button>
        </template>

        {{-- Tombol Navigasi Panah Kanan (Di Luar Modal, Kanan Layar) --}}
        <template x-if="galleryItems.length > 1">
            <button type="button" @click.stop="nextSlide()" 
                    class="fixed right-3 sm:right-6 md:right-8 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    title="Selanjutnya (Tombol Panah Kanan)">
                <i class="fa-solid fa-chevron-right text-xs sm:text-sm md:text-base"></i>
            </button>
        </template>

        {{-- Container Modal Konten --}}
        <div class="relative w-full h-full overflow-hidden flex items-center justify-center cursor-default" @click.stop>
            <div class="relative flex transition-transform duration-500 ease-out h-full w-full"
                 :style="'transform: translateX(-' + (currentIndex * 100) + '%)'">
                @foreach($homeGalleryItems as $index => $item)
                <div class="w-full h-full flex-shrink-0 flex items-center justify-center px-12 pt-16 pb-20 sm:px-20 sm:pt-20 sm:pb-28 md:px-24 md:pt-20 md:pb-32 relative">
                    @if($item['type'] === 'video')
                        <div class="w-full max-w-5xl aspect-video bg-black relative overflow-hidden rounded-2xl shadow-2xl">
                            <template x-if="lightboxOpen && currentIndex === {{ $index }}">
                                <iframe
                                    class="w-full h-full"
                                    src="{{ $item['youtube_embed'] }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            </template>
                        </div>
                    @else
                        <img src="{{ $item['image_url'] }}" 
                             class="max-w-full max-h-full w-auto h-auto object-contain rounded-2xl shadow-2xl transition-all duration-300 select-none"
                             alt="{{ $item['title'] ?? 'Galeri Desa' }}"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer Info (Floating at viewport bottom) --}}
        <div class="fixed bottom-0 inset-x-0 p-6 sm:p-8 md:p-12 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent flex flex-col items-center text-center pointer-events-none z-10">
            <span class="text-[10px] md:text-xs font-black uppercase tracking-widest text-primary-400 drop-shadow-md" x-text="currentItem.created_at || ''"></span>
            <h3 id="home-gallery-lightbox-title" class="text-base md:text-2xl font-heading font-black tracking-tight text-white leading-snug line-clamp-2 mt-2 drop-shadow-xl max-w-3xl" x-text="currentItem.title || ''"></h3>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let apexChartsLoaded = false;
    let currentPopChart = null;


    // Data demografi terenkripsi dari server
    const genderData = [
        { label: 'Laki-laki', value: {{ (int)($lakiLakiCount ?? 0) }} },
        { label: 'Perempuan', value: {{ (int)($perempuanCount ?? 0) }} }
    ];

    const jobData = {!! json_encode($jobData->map(fn($item) => ['label' => $item->name ?: 'Tidak Diketahui', 'value' => (int)$item->total])->toArray()) !!};
    const eduData = {!! json_encode($eduData->map(fn($item) => ['label' => $item->name ?: 'Tidak Diketahui', 'value' => (int)$item->total])->toArray()) !!};

    function loadApexCharts(callback) {
        if (window.ApexCharts) {
            callback();
            return;
        }
        if (apexChartsLoaded) return;
        apexChartsLoaded = true;

        const script = document.createElement('script');
        script.src = "https://cdn.jsdelivr.net/npm/apexcharts";
        script.async = true;
        script.onload = callback;
        document.head.appendChild(script);
    }

    function initCharts() {
        loadApexCharts(function() {
            renderDemografiChart('gender');

        });
    }

    // Lazy load charts using IntersectionObserver
    const chartTarget = document.getElementById('populationChart') || document.getElementById('budgetChart');
    if (chartTarget) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        initCharts();
                        obs.disconnect();
                    }
                });
            }, { rootMargin: '200px 0px' });
            observer.observe(chartTarget);
        } else {
            initCharts();
        }
    }

    // Urutkan data descending
    jobData.sort((a, b) => b.value - a.value);
    eduData.sort((a, b) => b.value - a.value);

    function renderDemografiChart(type) {
        const elPop = document.getElementById('populationChart');
        if (!elPop) return;

        if (currentPopChart) {
            currentPopChart.destroy();
            currentPopChart = null;
        }

        const titleEl = document.getElementById('chartCardTitle');
        const subtitleEl = document.getElementById('chartCardSubtitle');

        let series, labels, colors;

        if (type === 'gender') {
            if (titleEl) titleEl.innerText = 'Demografi Penduduk';
            if (subtitleEl) subtitleEl.innerText = 'Perbandingan jumlah laki-laki dan perempuan aktif';

            series = genderData.map(d => d.value);
            labels = genderData.map(d => d.label);
            colors = ['#0ea5e9', '#ec4899'];
        } else {
            const isJob = type === 'job';
            if (titleEl) titleEl.innerText = isJob ? 'Status Pekerjaan' : 'Pendidikan Penduduk';
            if (subtitleEl) subtitleEl.innerText = isJob ? 'Distribusi warga aktif berdasarkan kedudukan pekerjaan' : 'Distribusi warga aktif berdasarkan tingkat pendidikan';

            let rawData = isJob ? jobData : eduData;
            // Limit data untuk efisiensi visual (top 7 + Lainnya)
            let displayData = [...rawData];
            if (displayData.length > 8) {
                const top = displayData.slice(0, 7);
                const rest = displayData.slice(7);
                const othersVal = rest.reduce((sum, item) => sum + item.value, 0);
                if (othersVal > 0) {
                    top.push({ label: 'Lainnya', value: othersVal });
                }
                displayData = top;
            }

            series = displayData.map(d => d.value);
            labels = displayData.map(d => d.label);
            colors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#f43f5e', '#06b6d4', '#14b8a6', '#f97316', '#3b82f6'];
        }

        const isDark = document.documentElement.classList.contains('dark');

        let optionsPop = {
            chart: {
                type: 'donut',
                height: '100%',
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            series: series,
            labels: labels,
            colors: colors,
            stroke: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { show: true, fontFamily: 'Poppins, sans-serif', fontWeight: 700, fontSize: '13px', color: isDark ? '#94a3b8' : '#64748b' },
                            value: {
                                show: true,
                                fontFamily: 'Inter, sans-serif',
                                fontWeight: 800,
                                fontSize: '20px',
                                color: isDark ? '#f8fafc' : '#0f172a',
                                formatter: function(val) { return parseInt(val).toLocaleString('id-ID'); }
                            },
                            total: {
                                show: true,
                                label: 'Total Warga',
                                fontFamily: 'Poppins, sans-serif',
                                fontWeight: 700,
                                fontSize: '10px',
                                color: isDark ? '#64748b' : '#94a3b8',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontFamily: 'Inter, sans-serif',
                fontWeight: 600,
                fontSize: '12px',
                labels: { colors: isDark ? '#cbd5e1' : '#64748b' },
                markers: { width: 9, height: 9, radius: 9, offsetY: -1 }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: function(val) {
                        return val.toLocaleString('id-ID') + ' Jiwa';
                    }
                }
            }
        };

        currentPopChart = new ApexCharts(elPop, optionsPop);
        currentPopChart.render();
    }



    // Listener switch dropdown
    const selectHomeChart = document.getElementById('homeChartType');
    if (selectHomeChart) {
        selectHomeChart.addEventListener('change', function(e) {
            if (window.ApexCharts) {
                renderDemografiChart(e.target.value);
            } else {
                loadApexCharts(function() {
                    renderDemografiChart(e.target.value);
                });
            }
        });
    }

    // Observer untuk update chart jika mode gelap ditoggle
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class' && currentPopChart) {
                const type = selectHomeChart ? selectHomeChart.value : 'gender';
                renderDemografiChart(type);
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
});
</script>
@endpush