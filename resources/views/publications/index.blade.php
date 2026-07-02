@extends('layouts.app')

@section('title', 'Publikasi - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- HERO GELAP --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="relative bg-slate-900 py-24 md:py-36 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-bold uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-400 transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
                        <span class="text-white">Publikasi</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Publikasi <span class="text-emerald-500 italic">Statistik</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Dokumen resmi dan hasil analisis data Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- KONTEN UTAMA --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">

    {{-- ─── Header Statistik ─── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-14">
        <div>
            <div class="flex items-center gap-3 mb-4"><div class="h-px w-8 bg-emerald-500"></div><span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Perpustakaan Digital</span></div>
            <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900">
                Semua Publikasi
            </h2>
            <p class="text-slate-500 font-medium mt-2">
                Tersedia <span class="text-emerald-600 font-bold">{{ $publications->total() }}</span> dokumen publikasi
            </p>
        </div>
        <div class="flex-shrink-0 hidden sm:flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-2xl px-6 py-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white">
                <i class="fa-solid fa-book-open text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-heading font-black text-slate-900">{{ $publications->total() }}</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Total Publikasi</div>
            </div>
        </div>
    </div>

    {{-- ─── Grid 4 Kolom ─── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
        @forelse($publications as $pub)
        <div class="group flex flex-col bg-white rounded-3xl overflow-hidden shadow-md shadow-slate-200/60 border border-slate-100 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-50 transition-all duration-500 hover:-translate-y-2">

            {{-- Cover Image (rasio buku: 3/4) --}}
            <div class="relative aspect-[3/4] bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden flex-shrink-0">
                @if($pub->cover)
                    <img
                        src="{{ asset('storage/' . $pub->cover) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        alt="{{ $pub->title }}"
                        loading="lazy"
                    >
                @else
                    {{-- Placeholder cover --}}
                    <div class="w-full h-full flex flex-col items-center justify-center p-10 text-center bg-gradient-to-br from-slate-100 to-slate-200">
                        <i class="fa-solid fa-book-open text-5xl text-slate-300 mb-4"></i>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Arsip Digital</span>
                    </div>
                @endif

                {{-- Overlay hover --}}
                <div class="absolute inset-0 bg-emerald-700/0 group-hover:bg-emerald-700/15 transition-all duration-500"></div>

                {{-- Badge Tahun --}}
                <div class="absolute top-4 left-4">
                    <span class="bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full shadow-lg tracking-wider">
                        {{ $pub->year }}
                    </span>
                </div>

                {{-- Badge Tipe --}}
                @php
                    $typeLabel = $pub->type ?? ($pub->category ?? 'Publik');
                    $typeColor = match(strtolower($typeLabel)) {
                        'laporan' => 'bg-blue-600',
                        'monografi' => 'bg-purple-600',
                        'perda', 'peraturan' => 'bg-amber-600',
                        'infografis' => 'bg-pink-600',
                        default => 'bg-emerald-600',
                    };
                @endphp
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center {{ $typeColor }} text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-lg">
                        {{ Str::limit($typeLabel, 10) }}
                    </span>
                </div>

                {{-- PDF Icon overlay on hover --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="w-16 h-16 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center shadow-xl text-red-600">
                        <i class="fa-solid fa-file-pdf text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-6 flex flex-col flex-1">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-2 leading-tight">
                    {{ $pub->category ?? 'Dokumen Publik' }}
                </p>
                <h3 class="text-base font-heading font-bold text-slate-900 leading-snug mb-auto line-clamp-3 group-hover:text-emerald-700 transition">
                    {{ $pub->title }}
                </h3>

                {{-- Footer card --}}
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <i class="fa-solid fa-file-pdf text-red-400 text-sm"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">PDF</span>
                    </div>

                    {{-- Tombol Download PDF --}}
                    <a
                        href="{{ asset('storage/' . $pub->file_path) }}"
                        class="group/dl inline-flex items-center gap-2 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-wider px-4 py-2.5 rounded-xl hover:bg-emerald-700 transition-all duration-300 shadow-md shadow-emerald-600/30 hover:shadow-lg hover:shadow-emerald-600/40 hover:-translate-y-0.5"
                        download
                        title="Unduh {{ $pub->title }}"
                    >
                        <i class="fa-solid fa-download group-hover/dl:animate-bounce"></i>
                        <span>Unduh PDF</span>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-40 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
            <i class="fa-solid fa-book-open text-5xl text-slate-300 mb-6 block"></i>
            <p class="text-slate-400 font-bold italic">Dokumen belum tersedia.</p>
        </div>
        @endforelse
    </div>

    {{-- ─── Paginasi ─── --}}
    @if($publications->hasPages())
    <div class="mt-16">
        {{ $publications->links() }}
    </div>
    @endif
</div>
@endsection
