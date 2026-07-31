@extends('layouts.app')

@section('title', 'Dokumen | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Pusat arsip dokumen hukum, peraturan desa, keputusan kepala desa, dan berkas administrasi resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
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
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Dokumen</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Arsip <span class="text-primary-500 italic">Dokumen</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                Unduh regulasi, keputusan, dan berkas resmi Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- KONTEN UTAMA --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

        {{-- ─── Header Statistik ─── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-10 md:mb-12">
            <div>
                <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 mb-2">Semua Dokumen Publik</h2>
                <p class="text-slate-600 text-sm">
                    Ditemukan <span class="text-primary-700 font-bold">{{ $documents->total() }}</span> berkas dokumen tersedia.
                </p>
            </div>
            {{-- Stat Badge --}}
            <div class="flex-shrink-0 hidden sm:flex items-center gap-3.5 bg-white border border-slate-200/80 rounded-2xl px-6 py-4 shadow-lg shadow-slate-200/50">
                <div class="w-11 h-11 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-xs">
                    <i class="fa-solid fa-folder-open text-base"></i>
                </div>
                <div>
                    <div class="text-2xl font-heading font-black text-slate-900 leading-none mb-0.5">{{ $documents->total() }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Dokumen</div>
                </div>
            </div>
        </div>

        {{-- ─── List Card ─── --}}
        @forelse($documents as $doc)
        @php
            $ext = $doc->file ? strtolower(pathinfo($doc->file, PATHINFO_EXTENSION)) : 'file';
            $iconMap = [
                'pdf'  => ['icon' => 'fa-file-pdf',  'bg' => 'bg-red-50',    'text' => 'text-red-600',    'badge' => 'bg-red-100/80 text-red-800 border-red-200/60'],
                'doc'  => ['icon' => 'fa-file-word',  'bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'badge' => 'bg-blue-100/80 text-blue-800 border-blue-200/60'],
                'docx' => ['icon' => 'fa-file-word',  'bg' => 'bg-blue-50',   'text' => 'text-blue-600',   'badge' => 'bg-blue-100/80 text-blue-800 border-blue-200/60'],
                'xls'  => ['icon' => 'fa-file-excel', 'bg' => 'bg-emerald-50','text' => 'text-emerald-600','badge' => 'bg-emerald-100/80 text-emerald-800 border-emerald-200/60'],
                'xlsx' => ['icon' => 'fa-file-excel', 'bg' => 'bg-emerald-50','text' => 'text-emerald-600','badge' => 'bg-emerald-100/80 text-emerald-800 border-emerald-200/60'],
                'ppt'  => ['icon' => 'fa-file-powerpoint', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'badge' => 'bg-orange-100/80 text-orange-800 border-orange-200/60'],
                'pptx' => ['icon' => 'fa-file-powerpoint', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'badge' => 'bg-orange-100/80 text-orange-800 border-orange-200/60'],
                'zip'  => ['icon' => 'fa-file-zipper','bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'badge' => 'bg-amber-100/80 text-amber-800 border-amber-200/60'],
            ];
            $fileStyle = $iconMap[$ext] ?? ['icon' => 'fa-file-lines', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200'];
        @endphp
        <div class="group flex flex-col sm:flex-row items-start sm:items-center gap-6 bg-white rounded-2xl md:rounded-3xl p-6 md:p-7 mb-4 shadow-lg shadow-slate-200/50 border border-slate-200/80 hover:border-primary-300 hover:shadow-xl hover:shadow-slate-300/60 hover:-translate-y-1 transition-all duration-300">

            {{-- Ikon file --}}
            <div class="flex-shrink-0 w-14 h-14 md:w-16 md:h-16 rounded-2xl {{ $fileStyle['bg'] }} {{ $fileStyle['text'] }} flex items-center justify-center text-2xl shadow-xs border border-black/5">
                <i class="fa-solid {{ $fileStyle['icon'] }}"></i>
            </div>

            {{-- Konten tengah --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 {{ $fileStyle['badge'] }} border text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full">
                        <i class="fa-solid fa-file text-[9px]"></i>
                        {{ strtoupper($ext) }}
                    </span>
                    @if($doc->category ?? false)
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                            {{ $doc->category }}
                        </span>
                    @endif
                </div>
                <h3 class="text-xl md:text-2xl font-heading font-extrabold tracking-tight text-slate-900 group-hover:text-primary-700 transition-colors duration-200 leading-snug break-words mb-2">
                    {{ $doc->title }}
                </h3>
                @if($doc->description)
                    <p class="text-sm text-slate-600 leading-relaxed line-clamp-2 mb-3">{{ $doc->description }}</p>
                @endif
                <div class="flex items-center gap-2 text-slate-500 text-xs font-medium">
                    <i class="fa-regular fa-calendar text-slate-400 text-[11px]"></i>
                    <span>Diunggah {{ $doc->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            {{-- Tombol download --}}
            <div class="flex-shrink-0 w-full sm:w-auto pt-2 sm:pt-0">
                <a
                    href="{{ asset('storage/' . $doc->file) }}"
                    class="inline-flex items-center justify-center gap-2.5 bg-slate-900 hover:bg-primary-600 text-white px-6 py-3 min-h-[44px] rounded-xl text-xs font-bold transition-all duration-200 shadow-sm active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 w-full sm:w-auto whitespace-nowrap"
                    target="_blank"
                    download
                >
                    <i class="fa-solid fa-download text-xs"></i>
                    <span>Unduh Dokumen</span>
                </a>
            </div>
        </div>
        @empty
        <div class="py-8">
            <x-empty-state
                icon="fa-solid fa-folder-open"
                title="Arsip Dokumen Masih Kosong"
                description="Belum ada dokumen atau berkas resmi yang diterbitkan untuk saat ini."
            />
        </div>
        @endforelse

        {{-- ─── Paginasi ─── --}}
        @if($documents->hasPages())
        <div class="mt-12 md:mt-16 flex justify-center">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
