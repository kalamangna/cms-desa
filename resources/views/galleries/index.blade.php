@extends('layouts.app')

@section('title', 'Galeri | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Galeri dokumentasi kegiatan pembangunan, pembinaan kemasyarakatan, dan aktivitas kedinasan Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

@section('content')
{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- HERO GELAP --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
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
                <li>
                    <a href="/" class="hover:text-emerald-400 transition-colors duration-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500/40"></i>
                    <span class="text-white">Galeri</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Galeri <span class="text-emerald-500 italic">Kegiatan</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Dokumentasi momen penting Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- KONTEN UTAMA --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28"
    x-data="{
        activeFilter: 'semua',
        filtersWithData: {{ json_encode($galleries->map(fn($g) => $g->type === 'video' ? 'video' : 'photo')->unique()->values()->toArray()) }},
        lightboxOpen: false,
        lightboxImage: '',
        lightboxTitle: '',
        lightboxVideo: '',
        getYoutubeEmbed(url) {
            if (!url) return '';
            const match = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/);
            return (match && match[2].length === 11) ? 'https://www.youtube.com/embed/' + match[2] : '';
        }
    }"
>
    {{-- ─── Filter Bar ─── --}}
    <div class="flex items-center justify-center gap-3 mb-14 flex-wrap">
        <button
            @click="activeFilter = 'semua'"
            :class="activeFilter === 'semua'
                ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/20'
                : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100 shadow-sm'"
            class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300 cursor-pointer"
        >
            <i class="fa-solid fa-border-all text-xs"></i>
            Semua
        </button>
        <button
            @click="activeFilter = 'photo'"
            :class="activeFilter === 'photo'
                ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/30'
                : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100 shadow-sm'"
            class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300 cursor-pointer"
        >
            <i class="fa-solid fa-camera text-xs"></i>
            Foto
        </button>
        <button
            @click="activeFilter = 'video'"
            :class="activeFilter === 'video'
                ? 'bg-red-600 text-white shadow-xl shadow-red-600/30'
                : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-100 shadow-sm'"
            class="inline-flex items-center gap-2 px-7 py-3 rounded-full text-sm font-bold uppercase tracking-wider transition-all duration-300 cursor-pointer"
        >
            <i class="fa-brands fa-youtube text-xs"></i>
            Video
        </button>
    </div>

    {{-- ─── Masonry Grid ─── --}}
    @if($galleries->isEmpty())
        <div class="text-center py-16">
            <i class="fa-solid fa-images text-slate-300 text-3xl mb-3 block"></i>
            <h3 class="text-slate-400 font-bold text-sm">Belum Ada Dokumentasi</h3>
        </div>
    @else
        <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 md:gap-8 space-y-6 md:space-y-8">
            @foreach($galleries as $item)
            <div
                class="break-inside-avoid group relative bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:border-emerald-200"
                x-show="activeFilter === 'semua' || activeFilter === '{{ $item->type === 'video' ? 'video' : 'photo' }}'"
                x-transition:enter="transition ease-out duration-400"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                {{-- Thumbnail --}}
                <div class="relative overflow-hidden">
                    <img
                        src="{{ $item->image_url ? $item->image_url : asset('img/meta.png') }}"
                        class="w-full object-cover group-hover:scale-105 transition duration-700"
                        alt="{{ $item->title }}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('img/meta.png') }}'"
                    >


                    {{-- Badge: Video (merah YouTube) --}}
                    @if($item->type === 'video')
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center gap-1.5 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg shadow-red-600/40">
                                <i class="fa-brands fa-youtube text-xs"></i>
                                YouTube
                            </span>
                        </div>
                        <div class="absolute inset-0 bg-slate-950/30 flex items-center justify-center group-hover:bg-slate-950/10 transition duration-300">
                            <div class="w-14 h-14 rounded-full bg-red-600 flex items-center justify-center shadow-xl shadow-red-950/40 text-white text-lg group-hover:scale-110 transition duration-300">
                                <i class="fa-solid fa-play ml-1"></i>
                            </div>
                        </div>
                    @else
                        {{-- Overlay hover untuk foto --}}
                        <div class="absolute inset-0 bg-emerald-900/0 group-hover:bg-emerald-900/20 transition duration-500"></div>
                    @endif
                </div>

                {{-- Card Body --}}
                <div class="p-6 md:p-8">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="text-base md:text-lg font-heading font-bold text-slate-900 group-hover:text-emerald-600 transition leading-tight line-clamp-2">
                            {{ $item->title }}
                        </h3>
                    </div>
                    @if($item->description)
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 font-medium mb-4">{{ $item->description }}</p>
                    @endif
                    <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                            {{ $item->created_at->translatedFormat('d M Y') }}
                        </span>
                        <button
                            @click="lightboxOpen = true; lightboxImage = '{{ $item->image_url }}'; lightboxTitle = '{{ addslashes($item->title) }}'; lightboxVideo = '{{ $item->type === 'video' ? $item->youtube_url : '' }}'"
                            class="inline-flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-widest hover:text-emerald-700 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-expand text-xs"></i>
                            Perbesar
                        </button>
                    </div>
                </div>

                {{-- Hover glow --}}
                <div class="absolute inset-0 bg-emerald-600/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none rounded-3xl"></div>
            </div>
            @endforeach
        </div>

         <!-- Empty category state -->
         <div x-show="activeFilter !== 'semua' && !filtersWithData.includes(activeFilter)"
              class="col-span-full text-center py-16"
              x-cloak>
             <i class="fa-solid fa-images text-slate-300 text-3xl mb-3 block"></i>
             <h3 class="text-slate-400 font-bold text-sm">Belum Ada Dokumentasi</h3>
         </div>
    @endif

    {{-- ─── Paginasi ─── --}}
    @if($galleries->hasPages())
        <div class="mt-20">
            {{ $galleries->links() }}
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- LIGHTBOX MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="lightboxOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/95 backdrop-blur-sm"
        x-cloak
        @keydown.escape.window="lightboxOpen = false; lightboxVideo = ''"
    >
        {{-- Tombol tutup --}}
        <button
            @click="lightboxOpen = false; lightboxVideo = ''"
            class="absolute top-6 right-6 md:top-10 md:right-10 text-white/50 hover:text-white focus:outline-none transition z-50 cursor-pointer"
        >
            <i class="fa-solid fa-xmark text-4xl"></i>
        </button>

        <div
            @click.away="lightboxOpen = false; lightboxVideo = ''"
            class="relative max-w-4xl w-full mx-4 flex flex-col items-center justify-center"
            x-show="lightboxOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <template x-if="lightboxVideo">
                <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/10">
                    <iframe
                        class="w-full h-full"
                        :src="getYoutubeEmbed(lightboxVideo)"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            </template>
            <template x-if="!lightboxVideo">
                <img :src="lightboxImage" :alt="lightboxTitle" class="w-full h-auto max-h-[85vh] object-contain rounded-xl shadow-2xl">
            </template>

            <h3 x-text="lightboxTitle" class="text-white text-center mt-6 font-heading font-bold text-xl md:text-2xl"></h3>
        </div>
    </div>
</div>
@endsection
