@extends('layouts.app')

@section('title', 'Profil Desa | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Profil lengkap sejarah pembentukan, letak geografis, visi dan misi penyelenggaraan pemerintahan Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@section('content')

{{-- ===================== HERO ===================== --}}
<div class="relative bg-slate-900 dark:bg-slate-950 py-16 md:py-24 lg:py-28 overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-500"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl transition-colors duration-500"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 dark:bg-primary-600/5 rounded-full blur-3xl transition-colors duration-500"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Profil</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Profil <span class="text-primary-500 italic">Desa</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Visi, misi, sejarah, dan profil wilayah Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== SECTION 1: SEJARAH DESA ===================== --}}
<section class="bg-white dark:bg-slate-900 py-16 md:py-20 lg:py-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h2 class="text-3xl md:text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Sejarah Desa</h2>
        </div>
        <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed font-medium">
            @if(!empty(trim(strip_tags($site_settings['village_history'] ?? ''))))
                {!! $site_settings['village_history'] !!}
            @else
                <x-empty-state 
                    icon="fa-solid fa-clock-rotate-left" 
                    title="Sejarah Belum Tersedia" 
                    description="Informasi sejarah desa belum diisi oleh pengelola." 
                />
            @endif
        </div>
    </div>
</section>

{{-- ===================== SECTION 2: VISI & MISI ===================== --}}
<section class="bg-slate-50 dark:bg-slate-950 py-16 md:py-20 lg:py-28 border-y border-slate-200 dark:border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl md:text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Visi &amp; Misi</h2>
        </div>

        {{-- Visi Card --}}
        @if(!empty(trim(strip_tags($site_settings['village_vision'] ?? ''))))
        <div class="relative bg-gradient-to-br from-primary-600 to-primary-800 rounded-3xl p-10 md:p-16 text-white mb-10 shadow-2xl shadow-primary-200/60 overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i class="fa-solid fa-eye text-white"></i>
                    </div>
                    <span class="text-primary-200 font-black text-[11px] uppercase tracking-widest">Visi</span>
                </div>
                <p class="text-2xl md:text-3xl font-heading font-extrabold italic leading-relaxed">
                    "{{ $site_settings['village_vision'] }}"
                </p>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 md:p-16 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <span class="text-slate-500 dark:text-slate-400 font-black text-[11px] uppercase tracking-widest">Visi</span>
            </div>
            <x-empty-state 
                icon="fa-solid fa-eye" 
                title="Visi Belum Tersedia" 
                description="Informasi visi desa belum diisi oleh pengelola." 
            />
        </div>
        @endif

        {{-- Misi Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 md:p-16 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <span class="text-slate-500 dark:text-slate-400 font-black text-[11px] uppercase tracking-widest">Misi</span>
            </div>
            <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 font-medium">
                @if(!empty(trim(strip_tags($site_settings['village_mission'] ?? ''))))
                    {!! $site_settings['village_mission'] !!}
                @else
                    <x-empty-state 
                        icon="fa-solid fa-list-check" 
                        title="Misi Belum Tersedia" 
                        description="Informasi misi desa belum diisi oleh pengelola." 
                    />
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ===================== SECTION 3: KARAKTERISTIK WILAYAH ===================== --}}
<section class="bg-white dark:bg-slate-900 py-16 md:py-20 lg:py-28">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <h2 class="text-3xl md:text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Karakteristik Wilayah</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            {{-- Luas Wilayah --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 text-center">
                <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xl mx-auto mb-5">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Luas Wilayah</p>
                <p class="text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">{{ $site_settings['village_area'] ?? '—' }}</p>
                <p class="text-slate-400 dark:text-slate-500 font-bold text-sm mt-1">{{ $site_settings['village_area_unit'] ?? 'km²' }}</p>
            </div>

            {{-- Populasi --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 text-center">
                <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xl mx-auto mb-5">
                    <i class="fa-solid fa-users"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Populasi</p>
                <p class="text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">{{ number_format($totalPenduduk ?? 0, 0, ',', '.') }}</p>
                <p class="text-slate-400 dark:text-slate-500 font-bold text-sm mt-1">Jiwa</p>
            </div>

            {{-- Jumlah Dusun --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 text-center">
                <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xl mx-auto mb-5">
                    <i class="fa-solid fa-map-pin"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Jumlah Dusun</p>
                <p class="text-4xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">{{ number_format($totalDusun ?? 0, 0, ',', '.') }}</p>
                <p class="text-slate-400 dark:text-slate-500 font-bold text-xs mt-1">{{ number_format($totalRw ?? 0, 0, ',', '.') }} RW / {{ number_format($totalRt ?? 0, 0, ',', '.') }} RT</p>
            </div>

            {{-- Topografi --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 text-center">
                <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xl mx-auto mb-5">
                    <i class="fa-solid fa-mountain-sun"></i>
                </div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Topografi</p>
                <p class="text-xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mt-2.5 line-clamp-1" title="{{ $site_settings['village_topography'] ?? '—' }}">{{ $site_settings['village_topography'] ?? '—' }}</p>
                <p class="text-slate-400 dark:text-slate-500 font-bold text-sm mt-1">Wilayah</p>
            </div>
        </div>


    </div>
</section>

@endsection
