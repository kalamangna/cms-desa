@extends('layouts.app')

@section('title', 'Open Data | Desa ' . ($site_settings['village_name'] ?? 'Tompobulu'))
@section('meta_description', 'Portal data terbuka (Open Data) resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' menyajikan kumpulan dataset publik untuk analisis, transparansi, dan kebutuhan akademis.')
@section('meta_image', asset('img/meta.png'))

@section('content')
<!-- Standardized Dark Hero -->
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
                    <span class="text-white">Open Data</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Open <span class="text-emerald-500 italic">Data</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Katalog data publik Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">
    <div class="bg-white rounded-[40px] md:rounded-[60px] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-8 bg-slate-50/30 text-center md:text-left">
            <div>
                <h2 class="text-2xl md:text-3xl font-heading font-bold text-slate-900">Katalog Dataset</h2>
                <p class="text-slate-500 font-medium mt-1">Ditemukan {{ $datasets->total() }} dataset yang tersedia</p>
            </div>
            <form action="/dataset" method="GET" class="relative w-full md:w-96">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dataset..." class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white border-none focus:ring-2 focus:ring-emerald-500 shadow-sm font-medium">
                <button type="submit" class="absolute left-4 top-4.5 text-slate-400 hover:text-emerald-600 transition">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[800px] lg:min-w-0">
                <thead>
                    <tr class="bg-white border-b border-slate-50">
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em]">Dataset & Deskripsi</th>
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em]">Tahun</th>
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em] text-right">Unduh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($datasets as $dataset)
                    <tr class="hover:bg-slate-50/50 transition duration-300">
                        <td class="px-8 md:px-12 py-8 md:py-10">
                            <div class="font-heading font-bold text-lg md:text-xl text-slate-900 mb-2">{{ $dataset->title }}</div>
                            <div class="text-sm text-slate-500 max-w-lg leading-relaxed font-medium">{{ $dataset->description }}</div>
                        </td>
                        <td class="px-8 md:px-12 py-8 md:py-10">
                            <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 font-black text-[10px] tracking-widest uppercase">{{ $dataset->year }}</span>
                        </td>
                        <td class="px-8 md:px-12 py-8 md:py-10 text-right">
                            <div class="flex justify-end gap-2">
                                @if($dataset->file_csv)
                                    @php
                                        $csvUrl = $dataset->file_csv === 'dynamic' 
                                            ? route('datasets.download', ['type' => ($dataset->slug === 'data-penduduk' ? 'penduduk' : 'keluarga')])
                                            : asset('storage/' . $dataset->file_csv);
                                    @endphp
                                    <a href="{{ $csvUrl }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-emerald-700 transition" title="Unduh CSV" download>
                                        <i class="fa-solid fa-download"></i>
                                        CSV
                                    </a>
                                @endif
                                @if($dataset->file_xlsx)
                                    @php
                                        $xlsxUrl = $dataset->file_xlsx === 'dynamic' 
                                            ? route('datasets.download', ['type' => ($dataset->slug === 'data-penduduk' ? 'penduduk-xlsx' : 'keluarga-xlsx')])
                                            : asset('storage/' . $dataset->file_xlsx);
                                    @endphp
                                    <a href="{{ $xlsxUrl }}" class="inline-flex items-center gap-2 bg-sky-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-sky-700 transition" title="Unduh XLSX" download>
                                        <i class="fa-solid fa-download"></i>
                                        XLSX
                                    </a>
                                @endif
                                @if($dataset->file_pdf)
                                    @php
                                        $pdfUrl = $dataset->file_pdf === 'dynamic' 
                                            ? route('datasets.download', ['type' => ($dataset->slug === 'data-penduduk' ? 'penduduk-pdf' : 'keluarga-pdf')])
                                            : asset('storage/' . $dataset->file_pdf);
                                    @endphp
                                    <a href="{{ $pdfUrl }}" class="inline-flex items-center gap-2 bg-rose-600 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-rose-700 transition" title="Unduh PDF" download>
                                        <i class="fa-solid fa-download"></i>
                                        PDF
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 md:px-12 py-20 md:py-32 text-center">
                            <p class="text-slate-400 font-bold italic">Dataset belum tersedia.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($datasets->hasPages())
        <div class="p-8 md:p-12 border-t border-slate-50 bg-slate-50/30">
            {{ $datasets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
