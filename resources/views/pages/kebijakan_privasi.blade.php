@extends('layouts.app')

@section('title', 'Kebijakan Privasi | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Kebijakan privasi dan perlindungan data pribadi warga pada portal resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
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
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Kebijakan Privasi</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-4 sm:mb-6 drop-shadow-2xl">
                Kebijakan <span class="text-primary-500 italic">Privasi</span>
            </h1>
            <p class="text-slate-300 text-base sm:text-lg mt-2 leading-relaxed">
                Komitmen Pemerintah Desa {{ $site_settings['village_name'] ?? '' }} dalam melindungi kerahasiaan data pribadi masyarakat.
            </p>
        </div>
    </div>
</div>

{{-- ===================== KONTEN UTAMA ===================== --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20 space-y-6">

        <section class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-3">
                1. Komitmen Perlindungan Data Pribadi
            </h2>
            <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed">
                Pemerintah Desa {{ $site_settings['village_name'] ?? '' }} mengelola portal informasi dan layanan publik ini dengan berpedoman pada Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi serta Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik. Keterbukaan penyelenggaraan pemerintahan dijalankan secara seimbang tanpa mengorbankan privasi warga.
            </p>
        </section>

        <section class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-3">
                2. Jenis Data yang Dikelola
            </h2>
            <ul class="space-y-2.5 text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed list-disc pl-5">
                <li>Data identitas dan berkas persyaratan yang dikirimkan secara sukarela melalui formulir Permohonan Layanan Administrasi Desa.</li>
                <li>Data pelapor dan rincian laporan pada kanal Pengaduan Masyarakat serta isian pada Buku Tamu digital.</li>
                <li>Data statistik kunjungan portal secara agregat tanpa menyimpan identitas pribadi pengunjung.</li>
            </ul>
        </section>

        <section class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-3">
                3. Larangan Publikasi Data Sensitif
            </h2>
            <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed">
                Portal desa tidak mempublikasikan Nomor Induk Kependudukan, nomor Kartu Keluarga, nomor telepon pribadi, alamat detail rumah warga, maupun pindaian dokumen identitas pribadi pada halaman berita, pengumuman, galeri foto, maupun arsip dokumen publik. Dokumen publik yang tersedia pada halaman <a href="{{ route('documents.index') }}" class="text-primary-600 dark:text-primary-400 font-semibold hover:underline">Arsip Dokumen</a> hanya memuat regulasi dan informasi publik yang telah disaring dari data pribadi sensitif.
            </p>
        </section>

        <section class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-3">
                4. Keamanan dan Penyimpanan Data
            </h2>
            <ul class="space-y-2.5 text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed list-disc pl-5">
                <li>Seluruh pertukaran data pada portal dilindungi menggunakan enkripsi koneksi HTTPS dan header keamanan aplikasi web.</li>
                <li>Akses terhadap data permohonan surat, lampiran persyaratan, dan pengaduan dibatasi hanya kepada operator desa yang berwenang sesuai peran masing-masing.</li>
                <li>Setiap perubahan data penting dan akses administratif tercatat dalam log audit sistem guna menjaga akuntabilitas pengelolaan data.</li>
            </ul>
        </section>

        <section class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800">
            <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-3">
                5. Hak Warga dan Kanal Permintaan
            </h2>
            <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed mb-4">
                Warga berhak mengajukan klarifikasi, perbaikan, atau permintaan penghapusan apabila menemukan data pribadi yang keliru tercantum pada konten publik portal desa. Permintaan dapat disampaikan melalui saluran resmi berikut:
            </p>
            <div class="space-y-2 text-sm text-slate-700 dark:text-slate-200 font-medium mb-6">
                @if(!empty($site_settings['village_email']))
                    <p>Email Resmi: <span class="font-bold">{{ $site_settings['village_email'] }}</span></p>
                @endif
                @if(!empty($site_settings['village_phone']))
                    <p>Telepon Resmi: <span class="font-bold">{{ $site_settings['village_phone'] }}</span></p>
                @endif
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kontak') }}"
                   class="inline-flex items-center justify-center gap-2 h-11 px-5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold transition active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                    <i class="fa-solid fa-envelope text-xs"></i>
                    <span>Hubungi Pemerintah Desa</span>
                </a>
                <a href="{{ route('complaints.index') }}"
                   class="inline-flex items-center justify-center gap-2 h-11 px-5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                    <i class="fa-solid fa-bullhorn text-xs"></i>
                    <span>Sampaikan Pengaduan</span>
                </a>
            </div>
        </section>

    </div>
</div>
@endsection
