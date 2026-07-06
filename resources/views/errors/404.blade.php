@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan (404) | Desa ' . ($site_settings['village_name'] ?? ''))

@section('content')
<div class="bg-slate-50 min-h-[70vh] flex items-center py-16 md:py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        {{-- Visual Illustration --}}
        <div class="relative inline-flex items-center justify-center mb-8">
            <div class="absolute inset-0 bg-emerald-500/10 rounded-full blur-2xl w-48 h-48 -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2"></div>
            <span class="text-8xl md:text-9xl font-heading font-black text-emerald-600/20 tracking-tighter">404</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fa-solid fa-compass-slash text-emerald-600 text-5xl md:text-6xl animate-bounce"></i>
            </div>
        </div>

        {{-- Heading & Message --}}
        <h1 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-4">
            Aduh! Halaman Tidak Ditemukan
        </h1>
        <p class="text-slate-500 text-base md:text-lg max-w-xl mx-auto leading-relaxed mb-10 font-medium">
            Sepertinya jalan yang Anda cari tidak ada atau halaman telah dipindahkan. Mari kembali ke jalan utama Desa {{ $site_settings['village_name'] ?? '' }}.
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mb-16">
            <a href="/" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-4 rounded-2xl transition duration-300 shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-house text-sm"></i>
                Kembali ke Beranda
            </a>
            <a href="/berita" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-8 py-4 rounded-2xl transition duration-300 shadow-sm">
                <i class="fa-solid fa-newspaper text-sm text-emerald-600"></i>
                Baca Berita Desa
            </a>
            <a href="/kontak" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-8 py-4 rounded-2xl transition duration-300 shadow-sm">
                <i class="fa-solid fa-envelope text-sm text-emerald-600"></i>
                Hubungi Kami
            </a>
        </div>

        {{-- Helpful Links --}}
        <div class="border-t border-slate-200 pt-8 max-w-lg mx-auto">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Mungkin Anda Mencari:</p>
            <div class="grid grid-cols-2 gap-3 text-sm text-slate-600 font-semibold">
                <a href="/layanan" class="hover:text-emerald-600 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-hand-holding-hand text-xs text-emerald-500"></i> Layanan Publik
                </a>
                <a href="/statistik" class="hover:text-emerald-600 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-chart-simple text-xs text-emerald-500"></i> Statistik Wilayah
                </a>
                <a href="/apbdes" class="hover:text-emerald-600 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-scale-balanced text-xs text-emerald-500"></i> Transparansi APBDes
                </a>
                <a href="/dataset" class="hover:text-emerald-600 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-database text-xs text-emerald-500"></i> Open Data
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
