@extends('layouts.app')

@section('title', $announcement->title . ' | Pengumuman Desa ' . ($site_settings['village_name'] ?? 'Tompobulu'))
@section('og_type', 'article')
@section('meta_description', Str::limit(strip_tags($announcement->content), 160))
@section('meta_image', asset('img/meta.png'))
@section('canonical', route('announcements.show', $announcement->slug))
@section('meta_keywords', $announcement->title . ', pengumuman desa, ' . ($site_settings['village_name'] ?? '') . ', ' . ($site_settings['district_name'] ?? ''))

@push('og_extra')
    <meta property="article:published_time" content="{{ $announcement->published_at?->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $announcement->updated_at->toIso8601String() }}">
    <meta property="article:author" content="Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}">
    <meta property="article:section" content="Pengumuman Desa">
@endpush

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ addslashes($announcement->title) }}",
    "description": "{{ addslashes(Str::limit(strip_tags($announcement->content), 160)) }}",
    "url": "{{ route('announcements.show', $announcement->slug) }}",
    "datePublished": "{{ $announcement->published_at?->toIso8601String() }}",
    "dateModified": "{{ $announcement->updated_at->toIso8601String() }}",
    "author": {
        "@type": "Organization",
        "@id": "{{ url('/') }}/#organization",
        "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}"
    },
    "publisher": {
        "@id": "{{ url('/') }}/#organization"
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ route('announcements.show', $announcement->slug) }}"
    }
}
</script>
@endpush

@section('content')

{{-- =========================================================
     HERO SECTION
     ========================================================= --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    {{-- Background patterns --}}
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-[0.04]"
             style="background-image:radial-gradient(#94a3b8 1px,transparent 1px);background-size:22px 22px;"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-amber-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-amber-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-amber-400 transition-colors duration-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-amber-500/40"></i>
                    <a href="{{ route('announcements.index') }}" class="hover:text-amber-400 transition-colors duration-200">Pengumuman</a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-amber-500/40"></i>
                    <span class="text-white line-clamp-1">{{ $announcement->title }}</span>
                </li>
            </ol>
        </nav>

        {{-- Badge --}}
        <div class="mb-6">
            <span class="inline-flex items-center gap-2 bg-amber-500/20 border border-amber-500/30 text-amber-400 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full">
                <i class="fa-solid fa-bullhorn text-[10px]"></i>
                Pengumuman
            </span>
        </div>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-heading font-extrabold text-white leading-tight mb-8">
            {{ $announcement->title }}
        </h1>

        {{-- Meta --}}
        <div class="flex flex-wrap items-center gap-4 md:gap-6 text-slate-400 text-xs font-bold uppercase tracking-widest">
            <span class="flex items-center gap-2">
                <i class="fa-regular fa-calendar text-amber-400"></i>
                {{ $announcement->published_at->translatedFormat('d F Y') }}
            </span>
            <span class="w-px h-4 bg-slate-700 hidden md:block"></span>
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-user text-amber-400"></i>
                Admin Desa
            </span>
            <span class="w-px h-4 bg-slate-700 hidden md:block"></span>
            <span class="flex items-center gap-2">
                <i class="fa-regular fa-clock text-amber-400"></i>
                {{ $announcement->published_at->diffForHumans() }}
            </span>
        </div>
    </div>
</div>

{{-- =========================================================
     ARTICLE CONTENT
     ========================================================= --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 md:-mt-12 pb-20 md:pb-32 relative z-10">

        <article class="bg-white rounded-3xl shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100">

            {{-- ─── Article Body ────────────────────────────────────── --}}
            <div class="px-6 md:px-12 lg:px-16 pt-10 md:pt-14 pb-8">
                {{-- Prose content --}}
                <div class="prose prose-lg md:prose-xl prose-amber max-w-none
                            prose-headings:font-heading prose-headings:text-slate-900 prose-headings:font-bold
                            prose-p:text-slate-600 prose-p:leading-relaxed prose-p:font-medium
                            prose-a:text-amber-600 prose-a:font-semibold hover:prose-a:text-amber-700
                            prose-img:rounded-2xl prose-img:shadow-lg
                            prose-blockquote:border-amber-500 prose-blockquote:bg-amber-50/50 prose-blockquote:rounded-r-2xl prose-blockquote:py-1
                            prose-strong:text-slate-800">
                    {!! $announcement->content !!}
                </div>
            </div>

            {{-- ─── Footer: Share & Back ────────────────────────────── --}}
            <div class="px-6 md:px-12 lg:px-16 py-8 border-t border-slate-100 bg-slate-50/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

                    {{-- Share --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] flex-shrink-0">
                            Bagikan:
                        </span>
                        <div class="flex gap-2.5">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-200 shadow-sm"
                               title="Bagikan ke Facebook">
                                <i class="fa-brands fa-facebook-f text-sm"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($announcement->title . ' - ' . request()->fullUrl()) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-green-500 hover:text-white hover:border-green-500 transition-all duration-200 shadow-sm"
                               title="Bagikan ke WhatsApp">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                            </a>
                            <button onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<i class=\'fa-solid fa-check\'></i>'; setTimeout(()=>{ this.innerHTML='<i class=\'fa-solid fa-link\'></i>'; }, 2000)"
                               class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all duration-200 shadow-sm"
                               title="Salin tautan">
                                <i class="fa-solid fa-link text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Back button --}}
                    <a href="{{ route('announcements.index') }}"
                       class="inline-flex items-center gap-2.5 bg-slate-900 text-white font-bold text-sm px-6 py-3 rounded-xl hover:bg-amber-600 transition-all duration-200 shadow-lg shadow-slate-900/15 group">
                        <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform duration-200"></i>
                        Kembali ke Pengumuman
                    </a>
                </div>
            </div>

        </article>

        {{-- ─── Pengumuman Terbaru Lainnya ──────────────────────────── --}}
        @if($recent->count() > 0)
        <div class="mt-14">
            <div class="flex items-center gap-3 mb-7">
                <h2 class="text-2xl font-heading font-extrabold text-slate-900">Pengumuman Lainnya</h2>
                <div class="flex-1 h-px bg-slate-200"></div>
                <a href="{{ route('announcements.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 whitespace-nowrap">
                    Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($recent as $item)
                <a href="{{ route('announcements.show', $item->slug) }}"
                   class="group bg-white rounded-2xl p-5 border border-slate-100 shadow-md shadow-slate-200/50 hover:shadow-lg hover:border-amber-200 hover:-translate-y-0.5 transition-all duration-200 flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 transition-colors duration-200">
                        <i class="fa-solid fa-bullhorn text-amber-500 text-sm group-hover:text-white transition-colors duration-200"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <time class="text-amber-600 font-bold text-[10px] uppercase tracking-widest mb-1 block">
                            {{ $item->published_at->translatedFormat('d M Y') }}
                        </time>
                        <h3 class="text-sm font-bold text-slate-800 line-clamp-2 group-hover:text-amber-600 transition-colors leading-snug">
                            {{ $item->title }}
                        </h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
