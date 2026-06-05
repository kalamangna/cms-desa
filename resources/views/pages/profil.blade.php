@extends('layouts.app')

@section('title', 'Profil - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
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
                        <span class="text-white">Profil</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Profil <span class="text-emerald-500 italic">Desa</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Sejarah, visi, misi, dan karakteristik wilayah Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-16 lg:gap-24">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-24">
            <!-- Sejarah -->
            <section>
                <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.3em] mb-4 block">Asal Usul</span>
                <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-8">Sejarah Desa</h2>
                <div class="prose prose-emerald max-w-none text-slate-600 leading-relaxed font-medium">
                    {!! $site_settings['village_history'] ?? '<p>Sejarah Desa ' . ($site_settings['village_name'] ?? '') . ' berawal dari pemukiman tradisional yang kaya akan nilai budaya dan gotong royong. Selama berpuluh-puluh tahun, desa ini terus berkembang menjadi pusat kegiatan ekonomi dan sosial bagi masyarakat sekitarnya.</p><p>Hingga saat ini, nilai-nilai luhur tersebut tetap dijaga sambil terus melakukan inovasi dalam pelayanan publik dan pembangunan berbasis data statistik yang presisi.</p>' !!}
                </div>
            </section>

            <!-- Visi Misi -->
            <section class="bg-slate-50 rounded-[40px] md:rounded-[60px] p-8 md:p-16 border border-slate-100 shadow-sm">
                <div class="mb-12">
                    <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.3em] mb-4 block">Arah Pembangunan</span>
                    <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-6">Visi & Misi</h2>
                    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm mb-12">
                        <p class="text-xl font-heading font-bold text-slate-900 text-center italic">
                            "{{ $site_settings['village_vision'] ?? 'Mewujudkan Desa Mandiri, Cerdas, dan Berbasis Data untuk Kesejahteraan Masyarakat.' }}"
                        </p>
                    </div>
                    <div class="prose prose-emerald max-w-none text-slate-600 font-medium">
                        {!! $site_settings['village_mission'] ?? '<ul><li>Meningkatkan kualitas pelayanan publik berbasis teknologi informasi.</li><li>Mendorong kemandirian ekonomi desa melalui pemberdayaan UMKM.</li><li>Meningkatkan kualitas sumber daya manusia melalui pendidikan dan pelatihan.</li><li>Mengoptimalkan pengelolaan data desa yang akurat dan transparan.</li></ul>' !!}
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar Info -->
        <aside class="space-y-12">
            <div class="bg-slate-900 rounded-[40px] p-10 text-white shadow-2xl shadow-slate-900/20 sticky top-32">
                <h3 class="text-xl font-heading font-bold mb-8 text-emerald-400 border-b border-white/10 pb-4">Karakteristik Wilayah</h3>
                <ul class="space-y-8">
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center flex-shrink-0 text-emerald-400">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Luas Wilayah</p>
                            <p class="text-lg font-bold">{{ $site_settings['village_area'] ?? '12.4' }} km²</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center flex-shrink-0 text-emerald-400">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Populasi</p>
                            <p class="text-lg font-bold">~{{ $site_settings['village_population'] ?? '3.500' }} Jiwa</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center flex-shrink-0 text-emerald-400">
                            <i class="fa-solid fa-mountain-sun"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Topografi</p>
                            <p class="text-lg font-bold">{{ $site_settings['village_topography'] ?? 'Dataran Tinggi' }}</p>
                        </div>
                    </li>
                </ul>
                <div class="mt-12 pt-8 border-t border-white/10">
                    <a href="/statistik" class="flex items-center justify-center gap-3 bg-emerald-600 text-white py-4 rounded-2xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-900/40">
                        Lihat Statistik Detail
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
