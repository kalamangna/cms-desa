@extends('layouts.app')

@section('title', 'Berita | Desa ' . ($site_settings['village_name'] ?? 'Tompobulu'))
@section('meta_description', 'Temukan artikel dan kabar berita kegiatan terbaru, serta program pembangunan Desa ' . ($site_settings['village_name'] ?? 'Tompobulu') . '.')
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
                    <span class="text-white">Berita</span>
                </li>
            </ol>
        </nav>

        {{-- Heading --}}
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Berita <span class="text-emerald-500 italic">Terbaru</span>
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
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

        {{-- ─── Search Bar (Mobile) ──────────────────────────────── --}}
        <div class="mb-10 lg:hidden">
            <form action="/berita" method="GET">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berita..."
                           class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-medium text-slate-700 shadow-sm text-sm">
                </div>
            </form>
        </div>

        <div class="flex flex-col lg:flex-row gap-12 xl:gap-16">

            {{-- =========================================================
                 LEFT: Posts Grid
                 ========================================================= --}}
            <main class="lg:w-2/3 xl:w-[68%]">

                @forelse($posts as $index => $post)

                    {{-- ─── Featured Card (first post) ──────────────────── --}}
                    @if($index === 0)
                    <a href="/berita/{{ $post->slug }}" class="group block mb-10 md:mb-14">
                        <div class="relative rounded-3xl overflow-hidden shadow-xl shadow-slate-200 border border-white">
                            {{-- Image --}}
                            <div class="aspect-[16/8] w-full overflow-hidden bg-slate-200">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                         alt="{{ $post->title }}">
                                @else
                                    <img src="{{ asset('img/meta.png') }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                         alt="{{ $post->title }}">
                                @endif
                            </div>

                            {{-- Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/40 to-transparent"></div>

                            {{-- Featured badge --}}
                            <div class="absolute top-5 left-5 flex gap-2">
                                <span class="bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest px-3.5 py-1.5 rounded-full shadow">
                                    <i class="fa-solid fa-star mr-1"></i> Utama
                                </span>
                                @if($post->category)
                                <span class="bg-white/20 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest px-3.5 py-1.5 rounded-full border border-white/20">
                                    {{ $post->category->name }}
                                </span>
                                @endif
                            </div>

                            {{-- Content overlay --}}
                            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8">
                                <time class="text-emerald-400 text-[10px] font-black uppercase tracking-widest mb-3 block">
                                    <i class="fa-regular fa-calendar mr-1.5"></i>
                                    {{ $post->published_at->translatedFormat('d F Y') }}
                                </time>
                                <h2 class="text-xl md:text-2xl lg:text-3xl font-heading font-extrabold text-white leading-tight group-hover:text-emerald-300 transition-colors duration-300 line-clamp-2 mb-3">
                                    {{ $post->title }}
                                </h2>
                                <p class="text-slate-300 text-sm leading-relaxed line-clamp-2 hidden md:block">
                                    {{ Str::limit(strip_tags($post->content), 160) }}
                                </p>
                                <div class="mt-5 inline-flex items-center gap-2 text-emerald-400 font-bold text-sm group-hover:gap-3 transition-all">
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
                    <article class="group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                        {{-- Thumbnail --}}
                        <a href="/berita/{{ $post->slug }}" class="block relative overflow-hidden aspect-[16/10] bg-slate-100 flex-shrink-0">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     alt="{{ $post->title }}">
                            @else
                                <img src="{{ asset('img/meta.png') }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     alt="{{ $post->title }}">
                            @endif
                            @if($post->category)
                            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-emerald-700 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full shadow-sm">
                                {{ $post->category->name }}
                            </span>
                            @endif
                        </a>

                        {{-- Body --}}
                        <div class="p-5 flex flex-col flex-grow">
                            <time class="text-emerald-600 font-bold text-[10px] uppercase tracking-widest mb-2.5 block">
                                {{ $post->published_at->translatedFormat('d M Y') }}
                            </time>
                            <h2 class="text-base font-heading font-bold text-slate-900 leading-snug mb-3 group-hover:text-emerald-600 transition-colors line-clamp-2 flex-grow">
                                <a href="/berita/{{ $post->slug }}">{{ $post->title }}</a>
                            </h2>
                            <p class="text-slate-500 text-xs leading-relaxed line-clamp-2 mb-4">
                                {{ Str::limit(strip_tags($post->content), 100) }}
                            </p>
                            <a href="/berita/{{ $post->slug }}"
                               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 hover:text-emerald-600 transition-colors group/link mt-auto">
                                Baca <i class="fa-solid fa-arrow-right text-[9px] group-hover/link:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </article>

                    {{-- ─── Close grid wrapper after last post ──────────── --}}
                    @if($loop->last)
                    </div>
                    @endif

                    @endif

                @empty
                {{-- Empty state --}}
                <div class="py-24 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200 shadow-sm">
                    <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-newspaper text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="font-heading font-bold text-slate-700 text-xl mb-2">Belum Ada Berita</h3>
                    <p class="text-slate-400 font-medium text-sm">Belum ada berita yang dipublikasikan saat ini.</p>
                </div>
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
            <aside class="lg:w-1/3 xl:w-[32%] space-y-8">

                {{-- Search (desktop) --}}
                <div class="hidden lg:block bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 transition-all duration-300">
                    <h3 class="text-base font-heading font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-emerald-500 text-sm"></i> Cari Berita
                    </h3>
                    <form action="/berita" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Kata kunci..."
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-medium text-slate-700 text-sm">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        </div>
                    </form>
                </div>

                {{-- Categories --}}
                <div class="bg-slate-900 rounded-3xl p-6 shadow-xl shadow-slate-900/15">
                    <h3 class="text-base font-heading font-bold text-emerald-400 mb-5 flex items-center gap-2 pb-4 border-b border-white/10">
                        <i class="fa-solid fa-tags text-sm"></i> Kategori
                    </h3>
                    <ul class="space-y-2.5">
                        <li>
                            <a href="/berita"
                               class="flex justify-between items-center py-2 px-3 rounded-xl {{ !request('category') ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }} transition-all duration-200 group">
                                <span class="font-medium text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-layer-group text-[10px]"></i> Semua Berita
                                </span>
                                <span class="bg-white/10 text-[10px] font-black px-2 py-0.5 rounded-md group-hover:bg-emerald-500 transition-colors">
                                    {{ $posts->total() }}
                                </span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                        <li>
                            <a href="/berita?category={{ $category->slug }}"
                               class="flex justify-between items-center py-2 px-3 rounded-xl {{ request('category') === $category->slug ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5' }} transition-all duration-200 group">
                                <span class="font-medium text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-circle text-[6px]"></i>
                                    {{ $category->name }}
                                </span>
                                <span class="bg-white/10 text-[10px] font-black px-2 py-0.5 rounded-md group-hover:bg-emerald-500 transition-colors">
                                    {{ $category->posts_count ?? '0' }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Info Box --}}
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-6 text-white shadow-xl shadow-emerald-500/20">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-bullhorn text-xl"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-lg mb-2">Pengumuman Resmi</h3>
                    <p class="text-emerald-100 text-sm leading-relaxed mb-4">
                        Lihat edaran dan informasi penting dari pemerintah desa.
                    </p>
                    <a href="/pengumuman"
                       class="inline-flex items-center gap-2 bg-white text-emerald-700 font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-emerald-50 transition-colors">
                        Lihat Pengumuman <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

            </aside>
        </div>
    </div>
</div>

@endsection
