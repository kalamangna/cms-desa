@extends('layouts.app')

@section('title', 'Layanan - ' . ($site_settings['village_name'] ?? 'Website Desa'))
@section('meta_description', 'Layanan administrasi dan publik Desa ' . ($site_settings['village_name'] ?? '') . '. Temukan prosedur, persyaratan, dan cara pengajuan layanan.')

@section('content')
{{-- Wrapper with Alpine JS state --}}
<div x-data="{ activeService: null }" class="relative">

    {{-- ===================== HERO ===================== --}}
    <div class="relative bg-slate-900 py-24 md:py-36 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
            <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
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
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-emerald-400 text-xs font-bold uppercase tracking-widest">Pelayanan Publik</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                    Layanan <span class="text-emerald-500 italic">Masyarakat</span>
                </h1>
                <p class="text-slate-300 text-lg mt-2">
                    Prosedur dan panduan administrasi Desa {{ $site_settings['village_name'] ?? '' }}.
                </p>
            </div>
        </div>
    </div>

    {{-- ===================== SERVICES GRID ===================== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">

        {{-- Section Header --}}
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-3 mb-4"><div class="h-px w-8 bg-emerald-500"></div><span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Apa yang Bisa Kami Bantu?</span><div class="h-px w-8 bg-emerald-500"></div></div>
            <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900">Daftar Layanan Desa</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @forelse($services as $service)
            <div class="group bg-white rounded-[32px] border border-slate-100 shadow-lg shadow-slate-200/50 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-100/60 hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden">

                {{-- Card Top --}}
                <div class="p-8 md:p-10 flex-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fa-solid {{ $service->icon ?? 'fa-file-alt' }}"></i>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-3">{{ $service->title }}</h3>

                    {{-- Description --}}
                    <p class="text-slate-500 text-sm leading-relaxed font-medium mb-6">
                        {{ Str::limit($service->description, 120) }}
                    </p>

                    {{-- Collapsible Requirements Preview --}}
                    <div x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-emerald-600 transition mb-0">
                            <i class="fa-solid fa-list-ul text-[10px]"></i>
                            Persyaratan
                            <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-3 prose prose-sm prose-emerald max-w-none text-slate-500 bg-slate-50 rounded-2xl p-4 text-xs leading-relaxed"
                            x-cloak>
                            <div>{!! $service->requirements ?? '<p class="text-slate-400 italic">Persyaratan belum diisi.</p>' !!}</div>
                        </div>
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="border-t border-slate-100 px-8 md:px-10 py-5 flex items-center justify-between gap-4 bg-slate-50/50">
                    <button
                        @click="activeService = {{ json_encode($service) }}"
                        class="text-sm font-bold text-slate-500 hover:text-emerald-600 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-xs"></i>
                        Detail
                    </button>
                    <a href="/kontak"
                       class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-full font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-200">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        Ajukan
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-24 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-200 shadow-sm">
                <i class="fa-solid fa-clipboard-list text-slate-200 text-6xl mb-6"></i>
                <p class="text-slate-400 font-medium italic">Belum ada layanan yang ditambahkan saat ini.</p>
            </div>
            @endforelse
        </div>

        {{-- ===================== INFO BANNER ===================== --}}
        <div class="mt-20 md:mt-28 bg-slate-900 rounded-[40px] md:rounded-[56px] p-10 md:p-20 text-white relative overflow-hidden shadow-2xl shadow-slate-900/20">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/5 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="max-w-2xl text-center md:text-left">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl mb-6 mx-auto md:mx-0">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-extrabold mb-4">Butuh Bantuan Lainnya?</h2>
                    <p class="text-slate-400 text-lg leading-relaxed">
                        Jika layanan yang Anda cari tidak tersedia di atas, silakan hubungi petugas administrasi kami atau datang langsung ke Kantor Desa pada jam operasional.
                    </p>
                </div>
                <a href="/kontak" class="flex-shrink-0 bg-emerald-600 text-white px-12 py-5 rounded-full font-bold text-lg hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/40 whitespace-nowrap flex items-center gap-3">
                    <i class="fa-solid fa-phone"></i>
                    Hubungi Petugas
                </a>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL OVERLAY ===================== --}}
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

        {{-- Modal Card --}}
        <div x-show="activeService"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-[40px] shadow-2xl p-8 md:p-12 max-w-2xl w-full max-h-[85vh] overflow-y-auto border border-slate-100 relative">

            {{-- Close Button --}}
            <button @click="activeService = null" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900 transition duration-300 w-10 h-10 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            {{-- Icon & Title --}}
            <div class="flex items-center gap-6 mb-8 pr-12">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm">
                    <i :class="'fa-solid ' + (activeService ? (activeService.icon ?? 'fa-file-alt') : '')"></i>
                </div>
                <div>
                    <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900" x-text="activeService ? activeService.title : ''"></h3>
                    <p class="text-slate-400 text-xs mt-1 uppercase tracking-wider font-bold">Persyaratan &amp; Prosedur</p>
                </div>
            </div>

            {{-- Description --}}
            <p class="text-slate-500 text-sm leading-relaxed font-medium mb-6 pb-6 border-b border-slate-100" x-text="activeService ? activeService.description : ''"></p>

            {{-- Requirements --}}
            <div class="prose prose-emerald max-w-none text-slate-600 leading-relaxed font-medium mb-10 text-sm md:text-base">
                <div x-html="activeService ? (activeService.requirements || '<p class=\'text-slate-400 italic\'>Persyaratan belum diisi.</p>') : ''"></div>
            </div>

            {{-- Modal Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-100">
                <a href="/kontak" class="flex-1 flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3.5 rounded-full font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                    <i class="fa-solid fa-paper-plane"></i>
                    Ajukan Layanan Ini
                </a>
                <button @click="activeService = null" class="flex-1 flex items-center justify-center gap-2 bg-slate-100 text-slate-700 px-6 py-3.5 rounded-full font-bold text-sm hover:bg-slate-200 transition">
                    <i class="fa-solid fa-xmark"></i>
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
