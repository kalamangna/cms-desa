@extends('layouts.app')

@section('title', (request('search') ? 'Hasil Pencarian: "' . request('search') . '"' : ($selectedCategory ? 'Kategori: ' . $selectedCategory->name : 'Berita')) . ' | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', request('search') ? 'Menampilkan hasil pencarian berita dengan kata kunci "' . request('search') . '" pada portal resmi Desa ' . ($site_settings['village_name'] ?? '') . '.' : ($selectedCategory ? 'Kumpulan berita resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' untuk kategori ' . $selectedCategory->name . '.' : 'Warta berita resmi dan siaran pers mengenai program pembangunan serta kegiatan pelayanan publik Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.'))
@section('meta_image', asset('img/meta.webp'))

@section('content')

{{-- =========================================================
     HERO SECTION
     ========================================================= --}}
<div class="relative bg-slate-900 dark:bg-slate-950 py-16 md:py-24 lg:py-28 overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-500"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl transition-colors duration-500"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 dark:bg-primary-600/5 rounded-full blur-3xl transition-colors duration-500"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Berita</span>
                </li>
            </ol>
        </nav>

        {{-- Heading --}}
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Berita <span class="text-primary-500 italic">Terbaru</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Kabar kegiatan, pembangunan, dan perkembangan Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- =========================================================
     MAIN CONTENT
     ========================================================= --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

        {{-- ─── Search Bar (Mobile) ──────────────────────────────── --}}
        <div class="mb-10 lg:hidden">
            <form action="/berita" method="GET">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berita..."
                           class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-600 shadow-sm text-sm">
                </div>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 xl:gap-16">

            {{-- =========================================================
                 LEFT: Posts Grid
                 ========================================================= --}}
            <main class="lg:w-2/3 xl:w-2/3">

                @forelse($posts as $index => $post)

                    {{-- ─── Featured Card (first post) ──────────────────── --}}
                    @if($index === 0)
                    <a href="/berita/{{ $post->slug }}" class="group block mb-10 md:mb-14 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-3xl transition-transform duration-300">
                        <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-slate-200/60 dark:shadow-slate-950/60 border border-slate-200/80 dark:border-slate-800">
                            {{-- Image --}}
                            <div class="aspect-video w-full overflow-hidden bg-slate-200 dark:bg-slate-800">
                                <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp') }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                     alt="{{ $post->title }}"
                                     loading="eager"
                                     fetchpriority="high"
                                     onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">

                            </div>

                            {{-- Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-transparent"></div>

                            {{-- Category badge --}}
                            <div class="absolute top-5 left-5 flex gap-2">
                                @if($post->category)
                                <span class="bg-primary-500 text-white text-[10px] font-black uppercase tracking-widest px-3.5 py-1.5 rounded-full shadow-md">
                                    {{ $post->category->name }}
                                </span>
                                @endif
                            </div>

                            {{-- Content overlay --}}
                            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                                <time class="text-slate-300 text-[10px] font-black uppercase tracking-widest mb-3 block">
                                    {{ $post->published_at->translatedFormat('d F Y') }}
                                </time>
                                <h2 class="text-xl md:text-2xl lg:text-3xl font-heading font-extrabold text-white leading-tight group-hover:text-primary-300 transition-colors duration-300 line-clamp-2 mb-3">
                                    {{ $post->title }}
                                </h2>
                                <p class="text-slate-300 text-sm leading-relaxed line-clamp-2 hidden md:block">
                                    {{ Str::limit(strip_tags($post->content), 160) }}
                                </p>
                                <div class="mt-5 inline-flex items-center gap-2 text-primary-400 font-bold text-sm group-hover:gap-3 transition-all">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>

                    @else

                    {{-- ─── First non-featured: open grid wrapper ─────────── --}}
                    @if($index === 1)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @endif

                    {{-- ─── Regular Post Card ────────────────────────────── --}}
                    <a href="/berita/{{ $post->slug }}" class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 hover:shadow-2xl hover:shadow-slate-300/60 dark:hover:shadow-slate-950 hover:-translate-y-1 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950 transition-all duration-300 flex flex-col">
                        {{-- Thumbnail --}}
                        <div class="block relative overflow-hidden aspect-video bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/meta.webp') }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 alt="{{ $post->title }}"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">

                            @if($post->category)
                            <span class="absolute top-4 left-4 bg-primary-500 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full shadow-md">
                                {{ $post->category->name }}
                            </span>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="p-5 flex flex-col flex-grow">
                            <time class="text-primary-600 font-bold text-[10px] uppercase tracking-widest mb-2.5 block">
                                {{ $post->published_at->translatedFormat('d M Y') }}
                            </time>
                            <h2 class="text-base font-heading font-bold text-slate-900 dark:text-slate-100 leading-snug mb-3 group-hover:text-primary-600 transition-colors line-clamp-2 flex-grow">
                                {{ $post->title }}
                            </h2>
                            <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed line-clamp-2 mb-4">
                                {{ Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <div class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-primary-600 transition-colors group/link mt-auto">
                                Baca <i class="fa-solid fa-arrow-right text-[9px] group-hover/link:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </a>

                    {{-- ─── Close grid wrapper after last post ──────────── --}}
                    @if($loop->last)
                    </div>
                    @endif

                    @endif

                @empty
                {{-- Empty state --}}
                <x-empty-state
                    icon="fa-solid fa-newspaper"
                    title="Belum Ada Artikel Berita"
                    description="Belum ada berita yang diterbitkan."
                />
                @endforelse

                {{-- ─── Pagination ──────────────────────────────────────── --}}
                @if($posts->hasPages())
                <div class="mt-12 md:mt-16">
                    {{ $posts->links() }}
                </div>
                @endif
            </main>

            {{-- =========================================================
                 RIGHT: Sidebar
                 ========================================================= --}}
            <aside class="lg:w-1/3 xl:w-1/3 space-y-8">

                {{-- Search (desktop) --}}
                <div class="hidden lg:block bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 transition-all duration-300">
                    <h3 class="text-base font-heading font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-primary-500 text-sm"></i> Cari Berita
                    </h3>
                    <form action="/berita" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Kata kunci..."
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-slate-700 dark:text-slate-300 text-sm">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>
                    </form>
                </div>

                {{-- Categories --}}
                <div class="bg-slate-900 rounded-3xl p-6 shadow-xl shadow-slate-900/15">
                    <h3 class="text-base font-heading font-bold text-primary-400 mb-5 flex items-center gap-2 pb-4 border-b border-white/10">
                        <i class="fa-solid fa-tags text-sm"></i> Kategori
                    </h3>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="/berita"
                               class="flex justify-between items-center py-2 px-3 rounded-xl {{ !request('category') ? 'bg-primary-500/20 text-primary-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }} transition-all duration-200 group active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900">
                                <span class="font-medium text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-layer-group text-[10px]"></i> Semua Berita
                                </span>
                                <span class="bg-white/10 text-[10px] font-black px-2 py-0.5 rounded-md group-hover:bg-primary-500 transition-colors">
                                    {{ $posts->total() }}
                                </span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                        <li>
                            <a href="/berita?category={{ $category->slug }}"
                               class="flex justify-between items-center py-2 px-3 rounded-xl {{ request('category') === $category->slug ? 'bg-primary-500/20 text-primary-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }} transition-all duration-200 group active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900">
                                <span class="font-medium text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-circle text-[6px]"></i>
                                    {{ $category->name }}
                                </span>
                                <span class="bg-white/10 text-[10px] font-black px-2 py-0.5 rounded-md group-hover:bg-primary-500 transition-colors">
                                    {{ $category->posts_count ?? '0' }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Info Box --}}
                <div class="bg-gradient-to-br from-primary-500 to-primary-600 dark:from-slate-900 dark:to-slate-800 dark:border dark:border-slate-800 rounded-3xl p-6 text-white shadow-xl shadow-primary-500/20 dark:shadow-slate-950/50 transition-colors duration-300">
                    <div class="w-12 h-12 rounded-xl bg-white/20 dark:bg-primary-500/20 flex items-center justify-center mb-4 text-white dark:text-primary-400">
                        <i class="fa-solid fa-bullhorn text-xl"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-lg mb-2 text-white dark:text-slate-100">Pengumuman Resmi</h3>
                    <p class="text-primary-100 dark:text-slate-400 text-sm leading-relaxed mb-4">
                        Lihat edaran dan informasi penting dari pemerintah desa.
                    </p>
                    <a href="/pengumuman"
                       class="inline-flex items-center gap-2 bg-white dark:bg-primary-600 text-primary-700 dark:text-white font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-500 transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-white dark:focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-primary-500 dark:focus:ring-offset-slate-900">
                        Lihat Pengumuman <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

            </aside>
        </div>
    </div>
</div>

@endsection
