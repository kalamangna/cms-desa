@extends('layouts.app')

@section('title', 'Layanan - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
<!-- Wrapper with Alpine JS state -->
<div x-data="{ activeService: null }" class="relative">

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
                            <span class="text-white">Layanan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="max-w-3xl text-center md:text-left">
                <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                    Layanan <span class="text-emerald-500 italic">Masyarakat</span>
                </h1>
                <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                    Prosedur dan panduan administrasi Desa {{ $site_settings['village_name'] ?? '' }}.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
            <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col justify-between group hover:border-emerald-500 transition duration-500">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-8 group-hover:bg-emerald-600 group-hover:text-white transition duration-500">
                        <i class="fa-solid {{ $service->icon }}"></i>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-4">{{ $service->title }}</h3>
                    <p class="text-slate-500 text-sm leading-relaxed font-medium mb-8">
                        {{ $service->description }}
                    </p>
                </div>
                <div>
                    <a href="#" @click.prevent="activeService = {{ json_encode($service) }}" class="inline-flex items-center gap-2 text-emerald-600 font-bold text-sm hover:gap-4 transition-all">
                        Lihat Persyaratan <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-medium italic">Belum ada layanan yang ditambahkan saat ini.</p>
            </div>
            @endforelse
        </div>

        <!-- Info Banner -->
        <div class="mt-24 bg-slate-900 rounded-[40px] md:rounded-[60px] p-10 md:p-20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="max-w-2xl text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-heading font-extrabold mb-6">Butuh Bantuan Lainnya?</h2>
                    <p class="text-slate-400 text-lg leading-relaxed">
                        Jika layanan yang Anda cari tidak tersedia di atas, silakan hubungi petugas administrasi kami atau datang langsung ke Kantor Desa pada jam operasional.
                    </p>
                </div>
                <a href="/kontak" class="bg-emerald-600 text-white px-12 py-5 rounded-full font-bold text-lg hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/40 whitespace-nowrap">
                    Hubungi Petugas
                </a>
            </div>
        </div>
    </div>

    <!-- Premium Modal Overlay -->
    <div x-show="activeService" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md" 
         style="display: none;"
         @click.self="activeService = null">
        
        <!-- Modal Card -->
        <div x-show="activeService"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-[40px] shadow-2xl p-8 md:p-12 max-w-2xl w-full max-h-[85vh] overflow-y-auto border border-slate-100 relative">
            
            <!-- Close Button -->
            <button @click="activeService = null" class="absolute top-8 right-8 text-slate-400 hover:text-slate-950 transition duration-300 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- Icon & Title -->
            <div class="flex items-center gap-6 mb-8 pr-12">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i :class="'fa-solid ' + (activeService ? activeService.icon : '')"></i>
                </div>
                <div>
                    <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900" x-text="activeService ? activeService.title : ''"></h3>
                    <p class="text-slate-400 text-xs mt-1 uppercase tracking-wider font-bold">Persyaratan & Prosedur</p>
                </div>
            </div>

            <!-- Content Area -->
            <div class="prose prose-emerald max-w-none text-slate-600 leading-relaxed font-medium mb-10 text-sm md:text-base">
                <div x-html="activeService ? activeService.requirements : '<p class=\'text-slate-400 italic\'>Persyaratan belum diisi.</p>'"></div>
            </div>

            <!-- Modal Action -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button @click="activeService = null" class="bg-slate-900 text-white px-8 py-3.5 rounded-full font-bold text-sm hover:bg-slate-800 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
