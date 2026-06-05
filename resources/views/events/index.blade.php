@extends('layouts.app')

@section('title', 'Kegiatan - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <x-heroicon-s-chevron-right class="w-4 h-4 mx-1" />
                        <span class="text-white">Kegiatan</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Agenda <span class="text-emerald-500 italic">Kegiatan</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Jadwal program kerja and aktivitas kemasyarakatan di Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
        @forelse($events as $event)
        <article class="bg-white rounded-[40px] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden group hover:border-emerald-200 transition-all duration-500">
            <div class="aspect-[16/9] overflow-hidden relative">
                @if($event->featured_image)
                    <img src="{{ asset('storage/' . $event->featured_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="{{ $event->title }}">
                @else
                    <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200">
                        <i class="fa-solid fa-calendar-check text-6xl opacity-20"></i>
                    </div>
                @endif
                <div class="absolute top-6 left-6">
                    <span class="bg-white/90 backdrop-blur-md text-slate-900 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl">
                        {{ $event->start_at->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
            <div class="p-8 md:p-10">
                <h2 class="text-2xl font-heading font-bold text-slate-900 mb-6 group-hover:text-emerald-600 transition">{{ $event->title }}</h2>
                <div class="space-y-4 mb-8">
                    <div class="flex items-center gap-3 text-slate-500 text-sm font-medium">
                        <i class="fa-solid fa-clock text-emerald-500 w-5 text-center"></i>
                        <span>{{ $event->start_at->format('H:i') }} - {{ $event->end_at ? $event->end_at->format('H:i') : 'Selesai' }} WITA</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-500 text-sm font-medium">
                        <i class="fa-solid fa-location-dot text-emerald-500 w-5 text-center"></i>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
                <div class="text-slate-500 text-sm leading-relaxed line-clamp-3 font-medium mb-8">
                    {{ strip_tags($event->content) }}
                </div>
                <button class="text-emerald-600 font-bold text-xs uppercase tracking-widest hover:gap-4 flex items-center gap-2 transition-all group">
                    Detail Kegiatan <i class="fa-solid fa-arrow-right-long opacity-50 group-hover:opacity-100"></i>
                </button>
            </div>
        </article>
        @empty
        <div class="col-span-full py-32 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
            <p class="text-slate-400 font-bold italic">Belum ada agenda kegiatan saat ini.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-20">
        {{ $events->links() }}
    </div>
</div>
@endsection
