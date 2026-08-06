@extends('layouts.app')

@section('title', $post->title . ' | Desa ' . ($site_settings['village_name'] ?? ''))
@section('og_type', 'article')
@section('meta_description', Str::limit(strip_tags($post->content), 160))
@section('meta_image', $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp'))
@section('canonical', route('posts.show', $post->slug))
@section('meta_keywords', $post->title . ', berita desa, ' . ($site_settings['village_name'] ?? '') . ', ' . ($site_settings['district_name'] ?? ''))

@push('og_extra')
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <meta property="article:author" content="Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}">
    <meta property="article:section" content="Berita Desa">
@endpush

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ addslashes($post->title) }}",
    "description": "{{ addslashes(Str::limit(strip_tags($post->content), 160)) }}",
    "url": "{{ route('posts.show', $post->slug) }}",
    "datePublished": "{{ $post->published_at?->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "image": "{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp') }}",
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
        "@id": "{{ route('posts.show', $post->slug) }}"
    }
}
</script>
@endpush

@section('content')

{{-- =========================================================
     HERO SECTION — dynamic background from featured_image
     ========================================================= --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    {{-- Blurred featured image as background --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp') }}"
             class="absolute inset-0 w-full h-full object-cover opacity-25 blur-sm scale-110"
             alt="Background"
             onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">

        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/80 to-slate-900"></div>
        <div class="absolute inset-0 opacity-5"
             style="background-image:radial-gradient(#94a3b8 1px,transparent 1px);background-size:22px 22px;"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex flex-wrap items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <a href="/berita" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 rounded-md px-1">Berita</a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white/70 truncate max-w-44 md:max-w-none normal-case tracking-normal font-medium">
                        {{ Str::limit($post->title, 40) }}
                    </span>
                </li>
            </ol>
        </nav>

        {{-- Category badge --}}
        @if($post->category)
        <div class="mb-5">
            <a href="/berita?category={{ $post->category->slug }}"
               class="inline-flex items-center gap-2 bg-primary-500/15 border border-primary-500/25 text-primary-400 text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-full hover:bg-primary-500/25 transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                <i class="fa-solid fa-tag text-[9px]"></i>
                {{ $post->category->name }}
            </a>
        </div>
        @endif

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl lg:text-7xl font-heading font-black tracking-tight text-white leading-[1.1] mb-8 drop-shadow-2xl">
            {{ $post->title }}
        </h1>

        {{-- Meta --}}
        <div class="flex flex-wrap items-center gap-4 md:gap-6 text-slate-400 text-xs font-bold uppercase tracking-widest">
            <span class="flex items-center gap-2">
                <i class="fa-regular fa-calendar text-primary-400"></i>
                {{ $post->published_at->translatedFormat('d F Y') }}
            </span>
            <span class="w-px h-4 bg-slate-700 hidden md:block"></span>
            <span class="flex items-center gap-2">
                <i class="fa-solid fa-user text-primary-400"></i>
                Admin Desa
            </span>
            <span class="w-px h-4 bg-slate-700 hidden md:block"></span>
            <span class="flex items-center gap-2">
                <i class="fa-regular fa-clock text-primary-400"></i>
                {{ $post->published_at->diffForHumans() }}
            </span>
        </div>
    </div>
</div>

{{-- =========================================================
     ARTICLE CONTENT
     ========================================================= --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 md:-mt-12 pb-20 md:pb-32 relative z-10">

        <article class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl shadow-slate-200/60 dark:shadow-slate-950/60 overflow-hidden border border-slate-100 dark:border-slate-800">

            {{-- ─── Featured Image ──────────────────────────────────── --}}
            <div class="w-full overflow-hidden" style="aspect-ratio:21/9;">
                <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp') }}"
                     class="w-full h-full object-cover"
                     alt="{{ $post->title }}"
                     loading="eager"
                     fetchpriority="high"
                     onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
            </div>


            {{-- ─── Article Body ────────────────────────────────────── --}}
            <div class="px-6 md:px-12 lg:px-16 pt-10 md:pt-14 pb-8">
                {{-- Prose content --}}
                <div class="prose prose-lg md:prose-xl prose-slate dark:prose-invert max-w-none
                            prose-headings:font-heading prose-headings:text-slate-900 dark:prose-headings:text-slate-100 prose-headings:font-bold
                            prose-p:text-slate-600 dark:prose-p:text-slate-300 prose-p:leading-relaxed prose-p:font-medium
                            prose-a:text-primary-600 dark:prose-a:text-primary-400 prose-a:font-semibold hover:prose-a:text-primary-700 dark:hover:prose-a:text-primary-300
                            prose-img:rounded-2xl prose-img:shadow-lg
                            prose-blockquote:border-primary-500 prose-blockquote:bg-primary-50/50 dark:prose-blockquote:bg-primary-950/30 prose-blockquote:rounded-r-2xl prose-blockquote:py-1
                            prose-code:text-primary-700 dark:prose-code:text-primary-300 prose-code:bg-primary-50 dark:prose-code:bg-primary-950/40 prose-code:rounded prose-code:px-1
                            prose-strong:text-slate-800 dark:prose-strong:text-slate-100">
                    {!! $post->content !!}
                </div>
            </div>

            {{-- ─── Footer: Tags & Share ────────────────────────────── --}}
            <div class="px-6 md:px-12 lg:px-16 py-8 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

                    {{-- Share --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <span class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest flex-shrink-0">
                            Bagikan:
                        </span>
                        <div class="flex gap-2.5">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[#1877F2] bg-blue-50 dark:bg-blue-950/40 hover:bg-[#1877F2] hover:text-white hover:border-blue-600 transition-all duration-200 shadow-sm active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                               title="Bagikan ke Facebook">
                                <i class="fa-brands fa-facebook-f text-sm"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . request()->fullUrl()) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[#25D366] bg-green-50 dark:bg-green-950/40 hover:bg-[#25D366] hover:text-white hover:border-green-500 transition-all duration-200 shadow-sm active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                               title="Bagikan ke WhatsApp">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                            </a>

                            <button onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<i class=\'fa-solid fa-check\'></i>'; setTimeout(()=>{ this.innerHTML='<i class=\'fa-solid fa-link\'></i>'; }, 2000)"
                               class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-primary-500 hover:text-white hover:border-primary-500 transition-all duration-200 shadow-sm active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                               title="Salin tautan">
                                <i class="fa-solid fa-link text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Back button --}}
                    <a href="/berita"
                       class="inline-flex items-center gap-2.5 bg-slate-900 dark:bg-slate-800 text-white font-bold text-sm px-6 py-3 rounded-xl hover:bg-primary-600 dark:hover:bg-primary-600 transition-all duration-200 shadow-lg shadow-slate-900/15 group active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform duration-200"></i>
                        Kembali ke Berita
                    </a>

                </div>
            </div>

        </article>

        {{-- ─── Navigation: Prev / Next Post ──────────────────────── --}}
        @if(isset($prevPost) || isset($nextPost))
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @if(isset($prevPost))
            <a href="/berita/{{ $prevPost->slug }}"
               class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-md shadow-slate-200/50 dark:shadow-slate-950/50 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-700 hover:-translate-y-0.5 transition-all duration-300 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-500 group-hover:text-white transition-all">
                    <i class="fa-solid fa-arrow-left text-sm text-slate-500 dark:text-slate-400 group-hover:text-white"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Sebelumnya</div>
                    <div class="text-sm font-bold text-slate-800 dark:text-slate-100 line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        {{ $prevPost->title }}
                    </div>
                </div>
            </a>
            @else
            <div></div>
            @endif

            @if(isset($nextPost))
            <a href="/berita/{{ $nextPost->slug }}"
               class="group flex items-center gap-4 bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-md shadow-slate-200/50 dark:shadow-slate-950/50 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-700 hover:-translate-y-0.5 transition-all duration-300 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950 sm:flex-row-reverse sm:text-right">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 group-hover:bg-primary-500 group-hover:text-white transition-all">
                    <i class="fa-solid fa-arrow-right text-sm text-slate-500 dark:text-slate-400 group-hover:text-white"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Selanjutnya</div>
                    <div class="text-sm font-bold text-slate-800 dark:text-slate-100 line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        {{ $nextPost->title }}
                    </div>
                </div>
            </a>
            @endif
        </div>
        @endif

        {{-- ─── Related Posts ───────────────────────────────────────── --}}
        @if(isset($relatedPosts) && $relatedPosts->count() > 0)
        <div class="mt-14">
            <div class="flex items-center gap-3 mb-7">
                <h2 class="text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100">Berita Terkait</h2>
                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-800"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                @foreach($relatedPosts as $related)
                <a href="/berita/{{ $related->slug }}" class="group bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-md shadow-slate-200/50 dark:shadow-slate-950/50 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-700 hover:-translate-y-1 transition-all duration-300 flex flex-col active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950">
                    <div class="aspect-video overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img src="{{ $related->featured_image ? asset('storage/' . $related->featured_image) : asset('img/meta.webp') }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             alt="{{ $related->title }}"
                             onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">

                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <time class="text-primary-600 dark:text-primary-400 font-bold text-[9px] uppercase tracking-widest mb-1.5 block">
                            {{ $related->published_at->translatedFormat('d M Y') }}
                        </time>
                        <h3 class="text-sm font-heading font-bold text-slate-800 dark:text-slate-100 line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            {{ $related->title }}
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
