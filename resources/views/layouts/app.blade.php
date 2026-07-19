<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Desa ' . ($site_settings['village_name'] ?? 'Website Desa'))</title>
    <meta name="description" content="@yield('meta_description', 'Portal Resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '. Menyajikan pelayanan publik, publikasi berita pembangunan, transparansi anggaran, dan statistik kependudukan secara akurat.')">
    <meta name="keywords" content="@yield('meta_keywords', 'desa, ' . ($site_settings['village_name'] ?? '') . ', ' . ($site_settings['district_name'] ?? '') . ', statistik desa, apbdes, pemerintah desa')">
    <meta name="author" content="Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sinjai.png') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('title', 'Desa ' . ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta property="og:description" content="@yield('meta_description', 'Portal Resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta property="og:image" content="@yield('meta_image', asset('img/meta.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @stack('og_extra')

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Desa ' . ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta name="twitter:description" content="@yield('meta_description', 'Portal Resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta name="twitter:image" content="@yield('meta_image', asset('img/meta.png'))">

    <!-- JSON-LD: Organization + WebSite (global) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": "{{ url('/') }}/#organization",
                "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('img/sinjai.png') }}"
                },
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "{{ $site_settings['village_address'] ?? '' }}",
                    "addressLocality": "{{ $site_settings['district_name'] ?? '' }}",
                    "addressCountry": "ID"
                },
                "telephone": "{{ $site_settings['village_phone'] ?? '' }}",
                "sameAs": [
                    "{{ $site_settings['social_facebook'] ?? '' }}",
                    "{{ $site_settings['social_instagram'] ?? '' }}"
                ]
            },
            {
                "@type": "WebSite",
                "@id": "{{ url('/') }}/#website",
                "url": "{{ url('/') }}",
                "name": "Desa {{ $site_settings['village_name'] ?? '' }}",
                "publisher": {
                    "@id": "{{ url('/') }}/#organization"
                },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "{{ url('/berita') }}?search={search_term_string}"
                    },
                    "query-input": "required name=search_term_string"
                }
            }
        ]
    }
    </script>

    <!-- JSON-LD: BreadcrumbList (dynamic based on route segments) -->
    @php
        $segments = request()->segments();
        $breadcrumbs = [];
        
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Beranda',
            'item' => url('/')
        ];
        
        $currentUrl = url('/');
        foreach ($segments as $idx => $segment) {
            $currentUrl .= '/' . $segment;
            
            $name = ucwords(str_replace('-', ' ', $segment));
            if (strtolower($name) === 'apbdes') {
                $name = 'APBDes';
            } elseif (strtolower($name) === 'berita') {
                $name = 'Berita';
            } elseif (strtolower($name) === 'pengumuman') {
                $name = 'Pengumuman';
            } elseif (strtolower($name) === 'dataset') {
                $name = 'Open Data';
            } elseif (strtolower($name) === 'aparatur') {
                $name = 'Aparatur Desa';
            } elseif (strtolower($name) === 'lembaga') {
                $name = 'Lembaga Desa';
            } elseif (strtolower($name) === 'peta') {
                $name = 'Peta Spasial';
            }
            
            // Override nama detail berita atau pengumuman jika variabel objek tersedia
            if ($idx === count($segments) - 1 && count($segments) > 1) {
                if (isset($post) && $segment === $post->slug) {
                    $name = $post->title;
                } elseif (isset($announcement) && $segment === $announcement->slug) {
                    $name = $announcement->title;
                }
            }
            
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $idx + 2,
                'name' => $name,
                'item' => $currentUrl
            ];
        }
    @endphp
    @if(count($segments) > 0)
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": {!! json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES) !!}
    }
    </script>
    @endif

    <!-- Extra head content (JSON-LD, etc.) from child views -->
    @stack('head')

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Scripts & Styles -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        .font-heading {
            font-family: 'Poppins', sans-serif;
        }

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic Primary Color Theme Styles -->
    @if(isset($site_settings['primary_color']))
    <style>
        :root {
            --primary-base: {{ $site_settings['primary_color'] }};
            --color-emerald-50: color-mix(in srgb, var(--primary-base) 5%, #ffffff);
            --color-emerald-100: color-mix(in srgb, var(--primary-base) 10%, #ffffff);
            --color-emerald-200: color-mix(in srgb, var(--primary-base) 20%, #ffffff);
            --color-emerald-300: color-mix(in srgb, var(--primary-base) 30%, #ffffff);
            --color-emerald-400: color-mix(in srgb, var(--primary-base) 50%, #ffffff);
            --color-emerald-500: var(--primary-base);
            --color-emerald-600: color-mix(in srgb, var(--primary-base) 85%, #000000);
            --color-emerald-700: color-mix(in srgb, var(--primary-base) 70%, #000000);
            --color-emerald-800: color-mix(in srgb, var(--primary-base) 55%, #000000);
            --color-emerald-900: color-mix(in srgb, var(--primary-base) 40%, #000000);
            --color-emerald-950: color-mix(in srgb, var(--primary-base) 25%, #000000);
        }
    </style>
    @endif
</head>

<body class="bg-slate-50 flex flex-col min-h-screen font-sans text-slate-900">
    <!-- Top Bar -->
    <div class="bg-emerald-900 text-white py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.2em]">
            <div class="flex gap-8">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-emerald-400"></i> {{ $site_settings['village_address'] ?? '' }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-phone text-emerald-400"></i> {{ $site_settings['village_phone'] ?? '-' }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-emerald-400"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <div class="flex gap-6 items-center">
                @auth
                <div class="flex items-center gap-6">
                    <a href="/admin" target="_blank" class="hover:text-emerald-400 transition flex items-center gap-2">
                        <i class="fa-solid fa-table-cells-large text-[10px]"></i>
                        Panel Admin
                    </a>
                    <form method="POST" action="/admin/logout">
                        @csrf
                        <button type="submit" class="hover:text-rose-400 transition flex items-center gap-2">
                            <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                            Keluar
                        </button>
                    </form>
                </div>
                @else
                <a href="/admin/login" target="_blank" class="hover:text-emerald-400 transition flex items-center gap-2">
                    <i class="fa-solid fa-user text-[10px]"></i>
                    Login Sistem &rarr;
                </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 transition-all duration-300 border-b border-slate-200/80"
        x-data="{ mobileMenuOpen: false, scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-md py-2' : 'bg-white py-4'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 transition-all duration-300">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-3">
                        <img class="h-10 w-auto transition-all duration-300" :class="scrolled ? 'h-9' : 'h-11'" src="{{ asset('img/sinjai.png') }}" alt="Logo">
                        <div class="flex flex-col">
                            <span class="font-heading font-bold text-lg leading-tight text-emerald-600">{{ $site_settings['village_name'] ?? 'Website Desa' }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Portal Resmi Desa</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:items-center lg:space-x-8" x-data="{ openMenu: null }">
                    <a href="/" class="relative py-2 px-1 text-sm font-bold transition-all duration-300 {{ request()->is('/') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">
                        Beranda
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600 transition-all duration-300 origin-left {{ request()->is('/') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                    </a>

                    <!-- Profil Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'profil'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">
                            Profil <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600 transition-all duration-300 origin-left {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'profil'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-48 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50" x-cloak>
                            <a href="/profil" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Profil Desa</a>
                            <a href="/aparatur" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Aparatur Desa</a>
                            <a href="/lembaga" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Lembaga Desa</a>
                            <a href="/potensi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Potensi Desa</a>
                        </div>
                    </div>

                    <!-- Informasi Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'info'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">
                            Informasi <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600 transition-all duration-300 origin-left {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'info'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-48 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50" x-cloak>
                            <a href="/berita" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Berita</a>
                            <a href="/pengumuman" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Pengumuman</a>
                            <a href="/galeri" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Galeri</a>
                            <a href="/dokumen" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Dokumen</a>
                        </div>
                    </div>

                    <!-- Transparansi Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'transparansi'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">
                            Transparansi <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600 transition-all duration-300 origin-left {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'transparansi'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full right-0 w-56 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50" x-cloak>
                            <a href="/apbdes" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">APBDes</a>
                            <a href="/statistik" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Statistik</a>
                            <a href="/dataset" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Open Data</a>
                            <a href="/publikasi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Publikasi Data</a>
                        </div>
                    </div>

                    <!-- Layanan Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'layanan'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('layanan*') || request()->is('kontak*') || request()->is('buku-tamu*') || request()->is('pengaduan*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">
                            Layanan <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600 transition-all duration-300 origin-left {{ request()->is('layanan*') || request()->is('kontak*') || request()->is('buku-tamu*') || request()->is('pengaduan*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'layanan'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full right-0 w-52 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50" x-cloak>
                            <a href="/layanan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Layanan Mandiri</a>
                            <a href="/pengaduan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Pengaduan</a>
                            <a href="/buku-tamu" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Buku Tamu</a>
                            <a href="/kontak" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Kontak</a>
                        </div>
                    </div>

                    <!-- Peta Spasial (Top-Level Link) -->
                    <a href="/peta" class="relative py-2 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('peta*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">
                        Peta Spasial
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-600 transition-all duration-300 origin-left {{ request()->is('peta*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-emerald-600 p-2 focus:outline-none transition-transform duration-200" :class="mobileMenuOpen ? 'rotate-90' : ''">
                        <i class="fa-solid fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fa-solid fa-xmark text-xl" x-show="mobileMenuOpen" x-cloak></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="lg:hidden bg-white border-b border-slate-200 overflow-y-auto max-h-[80vh]" x-cloak>
            <div class="px-4 pt-2 pb-8 space-y-6">
                <!-- Home Link -->
                <a href="/" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('/') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Beranda</a>

                <!-- Profil Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Profil</span>
                    <a href="/profil" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('profil*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Profil Desa</a>
                    <a href="/aparatur" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('aparatur*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Aparatur Desa</a>
                    <a href="/lembaga" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('lembaga*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Lembaga Desa</a>
                    <a href="/potensi" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('potensi*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Potensi Desa</a>
                </div>

                <!-- Informasi Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Informasi</span>
                    <a href="/berita" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('berita*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Berita</a>
                    <a href="/pengumuman" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('pengumuman*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Pengumuman</a>
                    <a href="/galeri" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('galeri*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Galeri</a>
                    <a href="/dokumen" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('dokumen*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Dokumen</a>
                </div>

                <!-- Transparansi Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Transparansi</span>
                    <a href="/apbdes" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('apbdes*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">APBDes</a>
                    <a href="/statistik" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('statistik*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Statistik</a>
                    <a href="/dataset" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('dataset*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Open Data</a>
                    <a href="/publikasi" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('publikasi*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Publikasi Data</a>
                </div>

                <!-- Layanan Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Layanan</span>
                    <a href="/layanan" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('layanan*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Layanan Mandiri</a>
                    <a href="/pengaduan" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('pengaduan*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Pengaduan</a>
                    <a href="/buku-tamu" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('buku-tamu*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Buku Tamu</a>
                    <a href="/kontak" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('kontak*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Kontak</a>
                </div>

                <a href="/peta" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('peta*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Peta Spasial</a>

                <div class="pt-6 border-t border-slate-100 mt-4">
                    @auth
                    <div class="flex flex-col gap-3">
                        <a href="/admin" target="_blank" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-emerald-600 text-white text-center shadow-lg shadow-emerald-200">
                            <i class="fa-solid fa-table-cells-large"></i>
                            Panel Admin
                        </a>
                        <form method="POST" action="/admin/logout" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-slate-100 text-slate-600 text-center">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="/admin/login" target="_blank" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-slate-900 text-white text-center shadow-lg">
                        <i class="fa-solid fa-user"></i>
                        Login Sistem
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative bg-slate-950 text-white overflow-hidden">
        <!-- Top gradient wave -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
        <div class="absolute -top-px left-0 right-0 h-24 bg-gradient-to-b from-emerald-500/5 to-transparent pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8">

                <!-- Brand col -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center">
                            <img class="h-8 w-auto" src="{{ asset('img/sinjai.png') }}" alt="Logo">
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold tracking-tight">{{ $site_settings['village_name'] ?? 'Website Desa' }}</h3>
                            <p class="text-[10px] uppercase tracking-widest text-emerald-500 font-bold">Portal Resmi Desa</p>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed mb-8 font-medium max-w-sm">
                        Portal resmi Pemerintah Desa {{ $site_settings['village_name'] ?? '' }} untuk keterbukaan informasi dan pelayanan publik.
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ $site_settings['social_facebook'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 hover:border-emerald-600 transition-all duration-200 text-slate-400 hover:text-white" title="Facebook">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </a>
                        <a href="{{ $site_settings['social_instagram'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 hover:border-emerald-600 transition-all duration-200 text-slate-400 hover:text-white" title="Instagram">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                        <a href="{{ $site_settings['social_youtube'] ?? '#' }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 hover:border-emerald-600 transition-all duration-200 text-slate-400 hover:text-white" title="YouTube">
                            <i class="fa-brands fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="lg:col-span-2">
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-emerald-500">Kontak Kami</h4>
                    <ul class="space-y-4 text-sm text-slate-400 font-medium">
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-emerald-500 text-xs"></i>
                            </span>
                            <span>{{ $site_settings['village_address'] ?? '-' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-emerald-500 text-xs"></i>
                            </span>
                            <span class="truncate">{{ $site_settings['village_email'] ?? '-' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-emerald-500 text-xs"></i>
                            </span>
                            <span>{{ $site_settings['village_phone'] ?? '-' }}</span>
                        </li>
                        <li class="mt-5 pt-5 border-t border-white/5">
                            <div class="flex items-start gap-3">
                                <span class="w-7 h-7 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fa-regular fa-clock text-emerald-500 text-xs"></i>
                                </span>
                                <div>
                                    <span class="block text-xs font-black uppercase tracking-widest text-emerald-500 mb-1">Jam Operasional</span>
                                    <span class="text-slate-300 text-sm font-semibold">Senin – Jumat</span><br>
                                    <span class="text-slate-400 text-sm font-medium">08.00 – 16.00 WITA</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Statistik Pengunjung -->
                <div>
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-6 text-emerald-500">Statistik Pengunjung</h4>
                    @if(isset($visitor_stats))
                    <ul class="space-y-4 text-sm text-slate-400 font-medium">
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-users text-emerald-500 text-xs animate-pulse"></i>
                            </span>
                            <span>Hari Ini: <strong class="text-white">{{ number_format($visitor_stats['today'], 0, ',', '.') }}</strong></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user-clock text-slate-400 text-xs"></i>
                            </span>
                            <span>Kemarin: <strong class="text-slate-300">{{ number_format($visitor_stats['yesterday'], 0, ',', '.') }}</strong></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-emerald-500 text-xs"></i>
                            </span>
                            <span>Total Pengunjung: <strong class="text-emerald-500">{{ number_format($visitor_stats['total'], 0, ',', '.') }}</strong></span>
                        </li>
                    </ul>
                    @else
                    <p class="text-xs text-slate-500 italic">Data tidak tersedia</p>
                    @endif
                </div>

            </div>

            <!-- Bottom bar -->
            <div class="mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-xs font-semibold">
                    &copy; {{ date('Y') }} Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}.
                </p>
                <p class="text-slate-600 text-[10px] font-bold uppercase tracking-[0.15em]">
                    Dikembangkan oleh <a href="https://github.com/kalamangna" target="_blank" class="text-emerald-500 hover:text-emerald-300 transition">kalamangna</a>
                </p>
            </div>
        </div>
    </footer>

    @if(!empty($site_settings['userway_widget_id']))
    <!-- UserWay Accessibility Widget -->
    <script>(function(d){var s = d.createElement("script");s.setAttribute("data-account", "{{ $site_settings['userway_widget_id'] }}");s.setAttribute("src", "https://cdn.userway.org/widget.js");(d.body || d.head).appendChild(s);})(document);</script>
    @endif

    @stack('scripts')
</body>

</html>