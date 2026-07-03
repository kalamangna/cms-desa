@extends('layouts.app')

@section('title', 'Kontak | Desa ' . ($site_settings['village_name'] ?? 'Tompobulu'))
@section('meta_description', 'Temukan alamat, nomor telepon, email, media sosial, dan lokasi kantor Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

@section('content')

{{-- ===================== HERO ===================== --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-emerald-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-emerald-400 transition-colors duration-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500/40"></i>
                    <span class="text-white">Kontak</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Hubungi <span class="text-emerald-500 italic">Kami</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Saluran masukan dan pengaduan warga Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== SPLIT LAYOUT ===================== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 lg:gap-16">

        {{-- ========== LEFT: Info + Social ========== --}}
        <div class="lg:col-span-2 space-y-10">
            <div>
                <div class="flex items-center gap-3 mb-4"><div class="h-px w-8 bg-emerald-500"></div><span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Informasi Kontak</span></div>
                <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-10">Saluran Layanan</h2>
            </div>

            {{-- Contact Info Cards --}}
            <div class="space-y-5">

                {{-- Alamat --}}
                <div class="flex items-start gap-5 bg-white rounded-[24px] p-6 border border-slate-100 shadow-md shadow-slate-200/40 hover:shadow-emerald-100/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Alamat Kantor</p>
                        <p class="text-slate-700 font-semibold leading-relaxed text-sm">{{ $site_settings['village_address'] ?? 'Jalan Poros Desa Tompobulu, Sinjai' }}</p>
                    </div>
                </div>

                {{-- Telepon / WA --}}
                <div class="flex items-start gap-5 bg-white rounded-[24px] p-6 border border-slate-100 shadow-md shadow-slate-200/40 hover:shadow-emerald-100/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Telepon / WhatsApp</p>
                        @php
                            $phone = $site_settings['village_phone'] ?? '';
                            $waNumber = preg_replace('/[^0-9]/', '', $phone);
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                        @endphp
                        @if($phone)
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                           class="text-slate-700 font-semibold text-sm hover:text-emerald-600 transition flex items-center gap-2 mt-1">
                            {{ $phone }}
                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5 text-[10px] font-black uppercase tracking-wide">
                                <i class="fa-brands fa-whatsapp text-[10px]"></i> Chat
                            </span>
                        </a>
                        @else
                        <p class="text-slate-400 text-sm font-medium">-</p>
                        @endif
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-start gap-5 bg-white rounded-[24px] p-6 border border-slate-100 shadow-md shadow-slate-200/40 hover:shadow-emerald-100/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Email Resmi</p>
                        @if(!empty($site_settings['village_email']))
                        <a href="mailto:{{ $site_settings['village_email'] }}" class="text-slate-700 font-semibold text-sm hover:text-emerald-600 transition break-all">
                            {{ $site_settings['village_email'] }}
                        </a>
                        @else
                        <p class="text-slate-400 text-sm font-medium">-</p>
                        @endif
                    </div>
                </div>

                {{-- Jam Operasional --}}
                <div class="flex items-start gap-5 bg-white rounded-[24px] p-6 border border-slate-100 shadow-md shadow-slate-200/40 hover:shadow-emerald-100/40 hover:border-emerald-200 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Jam Operasional</p>
                        <p class="text-slate-700 font-semibold text-sm">Senin – Jumat</p>
                        <p class="text-emerald-600 font-bold text-sm">08:00 – 16:00 WITA</p>
                    </div>
                </div>
            </div>

            {{-- Social Media --}}
            <div class="bg-slate-900 rounded-[28px] p-8 text-white">
                <h4 class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400 mb-6">Ikuti Media Sosial</h4>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $site_settings['social_facebook'] ?? '#' }}" target="_blank" rel="noopener"
                       class="flex items-center gap-2 bg-white/10 hover:bg-[#1877F2] border border-white/10 hover:border-[#1877F2] text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                        <i class="fa-brands fa-facebook-f"></i>
                        Facebook
                    </a>
                    <a href="{{ $site_settings['social_instagram'] ?? '#' }}" target="_blank" rel="noopener"
                       class="flex items-center gap-2 bg-white/10 hover:bg-[#E1306C] border border-white/10 hover:border-[#E1306C] text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                        <i class="fa-brands fa-instagram"></i>
                        Instagram
                    </a>
                    <a href="{{ $site_settings['social_youtube'] ?? '#' }}" target="_blank" rel="noopener"
                       class="flex items-center gap-2 bg-white/10 hover:bg-[#FF0000] border border-white/10 hover:border-[#FF0000] text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                        <i class="fa-brands fa-youtube"></i>
                        YouTube
                    </a>
                    @if(!empty($phone))
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                       class="flex items-center gap-2 bg-white/10 hover:bg-[#25D366] border border-white/10 hover:border-[#25D366] text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all duration-200">
                        <i class="fa-brands fa-whatsapp"></i>
                        WhatsApp
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ========== RIGHT: Map iframe ========== --}}
        <div class="lg:col-span-3 flex flex-col gap-8">

            {{-- Map --}}
            <div class="flex-1 rounded-[40px] overflow-hidden shadow-2xl shadow-slate-300/40 border-4 border-white min-h-[400px] relative bg-slate-100 isolate">
                @if(!empty($site_settings['village_latitude']) && !empty($site_settings['village_longitude']))
                    {{-- Leaflet Map --}}
                    <div id="contactMap" class="w-full h-full absolute inset-0 z-0"></div>
                @else
                    {{-- Placeholder when no coordinates configured --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 p-6 text-center">
                        <i class="fa-solid fa-map-location-dot text-7xl mb-4 opacity-30"></i>
                        <p class="font-bold text-slate-400">Peta belum dikonfigurasi</p>
                        <p class="text-sm text-slate-300 mt-1">Tambahkan koordinat Latitude & Longitude di pengaturan lokasi untuk mengaktifkan peta.</p>
                    </div>
                @endif
            </div>

            {{-- Quick WA CTA --}}
            @if(!empty($phone))
            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo, saya ingin bertanya tentang layanan Desa ' . ($site_settings['village_name'] ?? '')) }}"
               target="_blank" rel="noopener"
               class="flex items-center justify-center gap-3 bg-[#25D366] hover:bg-[#20b858] text-white py-5 px-8 rounded-[20px] font-bold text-lg transition-all duration-300 shadow-xl shadow-green-200/50 hover:shadow-green-300/60 hover:-translate-y-0.5 group">
                <i class="fa-brands fa-whatsapp text-2xl group-hover:scale-110 transition-transform"></i>
                Chat via WhatsApp Sekarang
                <i class="fa-solid fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
            </a>
            @endif

            {{-- Contact Form --}}
            <div class="bg-white rounded-[40px] p-8 md:p-10 border border-slate-100 shadow-xl shadow-slate-200/50">
                <h3 class="text-2xl font-heading font-bold text-slate-900 mb-2">Kirim Pesan atau Pengaduan</h3>
                <p class="text-slate-400 text-sm mb-8 font-medium">Kami akan merespons dalam 1x24 jam kerja.</p>
                <form action="#" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="contact-name" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Nama Lengkap</label>
                            <input type="text" id="contact-name" name="name" placeholder="Masukkan nama Anda"
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                        </div>
                        <div>
                            <label for="contact-phone" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Nomor WhatsApp</label>
                            <input type="tel" id="contact-phone" name="phone" placeholder="08xx-xxxx-xxxx"
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                        </div>
                    </div>
                    <div>
                        <label for="contact-category" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Tujuan Pesan</label>
                        <select id="contact-category" name="category"
                                class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-700 outline-none transition appearance-none cursor-pointer">
                            <option value="umum">Pertanyaan Umum</option>
                            <option value="layanan">Layanan Administrasi</option>
                            <option value="pengaduan">Pengaduan Masyarakat</option>
                            <option value="aspirasi">Aspirasi Pembangunan</option>
                        </select>
                    </div>
                    <div>
                        <label for="contact-message" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Isi Pesan</label>
                        <textarea id="contact-message" name="message" rows="5" placeholder="Tuliskan pesan atau laporan Anda secara detail..."
                                  class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition resize-none"></textarea>
                    </div>
                    <button type="button"
                            class="w-full flex items-center justify-center gap-3 bg-emerald-600 text-white py-4.5 py-5 rounded-2xl font-bold text-base hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/20 hover:shadow-emerald-200/60 active:scale-[0.98]">
                        <i class="fa-solid fa-paper-plane"></i>
                        Kirim Pesan Sekarang
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@if(!empty($site_settings['village_latitude']) && !empty($site_settings['village_longitude']))
@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('contactMap');
        if (!mapEl) return;
        const map = L.map('contactMap').setView([{{ $site_settings['village_latitude'] ?? '-5.23' }}, {{ $site_settings['village_longitude'] ?? '120.21' }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.marker([{{ $site_settings['village_latitude'] ?? '-5.23' }}, {{ $site_settings['village_longitude'] ?? '120.21' }}])
            .addTo(map)
            .bindPopup('Kantor Desa {{ $site_settings["village_name"] ?? "" }}')
            .openPopup();
    });
</script>
@endpush
@endif
