@extends('layouts.app')

@section('title', 'Kontak | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Informasi kontak pelayanan publik dan alamat kantor sekretariat Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' untuk pengaduan dan koordinasi.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "GovernmentOffice",
    "@@id": "{{ url('/kontak') }}#governmentoffice",
    "name": "Kantor Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}",
    "url": "{{ url('/kontak') }}",
    "logo": {
        "@@type": "ImageObject",
        "url": "{{ asset('img/sinjai.webp') }}"
    },
    "image": "{{ asset('img/meta.webp') }}",
    "description": "Kantor pelayanan publik dan administrasi Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}.",
    "telephone": {!! json_encode($site_settings['village_phone'] ?? '') !!},
    "email": {!! json_encode($site_settings['village_email'] ?? '') !!},
    "address": {
        "@@type": "PostalAddress",
        "streetAddress": {!! json_encode($site_settings['village_address'] ?? '') !!},
        "addressLocality": {!! json_encode($site_settings['district_name'] ?? '') !!},
        "addressRegion": {!! json_encode(($site_settings['regency_name'] ?? '') . ', SULAWESI SELATAN') !!},
        "addressCountry": "ID"
    }
    @if(!empty($site_settings['village_latitude']) && !empty($site_settings['village_longitude'])),
    "geo": {
        "@@type": "GeoCoordinates",
        "latitude": "{{ $site_settings['village_latitude'] }}",
        "longitude": "{{ $site_settings['village_longitude'] }}"
    }
    @endif
}
</script>
@endpush

@section('content')

{{-- ===================== HERO ===================== --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 rounded-full blur-3xl"></div>
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
                    <span class="text-white">Kontak</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Kontak <span class="text-primary-500 italic">Desa</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                Informasi kontak dan saluran pelayanan Pemerintah Desa.
            </p>
        </div>
    </div>
</div>

{{-- ===================== MAIN LAYOUT ===================== --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

            {{-- ========== LEFT: Info Kontak + Sosmed ========== --}}
            <div class="space-y-6">

                {{-- Alamat --}}
                <div class="flex items-start gap-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg flex-shrink-0 shadow-xs">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Alamat Kantor</p>
                        <p class="text-slate-800 font-semibold leading-relaxed text-sm">{{ $site_settings['village_address'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- Telepon / WA --}}
                <div class="flex items-start gap-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg flex-shrink-0 shadow-xs">
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
                           class="text-slate-800 font-semibold text-sm hover:text-primary-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 inline-flex items-center gap-2 mt-1 rounded-md px-1 -ml-1">
                            {{ $phone }}
                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide border border-emerald-200">
                                <i class="fa-brands fa-whatsapp text-[10px]"></i> Chat
                            </span>
                        </a>
                        @else
                        <p class="text-slate-400 text-sm font-medium">-</p>
                        @endif
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-start gap-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg flex-shrink-0 shadow-xs">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Email Resmi</p>
                        @if(!empty($site_settings['village_email']))
                        <a href="mailto:{{ $site_settings['village_email'] }}" class="text-slate-800 font-semibold text-sm hover:text-primary-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 break-all inline-block rounded-md px-1 -ml-1">
                            {{ $site_settings['village_email'] }}
                        </a>
                        @else
                        <p class="text-slate-400 text-sm font-medium">-</p>
                        @endif
                    </div>
                </div>

                {{-- Jam Operasional --}}
                <div class="flex items-start gap-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg flex-shrink-0 shadow-xs">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Jam Operasional Pelayanan</p>
                        <p class="text-slate-800 font-semibold text-sm">Senin – Jumat, 08.00 – 16.00 WITA</p>
                    </div>
                </div>

                {{-- Social Media --}}
                <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-md">
                    <h4 class="text-xs font-black uppercase tracking-widest text-primary-400 mb-6">Ikuti Media Sosial</h4>
                    <div class="flex flex-wrap gap-3">
                        @if(!empty($site_settings['social_facebook']))
                        <a href="{{ $site_settings['social_facebook'] }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 bg-white/10 hover:bg-[#1877F2] border border-white/10 hover:border-[#1877F2] text-white px-4 py-2.5 rounded-xl font-bold text-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 shadow-xs cursor-pointer">
                            <i class="fa-brands fa-facebook-f"></i> Facebook
                        </a>
                        @else
                        <span class="flex items-center gap-2 bg-white/5 border border-white/5 text-slate-500 px-4 py-2.5 rounded-xl font-bold text-sm cursor-not-allowed opacity-40 select-none" title="Belum diatur">
                            <i class="fa-brands fa-facebook-f"></i> Facebook
                        </span>
                        @endif

                        @if(!empty($site_settings['social_instagram']))
                        <a href="{{ $site_settings['social_instagram'] }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 bg-white/10 hover:bg-[#E1306C] border border-white/10 hover:border-[#E1306C] text-white px-4 py-2.5 rounded-xl font-bold text-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 shadow-xs cursor-pointer">
                            <i class="fa-brands fa-instagram"></i> Instagram
                        </a>
                        @else
                        <span class="flex items-center gap-2 bg-white/5 border border-white/5 text-slate-500 px-4 py-2.5 rounded-xl font-bold text-sm cursor-not-allowed opacity-40 select-none" title="Belum diatur">
                            <i class="fa-brands fa-instagram"></i> Instagram
                        </span>
                        @endif

                        @if(!empty($site_settings['social_youtube']))
                        <a href="{{ $site_settings['social_youtube'] }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 bg-white/10 hover:bg-[#FF0000] border border-white/10 hover:border-[#FF0000] text-white px-4 py-2.5 rounded-xl font-bold text-sm active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-all duration-200 shadow-xs cursor-pointer">
                            <i class="fa-brands fa-youtube"></i> YouTube
                        </a>
                        @else
                        <span class="flex items-center gap-2 bg-white/5 border border-white/5 text-slate-500 px-4 py-2.5 rounded-xl font-bold text-sm cursor-not-allowed opacity-40 select-none" title="Belum diatur">
                            <i class="fa-brands fa-youtube"></i> YouTube
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Link Pengaduan --}}
                <a href="/pengaduan" class="flex items-center gap-4 bg-primary-50 hover:bg-primary-100/80 border border-primary-200/80 rounded-2xl p-6 transition-all duration-200 group active:scale-[0.99] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                    <div class="w-12 h-12 rounded-2xl bg-primary-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-xs">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-slate-900 font-extrabold text-sm">Punya pengaduan atau aspirasi?</p>
                        <p class="text-primary-700 text-xs font-semibold mt-0.5">Sampaikan melalui halaman Pengaduan →</p>
                    </div>
                </a>
            </div>

            {{-- ========== RIGHT: Peta ========== --}}
            <div class="lg:sticky lg:top-24 self-start">
                <div class="rounded-3xl overflow-hidden shadow-lg shadow-slate-200/50 border border-slate-200/80 h-[520px] lg:h-[640px] relative bg-slate-100">
                    @if(!empty($site_settings['village_name']))
                    <iframe
                        class="w-full h-full absolute inset-0 z-0 border-0"
                        src="https://maps.google.com/maps?q=Desa+{{ urlencode($site_settings['village_name']) }}+Kecamatan+{{ urlencode($site_settings['district_name'] ?? '') }}+Kabupaten+{{ urlencode($site_settings['regency_name'] ?? '') }}&z=12&output=embed"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 p-6 text-center">
                        <i class="fa-solid fa-map-location-dot text-7xl mb-4 opacity-30"></i>
                        <p class="font-bold text-slate-400">Peta belum dikonfigurasi</p>
                        <p class="text-sm text-slate-300 mt-1">Tambahkan nama desa di pengaturan untuk mengaktifkan peta.</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


