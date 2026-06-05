@extends('layouts.app')

@section('title', $post->title . ' - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('meta_description', Str::limit(strip_tags($post->content), 160))
@section('meta_image', $post->featured_image ? asset('storage/' . $post->featured_image) : asset('img/sinjai.png'))

@section('content')
<!-- Standardized Dark Hero -->
<div class="relative bg-slate-900 py-20 md:py-32 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}" class="absolute inset-0 w-full h-full object-cover opacity-20 blur-sm" alt="Background">
        @endif
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
                        <a href="/berita" class="hover:text-emerald-400 transition">Berita</a>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-4xl text-center md:text-left">
            <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest mb-6 border border-emerald-500/30">
                {{ $post->category->name ?? 'Update Desa' }}
            </span>
            <h1 class="text-3xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-8">
                {{ $post->title }}
            </h1>
            <div class="flex flex-col md:flex-row items-center md:items-center gap-4 md:gap-6 text-slate-400 text-xs md:text-sm font-bold uppercase tracking-widest">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days w-4 h-4 text-emerald-500"></i>
                    {{ $post->published_at->translatedFormat('d F Y') }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-user w-4 h-4 text-emerald-500"></i>
                    Admin Desa
                </span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 md:-mt-16 mb-20 md:mb-32 relative z-20">
    <article class="bg-white rounded-[40px] md:rounded-[60px] shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        @if($post->featured_image)
            <div class="aspect-[16/9] md:aspect-[21/9] w-full">
                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover" alt="{{ $post->title }}">
            </div>
        @endif
        
        <div class="p-6 md:p-12 lg:p-20">
            <div class="prose prose-emerald max-w-none prose-base md:prose-lg text-slate-600 leading-relaxed font-medium mb-16">
                {!! $post->content !!}
            </div>

            <div class="pt-12 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Bagikan:</span>
                    <div class="flex gap-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition cursor-pointer" title="Bagikan ke Facebook">
                            <i class="fa-brands fa-facebook-f text-lg"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-green-500 hover:text-white transition cursor-pointer" title="Bagikan ke WhatsApp">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener noreferrer" class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition cursor-pointer" title="Bagikan ke X (Twitter)">
                            <i class="fa-brands fa-x-twitter text-lg"></i>
                        </a>
                    </div>
                </div>
                <a href="/berita" class="bg-slate-900 text-white px-8 md:px-10 py-4 rounded-full font-bold text-sm hover:bg-slate-800 transition shadow-xl w-full md:w-auto text-center">
                    &larr; Kembali ke Berita
                </a>
            </div>
        </div>
    </article>
</div>
@endsection
