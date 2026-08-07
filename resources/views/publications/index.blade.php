@extends('layouts.app')

@section('title', 'Publikasi Data | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Katalog berkas publikasi resmi, laporan berkala, dan dokumen infografis dari Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@section('content')
{{-- ═══════════════════════════════════════════════════════
     DARK HERO
═══════════════════════════════════════════════════════ --}}
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
                    <span class="text-white">Publikasi</span>
                </li>
            </ol>
        </nav>

        {{-- Heading --}}
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Publikasi <span class="text-primary-500 italic">Statistik</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                Dokumen resmi dan hasil analisis data Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

        {{-- ─── Header Statistik ─── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Semua Publikasi</h2>
                <p class="text-slate-600 dark:text-slate-300 text-sm font-medium">
                    Tersedia <span class="text-primary-700 dark:text-primary-400 font-extrabold">{{ $publications->total() }}</span> dokumen publikasi publik.
                </p>
            </div>
            <div class="flex-shrink-0 hidden sm:flex items-center gap-3.5 bg-primary-50 dark:bg-primary-950/40 border border-primary-100/80 dark:border-primary-900/50 rounded-2xl px-6 py-3.5 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-xs">
                    <i class="fa-solid fa-book-open text-sm"></i>
                </div>
                <div>
                    <div class="text-2xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">{{ $publications->total() }}</div>
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Total Publikasi</div>
                </div>
            </div>
        </div>

        {{-- ─── Grid 4 Kolom ─── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            @forelse($publications as $pub)
            <div class="flex flex-col bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800">

                {{-- Cover Image (rasio buku: 3/4) --}}
                <div class="relative aspect-[3/4] bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 overflow-hidden flex-shrink-0">
                    @if($pub->cover)
                        <img
                            src="{{ asset('storage/' . $pub->cover) }}"
                            class="w-full h-full object-cover"
                            alt="{{ $pub->title }}"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'"
                        >
                    @else
                        <img
                            src="{{ asset('img/meta.webp') }}"
                            class="w-full h-full object-cover"
                            alt="{{ $pub->title }}"
                            loading="lazy"
                        >
                    @endif
                </div>

                {{-- Card Body --}}
                <div class="p-6 flex flex-col flex-1">
                    @php
                        $typeLabel = $pub->type ?? 'Publik';
                        $typeColor = match(strtolower($typeLabel)) {
                            'laporan' => 'bg-blue-600',
                            'monografi' => 'bg-purple-600',
                            'perda', 'peraturan' => 'bg-amber-600',
                            'infografis' => 'bg-pink-600',
                            default => 'bg-primary-600',
                        };
                    @endphp
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[9px] font-black px-2.5 py-1 rounded-full tracking-wider uppercase border border-slate-200 dark:border-slate-700">
                            {{ $pub->year }}
                        </span>
                        <span class="inline-flex items-center {{ $typeColor }} text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full shadow-xs">
                            {{ $typeLabel }}
                        </span>
                        @if($pub->category)
                            <span class="bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border border-primary-100 dark:border-primary-900/50">
                                {{ $pub->category }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 leading-snug mb-auto line-clamp-3">
                        {{ $pub->title }}
                    </h3>

                    {{-- Footer card --}}
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-1.5 text-slate-400 dark:text-slate-500">
                            <i class="fa-solid fa-file-pdf text-rose-500 text-sm"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">PDF</span>
                        </div>

                        {{-- Tombol Download PDF --}}
                        <a
                            href="{{ asset('storage/' . $pub->pdf_file) }}"
                            class="group/dl inline-flex items-center gap-2 bg-red-600 text-white text-[11px] font-black uppercase tracking-wider px-4 py-2.5 rounded-xl hover:bg-red-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer"
                            download
                            title="Unduh {{ $pub->title }}"
                        >
                            <i class="fa-solid fa-download text-[11px]"></i>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-8">
                <x-empty-state
                    icon="fa-solid fa-book-open"
                    title="Belum Ada Dokumen Publikasi"
                    description="Belum ada dokumen publikasi yang tersedia."
                />
            </div>
            @endforelse
        </div>

        {{-- ─── Paginasi ─── --}}
        @if($publications->hasPages())
        <div class="mt-12 md:mt-16">
            {{ $publications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
