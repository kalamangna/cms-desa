@extends('layouts.app')

@section('title', 'Layanan Masyarakat - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <x-heroicon-s-chevron-right class="w-4 h-4 mx-1" />
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
                Informasi prosedur, persyaratan, dan panduan administrasi kependudukan di Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @php
            $services = [
                ['title' => 'Kartu Keluarga (KK)', 'icon' => 'fa-users', 'desc' => 'Prosedur pembuatan KK baru, penambahan anggota keluarga, atau perubahan data.'],
                ['title' => 'KTP Elektronik', 'icon' => 'fa-id-card', 'desc' => 'Syarat perekaman KTP-el baru, penggantian KTP rusak, atau hilang.'],
                ['title' => 'Akta Kelahiran', 'icon' => 'fa-baby', 'desc' => 'Persyaratan pembuatan akta kelahiran untuk anak yang baru lahir.'],
                ['title' => 'Surat Pengantar Nikah', 'icon' => 'fa-heart', 'desc' => 'Layanan administrasi bagi warga yang akan melangsungkan pernikahan.'],
                ['title' => 'Keterangan Domisili', 'icon' => 'fa-house-user', 'desc' => 'Pembuatan surat keterangan tempat tinggal untuk berbagai keperluan.'],
                ['title' => 'Keterangan Tidak Mampu', 'icon' => 'fa-hand-holding-heart', 'desc' => 'Layanan SKTM untuk keperluan pendidikan, kesehatan, atau bantuan sosial.'],
            ];
        @endphp

        @foreach($services as $service)
        <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 group hover:border-emerald-500 transition duration-500">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-8 group-hover:bg-emerald-600 group-hover:text-white transition duration-500">
                <i class="fa-solid {{ $service['icon'] }}"></i>
            </div>
            <h3 class="text-xl font-heading font-bold text-slate-900 mb-4">{{ $service['title'] }}</h3>
            <p class="text-slate-500 text-sm leading-relaxed font-medium mb-8">
                {{ $service['desc'] }}
            </p>
            <a href="#" class="inline-flex items-center gap-2 text-emerald-600 font-bold text-sm hover:gap-4 transition-all">
                Lihat Persyaratan <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        @endforeach
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
@endsection
