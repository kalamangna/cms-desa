@extends('layouts.app')

@section('title', 'Pengumuman - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <span class="text-white">Pengumuman</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Warta <span class="text-emerald-500 italic">Pengumuman</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Pusat informasi resmi dan edaran penting Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 gap-8 md:gap-12">
        @forelse($announcements as $announcement)
        <article class="bg-white rounded-[40px] p-8 md:p-12 shadow-2xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row gap-8 items-start group hover:border-emerald-200 transition-all duration-500">
            <div class="flex-shrink-0 w-full md:w-48">
                <div class="bg-emerald-50 rounded-3xl p-6 text-center">
                    <span class="block text-4xl font-heading font-black text-emerald-600 mb-1">{{ $announcement->published_at->format('d') }}</span>
                    <span class="block text-[10px] font-black uppercase tracking-widest text-emerald-500">{{ $announcement->published_at->translatedFormat('F Y') }}</span>
                </div>
            </div>
            <div class="flex-grow">
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest border border-slate-200">Official Info</span>
                    <span class="text-slate-400 text-xs font-medium">{{ $announcement->published_at->diffForHumans() }}</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 mb-6 group-hover:text-emerald-600 transition">{{ $announcement->title }}</h2>
                <div class="prose prose-emerald max-w-none text-slate-600 leading-relaxed font-medium line-clamp-3 mb-8">
                    {{ strip_tags($announcement->content) }}
                </div>
                <button class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-sm hover:bg-emerald-600 transition shadow-lg shadow-slate-900/10 flex items-center gap-3">
                    Baca Selengkapnya <i class="fa-solid fa-arrow-right-long"></i>
                </button>
            </div>
        </article>
        @empty
        <div class="py-32 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
            <p class="text-slate-400 font-bold italic text-lg">Belum ada pengumuman resmi saat ini.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-20">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
