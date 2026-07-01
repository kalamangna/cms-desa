@extends('layouts.app')

@section('title', 'Galeri - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
<!-- Standardized Dark Hero -->
<div class="relative bg-slate-900 py-20 md:py-32 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-sm font-bold uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-400 transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
                        <span class="text-white">Galeri</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Galeri <span class="text-emerald-500 italic">Kegiatan</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Dokumentasi momen penting Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32" x-data="{ 
    lightboxOpen: false, 
    lightboxImage: '', 
    lightboxTitle: '', 
    lightboxVideo: '',
    getYoutubeEmbed(url) {
        if (!url) return '';
        const match = url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
        return (match && match[2].length === 11) ? 'https://www.youtube.com/embed/' + match[2] : '';
    }
}">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
        @forelse($galleries as $item)
        <div class="group relative bg-white rounded-[40px] overflow-hidden shadow-2xl shadow-slate-200/50 border border-slate-100 transition-all duration-500 hover:-translate-y-4">
            <div class="relative aspect-[4/3] overflow-hidden">
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="{{ $item->title }}">
                @else
                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                        <i class="fa-solid fa-image text-5xl opacity-20"></i>
                    </div>
                @endif

                @if($item->type === 'video')
                    <div class="absolute inset-0 bg-slate-950/40 flex items-center justify-center text-white group-hover:bg-slate-950/20 transition duration-300">
                        <div class="w-16 h-16 rounded-full bg-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-950/40 text-xl group-hover:scale-110 transition duration-300">
                            <i class="fa-solid fa-play ml-1"></i>
                        </div>
                    </div>
                @endif
            </div>
            <div class="p-8 md:p-10">
                <h3 class="text-xl font-heading font-bold text-slate-900 mb-4 group-hover:text-emerald-600 transition">{{ $item->title }}</h3>
                @if($item->description)
                    <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 font-medium">{{ $item->description }}</p>
                @endif
                <div class="mt-8 pt-6 border-t border-slate-50 flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $item->created_at->translatedFormat('d M Y') }}</span>
                    <button @click="lightboxOpen = true; lightboxImage = '{{ $item->image_url }}'; lightboxTitle = '{{ $item->title }}'; lightboxVideo = '{{ $item->type === 'video' ? $item->youtube_url : '' }}'" class="text-emerald-600 font-bold text-xs uppercase tracking-widest hover:text-emerald-700 transition">Perbesar</button>
                </div>
            </div>
            <div class="absolute inset-0 bg-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
        </div>
        @empty
        <div class="col-span-full py-32 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
            <p class="text-slate-400 font-bold italic">Belum ada foto di galeri saat ini.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-20">
        {{ $galleries->links() }}
    </div>

    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/95 backdrop-blur-sm" x-cloak>
        <button @click="lightboxOpen = false; lightboxVideo = ''" class="absolute top-6 right-6 md:top-10 md:right-10 text-white/50 hover:text-white focus:outline-none transition z-50">
            <i class="fa-solid fa-xmark text-4xl"></i>
        </button>
        <div @click.away="lightboxOpen = false; lightboxVideo = ''" class="relative max-w-4xl w-full mx-4 flex flex-col items-center justify-center"
             x-show="lightboxOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <template x-if="lightboxVideo">
                <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/10">
                    <iframe class="w-full h-full" :src="getYoutubeEmbed(lightboxVideo)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
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
