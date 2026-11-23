<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Dark mode: set class .dark sedini mungkin agar tidak berkedip (FOUC) sebelum CSS termuat --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- SEO Meta Tags -->
    @php
        $metaTitle       = strip_tags($__env->yieldContent('title') ?: ('Desa ' . ($site_settings['village_name'] ?? 'Website Desa')));
        $metaDescription = strip_tags($__env->yieldContent('meta_description') ?: ('Portal Resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '. Menyajikan pelayanan publik, publikasi berita pembangunan, transparansi anggaran, dan statistik kependudukan secara akurat.'));
        $metaImage       = $__env->yieldContent('meta_image') ?: asset('img/meta.webp');
        $metaCanonical   = $__env->yieldContent('canonical') ?: url()->current();
        $metaOgType      = $__env->yieldContent('og_type') ?: 'website';
    @endphp
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="@yield('meta_keywords', 'Desa ' . ($site_settings['village_name'] ?? '') . ', Desa ' . ($site_settings['village_name'] ?? '') . ' ' . \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? '')) . ', Kecamatan ' . \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? '')) . ', Kabupaten ' . \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? '')) . ', ' . ($site_settings['village_name'] ?? '') . ' ' . \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? '')) . ', pemerintah desa, apbdes, berita desa')">
    <meta name="author" content="Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $metaCanonical }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sinjai.png') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $metaOgType }}">
    <meta property="og:site_name" content="Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:url" content="{{ $metaCanonical }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @stack('og_extra')

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    <!-- JSON-LD: Organization + WebSite (global) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@graph": [
            @php
                $vName = trim($site_settings['village_name'] ?? '');
                $distName = \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? ''));
                $regName  = \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? ''));

                // Koordinat presisi kantor desa (dari cache global AppServiceProvider)
                $officeFacility = $office_facility ?? null;

                $spellingVariants = [];
                if ($vName !== '') {
                    // 1. Nama Asli
                    $spellingVariants[] = $vName;

                    // 2. Pemisahan / penggabungan kata khusus untuk desa yang memiliki variasi ejaan resmi
                    $vLower = strtolower($vName);
                    if ($vLower === 'tompobulu') {
                        $spellingVariants[] = 'Tompo Bulu';
                    } elseif ($vLower === 'tompo bulu') {
                        $spellingVariants[] = 'Tompobulu';
                    } elseif ($vLower === 'lappacinrana') {
                        $spellingVariants[] = 'Lappa Cinrana';
                    } elseif ($vLower === 'lappa cinrana') {
                        $spellingVariants[] = 'Lappacinrana';
                    } elseif ($vLower === 'lamattiriattang') {
                        $spellingVariants[] = 'Lamatti Riattang';
                    } elseif ($vLower === 'lamatti riattang') {
                        $spellingVariants[] = 'Lamattiriattang';
                    }

                    $spellingVariants = array_unique($spellingVariants);
                }

                // Generasi kombinasi alternateName dinamis lengkap
                $alternateNames = [];
                foreach ($spellingVariants as $variant) {
                    $alternateNames[] = "Desa {$variant}";
                    $alternateNames[] = "Pemerintah Desa {$variant}";
                    $alternateNames[] = "Pemdes {$variant}";
                    $alternateNames[] = "{$variant}";
                    if ($regName) {
                        $alternateNames[] = "Desa {$variant} {$regName}";
                        $alternateNames[] = "{$variant} {$regName}";
                    }
                    if ($distName) {
                        $alternateNames[] = "Desa {$variant} {$distName}";
                        $alternateNames[] = "{$variant} {$distName}";
                    }
                }
                $alternateNames = array_values(array_unique($alternateNames));
            @endphp
            {
                "@type": "GovernmentOffice",
                "@id": "{{ url('/') }}/#organization",
                "name": "Pemerintah Desa {{ $vName }}",
                "alternateName": {!! json_encode($alternateNames, JSON_UNESCAPED_SLASHES) !!},
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('img/sinjai.webp') }}"
                },
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "{{ $site_settings['village_address'] ?? '' }}",
                    "addressLocality": "{{ $site_settings['district_name'] ?? '' }}",
                    "addressRegion": "{{ $site_settings['regency_name'] ?? '' }}",
                    "addressCountry": "ID"
                },
                "telephone": "{{ $site_settings['village_phone'] ?? '' }}",
                "sameAs": [
                    "{{ $site_settings['social_facebook'] ?? '' }}",
                    "{{ $site_settings['social_instagram'] ?? '' }}"
                ],
                "hasMap": "https://maps.google.com/maps?q={{ urlencode('Desa ' . $vName . ' Kecamatan ' . $distName . ' Kabupaten ' . $regName) }}"@if($officeFacility),
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": "{{ $officeFacility->latitude }}",
                    "longitude": "{{ $officeFacility->longitude }}"
                }
                @endif
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

    <!-- Fonts & Icons Resource Hints -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://sinjaikab.go.id">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" fetchpriority="high">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    </noscript>
    {{-- Preload FontAwesome woff2 fonts agar browser download paralel sejak awal --}}
    {{-- tanpa ini, browser baru tahu setelah all.min.css selesai diparse (~1000ms) --}}
    <link rel="preload" as="font" type="font/woff2" crossorigin
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-solid-900.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-brands-400.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-regular-400.woff2">
    <script>
        (function() {
            function loadFA() {
                var l = document.createElement('link');
                l.rel = 'stylesheet';
                l.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css';
                document.head.appendChild(l);
            }
            if (window.requestIdleCallback) {
                requestIdleCallback(loadFA);
            } else {
                setTimeout(loadFA, 1500);
            }
        })();
    </script>
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    </noscript>
    @php
        static $cachedManifest = null;
        if ($cachedManifest === null) {
            $isViteHot = file_exists(public_path('hot'));
            $cachedManifest = [];
            $manifestPath = public_path('build/manifest.json');
            if (!$isViteHot && file_exists($manifestPath)) {
                $cachedManifest = json_decode(file_get_contents($manifestPath), true) ?? [];
            }
        }
        $cssFile = $cachedManifest['resources/css/app.css']['file'] ?? null;
        $jsFile  = $cachedManifest['resources/js/app.js']['file'] ?? null;
    @endphp

    @if($cssFile)
        {{-- Preload hint agar browser segera ambil CSS --}}
        <link rel="preload" as="style" href="{{ asset('build/' . $cssFile) }}" fetchpriority="high">
        {{-- Render-blocking (mencegah FOUC) --}}
        <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    @else
        {{-- Fallback dev (Vite dev server) --}}
        @vite(['resources/css/app.css'])
    @endif

    <!-- Scripts & Styles -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        /*
         * Override FontAwesome CDN font-display:block → swap
         * Deklarasi lengkap (dengan src) diperlukan agar override bekerja.
         * Referensi: https://developer.chrome.com/docs/lighthouse/performance/font-display
         */
        @font-face {
            font-family: 'Font Awesome 6 Free';
            font-style: normal;
            font-weight: 900;
            font-display: swap;
            src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-solid-900.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Font Awesome 6 Free';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-regular-400.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Font Awesome 6 Brands';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/webfonts/fa-brands-400.woff2') format('woff2');
        }

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
    @if($jsFile)
        <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Dynamic Primary Color Theme Styles -->
    @if(isset($site_settings['primary_color']))
    <style>
        :root {
            --primary-base: {{ $site_settings['primary_color'] }};
            --color-primary-50: color-mix(in srgb, var(--primary-base) 5%, #ffffff);
            --color-primary-100: color-mix(in srgb, var(--primary-base) 10%, #ffffff);
            --color-primary-200: color-mix(in srgb, var(--primary-base) 20%, #ffffff);
            --color-primary-300: color-mix(in srgb, var(--primary-base) 30%, #ffffff);
            --color-primary-400: color-mix(in srgb, var(--primary-base) 50%, #ffffff);
            --color-primary-500: var(--primary-base);
            --color-primary-600: color-mix(in srgb, var(--primary-base) 85%, #000000);
            --color-primary-700: color-mix(in srgb, var(--primary-base) 70%, #000000);
            --color-primary-800: color-mix(in srgb, var(--primary-base) 55%, #000000);
            --color-primary-900: color-mix(in srgb, var(--primary-base) 40%, #000000);
            --color-primary-950: color-mix(in srgb, var(--primary-base) 25%, #000000);
        }
    </style>
    @endif
</head>

<body class="bg-slate-50 dark:bg-slate-950 flex flex-col min-h-screen font-sans text-slate-900 dark:text-slate-100">
    <!-- Top Bar -->
    <div class="bg-slate-950 dark:bg-black text-slate-400 py-2 hidden md:block border-b border-transparent dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4 text-[10px] font-bold uppercase tracking-widest">
            <div class="flex gap-4 lg:gap-8 min-w-0">
                <span class="flex items-center gap-2 min-w-0">
                    <i class="fa-solid fa-location-dot text-primary-500 shrink-0"></i>
                    <span class="truncate">{{ !empty($site_settings['village_address']) ? $site_settings['village_address'] : '-' }}</span>
                </span>
                <span class="flex items-center gap-2 shrink-0 whitespace-nowrap">
                    <i class="fa-solid fa-phone text-primary-500 shrink-0"></i> {{ !empty($site_settings['village_phone']) ? $site_settings['village_phone'] : '-' }}
                </span>
            </div>
            <div class="flex gap-4 items-center shrink-0 whitespace-nowrap">
                @auth
                <a href="/admin" target="_blank" rel="noopener" class="hover:text-primary-400 transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-gauge text-[9px]"></i> Dashboard
                </a>
                <span class="opacity-30">|</span>
                <form method="POST" action="/admin/logout" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-rose-400 transition-all duration-200 flex items-center gap-1.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 rounded-sm">
                        <i class="fa-solid fa-right-from-bracket text-[9px]"></i> Logout
                    </button>
                </form>
                @else
                <a href="/admin/login" target="_blank" rel="noopener" class="hover:text-primary-400 transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-lock text-[9px]"></i> Login
                </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 py-2 backdrop-blur-xl transition-shadow duration-300 border-b border-slate-200/80 dark:border-slate-800"
        x-data="{
            mobileMenuOpen: false,
            scrolled: false,
            activeGroup: '{{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'profil' : (request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'info' : (request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'transparansi' : (request()->is('layanan*') || request()->is('pengaduan*') || request()->is('buku-tamu*') || request()->is('kontak*') ? 'layanan' : ''))) }}'
        }"
        x-effect="document.body.classList.toggle('overflow-hidden', mobileMenuOpen)"
        @keydown.escape.window="mobileMenuOpen = false"
        @resize.window="if (window.innerWidth >= 1024) mobileMenuOpen = false"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        :class="scrolled ? 'bg-white/90 dark:bg-slate-900/90 shadow-lg shadow-slate-200/40 dark:shadow-slate-950/40' : 'bg-white/95 dark:bg-slate-900/95'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center min-w-0">
                    <a href="/" class="min-w-0 flex items-center gap-2.5 sm:gap-3">
                        <img class="h-10 w-auto shrink-0" src="{{ asset('img/sinjai.webp') }}" alt="Logo" width="44" height="44">
                        <div class="flex flex-col min-w-0">
                            <span class="font-heading font-bold text-base sm:text-lg leading-tight text-primary-700 dark:text-white truncate">{{ $site_settings['village_name'] ?? 'Website Desa' }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 dark:text-slate-400 font-bold truncate">Portal Resmi Desa</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:items-center lg:space-x-5 xl:space-x-8 shrink-0" x-data="{ openMenu: null }" @click.outside="openMenu = null">
                    <a href="/" class="relative py-2 px-1 text-sm font-bold whitespace-nowrap transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-lg {{ request()->is('/') ? 'text-primary-700 dark:text-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-700 dark:hover:text-primary-400' }}">
                        Beranda
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 dark:bg-primary-400 transition-all duration-300 origin-left {{ request()->is('/') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                    </a>

                    <!-- Profil Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'profil'" @mouseleave="openMenu = null">
                        <button type="button" @click="openMenu = openMenu === 'profil' ? null : 'profil'" :aria-expanded="openMenu === 'profil'" class="relative py-1 px-1 text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-lg {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'text-primary-700 dark:text-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-700 dark:hover:text-primary-400' }}">
Profil <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 dark:bg-primary-400 transition-all duration-300 origin-left {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'profil'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-48 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-slate-950/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/profil" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Profil Desa</a>
                            <a href="/aparatur" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Aparatur Desa</a>
                            <a href="/lembaga" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Lembaga Desa</a>
                            <a href="/potensi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Potensi Desa</a>
                        </div>
                    </div>

                    <!-- Informasi Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'info'" @mouseleave="openMenu = null">
                        <button type="button" @click="openMenu = openMenu === 'info' ? null : 'info'" :aria-expanded="openMenu === 'info'" class="relative py-1 px-1 text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-lg {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'text-primary-700 dark:text-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-700 dark:hover:text-primary-400' }}">
Informasi <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 dark:bg-primary-400 transition-all duration-300 origin-left {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'info'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-48 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-slate-950/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/berita" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Berita</a>
                            <a href="/pengumuman" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Pengumuman</a>
                            <a href="/galeri" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Galeri</a>
                            <a href="/dokumen" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Dokumen</a>
                        </div>
                    </div>

                    <!-- Transparansi Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'transparansi'" @mouseleave="openMenu = null">
                        <button type="button" @click="openMenu = openMenu === 'transparansi' ? null : 'transparansi'" :aria-expanded="openMenu === 'transparansi'" class="relative py-1 px-1 text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-lg {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'text-primary-700 dark:text-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-700 dark:hover:text-primary-400' }}">
Transparansi <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 dark:bg-primary-400 transition-all duration-300 origin-left {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'transparansi'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full right-0 w-56 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-slate-950/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/apbdes" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">APBDes</a>
                            <a href="/statistik" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Statistik</a>
                            <a href="/dataset" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Open Data</a>
                            <a href="/publikasi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-700 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Publikasi Data</a>
                        </div>
                    </div>

                    <!-- Layanan Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'layanan'" @mouseleave="openMenu = null">
                        <button type="button" @click="openMenu = openMenu === 'layanan' ? null : 'layanan'" :aria-expanded="openMenu === 'layanan'" class="relative py-1 px-1 text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-lg {{ request()->is('layanan*') || request()->is('kontak*') || request()->is('buku-tamu*') || request()->is('pengaduan*') ? 'text-primary-700 dark:text-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-700 dark:hover:text-primary-400' }}">
Layanan <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 dark:bg-primary-400 transition-all duration-300 origin-left {{ request()->is('layanan*') || request()->is('kontak*') || request()->is('buku-tamu*') || request()->is('pengaduan*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'layanan'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full right-0 w-52 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-slate-950/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/layanan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-600 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Layanan Mandiri</a>
                            <a href="/pengaduan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-600 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Pengaduan</a>
                            <a href="/buku-tamu" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-600 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Buku Tamu</a>
                            <a href="/kontak" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-primary-50 dark:hover:bg-primary-950/40 hover:text-primary-600 dark:hover:text-primary-300 active:scale-95 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400">Kontak</a>
                        </div>
                    </div>

                    <!-- Peta Spasial (Top-Level Link) -->
                    <a href="/peta" class="relative py-2 px-1 text-sm font-bold whitespace-nowrap transition-all duration-300 flex items-center gap-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-lg {{ request()->is('peta*') ? 'text-primary-600 dark:text-primary-400' : 'text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400' }}">
                        Peta Spasial
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-600 dark:bg-primary-400 transition-all duration-300 origin-left {{ request()->is('peta*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                    </a>

                    <!-- Theme Toggle (Desktop) -->
                    <button type="button"
                        x-data="{ dark: document.documentElement.classList.contains('dark') }"
                        @click="dark = !dark; localStorage.setItem('theme', dark ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', dark)"
                        aria-label="Ganti mode gelap atau terang" title="Mode Gelap / Terang"
                        class="h-11 w-11 inline-flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 active:scale-95 transition-all duration-200">
                        <i class="fa-solid fa-sun text-lg" x-show="!dark"></i>
                        <i class="fa-solid fa-moon text-lg" x-show="dark" x-cloak></i>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-1 lg:hidden shrink-0">
                    <!-- Theme Toggle (Mobile) -->
                    <button type="button"
                        x-data="{ dark: document.documentElement.classList.contains('dark') }"
                        @click="dark = !dark; localStorage.setItem('theme', dark ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', dark)"
                        aria-label="Ganti mode gelap atau terang" title="Mode Gelap atau Terang"
                        class="h-11 w-11 inline-flex items-center justify-center rounded-md text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 active:scale-95 transition-all duration-200">
                        <i class="fa-solid fa-sun text-lg" x-show="!dark"></i>
                        <i class="fa-solid fa-moon text-lg" x-show="dark" x-cloak></i>
                    </button>
                    <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        :aria-expanded="mobileMenuOpen"
                        aria-controls="mobile-navigation-drawer"
                        aria-label="Menu Utama"
                        title="Menu Utama"
                        class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 h-11 w-11 inline-flex items-center justify-center focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 active:scale-95 rounded-md transition-all duration-200"
                        :class="mobileMenuOpen ? 'rotate-90' : ''">
                        <span class="sr-only">Menu Utama Navigasi</span>
                        <i class="fa-solid fa-bars text-xl" x-show="!mobileMenuOpen"></i>
                        <i class="fa-solid fa-xmark text-xl" x-show="mobileMenuOpen" x-cloak></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Backdrop -->
        <div x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileMenuOpen = false"
            class="lg:hidden fixed inset-0 top-[81px] bg-slate-950/50 backdrop-blur-xs z-40"
            x-cloak></div>

        <!-- Mobile Menu Drawer -->
        <div id="mobile-navigation-drawer"
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden absolute inset-x-0 top-full z-50 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-950/20 overflow-y-auto h-[calc(100dvh-5rem)] md:h-auto md:max-h-[calc(100dvh-5rem)] overscroll-contain"
            x-cloak>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 pb-6 min-h-full flex flex-col justify-between gap-6">
                <div class="space-y-2 md:space-y-5">
                <!-- Home Link -->
                <a href="/" class="flex items-center justify-between px-4 min-h-11 py-2.5 rounded-2xl text-sm font-bold {{ request()->is('/') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">
                    <span>Beranda</span>
                </a>

                <div class="space-y-1.5 md:space-y-0 md:grid md:grid-cols-2 md:gap-6">
                    <!-- Profil Section -->
                    <div class="rounded-2xl md:rounded-none">
                        <button type="button"
                            @click="activeGroup = activeGroup === 'profil' ? '' : 'profil'"
                            :aria-expanded="activeGroup === 'profil'"
                            class="w-full md:hidden flex items-center justify-between px-4 min-h-11 py-2.5 rounded-2xl text-sm font-bold {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'text-primary-700 bg-primary-50/70 dark:text-primary-400 dark:bg-primary-950/40' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">
                            <span>Profil</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="activeGroup === 'profil' ? 'rotate-180 text-primary-600 dark:text-primary-400' : ''"></i>
                        </button>
                        <span class="hidden md:block px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Profil</span>
                        <div class="grid transition-[grid-template-rows,opacity,visibility] duration-200 ease-out md:grid-rows-[1fr]! md:opacity-100! md:visible!"
                            :class="activeGroup === 'profil' ? 'grid-rows-[1fr] opacity-100 visible' : 'grid-rows-[0fr] opacity-0 invisible'">
                            <div class="overflow-hidden min-h-0">
                                <div class="pt-1 pl-3 space-y-1 md:pl-0 md:pt-0">
                                    <a href="/profil" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('profil*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Profil Desa</a>
                                    <a href="/aparatur" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('aparatur*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Aparatur Desa</a>
                                    <a href="/lembaga" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('lembaga*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Lembaga Desa</a>
                                    <a href="/potensi" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('potensi*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Potensi Desa</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Section -->
                    <div class="rounded-2xl md:rounded-none">
                        <button type="button"
                            @click="activeGroup = activeGroup === 'info' ? '' : 'info'"
                            :aria-expanded="activeGroup === 'info'"
                            class="w-full md:hidden flex items-center justify-between px-4 min-h-11 py-2.5 rounded-2xl text-sm font-bold {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'text-primary-700 bg-primary-50/70 dark:text-primary-400 dark:bg-primary-950/40' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">
                            <span>Informasi</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="activeGroup === 'info' ? 'rotate-180 text-primary-600 dark:text-primary-400' : ''"></i>
                        </button>
                        <span class="hidden md:block px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Informasi</span>
                        <div class="grid transition-[grid-template-rows,opacity,visibility] duration-200 ease-out md:grid-rows-[1fr]! md:opacity-100! md:visible!"
                            :class="activeGroup === 'info' ? 'grid-rows-[1fr] opacity-100 visible' : 'grid-rows-[0fr] opacity-0 invisible'">
                            <div class="overflow-hidden min-h-0">
                                <div class="pt-1 pl-3 space-y-1 md:pl-0 md:pt-0">
                                    <a href="/berita" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('berita*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Berita</a>
                                    <a href="/pengumuman" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('pengumuman*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Pengumuman</a>
                                    <a href="/galeri" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('galeri*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Galeri</a>
                                    <a href="/dokumen" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('dokumen*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Dokumen</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transparansi Section -->
                    <div class="rounded-2xl md:rounded-none">
                        <button type="button"
                            @click="activeGroup = activeGroup === 'transparansi' ? '' : 'transparansi'"
                            :aria-expanded="activeGroup === 'transparansi'"
                            class="w-full md:hidden flex items-center justify-between px-4 min-h-11 py-2.5 rounded-2xl text-sm font-bold {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'text-primary-700 bg-primary-50/70 dark:text-primary-400 dark:bg-primary-950/40' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">
                            <span>Transparansi</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="activeGroup === 'transparansi' ? 'rotate-180 text-primary-600 dark:text-primary-400' : ''"></i>
                        </button>
                        <span class="hidden md:block px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Transparansi</span>
                        <div class="grid transition-[grid-template-rows,opacity,visibility] duration-200 ease-out md:grid-rows-[1fr]! md:opacity-100! md:visible!"
                            :class="activeGroup === 'transparansi' ? 'grid-rows-[1fr] opacity-100 visible' : 'grid-rows-[0fr] opacity-0 invisible'">
                            <div class="overflow-hidden min-h-0">
                                <div class="pt-1 pl-3 space-y-1 md:pl-0 md:pt-0">
                                    <a href="/apbdes" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('apbdes*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">APBDes</a>
                                    <a href="/statistik" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('statistik*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Statistik</a>
                                    <a href="/dataset" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('dataset*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Open Data</a>
                                    <a href="/publikasi" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('publikasi*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Publikasi Data</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Layanan Section -->
                    <div class="rounded-2xl md:rounded-none">
                        <button type="button"
                            @click="activeGroup = activeGroup === 'layanan' ? '' : 'layanan'"
                            :aria-expanded="activeGroup === 'layanan'"
                            class="w-full md:hidden flex items-center justify-between px-4 min-h-11 py-2.5 rounded-2xl text-sm font-bold {{ request()->is('layanan*') || request()->is('pengaduan*') || request()->is('buku-tamu*') || request()->is('kontak*') ? 'text-primary-700 bg-primary-50/70 dark:text-primary-400 dark:bg-primary-950/40' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">
                            <span>Layanan</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="activeGroup === 'layanan' ? 'rotate-180 text-primary-600 dark:text-primary-400' : ''"></i>
                        </button>
                        <span class="hidden md:block px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2">Layanan</span>
                        <div class="grid transition-[grid-template-rows,opacity,visibility] duration-200 ease-out md:grid-rows-[1fr]! md:opacity-100! md:visible!"
                            :class="activeGroup === 'layanan' ? 'grid-rows-[1fr] opacity-100 visible' : 'grid-rows-[0fr] opacity-0 invisible'">
                            <div class="overflow-hidden min-h-0">
                                <div class="pt-1 pl-3 space-y-1 md:pl-0 md:pt-0">
                                    <a href="/layanan" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('layanan*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Layanan Mandiri</a>
                                    <a href="/pengaduan" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('pengaduan*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Pengaduan</a>
                                    <a href="/buku-tamu" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('buku-tamu*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Buku Tamu</a>
                                    <a href="/kontak" class="flex items-center px-4 min-h-11 py-2.5 rounded-xl text-sm font-semibold {{ request()->is('kontak*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">Kontak</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="/peta" class="flex items-center justify-between px-4 min-h-11 py-2.5 rounded-2xl text-sm font-bold {{ request()->is('peta*') ? 'text-primary-700 bg-primary-50 dark:text-primary-400 dark:bg-primary-950/40' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60' }} transition">
                    <span>Peta Spasial</span>
                </a>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    @auth
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="/admin" target="_blank" rel="noopener noreferrer" class="flex-1 flex items-center justify-center gap-2 px-4 min-h-12 py-3 rounded-2xl text-sm font-bold bg-primary-600 text-white text-center shadow-sm">
                            <i class="fa-solid fa-gauge"></i>
                            Dashboard
                        </a>
                        <form method="POST" action="/admin/logout" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 min-h-12 py-3 rounded-2xl text-sm font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-center hover:bg-slate-200 dark:hover:bg-slate-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 dark:focus-visible:ring-slate-600 transition-all duration-200">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="/admin/login" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 px-4 min-h-12 py-3 rounded-2xl text-sm font-bold bg-slate-900 dark:bg-slate-800 text-white dark:border dark:border-slate-700 text-center shadow-sm hover:bg-slate-800 dark:hover:bg-slate-700 active:scale-95 transition-all duration-200">
                        <i class="fa-solid fa-lock"></i>
                        Login
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
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-primary-500 to-transparent"></div>
        <div class="absolute -top-px left-0 right-0 h-24 bg-gradient-to-b from-primary-500/5 to-transparent pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8">

                <!-- Brand col -->
                <div class="md:col-span-2 lg:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary-600/20 border border-primary-500/30 flex items-center justify-center">
                            <img class="h-8 w-auto" src="{{ asset('img/sinjai.webp') }}" alt="Logo" width="32" height="32">
                        </div>
                        <div>
                            <h3 class="text-lg font-heading font-bold tracking-tight">{{ $site_settings['village_name'] ?? 'Website Desa' }}</h3>
                            <p class="text-[10px] uppercase tracking-widest text-primary-500 font-bold">Portal Resmi Desa</p>
                        </div>
                    </div>
                    <p class="text-slate-300 text-sm leading-relaxed mb-8 font-medium max-w-sm">
                        Portal resmi Pemerintah Desa {{ $site_settings['village_name'] ?? '' }} untuk keterbukaan informasi dan pelayanan publik.
                    </p>
                    <div class="flex gap-3">
                        @if(!empty($site_settings['social_facebook']))
                        <a href="{{ $site_settings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook resmi desa" class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-500 hover:bg-blue-600 hover:border-blue-600 hover:text-white transition-all duration-200 active:scale-95" title="Facebook">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </a>
                        @else
                        <span class="w-11 h-11 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-500 opacity-40 cursor-not-allowed select-none" title="Facebook belum diatur">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </span>
                        @endif

                        @if(!empty($site_settings['social_instagram']))
                        <a href="{{ $site_settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram resmi desa" class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-pink-500 hover:bg-pink-600 hover:border-pink-600 hover:text-white transition-all duration-200 active:scale-95" title="Instagram">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                        @else
                        <span class="w-11 h-11 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-500 opacity-40 cursor-not-allowed select-none" title="Instagram belum diatur">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </span>
                        @endif

                        @if(!empty($site_settings['social_youtube']))
                        <a href="{{ $site_settings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube resmi desa" class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-red-500 hover:bg-red-600 hover:border-red-600 hover:text-white transition-all duration-200 active:scale-95" title="YouTube">
                            <i class="fa-brands fa-youtube text-sm"></i>
                        </a>
                        @else
                        <span class="w-11 h-11 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-500 opacity-40 cursor-not-allowed select-none" title="YouTube belum diatur">
                            <i class="fa-brands fa-youtube text-sm"></i>
                        </span>
                        @endif

                    </div>
                </div>

                <!-- Kontak -->
                <div class="lg:col-span-2">
                    <h2 class="text-xs font-black uppercase tracking-widest mb-6 text-primary-400">Kontak Kami</h2>
                    <ul class="space-y-4 text-sm text-slate-300 font-medium">
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-600/20 border border-primary-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-primary-400 text-xs"></i>
                            </span>
                            <span>{{ $site_settings['village_address'] ?? '-' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-600/20 border border-primary-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-primary-400 text-xs"></i>
                            </span>
                            <span class="truncate">{{ $site_settings['village_email'] ?? '-' }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-600/20 border border-primary-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-primary-400 text-xs"></i>
                            </span>
                            <span>{{ $site_settings['village_phone'] ?? '-' }}</span>
                        </li>
                        <li class="mt-5 pt-5 border-t border-white/5">
                            <div class="flex items-start gap-3">
                                <span class="w-7 h-7 rounded-lg bg-primary-600/20 border border-primary-500/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fa-regular fa-clock text-primary-400 text-xs"></i>
                                </span>
                                <div>
                                    <span class="block text-xs font-black uppercase tracking-widest text-primary-400 mb-1">Jam Operasional</span>
                                    <span class="text-slate-300 text-sm font-semibold">Senin – Jumat</span><br>
                                    <span class="text-slate-300 text-sm font-medium">08.00 – 16.00 WITA</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Statistik Pengunjung -->
                <div>
                    <h2 class="text-xs font-black uppercase tracking-widest mb-6 text-primary-400">Statistik Pengunjung</h2>
                    @if(isset($visitor_stats))
                    <ul class="space-y-4 text-sm text-slate-300 font-medium">
                        <li class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-primary-600/20 border border-primary-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-users text-primary-500 text-xs animate-pulse"></i>
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
                            <span class="w-7 h-7 rounded-lg bg-primary-600/20 border border-primary-500/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-chart-line text-primary-500 text-xs"></i>
                            </span>
                            <span>Total Pengunjung: <strong class="text-primary-500">{{ number_format($visitor_stats['total'], 0, ',', '.') }}</strong></span>
                        </li>
                    </ul>
                    @else
                    <p class="text-xs text-slate-500 italic">Data tidak tersedia</p>
                    @endif
                </div>

            </div>

            <!-- Bottom bar -->
            <div class="mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-400 text-xs font-semibold">
                    &copy; {{ date('Y') }} Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}.
                </p>
                <p class="text-slate-300 text-[10px] font-bold uppercase tracking-widest">
                    Dikembangkan oleh <a href="https://github.com/kalamangna" target="_blank" rel="noopener noreferrer" class="text-primary-400 hover:text-primary-300 underline underline-offset-2 transition">kalamangna</a> &bull; v{{ config('app.version', '1.28.1') }}
                </p>
            </div>
        </div>
    </footer>

    @if(filter_var($site_settings['enable_a11y_widget'] ?? true, FILTER_VALIDATE_BOOLEAN))
    <!-- Widget Aksesibilitas Sinjai (a11y-sinjaikab) -->
    <script 
        src="https://cdn.jsdelivr.net/gh/kalamangna/a11y-sinjaikab@main/dist/widget.min.js" 
        data-position="{{ $site_settings['a11y_widget_position'] ?? 'bottom-left' }}" 
        data-lang="id" 
        defer>
    </script>
    @endif

    @stack('scripts')
</body>

</html>