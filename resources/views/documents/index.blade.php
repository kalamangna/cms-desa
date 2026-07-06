@extends('layouts.app')

@section('title', 'Dokumen | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Pusat arsip dokumen hukum, peraturan desa, keputusan kepala desa, dan berkas administrasi resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
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
                    <span class="text-white">Dokumen</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Arsip <span class="text-emerald-500 italic">Dokumen</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Unduh regulasi, dokumen resmi, dan formulir layanan Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- KONTEN UTAMA --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

    {{-- ─── Header Statistik ─── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-4"><div class="h-px w-8 bg-emerald-500"></div><span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Arsip Resmi</span></div>
            <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900">
                Daftar Dokumen
            </h2>
            <p class="text-slate-500 font-medium mt-2">
                Ditemukan <span class="text-emerald-600 font-bold">{{ $documents->total() }}</span> dokumen tersedia
            </p>
        </div>
        {{-- Stat Badge --}}
        <div class="flex-shrink-0 hidden sm:flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-2xl px-6 py-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white">
                <i class="fa-solid fa-folder-open text-sm"></i>
            </div>
            <div>
                <div class="text-xl font-heading font-black text-slate-900">{{ $documents->total() }}</div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Total Dokumen</div>
            </div>
        </div>
    </div>

    {{-- ─── List Card ─── --}}
    @forelse($documents as $doc)
    @php
        $ext = $doc->file ? strtolower(pathinfo($doc->file, PATHINFO_EXTENSION)) : 'file';
        $iconMap = [
            'pdf'  => ['icon' => 'fa-file-pdf',  'bg' => 'bg-red-50',    'text' => 'text-red-500',    'badge' => 'bg-red-100 text-red-700'],
            'doc'  => ['icon' => 'fa-file-word',  'bg' => 'bg-blue-50',   'text' => 'text-blue-500',   'badge' => 'bg-blue-100 text-blue-700'],
            'docx' => ['icon' => 'fa-file-word',  'bg' => 'bg-blue-50',   'text' => 'text-blue-500',   'badge' => 'bg-blue-100 text-blue-700'],
            'xls'  => ['icon' => 'fa-file-excel', 'bg' => 'bg-green-50',  'text' => 'text-green-600',  'badge' => 'bg-green-100 text-green-700'],
            'xlsx' => ['icon' => 'fa-file-excel', 'bg' => 'bg-green-50',  'text' => 'text-green-600',  'badge' => 'bg-green-100 text-green-700'],
            'ppt'  => ['icon' => 'fa-file-powerpoint', 'bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'badge' => 'bg-orange-100 text-orange-700'],
            'pptx' => ['icon' => 'fa-file-powerpoint', 'bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'badge' => 'bg-orange-100 text-orange-700'],
            'zip'  => ['icon' => 'fa-file-zipper','bg' => 'bg-yellow-50', 'text' => 'text-yellow-600', 'badge' => 'bg-yellow-100 text-yellow-700'],
        ];
        $fileStyle = $iconMap[$ext] ?? ['icon' => 'fa-file-lines', 'bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'badge' => 'bg-slate-100 text-slate-600'];
    @endphp
    <div class="group flex flex-col sm:flex-row items-start sm:items-center gap-6 bg-white rounded-3xl p-6 md:p-8 mb-4 shadow-sm shadow-slate-200/60 border border-slate-100 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-50 transition-all duration-300">

        {{-- Ikon file --}}
        <div class="flex-shrink-0 w-16 h-16 rounded-2xl {{ $fileStyle['bg'] }} {{ $fileStyle['text'] }} flex items-center justify-center text-2xl shadow-inner">
            <i class="fa-solid {{ $fileStyle['icon'] }}"></i>
        </div>

        {{-- Konten tengah --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1 {{ $fileStyle['badge'] }} text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                    <i class="fa-solid fa-tag text-[9px]"></i>
                    {{ strtoupper($ext) }}
                </span>
                @if($doc->category ?? false)
                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                        {{ $doc->category }}
                    </span>
                @endif
            </div>
            <h3 class="text-lg md:text-xl font-heading font-bold text-slate-900 group-hover:text-emerald-700 transition leading-tight break-words mb-2">
                {{ $doc->title }}
            </h3>
            @if($doc->description)
                <p class="text-sm text-slate-500 font-medium leading-relaxed line-clamp-2">{{ $doc->description }}</p>
            @endif
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mt-2">
                <i class="fa-solid fa-calendar-days mr-1"></i>
                {{ $doc->created_at->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- Tombol download --}}
        <div class="flex-shrink-0 w-full sm:w-auto">
            <a
                href="{{ asset('storage/' . $doc->file) }}"
                class="group/btn inline-flex items-center justify-center gap-3 bg-slate-900 text-white px-6 py-3.5 rounded-2xl text-sm font-bold hover:bg-emerald-600 transition-all duration-300 shadow-lg shadow-slate-900/10 hover:shadow-emerald-600/30 w-full sm:w-auto whitespace-nowrap"
                target="_blank"
                download
            >
                <i class="fa-solid fa-download group-hover/btn:animate-bounce transition"></i>
                <span>Unduh Dokumen</span>
            </a>
        </div>
    </div>
    @empty
    <div class="py-32 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
        <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-6 block"></i>
        <p class="text-slate-400 font-bold italic">Dokumen belum tersedia.</p>
    </div>
    @endforelse

    {{-- ─── Paginasi ─── --}}
    @if($documents->hasPages())
    <div class="mt-12">
        {{ $documents->links() }}
    </div>
    @endif
</div>
@endsection
