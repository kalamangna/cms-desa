@extends('layouts.app')

@section('title', 'Profil - ' . ($site_settings['village_name'] ?? 'Website Desa'))
@section('meta_description', 'Profil lengkap Desa ' . ($site_settings['village_name'] ?? '') . ': sejarah, visi misi, dan karakteristik wilayah.')

@section('content')

{{-- ===================== HERO ===================== --}}
<div class="relative bg-slate-900 py-20 md:py-32 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        {{-- Decorative orbs --}}
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-8 right-1/3 w-64 h-64 bg-emerald-400/5 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-400 transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
                        <span class="text-white">Profil</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-4 py-1.5 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-emerald-400 text-xs font-bold uppercase tracking-widest">Tentang Desa</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Profil <span class="text-emerald-500 italic">Desa</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Sejarah, visi, misi, dan karakteristik wilayah Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== TAB NAVIGATION + CONTENT ===================== --}}
<div x-data="{ activeTab: 'sejarah' }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">

    {{-- Tab Pills --}}
    <div class="flex flex-wrap gap-3 mb-16 border-b border-slate-200 pb-0" role="tablist">
        <button
            @click="activeTab = 'sejarah'"
            :class="activeTab === 'sejarah'
                ? 'text-emerald-600 border-b-2 border-emerald-600 bg-emerald-50'
                : 'text-slate-500 border-b-2 border-transparent hover:text-slate-800 hover:bg-slate-50'"
            class="flex items-center gap-2 px-6 py-4 font-bold text-sm transition-all duration-200 rounded-t-xl focus:outline-none"
            role="tab" id="tab-sejarah" aria-controls="panel-sejarah"
            :aria-selected="activeTab === 'sejarah'">
            <i class="fa-solid fa-landmark text-xs"></i>
            Sejarah Desa
        </button>
        <button
            @click="activeTab = 'visimisi'"
            :class="activeTab === 'visimisi'
                ? 'text-emerald-600 border-b-2 border-emerald-600 bg-emerald-50'
                : 'text-slate-500 border-b-2 border-transparent hover:text-slate-800 hover:bg-slate-50'"
            class="flex items-center gap-2 px-6 py-4 font-bold text-sm transition-all duration-200 rounded-t-xl focus:outline-none"
            role="tab" id="tab-visimisi" aria-controls="panel-visimisi"
            :aria-selected="activeTab === 'visimisi'">
            <i class="fa-solid fa-bullseye text-xs"></i>
            Visi &amp; Misi
        </button>
        <button
            @click="activeTab = 'karakteristik'"
            :class="activeTab === 'karakteristik'
                ? 'text-emerald-600 border-b-2 border-emerald-600 bg-emerald-50'
                : 'text-slate-500 border-b-2 border-transparent hover:text-slate-800 hover:bg-slate-50'"
            class="flex items-center gap-2 px-6 py-4 font-bold text-sm transition-all duration-200 rounded-t-xl focus:outline-none"
            role="tab" id="tab-karakteristik" aria-controls="panel-karakteristik"
            :aria-selected="activeTab === 'karakteristik'">
            <i class="fa-solid fa-map-location-dot text-xs"></i>
            Karakteristik Wilayah
        </button>
    </div>

    {{-- ======= PANEL: SEJARAH (Vertical Timeline) ======= --}}
    <div x-show="activeTab === 'sejarah'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         id="panel-sejarah" role="tabpanel" aria-labelledby="tab-sejarah">

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-16">
            {{-- Sejarah prose --}}
            <div class="lg:col-span-3">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Asal Usul</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-8">Sejarah Desa</h2>
                <div class="prose prose-emerald max-w-none text-slate-600 leading-relaxed font-medium mb-16">
                    {!! $site_settings['village_history'] ?? '<p>Sejarah Desa ' . ($site_settings['village_name'] ?? '') . ' berawal dari pemukiman tradisional yang kaya akan nilai budaya dan gotong royong. Selama berpuluh-puluh tahun, desa ini terus berkembang menjadi pusat kegiatan ekonomi dan sosial bagi masyarakat sekitarnya.</p><p>Hingga saat ini, nilai-nilai luhur tersebut tetap dijaga sambil terus melakukan inovasi dalam pelayanan publik dan pembangunan berbasis data statistik yang presisi.</p>' !!}
                </div>
            </div>

            {{-- Vertical Timeline --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Milestone</span>
                </div>
                <h3 class="text-2xl font-heading font-extrabold text-slate-900 mb-8">Perjalanan Desa</h3>
                <div class="relative">
                    {{-- Vertical line --}}
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gradient-to-b from-emerald-500 via-emerald-300 to-slate-200 rounded-full"></div>

                    <div class="space-y-10 pl-16">
                        <div class="relative">
                            <div class="absolute -left-11 top-1 w-10 h-10 rounded-full bg-emerald-600 border-4 border-white shadow-lg shadow-emerald-200 flex items-center justify-center">
                                <i class="fa-solid fa-flag text-white text-xs"></i>
                            </div>
                            <p class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-1">Pendirian</p>
                            <h4 class="font-heading font-bold text-slate-800 text-lg">Pembentukan Desa</h4>
                            <p class="text-slate-500 text-sm leading-relaxed mt-1">Desa {{ $site_settings['village_name'] ?? '' }} resmi berdiri sebagai satuan wilayah administratif.</p>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-11 top-1 w-10 h-10 rounded-full bg-white border-4 border-emerald-400 shadow-md flex items-center justify-center">
                                <i class="fa-solid fa-seedling text-emerald-600 text-xs"></i>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Perkembangan</p>
                            <h4 class="font-heading font-bold text-slate-800 text-lg">Era Pembangunan</h4>
                            <p class="text-slate-500 text-sm leading-relaxed mt-1">Infrastruktur dan pelayanan publik terus ditingkatkan untuk kesejahteraan warga.</p>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-11 top-1 w-10 h-10 rounded-full bg-white border-4 border-emerald-400 shadow-md flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-emerald-600 text-xs"></i>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Inovasi</p>
                            <h4 class="font-heading font-bold text-slate-800 text-lg">Digitalisasi &amp; Data</h4>
                            <p class="text-slate-500 text-sm leading-relaxed mt-1">Menerapkan sistem informasi desa berbasis data statistik (Cantik) untuk transparansi.</p>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-11 top-1 w-10 h-10 rounded-full bg-emerald-50 border-4 border-emerald-300 shadow-md flex items-center justify-center">
                                <i class="fa-solid fa-star text-emerald-500 text-xs"></i>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Kini</p>
                            <h4 class="font-heading font-bold text-slate-800 text-lg">Desa Mandiri</h4>
                            <p class="text-slate-500 text-sm leading-relaxed mt-1">Terus bergerak menuju desa yang cerdas, mandiri, dan sejahtera.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= PANEL: VISI MISI ======= --}}
    <div x-show="activeTab === 'visimisi'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-cloak
         id="panel-visimisi" role="tabpanel" aria-labelledby="tab-visimisi">

        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-3 mb-4 justify-center">
                <div class="h-px w-8 bg-emerald-500"></div>
                <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Arah Pembangunan</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-12 text-center">Visi &amp; Misi</h2>

            {{-- Visi Card --}}
            <div class="relative bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-[40px] p-10 md:p-16 text-white mb-10 shadow-2xl shadow-emerald-200/60 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-eye text-white"></i>
                        </div>
                        <span class="text-emerald-200 font-black text-[11px] uppercase tracking-[0.3em]">Visi</span>
                    </div>
                    <p class="text-xl md:text-2xl font-heading font-bold italic leading-relaxed">
                        "{{ $site_settings['village_vision'] ?? 'Mewujudkan Desa Mandiri, Cerdas, dan Berbasis Data untuk Kesejahteraan Masyarakat.' }}"
                    </p>
                </div>
            </div>

            {{-- Misi --}}
            <div class="bg-slate-50 rounded-[40px] p-10 md:p-16 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <span class="text-slate-500 font-black text-[11px] uppercase tracking-[0.3em]">Misi</span>
                </div>
                <div class="prose prose-emerald max-w-none text-slate-600 font-medium">
                    {!! $site_settings['village_mission'] ?? '<ul><li>Meningkatkan kualitas pelayanan publik berbasis teknologi informasi.</li><li>Mendorong kemandirian ekonomi desa melalui pemberdayaan UMKM.</li><li>Meningkatkan kualitas sumber daya manusia melalui pendidikan dan pelatihan.</li><li>Mengoptimalkan pengelolaan data desa yang akurat dan transparan.</li></ul>' !!}
                </div>
            </div>
        </div>
    </div>

    {{-- ======= PANEL: KARAKTERISTIK ======= --}}
    <div x-show="activeTab === 'karakteristik'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-cloak
         id="panel-karakteristik" role="tabpanel" aria-labelledby="tab-karakteristik">

        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-3 mb-4 justify-center">
                <div class="h-px w-8 bg-emerald-500"></div>
                <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Data Wilayah</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-12 text-center">Karakteristik Wilayah</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                {{-- Luas Wilayah --}}
                <div class="group bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Luas Wilayah</p>
                    <p class="text-4xl font-heading font-extrabold text-slate-900">{{ $site_settings['village_area'] ?? '12.4' }}</p>
                    <p class="text-slate-400 font-bold text-sm mt-1">km²</p>
                </div>

                {{-- Populasi --}}
                <div class="group bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Populasi</p>
                    <p class="text-4xl font-heading font-extrabold text-slate-900">{{ $site_settings['village_population'] ?? '3.500' }}</p>
                    <p class="text-slate-400 font-bold text-sm mt-1">Jiwa</p>
                </div>

                {{-- Topografi --}}
                <div class="group bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-mountain-sun"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Topografi</p>
                    <p class="text-2xl font-heading font-extrabold text-slate-900 mt-2">{{ $site_settings['village_topography'] ?? 'Dataran Tinggi' }}</p>
                    <p class="text-slate-400 font-bold text-sm mt-1">Wilayah</p>
                </div>
            </div>

            {{-- CTA to Statistik --}}
            <div class="bg-slate-900 rounded-[40px] p-10 md:p-16 text-white text-center relative overflow-hidden shadow-2xl shadow-slate-900/20">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
                <div class="relative z-10">
                    <i class="fa-solid fa-chart-bar text-emerald-400 text-4xl mb-6"></i>
                    <h3 class="text-2xl md:text-3xl font-heading font-extrabold mb-4">Data Statistik Lengkap</h3>
                    <p class="text-slate-400 leading-relaxed mb-8 max-w-xl mx-auto">Lihat data kependudukan, ekonomi, dan pembangunan desa yang lebih komprehensif di halaman statistik kami.</p>
                    <a href="/statistik" class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-4 rounded-2xl transition shadow-xl shadow-emerald-900/40">
                        <i class="fa-solid fa-chart-line"></i>
                        Lihat Statistik Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
