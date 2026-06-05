@extends('layouts.app')

@section('title', 'Open Data - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
                        <span class="text-white">Open Data</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Open <span class="text-emerald-500 italic">Data</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Katalog data publik Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="bg-white rounded-[40px] md:rounded-[60px] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        <div class="p-8 md:p-12 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-8 bg-slate-50/30 text-center md:text-left">
            <div>
                <h2 class="text-2xl md:text-3xl font-heading font-bold text-slate-900">Katalog Dataset</h2>
                <p class="text-slate-500 font-medium mt-1">Ditemukan {{ $datasets->total() }} dataset yang tersedia</p>
            </div>
            <div class="relative w-full md:w-96">
                <input type="text" placeholder="Cari dataset..." class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white border-none focus:ring-2 focus:ring-emerald-500 shadow-sm font-medium">
                <i class="fa-solid fa-magnifying-glass h-5 w-5 text-slate-400 absolute left-4 top-4.5"></i>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[800px] lg:min-w-0">
                <thead>
                    <tr class="bg-white border-b border-slate-50">
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em]">Dataset & Deskripsi</th>
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em]">Tahun</th>
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em]">Format</th>
                        <th class="px-8 md:px-12 py-6 md:py-8 font-black text-slate-900 text-[10px] uppercase tracking-[0.3em] text-right">Aksi</th>
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
                        <td class="px-8 md:px-12 py-8 md:py-10">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl bg-amber-50 text-amber-700 font-black text-[10px] border border-amber-100 uppercase tracking-widest">
                                <i class="fa-solid fa-file-lines w-3 h-3"></i>
                                {{ $dataset->format }}
                            </span>
                        </td>
                        <td class="px-8 md:px-12 py-8 md:py-10 text-right">
                            <a href="{{ asset('storage/' . $dataset->file_path) }}" class="inline-flex items-center gap-3 bg-emerald-600 text-white px-6 md:px-8 py-3 md:py-4 rounded-2xl text-sm font-bold hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/10" download>
                                <i class="fa-solid fa-download w-4 h-4"></i>
                                <span class="hidden sm:inline">Unduh</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 md:px-12 py-20 md:py-32 text-center">
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
