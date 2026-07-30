@extends('layouts.app')

@section('title', 'Galeri | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Galeri dokumentasi kegiatan pembangunan, pembinaan kemasyarakatan, dan aktivitas kedinasan Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@section('content')
{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- HERO SECTION --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-primary-500/60" aria-label="Breadcrumb">
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
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
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
    $galleryItems = $galleries->map(fn($g) => [
        'id' => $g->id,
        'type' => $g->type === 'video' ? 'video' : 'photo',
        'image_url' => $g->image_url ? $g->image_url : asset('img/meta.webp'),
        'title' => $g->title,
        'youtube_url' => $g->type === 'video' ? $g->youtube_url : '',
        'created_at' => $g->created_at->translatedFormat('d M Y')
    ])->values()->toArray();
@endphp

<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20"
    x-data="{
        activeFilter: 'semua',
        filtersWithData: {{ json_encode($galleries->map(fn($g) => $g->type === 'video' ? 'video' : 'photo')->unique()->values()->toArray()) }},
        galleryItems: @js($galleryItems),
        currentIndex: 0,
        lightboxOpen: false,
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
            document.body.classList.add('sotk-modal-open');
        },
        closeLightbox() {
            this.lightboxOpen = false;
            document.body.classList.remove('sotk-modal-open');
        },
        nextSlide() {
            if (this.filteredItems.length === 0) return;
            this.currentIndex = (this.currentIndex + 1) % this.filteredItems.length;
        },
        prevSlide() {
            if (this.filteredItems.length === 0) return;
            this.currentIndex = (this.currentIndex - 1 + this.filteredItems.length) % this.filteredItems.length;
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
                : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200 shadow-xs'"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-h-[44px] rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 cursor-pointer"
        >
            <i class="fa-solid fa-border-all text-xs"></i>
            Semua
        </button>
        <button
            @click="activeFilter = 'photo'"
            :class="activeFilter === 'photo'
                ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20 border-transparent'
                : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200 shadow-xs'"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-h-[44px] rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 cursor-pointer"
        >
            <i class="fa-solid fa-camera text-xs"></i>
            Foto
        </button>
        <button
            @click="activeFilter = 'video'"
            :class="activeFilter === 'video'
                ? 'bg-primary-600 text-white shadow-md shadow-primary-600/20 border-transparent'
                : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200 shadow-xs'"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 min-h-[44px] rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 cursor-pointer"
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
            <div
                class="group relative bg-slate-900 rounded-2xl md:rounded-3xl overflow-hidden shadow-sm aspect-[16/10] cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
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
        @keydown.arrow-left.window="prevSlide()"
        @keydown.arrow-right.window="nextSlide()"
        @click="closeLightbox()"
        class="fixed inset-0 z-[9999] bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 md:p-10 cursor-pointer select-none"
        role="dialog" aria-modal="true" aria-labelledby="gallery-lightbox-title"
    >
        {{-- Counter Slide (Di Luar Modal, Kiri Atas Layar) --}}
        <template x-if="filteredItems.length > 1">
            <div class="fixed top-5 left-5 sm:top-8 sm:left-8 z-50 bg-slate-900/80 backdrop-blur-md border border-white/20 text-white text-xs font-black uppercase tracking-wider px-4 py-2 rounded-full shadow-2xl">
                <span x-text="(currentIndex + 1) + ' / ' + filteredItems.length"></span>
            </div>
        </template>

        {{-- Tombol Tutup (Di Luar Modal, Kanan Atas Layar) --}}
        <button
            type="button"
            @click.stop="closeLightbox()"
            class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110 focus:outline-none focus:ring-2 focus:ring-primary-500"
            title="Tutup (Esc)"
        >
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        {{-- Tombol Navigasi Panah Kiri (Di Luar Modal, Kiri Layar) --}}
        <template x-if="filteredItems.length > 1">
            <button type="button" @click.stop="prevSlide()" 
                    class="fixed left-2 sm:left-6 md:left-10 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition duration-300 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    title="Sebelumnya (Tombol Panah Kiri)">
                <i class="fa-solid fa-chevron-left text-base sm:text-lg"></i>
            </button>
        </template>

        {{-- Tombol Navigasi Panah Kanan (Di Luar Modal, Kanan Layar) --}}
        <template x-if="filteredItems.length > 1">
            <button type="button" @click.stop="nextSlide()" 
                    class="fixed right-2 sm:right-6 md:right-10 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-14 sm:h-14 rounded-full bg-slate-900/60 sm:bg-slate-900/80 hover:bg-primary-600 text-white flex items-center justify-center transition duration-300 z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    title="Selanjutnya (Tombol Panah Kanan)">
                <i class="fa-solid fa-chevron-right text-base sm:text-lg"></i>
            </button>
        </template>

        {{-- Container Modal Konten --}}
        <div
            class="relative bg-slate-900 rounded-[28px] w-[92vw] sm:w-auto sm:max-w-[85vw] md:max-w-[75vw] flex flex-col overflow-hidden border border-slate-700/80 shadow-2xl cursor-default"
            @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        >
            {{-- Slider Content --}}
            <div class="relative overflow-hidden flex items-center justify-center bg-slate-950 min-h-[200px] w-full">
                <template x-if="lightboxOpen && currentItem.type === 'video'">
                    <div class="w-full sm:w-[80vw] max-w-4xl aspect-video bg-black relative overflow-hidden">
                        <iframe
                            class="w-full h-full"
                            :src="getYoutubeEmbed(currentItem.youtube_url)"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                </template>
                <template x-if="currentItem.type !== 'video'">
                    <div class="relative overflow-hidden flex items-center justify-center bg-slate-950 w-full">
                        <img :src="currentItem.image_url" :alt="currentItem.title" class="w-full sm:w-auto h-auto max-h-[65vh] sm:max-h-[75vh] object-contain transition-all duration-300">
                    </div>
                </template>
            </div>

            {{-- Footer Info --}}
            <div class="p-5 bg-white border-t border-slate-100 flex items-center justify-between gap-4 relative z-10">
                <div class="flex flex-col text-left min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider text-primary-600" x-text="currentItem.created_at || ''"></span>
                    <h3 id="gallery-lightbox-title" class="text-base md:text-xl font-heading font-extrabold text-slate-800 leading-snug line-clamp-1 mt-0.5" x-text="currentItem.title || ''"></h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
