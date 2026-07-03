@extends('layouts.app')

@section('content')
{{-- 1. HERO --}}
<div class="relative bg-slate-900 min-h-[90vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1500382017468-9049fee74a62?auto=format&fit=crop&q=80&w=2000"
             class="w-full h-full object-cover opacity-20" alt="Hero">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/80 to-slate-900/30"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,_var(--tw-gradient-stops))] from-emerald-900/30 via-transparent to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-40 w-full">
        <div class="max-w-4xl">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-px w-12 bg-emerald-500"></div>
                <span class="text-emerald-400 text-xs font-black uppercase tracking-[0.3em]">Portal Resmi Pemerintah Desa</span>
            </div>
            <h1 class="text-6xl md:text-8xl lg:text-9xl font-heading font-extrabold text-white leading-[1.05] mb-8">
                Desa<br><span class="text-emerald-500 italic">{{ $site_settings['village_name'] ?? 'Tompobulu' }}</span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-300 mb-4 font-medium leading-relaxed max-w-2xl">
                Kecamatan {{ \Illuminate\Support\Str::title($site_settings['district_name'] ?? '...') }},
                Kabupaten {{ \Illuminate\Support\Str::title(preg_replace('/^Kabupaten\s+/i', '', $site_settings['regency_name'] ?? '...')) }},
                Provinsi {{ \Illuminate\Support\Str::title($site_settings['province_name'] ?? '...') }}
            </p>
            <p class="text-slate-400 text-base mb-14 max-w-xl">
                Pemerintahan yang transparan, akuntabel, dan berbasis data presisi untuk kemajuan seluruh warga desa.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="/statistik" class="group inline-flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base px-8 py-4 rounded-2xl shadow-xl shadow-emerald-900/40 transition-all duration-300 hover:-translate-y-0.5">
                    <i class="fa-solid fa-chart-pie group-hover:rotate-12 transition-transform"></i>
                    Dashboard Statistik
                </a>
                <a href="/layanan" class="group inline-flex items-center justify-center gap-3 bg-white/10 backdrop-blur-md hover:bg-white/20 text-white font-bold text-base px-8 py-4 rounded-2xl border border-white/20 transition-all duration-300 hover:-translate-y-0.5">
                    <i class="fa-solid fa-file-signature"></i>
                    Layanan Mandiri
                </a>
            </div>
        </div>
    </div>
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2 text-slate-500">
        <span class="text-[9px] uppercase tracking-widest font-bold">Scroll</span>
        <div class="w-px h-8 bg-gradient-to-b from-slate-500 to-transparent animate-pulse"></div>
    </div>
</div>

{{-- 2. STAT CARDS --}}
<div class="relative z-20 -mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-2xl shadow-slate-200/60 border border-slate-100 flex items-center gap-4 group hover:border-emerald-400 hover:-translate-y-1 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-0.5">Penduduk</p>
                <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 leading-none">{{ number_format($totalPenduduk, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Jiwa Aktif</p>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-2xl shadow-slate-200/60 border border-slate-100 flex items-center gap-4 group hover:border-sky-400 hover:-translate-y-1 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                <i class="fa-solid fa-house-chimney text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-0.5">Keluarga</p>
                <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 leading-none">{{ number_format(\App\Models\Family::count(), 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Kepala Keluarga</p>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-2xl shadow-slate-200/60 border border-slate-100 flex items-center gap-4 group hover:border-amber-400 hover:-translate-y-1 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                <i class="fa-solid fa-map-location-dot text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-0.5">Dusun</p>
                <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 leading-none">{{ $totalDusun ?: ($site_settings['village_dusun_count'] ?? '6') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Wilayah</p>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-2xl shadow-slate-200/60 border border-slate-100 flex items-center gap-4 group hover:border-violet-400 hover:-translate-y-1 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center text-violet-600 group-hover:bg-violet-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                <i class="fa-solid fa-ruler-combined text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-0.5">Luas Wilayah</p>
                <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 leading-none">{{ $site_settings['village_area'] ?? '12,4' }}</h3>
                <p class="text-xs text-slate-400 mt-1">Km²</p>
            </div>
        </div>
    </div>
</div>

{{-- 3. SAMBUTAN KEPALA DESA --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36">
    <div class="bg-white rounded-[40px] shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="flex flex-col lg:flex-row items-stretch">
            <div class="lg:w-5/12 p-4 lg:p-6 flex-shrink-0">
                @if($villageHead && $villageHead->photo)
                    <img src="{{ asset('storage/' . $villageHead->photo) }}"
                          class="rounded-[32px] w-full h-[320px] md:h-full max-h-[560px] object-cover object-top" alt="Kepala Desa">
                @else
                    <img src="{{ asset('img/meta.png') }}"
                          class="rounded-[32px] w-full h-[320px] md:h-full max-h-[560px] object-cover object-top animate-pulse" alt="Kepala Desa">
                @endif
            </div>
            <div class="lg:w-7/12 p-8 md:p-12 lg:p-16 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Sambutan Kepala Desa</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-8 leading-tight">
                    {{ $site_settings['village_head_greeting_title'] ?? 'Mewujudkan Desa Mandiri Berbasis Data Presisi' }}
                </h2>
                <blockquote class="text-slate-500 text-base md:text-lg leading-relaxed italic mb-10 border-l-4 border-emerald-500 pl-6">
                    {!! $site_settings['village_head_greeting'] ?? 'Selamat datang di portal resmi Desa ' . ($site_settings['village_name'] ?? '') . '. Kami berkomitmen untuk menghadirkan pemerintahan yang transparan dan akuntabel.' !!}
                </blockquote>
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600 flex items-center justify-center text-white flex-shrink-0">
                        <i class="fa-solid fa-signature text-xl"></i>
                    </div>
                    <div>
                        <p class="font-heading font-bold text-lg text-slate-900">{{ $villageHead->name ?? ($site_settings['village_head'] ?? 'Nama Kepala Desa') }}</p>
                        <p class="text-slate-500 text-sm font-medium">Kepala Desa {{ $site_settings['village_name'] ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. DATA DEMOGRAFI --}}
<div class="bg-slate-50 py-24 md:py-36">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Transparansi Data</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-heading font-extrabold text-slate-900">Grafik <span class="text-emerald-600">Kependudukan</span> Desa</h2>
            </div>
            <a href="/statistik" class="inline-flex items-center gap-2 font-bold text-emerald-600 hover:text-emerald-700 transition text-sm group">
                Lihat Statistik Lengkap <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Grafik Demografi Penduduk --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-heading font-extrabold text-xl text-slate-900">Demografi Penduduk</h3>
                        <p class="text-slate-400 text-sm mt-1">Perbandingan jumlah laki-laki dan perempuan aktif</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-4 py-1.5 rounded-full border border-emerald-100">{{ date('Y') }}</span>
                </div>
                <div class="p-8">
                    <div class="h-72"><canvas id="populationChart"></canvas></div>
                    <a href="/statistik" class="mt-6 flex items-center justify-center gap-2 w-full py-3 rounded-2xl bg-slate-50 border border-slate-200 text-sm font-bold text-slate-600 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all duration-200">
                        <i class="fa-solid fa-chart-line"></i> Statistik Lengkap
                    </a>
                </div>
            </div>

            {{-- APBDes --}}
            <div class="bg-slate-900 rounded-3xl text-white overflow-hidden">
                <div class="p-8 border-b border-white/10 flex justify-between items-center">
                    <div>
                        <h3 class="font-heading font-extrabold text-xl text-white">APBDes {{ date('Y') }}</h3>
                        <p class="text-slate-400 text-sm mt-1">Realisasi anggaran desa berjalan</p>
                    </div>
                    <a href="/apbdes" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
                        Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
                <div class="p-8">
                    @php
                        $pendapatanPct = $budgetSummary['pendapatan']['budget'] > 0 ? min(($budgetSummary['pendapatan']['realization'] / $budgetSummary['pendapatan']['budget']) * 100, 100) : 0;
                        $belanjaPct   = $budgetSummary['belanja']['budget'] > 0 ? min(($budgetSummary['belanja']['realization'] / $budgetSummary['belanja']['budget']) * 100, 100) : 0;
                    @endphp
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-emerald-400 text-sm">Total Pendapatan</span>
                            <span class="text-sm font-bold text-white">{{ number_format($pendapatanPct, 1) }}%</span>
                        </div>
                        <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pendapatanPct }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Target: Rp {{ number_format($budgetSummary['pendapatan']['budget'], 0, ',', '.') }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-sky-400 text-sm">Total Belanja</span>
                            <span class="text-sm font-bold text-white">{{ number_format($belanjaPct, 1) }}%</span>
                        </div>
                        <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-sky-500 rounded-full" style="width: {{ $belanjaPct }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Target: Rp {{ number_format($budgetSummary['belanja']['budget'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                        <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4 text-center">Alokasi Belanja Desa</p>
                        <div class="h-52"><canvas id="budgetRingChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 5. BERITA & PENGUMUMAN --}}
<div class="bg-white py-24 md:py-36">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Informasi Terbaru</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-heading font-extrabold text-slate-900">Kabar & <span class="text-emerald-600">Pengumuman</span></h2>
            </div>
            <a href="/berita" class="text-sm font-bold text-slate-600 hover:text-emerald-600 transition flex items-center gap-2 group">
                Semua Berita <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
                @if($featuredPost)
                <a href="/berita/{{ $featuredPost->slug }}" class="block group">
                    <div class="relative rounded-3xl overflow-hidden aspect-[16/9] mb-6 shadow-xl">
                        @if($featuredPost->featured_image)
                            <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $featuredPost->title }}">
                        @else
                            <img src="{{ asset('img/meta.png') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="{{ $featuredPost->title }}">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                        <div class="absolute top-6 left-6">
                            <span class="bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full">Berita Utama</span>
                        </div>
                        <div class="absolute bottom-6 left-6 right-6">
                            <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-2">{{ $featuredPost->published_at->translatedFormat('d M Y') }}</p>
                            <h3 class="text-white font-heading font-extrabold text-xl md:text-2xl leading-snug line-clamp-2 group-hover:text-emerald-300 transition">{{ $featuredPost->title }}</h3>
                        </div>
                    </div>
                </a>
                @endif

                <div class="space-y-4">
                    @forelse($recentPosts as $post)
                    <a href="/berita/{{ $post->slug }}" class="flex gap-5 group items-center p-4 rounded-2xl hover:bg-slate-50 transition-all duration-200 -mx-4">
                        <div class="w-20 h-20 md:w-24 md:h-24 flex-shrink-0 rounded-2xl overflow-hidden bg-slate-100">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $post->title }}">
                            @else
                                <img src="{{ asset('img/meta.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $post->title }}">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-emerald-600 text-[10px] font-bold uppercase tracking-widest mb-1.5">{{ $post->published_at->translatedFormat('d M Y') }}</p>
                            <h4 class="font-heading font-bold text-slate-900 text-base leading-snug group-hover:text-emerald-600 transition line-clamp-2">{{ $post->title }}</h4>
                        </div>
                        <i class="fa-solid fa-arrow-right text-slate-200 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all flex-shrink-0 hidden md:block"></i>
                    </a>
                    @empty
                        @if(!$featuredPost)<p class="text-center text-slate-400 italic py-10">Belum ada berita terbaru.</p>@endif
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-slate-50 border border-slate-200 rounded-3xl p-7 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-heading font-extrabold text-lg text-slate-900">Pengumuman</h3>
                        <a href="/pengumuman" class="text-[10px] font-bold text-emerald-600 hover:underline uppercase tracking-widest">Semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($announcements as $ann)
                        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:border-emerald-300 transition-all duration-200">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fa-solid fa-bullhorn text-amber-600 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ $ann->published_at->translatedFormat('d M Y') }}</p>
                                    <h4 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2">{{ $ann->title }}</h4>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-slate-400 italic py-6 text-sm">Belum ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 6. GALERI & PUBLIKASI --}}
<div class="bg-slate-50 py-24 md:py-36">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Dokumentasi & Arsip</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-heading font-extrabold text-slate-900">Galeri & <span class="text-emerald-600">Publikasi</span></h2>
            </div>
            <div class="flex gap-4 items-center">
                <a href="/galeri" class="text-sm font-bold text-slate-600 hover:text-emerald-600 transition flex items-center gap-2 group">Galeri <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i></a>
                <span class="text-slate-300">|</span>
                <a href="/publikasi" class="text-sm font-bold text-slate-600 hover:text-emerald-600 transition flex items-center gap-2 group">Publikasi <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i></a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Galeri Masonry --}}
            <div class="lg:col-span-7">
                <div class="columns-2 gap-4 space-y-4">
                    @forelse($galleries as $gallery)
                    <div class="relative group overflow-hidden rounded-3xl shadow-md break-inside-avoid hover:-translate-y-1 transition-transform duration-300">
                        <img src="{{ $gallery->image_url }}"
                             class="w-full h-auto object-cover group-hover:scale-110 transition-transform duration-700"
                             alt="{{ $gallery->title }}"
                             onerror="this.src='{{ asset('img/meta.png') }}'">
                        @if($gallery->type === 'video')
                        <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-red-600 flex items-center justify-center shadow-lg">
                            <i class="fa-brands fa-youtube text-white text-xs"></i>
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <p class="text-white font-bold text-sm line-clamp-1">{{ $gallery->title }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-slate-400 italic py-10 col-span-2">Belum ada foto galeri.</p>
                    @endforelse
                </div>
            </div>

            {{-- Publikasi --}}
            <div class="lg:col-span-5">
                <div class="space-y-4">
                    @forelse($publications as $pub)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 transition-all duration-200 overflow-hidden group">
                        <div class="flex items-center gap-5 p-5">
                            <div class="w-16 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100">
                                @if($pub->cover)
                                    <img src="{{ asset('storage/' . $pub->cover) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $pub->title }}">
                                @else
                                    <img src="{{ asset('img/meta.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $pub->title }}">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">{{ $pub->type }} · {{ $pub->year }}</span>
                                <h4 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2 mt-1 mb-3 group-hover:text-emerald-600 transition">{{ $pub->title }}</h4>
                                <a href="{{ $pub->pdf_file ? asset('storage/' . $pub->pdf_file) : '#' }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 transition">
                                    <i class="fa-solid fa-download text-[10px]"></i> Unduh PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl border border-slate-100 p-8 text-center">
                        <i class="fa-solid fa-book-open text-slate-200 text-4xl mb-3"></i>
                        <p class="text-slate-400 italic text-sm">Belum ada publikasi tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Population Chart (Doughnut)
    const ctxPop = document.getElementById('populationChart');
    if (ctxPop) {
        new Chart(ctxPop.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $lakiLakiCount }}, {{ $perempuanCount }}],
                    backgroundColor: ['#0284c7', '#ec4899'],
                    borderWidth: 0,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#64748b',
                            font: { family: 'Inter', size: 12, weight: 'bold' },
                            padding: 20,
                            boxWidth: 12
                        }
                    }
                }
            }
        });
    }

    // Budget Donut
    const ctxBudget = document.getElementById('budgetRingChart');
    if (ctxBudget) {
        @php
            $donutPalette = ['#10b981','#0ea5e9','#f59e0b','#6366f1','#ec4899','#8b5cf6','#06b6d4','#14b8a6','#f97316','#3b82f6'];
            $donutColors = [];
            foreach ($belanjaDetails as $i => $d) { $donutColors[] = $donutPalette[$i % count($donutPalette)]; }
        @endphp
        new Chart(ctxBudget.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [@foreach($belanjaDetails as $d) '{{ Str::limit($d->title, 20) }}', @endforeach],
                datasets: [{ data: [@foreach($belanjaDetails as $d) {{ $d->realization_amount }}, @endforeach],
                    backgroundColor: {!! json_encode($donutColors) !!}, borderWidth: 0, hoverOffset: 14 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '68%',
                plugins: { legend: { position:'bottom', labels: { color:'#94a3b8', font:{ family:'Inter', size:10 }, padding:12, boxWidth:10 } } }
            }
        });
    }
});
</script>
@endpush