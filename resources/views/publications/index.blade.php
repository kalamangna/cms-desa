@extends('layouts.app')

@section('title', 'Publikasi - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <span class="text-white">Publikasi</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Publikasi <span class="text-emerald-500 italic">Statistik</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Dokumen resmi dan hasil analisis data Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12">
        @forelse($publications as $pub)
        <div class="group">
            <div class="relative aspect-[3/4] bg-slate-200 rounded-[40px] overflow-hidden shadow-2xl shadow-slate-200/50 group-hover:-translate-y-4 transition-transform duration-700 mb-10">
                @if($pub->cover)
                    <img src="{{ asset('storage/' . $pub->cover) }}" class="w-full h-full object-cover" alt="{{ $pub->title }}">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center p-12 text-center bg-gradient-to-br from-slate-100 to-slate-200">
                        <x-heroicon-o-book-open class="w-20 h-20 text-slate-300 mb-8" />
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Arsip Digital</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-emerald-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="absolute top-8 right-8">
                    <span class="bg-white/90 backdrop-blur-md text-slate-900 text-[10px] font-black px-4 py-2 rounded-full shadow-lg">
                        {{ $pub->year }}
                    </span>
                </div>
            </div>
            
            <div class="px-2">
                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-3 block">{{ $pub->category ?? 'Dokumen Publik' }}</span>
                <h3 class="text-xl font-heading font-bold text-slate-900 leading-tight mb-6 line-clamp-2 h-14 group-hover:text-emerald-600 transition">
                    {{ $pub->title }}
                </h3>
                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PDF Document</span>
                    <a href="{{ asset('storage/' . $pub->file_path) }}" class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-emerald-600 hover:bg-emerald-600 hover:text-white transition shadow-sm" download>
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-40 text-center">
            <p class="text-slate-400 font-bold italic">Dokumen belum tersedia.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
