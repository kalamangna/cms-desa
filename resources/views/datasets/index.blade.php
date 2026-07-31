@extends('layouts.app')

@section('title', 'Open Data | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Portal data terbuka (Open Data) resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' menyajikan kumpulan dataset publik untuk analisis, transparansi, dan kebutuhan akademis.')
@section('meta_image', asset('img/meta.webp'))

@section('content')
{{-- ═══════════════════════════════════════════════════════
     DARK HERO
═══════════════════════════════════════════════════════ --}}
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
                    <span class="text-white">Open Data</span>
                </li>
            </ol>
        </nav>

        {{-- Heading --}}
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Open <span class="text-primary-500 italic">Data</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                Katalog data publik Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
        <div class="bg-white rounded-3xl md:rounded-3xl shadow-lg shadow-slate-200/50 border border-slate-200/80 overflow-hidden">
            {{-- Header Toolbar --}}
            <div class="p-8 md:p-12 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-8 bg-slate-50/50 text-center md:text-left">
                <div>
                    <h2 class="text-3xl md:text-4xl font-heading font-black tracking-tight text-slate-900">Katalog Dataset</h2>
                    <p class="text-slate-500 font-medium text-sm mt-1">Ditemukan {{ $datasets->total() }} dataset publik yang tersedia</p>
                </div>
                <form action="/dataset" method="GET" class="relative w-full md:w-96">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dataset..." class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-slate-700 shadow-xs text-sm">
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary-600 transition cursor-pointer active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-md" aria-label="Cari Dataset">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>
                </form>
            </div>
            
            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px] lg:min-w-0">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-8 md:px-12 py-5 font-black text-slate-500 text-[10px] uppercase tracking-widest">Dataset & Deskripsi</th>
                            <th class="px-8 md:px-12 py-5 font-black text-slate-500 text-[10px] uppercase tracking-widest">Tahun</th>
                            <th class="px-8 md:px-12 py-5 font-black text-slate-500 text-[10px] uppercase tracking-widest text-right">Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($datasets as $dataset)
                        <tr class="hover:bg-slate-50/80 transition duration-200">
                            <td class="px-8 md:px-12 py-7 md:py-8">
                                <div class="font-heading font-black tracking-tight text-xl md:text-2xl text-slate-900 mb-1.5">{{ $dataset->title }}</div>
                                <div class="text-sm text-slate-600 max-w-xl leading-relaxed font-medium">{{ $dataset->description }}</div>
                            </td>
                            <td class="px-8 md:px-12 py-7 md:py-8">
                                <span class="px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] tracking-widest uppercase border border-slate-200">{{ $dataset->year }}</span>
                            </td>
                            <td class="px-8 md:px-12 py-7 md:py-8 text-right">
                                <div class="flex justify-end gap-2.5">
                                    @php
                                        $key = $dataset->id ?: ($dataset->slug ?: 'penduduk');

                                        $csvUrl = ($dataset->file_csv && $dataset->file_csv !== 'dynamic') 
                                            ? asset('storage/' . $dataset->file_csv)
                                            : route('datasets.download', ['type' => $key]);

                                        $xlsxUrl = ($dataset->file_xlsx && $dataset->file_xlsx !== 'dynamic') 
                                            ? asset('storage/' . $dataset->file_xlsx)
                                            : route('datasets.download', ['type' => $key . '-xlsx']);
                                    @endphp

                                    <a href="{{ $csvUrl }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-emerald-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer" title="Unduh CSV">
                                        <i class="fa-solid fa-download text-[11px]"></i>
                                        CSV
                                    </a>
                                    <a href="{{ $xlsxUrl }}" class="inline-flex items-center gap-2 bg-sky-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-sky-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer" title="Unduh XLSX">
                                        <i class="fa-solid fa-download text-[11px]"></i>
                                        XLSX
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6">
                                <x-empty-state
                                    icon="fa-solid fa-database"
                                    title="Belum Ada Dataset Publik"
                                    description="Belum ada dataset publik yang tersedia."
                                />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($datasets->hasPages())
            <div class="p-8 md:p-12 border-t border-slate-100 bg-slate-50/50">
                {{ $datasets->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
