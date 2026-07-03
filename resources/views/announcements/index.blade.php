@extends('layouts.app')

@section('title', 'Pengumuman | Desa ' . ($site_settings['village_name'] ?? 'Tompobulu'))
@section('meta_description', 'Temukan pengumuman resmi dan maklumat penting dari Pemerintah Desa ' . ($site_settings['village_name'] ?? 'Tompobulu') . ' untuk seluruh warga.')
@section('meta_image', asset('img/meta.png'))

@section('content')

{{-- =========================================================
     HERO SECTION
     ========================================================= --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-emerald-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-emerald-400 transition-colors duration-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500/40"></i>
                    <span class="text-white">Pengumuman</span>
                </li>
            </ol>
        </nav>

        {{-- Heading --}}
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Warta <span class="text-emerald-500 italic">Pengumuman</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 max-w-2xl">
                Informasi resmi, edaran, dan pengumuman penting Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- =========================================================
     STATS BAR
     ========================================================= --}}
<div class="bg-white border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-500">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                <span class="font-medium">Diperbarui secara berkala</span>
            </div>
            <div class="flex items-center gap-2 font-medium">
                <i class="fa-solid fa-list-check text-amber-500"></i>
                Total {{ $announcements->total() }} pengumuman
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     TIMELINE CONTENT
     ========================================================= --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

        @forelse($announcements as $index => $announcement)

        {{-- Year / Month divider --}}
        @if($index === 0 || $announcement->published_at->format('Ym') !== $announcements[$index - 1]->published_at->format('Ym'))
        <div class="flex items-center gap-4 mb-8 {{ $index > 0 ? 'mt-14' : '' }}">
            <div class="bg-slate-900 text-white text-xs font-black uppercase tracking-[0.2em] px-4 py-2 rounded-full">
                {{ $announcement->published_at->translatedFormat('F Y') }}
            </div>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>
        @endif

        {{-- ─── Timeline Item ─────────────────────────────────────── --}}
        <div class="relative flex gap-5 md:gap-8 mb-6 group">

            {{-- Timeline stem --}}
            @if(!$loop->last)
            <div class="absolute left-[26px] md:left-[30px] top-14 bottom-0 w-px bg-slate-200 group-last:hidden -z-0"></div>
            @endif

            {{-- Icon dot --}}
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-amber-50 border-2 border-amber-200 flex items-center justify-center shadow-sm group-hover:bg-amber-500 group-hover:border-amber-500 transition-all duration-300 z-10">
                <i class="fa-solid fa-bullhorn text-amber-500 group-hover:text-white transition-colors duration-300"></i>
            </div>

            {{-- Card --}}
            <div class="flex-1 bg-white rounded-2xl border border-slate-100 shadow-md shadow-slate-200/50 overflow-hidden group-hover:shadow-lg group-hover:shadow-amber-100/40 group-hover:border-amber-200/60 transition-all duration-300">

                {{-- Card header --}}
                <div class="px-6 pt-5 pb-4 border-b border-slate-50">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Status badge --}}
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-circle text-[6px] text-amber-500"></i>
                                Pengumuman Resmi
                            </span>
                            <span class="text-slate-400 text-xs font-medium">
                                {{ $announcement->published_at->diffForHumans() }}
                            </span>
                        </div>
                        {{-- Date badge --}}
                        <div class="flex-shrink-0 text-right">
                            <div class="inline-flex flex-col items-center bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2">
                                <span class="text-2xl font-heading font-black text-emerald-600 leading-none">
                                    {{ $announcement->published_at->format('d') }}
                                </span>
                                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500 leading-tight mt-0.5">
                                    {{ $announcement->published_at->translatedFormat('M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card body --}}
                <div class="px-6 py-5">
                    <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 mb-3 group-hover:text-amber-700 transition-colors duration-200 leading-snug">
                        {{ $announcement->title }}
                    </h2>
                    <div class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-5">
                        {{ Str::limit(strip_tags($announcement->content), 240) }}
                    </div>

                    {{-- Read more accordion (Alpine.js) --}}
                    <div x-data="{ open: false }">
                        {{-- Full content (hidden by default) --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="prose prose-sm prose-emerald max-w-none text-slate-600 mb-5 border-t border-slate-100 pt-4 mt-2">
                            {!! $announcement->content !!}
                        </div>

                        <button @click="open = !open"
                                class="inline-flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl transition-all duration-200"
                                :class="open
                                    ? 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                    : 'bg-amber-500 text-white hover:bg-amber-600 shadow-md shadow-amber-500/25'">
                            <span x-text="open ? 'Sembunyikan' : 'Baca Selengkapnya'"></span>
                            <i class="fa-solid transition-transform duration-200"
                               :class="open ? 'fa-chevron-up rotate-0' : 'fa-arrow-right'"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        @empty

        {{-- Empty state --}}
        <div class="py-28 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200 shadow-sm">
            <div class="w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-6 border-2 border-amber-100">
                <i class="fa-solid fa-bullhorn text-3xl text-amber-300"></i>
            </div>
            <h3 class="font-heading font-bold text-slate-700 text-xl mb-2">Belum Ada Pengumuman</h3>
            <p class="text-slate-400 font-medium text-sm">Belum ada pengumuman resmi yang dipublikasikan saat ini.</p>
        </div>

        @endforelse

        {{-- ─── Pagination ──────────────────────────────────────────── --}}
        @if($announcements->hasPages())
        <div class="mt-14">
            {{ $announcements->links() }}
        </div>
        @endif

    </div>
</div>

@endsection
