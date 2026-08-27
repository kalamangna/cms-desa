@extends('layouts.app')

@section('title', 'Galeri Kegiatan | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Galeri dokumentasi kegiatan pembangunan, pembinaan kemasyarakatan, dan aktivitas kedinasan Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@section('content')
{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- HERO SECTION --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="relative bg-slate-900 dark:bg-slate-950 py-16 md:py-24 lg:py-28 overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-500"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl transition-colors duration-500"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 dark:bg-primary-600/5 rounded-full blur-3xl transition-colors duration-500"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Galeri</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Galeri <span class="text-primary-500 italic">Kegiatan</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                Dokumentasi kegiatan, pembangunan, dan momen penting Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
@php
    $galleryItems = $galleries->map(function($g) {
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

<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20"
    x-data="{
        activeFilter: 'semua',
        filtersWithData: {{ json_encode($galleries->map(fn($g) => $g->type === 'video' ? 'video' : 'photo')->unique()->values()->toArray()) }},
        galleryItems: @js($galleryItems),
        currentIndex: 0,
        lightboxOpen: false,
        touchStartX: 0,
        touchEndX: 0,
        get filteredItems() {
            if (this.activeFilter === 'semua') return this.galleryItems;
            return this.galleryItems.filter(item => item.type === this.activeFilter);
        },
        get currentItem() {
            return this.filteredItems[this.currentIndex] || {};
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
            if (this.filteredItems.length === 0) return;
            this.currentIndex = (this.currentIndex + 1) % this.filteredItems.length;
        },
        prevSlide() {
            if (this.filteredItems.length === 0) return;
            this.currentIndex = (this.currentIndex - 1 + this.filteredItems.length) % this.filteredItems.length;
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
        },
        getYoutubeEmbed(url) {
            if (!url) return '';
            const match = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/);
            return (match && match[2].length === 11) ? 'https://www.youtube-nocookie.com/embed/' + match[2] : '';
        }
    }"
>
    {{-- ─── Filter Bar ─── --}}
    <div class="flex items-center justify-center gap-3 mb-12 flex-wrap">
        <button
            @click="activeFilter = 'semua'"
            :class="activeFilter === 'semua'
                ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20 border-transparent'
                : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border-slate-200 dark:border-slate-700 shadow-xs'"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-h-11 rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950 cursor-pointer"
        >
            <i class="fa-solid fa-border-all text-xs"></i>
            Semua
        </button>
        <button
            @click="activeFilter = 'photo'"
            :class="activeFilter === 'photo'
                ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20 border-transparent'
                : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border-slate-200 dark:border-slate-700 shadow-xs'"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-h-11 rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950 cursor-pointer"
        >
            <i class="fa-solid fa-camera text-xs"></i>
            Foto
        </button>
        <button
            @click="activeFilter = 'video'"
            :class="activeFilter === 'video'
                ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20 border-transparent'
                : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border-slate-200 dark:border-slate-700 shadow-xs'"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-h-11 rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950 cursor-pointer"
        >
            <i class="fa-brands fa-youtube text-xs"></i>
            Video
        </button>
    </div>

    {{-- ─── Grid Galeri Rapi (Aspect Ratio 16:10) ─── --}}
    @if($galleries->isEmpty())
        <x-empty-state
            icon="fa-solid fa-images"
            title="Galeri Foto & Video Belum Tersedia"
            description="Belum ada foto atau video kegiatan yang diunggah."
        />
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach($galleries as $index => $item)
            <div class="group relative bg-slate-900 rounded-2xl md:rounded-3xl overflow-hidden shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 aspect-video cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-slate-300/60 dark:hover:shadow-slate-950/80"
                x-show="activeFilter === 'semua' || activeFilter === '{{ $item->type === 'video' ? 'video' : 'photo' }}'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click="openLightboxByIndex(filteredItems.findIndex(i => i.id === {{ $item->id }}))"
            >
                {{-- Foto Full Background --}}
                <img
                    src="{{ $item->image_url ? $item->image_url : asset('img/meta.webp') }}"
                    class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500"
                    alt="{{ $item->title }}"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'"
                >

                {{-- Tombol Play Video --}}
                @if($item->type === 'video')
                    <div class="absolute inset-0 bg-slate-950/30 flex items-center justify-center group-hover:opacity-0 transition-opacity duration-300 pointer-events-none">
                        <div class="w-14 h-14 rounded-full bg-red-600 flex items-center justify-center shadow-xl shadow-red-950/40 text-white text-lg">
                            <i class="fa-solid fa-play ml-1"></i>
                        </div>
                    </div>
                @endif

                {{-- Hover Overlay Gradient & Info --}}
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/60 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-6 md:p-7 text-white">
                    <span class="text-[10px] font-black uppercase tracking-widest text-primary-400 mb-1">
                        {{ $item->created_at->translatedFormat('d M Y') }}
                    </span>
                    <h3 class="text-base md:text-lg font-heading font-extrabold text-white leading-snug line-clamp-2">
                        {{ $item->title }}
                    </h3>
                    @if($item->description)
                        <p class="text-slate-300 text-xs leading-relaxed line-clamp-2 font-medium mt-1.5">{{ $item->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

         <!-- Empty category state -->
         <div x-show="activeFilter !== 'semua' && !filtersWithData.includes(activeFilter)"
              class="col-span-full py-8"
              x-cloak>
             <x-empty-state
                 icon="fa-solid fa-images"
                 title="Tidak Ada di Kategori Ini"
                 description="Belum ada foto atau video untuk kategori filter yang dipilih."
             />
         </div>
    @endif

    {{-- ─── Paginasi ─── --}}
    @if($galleries->hasPages())
        <div class="mt-14 md:mt-16 flex justify-center">
            {{ $galleries->links() }}
        </div>
    @endif

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
        role="dialog" aria-modal="true" aria-labelledby="gallery-lightbox-title"
    >
        {{-- Counter Slide (Di Luar Modal, Kiri Atas Layar) --}}
        <template x-if="filteredItems.length > 1">
            <div class="fixed top-4 left-4 sm:top-6 sm:left-6 md:top-8 md:left-8 z-50 bg-slate-900/80 backdrop-blur-md border border-white/20 text-white text-[11px] sm:text-xs font-black uppercase tracking-wider px-3 py-1.5 sm:px-4 sm:py-2 rounded-full shadow-2xl pointer-events-none">
                <span x-text="(currentIndex + 1) + ' / ' + filteredItems.length"></span>
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
        <template x-if="filteredItems.length > 1">
            <button type="button" @click.stop="prevSlide()" 
                    class="fixed left-3 sm:left-6 md:left-8 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition-all duration-200 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    title="Sebelumnya (Tombol Panah Kiri)">
                <i class="fa-solid fa-chevron-left text-xs sm:text-sm md:text-base"></i>
            </button>
        </template>

        {{-- Tombol Navigasi Panah Kanan (Di Luar Modal, Kanan Layar) --}}
        <template x-if="filteredItems.length > 1">
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
                <template x-for="(item, index) in filteredItems" :key="item.id">
                    <div class="w-full h-full flex-shrink-0 min-w-full flex items-center justify-center px-12 pt-16 pb-20 sm:px-20 sm:pt-20 sm:pb-28 md:px-24 md:pt-20 md:pb-32 relative">
                        <template x-if="item.type === 'video'">
                            <div class="w-full max-w-5xl aspect-video bg-black relative overflow-hidden rounded-2xl shadow-2xl">
                                <template x-if="lightboxOpen && currentIndex === index">
                                    <iframe
                                        class="w-full h-full"
                                        :src="item.youtube_embed"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                    ></iframe>
                                </template>
                            </div>
                        </template>
                        <template x-if="item.type !== 'video'">
                            <img :src="item.image_url" 
                                 class="max-w-full max-h-full w-auto h-auto object-contain rounded-2xl shadow-2xl transition-all duration-300 select-none"
                                 :alt="item.title || 'Galeri Desa'"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- Footer Info (Floating at viewport bottom) --}}
        <div class="fixed bottom-0 inset-x-0 p-6 sm:p-8 md:p-12 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent flex flex-col items-center text-center pointer-events-none z-10">
            <span class="text-[10px] md:text-xs font-black uppercase tracking-widest text-primary-400 drop-shadow-md" x-text="currentItem.created_at || ''"></span>
            <h3 id="gallery-lightbox-title" class="text-base md:text-2xl font-heading font-black tracking-tight text-white leading-snug line-clamp-2 mt-2 drop-shadow-xl max-w-3xl" x-text="currentItem.title || ''"></h3>
        </div>
    </div>
</div>
@endsection
