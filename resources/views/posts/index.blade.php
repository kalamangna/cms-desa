@extends('layouts.app')

@section('title', 'Kabar Desa - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
<!-- Standardized Dark Hero -->
<div class="relative bg-slate-900 py-32 overflow-hidden">
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
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-white">Kabar Desa</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Warta <span class="text-emerald-500 italic">Terkini</span>
            </h1>
            <p class="text-xl text-slate-400 leading-relaxed font-medium">
                Informasi mengenai program kerja, pembangunan, dan kegiatan kemasyarakatan Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
    <div class="flex flex-col lg:flex-row gap-20">
        <!-- Main Content -->
        <div class="lg:w-2/3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                @forelse($posts as $post)
                <article class="group">
                    <div class="relative rounded-[40px] overflow-hidden aspect-[16/10] mb-8 shadow-lg shadow-slate-200/50">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="{{ $post->title }}">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">No Image</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                        <div class="absolute top-6 left-6">
                            <span class="bg-white/90 backdrop-blur-md text-emerald-600 text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                                {{ $post->category->name ?? 'Berita' }}
                            </span>
                        </div>
                    </div>
                    <div class="px-2">
                        <time class="text-emerald-600 font-bold text-xs uppercase tracking-widest mb-4 block">{{ $post->published_at->format('d M Y') }}</time>
                        <h2 class="text-2xl font-heading font-bold text-slate-900 leading-tight mb-4 group-hover:text-emerald-600 transition">
                            <a href="/berita/{{ $post->slug }}">{{ $post->title }}</a>
                        </h2>
                        <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-6">
                            {{ strip_tags($post->content) }}
                        </p>
                        <a href="/berita/{{ $post->slug }}" class="inline-flex items-center gap-2 font-bold text-slate-900 hover:text-emerald-600 transition group/link">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 group-hover/link:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </article>
                @empty
                <div class="col-span-full py-20 text-center bg-slate-50 rounded-[40px] border-2 border-dashed border-slate-200">
                    <p class="text-slate-400 font-medium italic">Belum ada berita yang dipublikasikan saat ini.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-20">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-1/3 space-y-12">
            <!-- Search -->
            <div class="bg-white p-10 rounded-[40px] shadow-2xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-xl font-heading font-bold text-slate-900 mb-6">Cari Berita</h3>
                <form action="/berita" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Kata kunci..." class="w-full pl-12 pr-4 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400 absolute left-4 top-4.5" />
                </form>
            </div>

            <!-- Categories -->
            <div class="bg-slate-900 p-10 rounded-[40px] text-white shadow-2xl shadow-slate-900/20">
                <h3 class="text-xl font-heading font-bold mb-8 text-emerald-400 border-b border-white/10 pb-4">Kategori</h3>
                <ul class="space-y-4">
                    @foreach($categories as $category)
                    <li>
                        <a href="/berita?category={{ $category->slug }}" class="flex justify-between items-center group">
                            <span class="text-slate-300 group-hover:text-emerald-400 transition">{{ $category->name }}</span>
                            <span class="bg-white/10 text-[10px] font-black px-2.5 py-1 rounded-lg group-hover:bg-emerald-500 transition">
                                {{ $category->posts_count ?? '0' }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
</div>
@endsection
