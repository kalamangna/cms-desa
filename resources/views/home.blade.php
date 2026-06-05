@extends('layouts.app')

@section('content')
<!-- Standardized Home Hero -->
<div class="relative bg-slate-900 min-h-[700px] flex items-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1500382017468-9049fee74a62?auto=format&fit=crop&q=80&w=2000" class="w-full h-full object-cover opacity-30" alt="Village Hero">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/10 to-transparent opacity-50"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
        <div class="max-w-4xl">
            <span class="inline-block px-5 py-2 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.3em] mb-8 border border-emerald-500/30 backdrop-blur-md">
                Program Desa Cantik (Cinta Statistik)
            </span>
            <h1 class="text-6xl md:text-8xl font-heading font-extrabold text-white leading-[1.1] mb-8">
                Desa <span class="text-emerald-500 italic">{{ $site_settings['village_name'] ?? 'Tompobulu' }}</span>
            </h1>
            <p class="text-2xl text-slate-300 mb-12 font-medium max-w-2xl leading-relaxed">
                Kec. {{ $site_settings['district_name'] ?? 'Kecamatan' }}, {{ $site_settings['regency_name'] ?? 'Kabupaten' }}
            </p>
            <div class="flex flex-wrap gap-6">
                <a href="/statistik" class="bg-emerald-600 text-white px-10 py-5 rounded-full font-bold text-lg shadow-2xl shadow-emerald-900/40 hover:bg-emerald-700 transition flex items-center gap-3 group">
                    <i class="fa-solid fa-chart-pie group-hover:rotate-12 transition"></i>
                    Dashboard Statistik
                </a>
                <a href="/dataset" class="bg-white/10 backdrop-blur-xl text-white border border-white/20 px-10 py-5 rounded-full font-bold text-lg hover:bg-white/20 transition flex items-center gap-3">
                    <i class="fa-solid fa-database text-sm"></i>
                    Akses Open Data
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Utama -->
<div class="relative z-20 -mt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-emerald-500 transition duration-300">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Penduduk</p>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900">{{ number_format($totalPenduduk, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Jiwa (Tahun {{ $latestYear }})</p>
            </div>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-sky-500 transition duration-300">
            <div class="w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition duration-300">
                <i class="fa-solid fa-house-chimney text-2xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Dusun</p>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900">{{ $site_settings['village_dusun_count'] ?? '6' }}</h3>
                <p class="text-xs text-slate-400 mt-1">Wilayah</p>
            </div>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-amber-500 transition duration-300">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition duration-300">
                <i class="fa-solid fa-store text-2xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">UMKM</p>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900">{{ $totalUMKM }}</h3>
                <p class="text-xs text-slate-400 mt-1">Unit Usaha</p>
            </div>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-6 group hover:border-emerald-500 transition duration-300">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                <i class="fa-solid fa-map-location-dot text-2xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-1">Luas Wilayah</p>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900">{{ $site_settings['village_area'] ?? '12,4' }}</h3>
                <p class="text-xs text-slate-400 mt-1">Km²</p>
            </div>
        </div>
    </div>
</div>

<!-- Sambutan Kepala Desa -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
    <div class="bg-white rounded-[40px] shadow-2xl shadow-slate-200/60 overflow-hidden border border-slate-100">
        <div class="flex flex-col lg:flex-row items-center">
            <div class="lg:w-2/5 p-4">
                @if($villageHead && $villageHead->photo)
                    <img src="{{ asset('storage/' . $villageHead->photo) }}" class="rounded-[32px] w-full h-[500px] object-cover" alt="Kepala Desa">
                @else
                    <div class="bg-slate-100 rounded-[32px] w-full h-[500px] flex items-center justify-center text-slate-300">
                        <i class="fa-solid fa-user text-6xl opacity-20"></i>
                    </div>
                @endif
            </div>
            <div class="lg:w-3/5 p-12 lg:p-20">
                <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs mb-4 block">Sambutan Kepala Desa</span>
                <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-8 leading-tight">
                    {{ $site_settings['village_head_greeting_title'] ?? 'Mewujudkan Desa Mandiri Berbasis Data Presisi' }}
                </h2>
                <div class="text-lg text-slate-600 leading-relaxed italic mb-10">
                    "{{ $site_settings['village_head_greeting'] ?? 'Selamat datang di portal resmi Desa ' . ($site_settings['village_name'] ?? '') . '. Kami berkomitmen untuk menghadirkan pemerintahan yang transparan dan akuntabel. Melalui Program Desa Cantik (Cinta Statistik), kami berupaya mengelola data desa secara profesional sebagai dasar pembangunan yang tepat sasaran.' }}"
                </div>
                <div class="flex items-center gap-6">
                    <div class="h-16 w-1 bg-emerald-500"></div>
                    <div>
                        <p class="font-heading font-bold text-xl text-slate-900">{{ $villageHead->name ?? 'Kepala Desa' }}</p>
                        <p class="text-slate-500 text-sm font-medium uppercase tracking-wider">Kepala Desa {{ $site_settings['village_name'] ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Peta Desa -->
<div class="bg-white py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-4">Peta Wilayah Desa</h2>
                <p class="text-slate-500 text-lg">Eksplorasi lokasi penting, potensi wisata, dan infrastruktur desa secara interaktif.</p>
            </div>
        </div>

        <div class="rounded-[40px] overflow-hidden shadow-2xl border border-slate-100 h-[600px] relative z-10" id="villageMap">
            <!-- Map Container -->
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
            <div class="bg-emerald-50 p-6 rounded-3xl border border-emerald-100 flex flex-col items-center text-center group hover:bg-emerald-600 transition duration-500">
                <i class="fa-solid fa-school text-2xl mb-3 text-emerald-600 group-hover:text-white transition"></i>
                <span class="font-bold text-slate-900 group-hover:text-white transition">Sekolah</span>
            </div>
            <div class="bg-sky-50 p-6 rounded-3xl border border-sky-100 flex flex-col items-center text-center group hover:bg-sky-600 transition duration-500">
                <i class="fa-solid fa-hospital text-2xl mb-3 text-sky-600 group-hover:text-white transition"></i>
                <span class="font-bold text-slate-900 group-hover:text-white transition">Kesehatan</span>
            </div>
            <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100 flex flex-col items-center text-center group hover:bg-amber-600 transition duration-500">
                <i class="fa-solid fa-mosque text-2xl mb-3 text-amber-600 group-hover:text-white transition"></i>
                <span class="font-bold text-slate-900 group-hover:text-white transition">Ibadah</span>
            </div>
            <div class="bg-rose-50 p-6 rounded-3xl border border-rose-100 flex flex-col items-center text-center group hover:bg-rose-600 transition duration-500">
                <i class="fa-solid fa-building-flag text-2xl mb-3 text-rose-600 group-hover:text-white transition"></i>
                <span class="font-bold text-slate-900 group-hover:text-white transition">Kantor Desa</span>
            </div>
        </div>
    </div>
</div>

<!-- Publikasi Statistik -->
<div class="bg-slate-50 py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-4">Publikasi Statistik</h2>
                <p class="text-slate-500 text-lg">Dokumen analisis data dan profil statistik desa yang dapat diunduh publik.</p>
            </div>
            <a href="/publikasi" class="bg-emerald-600 text-white px-8 py-3 rounded-full font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-900/10">Semua Publikasi</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($publications as $pub)
            <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm hover:shadow-xl transition group">
                <div class="aspect-[3/4] rounded-2xl bg-slate-100 mb-6 overflow-hidden relative">
                    @if($pub->cover_image)
                        <img src="{{ asset('storage/' . $pub->cover_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $pub->title }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 p-8 text-center">
                            <i class="fa-solid fa-book-open text-4xl mb-4 opacity-20"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">Digital Archive</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-emerald-600/10 opacity-0 group-hover:opacity-100 transition"></div>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-2 block">{{ $pub->type }} {{ $pub->year }}</span>
                <h3 class="font-heading font-bold text-slate-900 mb-6 line-clamp-2 group-hover:text-emerald-600 transition">{{ $pub->title }}</h3>
                <a href="{{ $pub->pdf_file ? asset('storage/' . $pub->pdf_file) : '#' }}" class="w-full py-3 bg-slate-50 rounded-xl text-center text-sm font-bold text-slate-600 hover:bg-emerald-600 hover:text-white transition block">
                    Unduh PDF
                </a>
            </div>
            @empty
            <p class="col-span-full text-center text-slate-400 italic py-10">Belum ada publikasi statistik.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Dashboard Data Desa Preview -->
<div class="bg-slate-50 py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-4">Dashboard Data Desa</h2>
            <p class="text-slate-500 max-w-2xl mx-auto">Visualisasi data kependudukan dan sosial desa secara real-time untuk mendukung perencanaan pembangunan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-10 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Distribusi Pekerjaan</h3>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Tahun {{ date('Y') }}</span>
                </div>
                <div class="h-80">
                    <canvas id="jobChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-10 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Tingkat Pendidikan</h3>
                    <span class="text-xs font-bold text-sky-600 bg-sky-50 px-3 py-1 rounded-full">Jiwa</span>
                </div>
                <div class="h-80">
                    <canvas id="eduChart"></canvas>
                </div>
            </div>
        </div>
        <div class="mt-12 text-center">
            <a href="/statistik" class="inline-flex items-center gap-2 font-bold text-emerald-600 hover:text-emerald-700 transition">
                Lihat Statistik Lengkap &rarr;
            </a>
        </div>
    </div>
</div>

<!-- Berita Terbaru -->
<div class="bg-white py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-4">Kabar Desa Terkini</h2>
                <p class="text-slate-500">Informasi terbaru seputar kegiatan dan perkembangan pembangunan desa.</p>
            </div>
            <a href="/berita" class="bg-slate-900 text-white px-8 py-3 rounded-full font-bold text-sm hover:bg-slate-800 transition">Lihat Semua Berita</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Berita Utama -->
            @if($featuredPost)
            <div class="lg:col-span-7 group">
                <div class="relative rounded-[40px] overflow-hidden aspect-[16/10] mb-8 shadow-2xl shadow-slate-200">
                    @if($featuredPost->featured_image)
                        <img src="{{ asset('storage/' . $featuredPost->featured_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" alt="{{ $featuredPost->title }}">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">No Image</div>
                    @endif
                    <div class="absolute top-8 left-8">
                        <span class="bg-emerald-500 text-white text-xs font-bold uppercase tracking-widest px-6 py-2 rounded-full shadow-lg">
                            Berita Utama
                        </span>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                </div>
                <p class="text-emerald-600 font-bold text-sm mb-4 uppercase tracking-wider">{{ $featuredPost->published_at->translatedFormat('d M Y') }} — {{ $featuredPost->category->name ?? 'Update' }}</p>
                <h3 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 leading-tight group-hover:text-emerald-600 transition mb-6">
                    {{ $featuredPost->title }}
                </h3>
                <p class="text-slate-500 text-lg mb-8 leading-relaxed line-clamp-3">
                    {{ strip_tags($featuredPost->content) }}
                </p>
                <a href="/berita/{{ $featuredPost->slug }}" class="inline-flex items-center gap-3 font-bold text-slate-900 border-b-2 border-emerald-500 pb-2 hover:text-emerald-600 transition">
                    Baca Selengkapnya
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            @endif

            <!-- List Berita Lainnya -->
            <div class="lg:col-span-5 space-y-10">
                @forelse($recentPosts as $post)
                <div class="flex gap-6 group">
                    <div class="w-32 h-32 flex-shrink-0 rounded-3xl overflow-hidden shadow-md">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $post->title }}">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300 text-[10px]">No Image</div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center">
                        <p class="text-emerald-600 font-bold text-[10px] mb-2 uppercase tracking-wider">{{ $post->published_at->translatedFormat('d M Y') }}</p>
                        <h4 class="text-lg font-heading font-bold text-slate-900 leading-snug group-hover:text-emerald-600 transition line-clamp-2">
                            <a href="/berita/{{ $post->slug }}">{{ $post->title }}</a>
                        </h4>
                    </div>
                </div>
                @empty
                    @if(!$featuredPost)
                        <p class="text-center text-slate-400 italic py-10">Belum ada berita terbaru.</p>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Section APBDes Ringkasan -->
<div class="bg-slate-900 py-32 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div>
                <h2 class="text-4xl font-heading font-extrabold mb-6 leading-tight">Transparansi <br>Anggaran Desa (APBDes)</h2>
                <p class="text-slate-400 text-lg mb-12">Wujud keterbukaan informasi publik dalam pengelolaan dana desa yang akuntabel.</p>
                
                <div class="space-y-8">
                    @php
                        $pendapatanPct = $budgetSummary['pendapatan']['budget'] > 0 ? ($budgetSummary['pendapatan']['realization'] / $budgetSummary['pendapatan']['budget']) * 100 : 0;
                        $belanjaPct = $budgetSummary['belanja']['budget'] > 0 ? ($budgetSummary['belanja']['realization'] / $budgetSummary['belanja']['budget']) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-emerald-400">Total Pendapatan</span>
                            <span class="text-sm font-medium">{{ number_format($pendapatanPct, 1) }}% Terkumpul</span>
                        </div>
                        <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pendapatanPct }}%"></div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2">Target: Rp {{ number_format($budgetSummary['pendapatan']['budget'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-sky-400">Total Belanja</span>
                            <span class="text-sm font-medium">{{ number_format($belanjaPct, 1) }}% Realisasi</span>
                        </div>
                        <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-sky-500 rounded-full" style="width: {{ $belanjaPct }}%"></div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2">Target: Rp {{ number_format($budgetSummary['belanja']['budget'], 0, ',', '.') }}</p>
                    </div>
                </div>
                
                <a href="/apbdes" class="inline-block mt-12 bg-white text-slate-900 px-10 py-4 rounded-full font-bold hover:bg-slate-100 transition shadow-xl">
                    Detail Anggaran
                </a>
            </div>
            <div class="relative">
                <div class="bg-emerald-600 w-full aspect-square rounded-[60px] absolute -rotate-6 scale-95 opacity-20"></div>
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-12 rounded-[60px] relative">
                    <h3 class="text-2xl font-heading font-bold mb-10 text-center">Alokasi Belanja Desa</h3>
                    <div class="h-80 flex items-center justify-center">
                        <canvas id="budgetRingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Galeri Desa -->
<div class="bg-white py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div>
                <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-4">Galeri Kegiatan</h2>
                <p class="text-slate-500 text-lg">Dokumentasi momen berharga dan perkembangan pembangunan di Desa {{ $site_settings['village_name'] ?? '' }}.</p>
            </div>
        </div>

        <div class="columns-2 md:columns-4 gap-6 space-y-6">
            @forelse($galleries as $gallery)
            <div class="relative group overflow-hidden rounded-3xl shadow-lg break-inside-avoid">
                <img src="{{ asset('storage/' . $gallery->image) }}" class="w-full h-auto object-cover group-hover:scale-110 transition duration-700" alt="{{ $gallery->title }}">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-500 flex flex-col justify-end p-6">
                    <p class="text-white font-bold text-sm">{{ $gallery->title }}</p>
                    <p class="text-white/70 text-[10px] uppercase tracking-widest mt-1">{{ $gallery->category }}</p>
                </div>
            </div>
            @empty
            <p class="col-span-full text-center text-slate-400 italic py-10">Belum ada foto galeri.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Kontak & Lokasi -->
<div class="bg-slate-50 py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-[60px] shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
            <div class="flex flex-col lg:flex-row">
                <div class="lg:w-1/2 p-12 lg:p-20">
                    <span class="text-emerald-600 font-bold tracking-widest uppercase text-xs mb-4 block">Hubungi Kami</span>
                    <h2 class="text-4xl font-heading font-extrabold text-slate-900 mb-10 leading-tight">
                        Layanan Informasi Publik Desa
                    </h2>
                    
                    <div class="space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 mb-1">Alamat Kantor</h4>
                                <p class="text-slate-500 text-sm leading-relaxed">{{ $site_settings['village_address'] ?? 'Jl. Poros Desa' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 mb-1">Email Resmi</h4>
                                <p class="text-slate-500 text-sm">{{ $site_settings['village_email'] ?? 'desa@example.com' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl flex-shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 mb-1">Telepon / WA</h4>
                                <p class="text-slate-500 text-sm">{{ $site_settings['village_phone'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex gap-4">
                        <a href="#" class="bg-emerald-600 text-white px-8 py-4 rounded-full font-bold shadow-lg shadow-emerald-900/10 hover:bg-emerald-700 transition">Kirim Pesan</a>
                        <a href="#" class="bg-slate-100 text-slate-600 px-8 py-4 rounded-full font-bold hover:bg-slate-200 transition">Media Sosial</a>
                    </div>
                </div>
                <div class="lg:w-1/2 bg-slate-200 h-[400px] lg:h-auto relative">
                    <!-- Leaflet Map Mini if needed, but we already have big map above -->
                    <div class="w-full h-full bg-[url('https://images.unsplash.com/photo-1496560235219-12378d505923?auto=format&fit=crop&q=80&w=1000')] bg-cover bg-center">
                        <div class="absolute inset-0 bg-emerald-900/20"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Village Map
        const map = L.map('villageMap').setView([{{ $site_settings['village_latitude'] ?? '-5.23' }}, {{ $site_settings['village_longitude'] ?? '120.21' }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([{{ $site_settings['village_latitude'] ?? '-5.23' }}, {{ $site_settings['village_longitude'] ?? '120.21' }}]).addTo(map)
            .bindPopup('Kantor Desa {{ $site_settings["village_name"] ?? "" }}')
            .openPopup();

        // Job Distribution Chart
        const ctxJob = document.getElementById('jobChart').getContext('2d');
        new Chart(ctxJob, {
            type: 'bar',
            data: {
                labels: [
                    @if($jobData)
                        @foreach($jobData->indicators as $indicator)
                            '{{ $indicator->name }}',
                        @endforeach
                    @else
                        'Petani', 'Buruh', 'PNS', 'Wiraswasta', 'Lainnya'
                    @endif
                ],
                datasets: [{
                    label: 'Jiwa',
                    data: [
                        @if($jobData)
                            @foreach($jobData->indicators as $indicator)
                                {{ $indicator->data->first()?->value ?? 0 }},
                            @endforeach
                        @else
                            850, 420, 110, 240, 180
                        @endif
                    ],
                    backgroundColor: '#10b981',
                    borderRadius: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
            }
        });

        // Education Level Chart
        const ctxEdu = document.getElementById('eduChart').getContext('2d');
        new Chart(ctxEdu, {
            type: 'line',
            data: {
                labels: [
                    @if($eduData)
                        @foreach($eduData->indicators as $indicator)
                            '{{ $indicator->name }}',
                        @endforeach
                    @else
                        'SD', 'SMP', 'SMA', 'Diploma', 'Sarjana'
                    @endif
                ],
                datasets: [{
                    label: 'Jiwa',
                    data: [
                        @if($eduData)
                            @foreach($eduData->indicators as $indicator)
                                {{ $indicator->data->first()?->value ?? 0 }},
                            @endforeach
                        @else
                            1200, 950, 780, 120, 180
                        @endif
                    ],
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } }
            }
        });

        // Budget Donut Chart
        const ctxBudget = document.getElementById('budgetRingChart').getContext('2d');
        new Chart(ctxBudget, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($belanjaDetails as $detail)
                        '{{ $detail->title }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($belanjaDetails as $detail)
                            {{ $detail->realization_amount }},
                        @endforeach
                    ],
                    backgroundColor: ['#10b981', '#0ea5e9', '#f59e0b', '#6366f1', '#ec4899', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#fff', font: { family: 'Poppins', size: 12 }, padding: 20 }
                    }
                }
            }
        });
    });
</script>
@endpush
