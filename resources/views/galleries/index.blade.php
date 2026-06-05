@extends('layouts.app')

@section('title', 'Galeri Kegiatan - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <span class="text-white">Galeri</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Dokumentasi <span class="text-emerald-500 italic">Kegiatan</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Kumpulan momen dan visualisasi pembangunan serta kegiatan kemasyarakatan di Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
        @forelse($galleries as $item)
        <div class="group relative bg-white rounded-[40px] overflow-hidden shadow-2xl shadow-slate-200/50 border border-slate-100 transition-all duration-500 hover:-translate-y-4">
            <div class="aspect-[4/3] overflow-hidden">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="{{ $item->title }}">
                @else
                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                        <i class="fa-solid fa-image text-5xl opacity-20"></i>
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
                    <button class="text-emerald-600 font-bold text-xs uppercase tracking-widest hover:text-emerald-700 transition">Perbesar</button>
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
</div>
@endsection
