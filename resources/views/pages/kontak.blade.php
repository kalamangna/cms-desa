@extends('layouts.app')

@section('title', 'Kontak - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <span class="text-white">Kontak</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Hubungi <span class="text-emerald-500 italic">Kami</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Saluran masukan and pengaduan warga Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24">
        <!-- Contact Information -->
        <div>
            <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.3em] mb-4 block">Informasi Kontak</span>
            <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-12">Saluran Layanan</h2>
            
            <div class="space-y-10">
                <div class="flex items-start gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-heading font-bold text-slate-900 mb-2">Alamat Kantor</h4>
                        <p class="text-slate-500 leading-relaxed font-medium">{{ $site_settings['village_address'] ?? 'Jalan Poros Desa Tompobulu, Sinjai' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-heading font-bold text-slate-900 mb-2">Telepon / WhatsApp</h4>
                        <p class="text-slate-500 leading-relaxed font-medium">{{ $site_settings['village_phone'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-heading font-bold text-slate-900 mb-2">Email Resmi</h4>
                        <p class="text-slate-500 leading-relaxed font-medium">{{ $site_settings['village_email'] ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-heading font-bold text-slate-900 mb-2">Jam Operasional</h4>
                        <p class="text-slate-500 leading-relaxed font-medium">Senin - Jumat: 08:00 - 16:00 WITA</p>
                    </div>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="mt-16 pt-10 border-t border-slate-100">
                <h4 class="text-sm font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Ikuti Media Sosial</h4>
                <div class="flex gap-4">
                    <a href="{{ $site_settings['social_facebook'] ?? '#' }}" target="_blank" class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-emerald-600 hover:text-white transition">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="{{ $site_settings['social_instagram'] ?? '#' }}" target="_blank" class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-emerald-600 hover:text-white transition">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="{{ $site_settings['social_youtube'] ?? '#' }}" target="_blank" class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-emerald-600 hover:text-white transition">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Form Card -->
        <div class="bg-white p-8 md:p-12 rounded-[40px] md:rounded-[60px] shadow-2xl shadow-slate-200/50 border border-slate-100">
            <h3 class="text-2xl font-heading font-bold text-slate-900 mb-8">Kirim Pesan atau Pengaduan</h3>
            <form action="#" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Nama Lengkap</label>
                        <input type="text" placeholder="Masukkan nama Anda" class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Nomor WhatsApp</label>
                        <input type="text" placeholder="Masukkan nomor HP" class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Tujuan Pesan</label>
                    <select class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium">
                        <option>Pertanyaan Umum</option>
                        <option>Layanan Administrasi</option>
                        <option>Pengaduan Masyarakat</option>
                        <option>Aspirasi Pembangunan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-3 ml-1">Isi Pesan</label>
                    <textarea rows="6" placeholder="Tuliskan pesan atau laporan Anda secara detail..." class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-emerald-500 font-medium"></textarea>
                </div>
                <button type="button" class="w-full bg-emerald-600 text-white py-5 rounded-2xl font-bold text-lg hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/20">
                    Kirim Pesan Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- Peta Lokasi -->
    <div class="mt-32">
        <div class="rounded-[40px] md:rounded-[60px] overflow-hidden bg-slate-200 h-[400px] md:h-[600px] shadow-2xl relative border-8 border-white">
             <!-- Placeholder for interactive map -->
             <div id="contactMap" class="w-full h-full"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('contactMap').setView([{{ $site_settings['village_latitude'] ?? '-5.23' }}, {{ $site_settings['village_longitude'] ?? '120.21' }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([{{ $site_settings['village_latitude'] ?? '-5.23' }}, {{ $site_settings['village_longitude'] ?? '120.21' }}]).addTo(map)
            .bindPopup('Kantor Desa {{ $site_settings["village_name"] ?? "" }}')
            .openPopup();
    });
</script>
@endpush
