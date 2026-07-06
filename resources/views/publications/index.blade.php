@extends('layouts.app')

@section('title', 'Publikasi Data | Desa ' . ($site_settings['village_name'] ?? 'Tompobulu'))
@section('meta_description', 'Katalog berkas publikasi resmi, laporan berkala, dan dokumen infografis dari Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
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
                    <span class="text-white">Publikasi</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

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
                        onerror="this.onerror=null;this.src='{{ asset('img/meta.png') }}'"
                    >
                @else
                    <img
                        src="{{ asset('img/meta.png') }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        alt="{{ $pub->title }}"
                        loading="lazy"
                    >
                @endif

                {{-- Overlay hover --}}
                <div class="absolute inset-0 bg-emerald-700/0 group-hover:bg-emerald-700/15 transition-all duration-500"></div>

                @php
                    $typeLabel = $pub->type ?? 'Publik';
                    $typeColor = match(strtolower($typeLabel)) {
                        'laporan' => 'bg-blue-600',
                        'monografi' => 'bg-purple-600',
                        'perda', 'peraturan' => 'bg-amber-600',
                        'infografis' => 'bg-pink-600',
                        default => 'bg-emerald-600',
                    };
                @endphp

                {{-- PDF Icon overlay on hover --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="w-16 h-16 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center shadow-xl text-red-600">
                        <i class="fa-solid fa-file-pdf text-2xl"></i>
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-6 flex flex-col flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="bg-slate-100 text-slate-700 text-[9px] font-bold px-2.5 py-1 rounded-full tracking-wider">
                        {{ $pub->year }}
                    </span>
                    <span class="inline-flex items-center {{ $typeColor }} text-white text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full">
                        {{ $typeLabel }}
                    </span>
                    @if($pub->category)
                        <span class="bg-emerald-50 text-emerald-700 text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full border border-emerald-100">
                            {{ $pub->category }}
                        </span>
                    @endif
                </div>
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
