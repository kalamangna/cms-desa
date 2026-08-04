<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Desa ' . ($site_settings['village_name'] ?? 'Website Desa'))</title>
    <meta name="description" content="@yield('meta_description', 'Portal Resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '. Menyajikan pelayanan publik, publikasi berita pembangunan, transparansi anggaran, dan statistik kependudukan secara akurat.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Desa ' . ($site_settings['village_name'] ?? '') . ', Desa ' . ($site_settings['village_name'] ?? '') . ' ' . \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? '')) . ', Kecamatan ' . \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? '')) . ', Kabupaten ' . \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? '')) . ', ' . ($site_settings['village_name'] ?? '') . ' ' . \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? '')) . ', pemerintah desa, apbdes, berita desa')">
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
    <meta property="og:image" content="@yield('meta_image', asset('img/meta.webp'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @stack('og_extra')

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Desa ' . ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta name="twitter:description" content="@yield('meta_description', 'Portal Resmi Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta name="twitter:image" content="@yield('meta_image', asset('img/meta.webp'))">

    <!-- JSON-LD: Organization + WebSite (global) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@graph": [
            @php
                $vName = trim($site_settings['village_name'] ?? '');
                $distName = \Illuminate\Support\Str::title(preg_replace('/^Kecamatan\s+/i', '', $site_settings['district_name'] ?? ''));
                $regName  = \Illuminate\Support\Str::title(preg_replace('/^(Kabupaten|Kota)\s+/i', '', $site_settings['regency_name'] ?? ''));

                // Ambil koordinat presisi kantor desa dari fitur Peta Spasial (PublicFacility) jika diinput admin
                $officeFacility = \App\Models\PublicFacility::where(function($q) {
                    $q->where('type', 'like', '%kantor%')
                      ->orWhere('name', 'like', '%kantor%');
                })->whereNotNull('latitude')->whereNotNull('longitude')->first();

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
        $isViteHot = file_exists(public_path('hot'));
        $manifest = [];
        $manifestPath = public_path('build/manifest.json');
        if (!$isViteHot && file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true) ?? [];
        }
        $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
        $jsFile  = $manifest['resources/js/app.js']['file'] ?? null;
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

<body class="bg-slate-50 flex flex-col min-h-screen font-sans text-slate-900">
    <!-- Top Bar -->
    <div class="bg-slate-950 text-slate-400 py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-[10px] font-bold uppercase tracking-widest">
            <div class="flex gap-8">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-primary-500"></i> {{ !empty($site_settings['village_address']) ? $site_settings['village_address'] : '-' }}
                </span>
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-phone text-primary-500"></i> {{ !empty($site_settings['village_phone']) ? $site_settings['village_phone'] : '-' }}
                </span>
            </div>
            <div class="flex gap-4 items-center">
                @auth
                <a href="/admin" target="_blank" rel="noopener" class="hover:text-primary-400 transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-gauge text-[9px]"></i> Dashboard
                </a>
                <span class="opacity-30">|</span>
                <form method="POST" action="/admin/logout" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-rose-400 transition-all duration-200 flex items-center gap-1.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 rounded-sm">
                        <i class="fa-solid fa-right-from-bracket text-[9px]"></i> Keluar
                    </button>
                </form>
                @else
                <a href="/admin/login" target="_blank" rel="noopener" class="hover:text-primary-400 transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-lock text-[9px]"></i> Login Admin
                </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 transition-all duration-300 border-b border-slate-200/80"
        x-data="{ mobileMenuOpen: false, scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        :class="scrolled ? 'bg-white/90 backdrop-blur-xl shadow-lg shadow-slate-200/40 py-2' : 'bg-white/95 backdrop-blur-xl py-4'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 transition-all duration-300">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-3">
                        <img class="h-10 w-auto transition-all duration-300" :class="scrolled ? 'h-9' : 'h-11'" src="{{ asset('img/sinjai.webp') }}" alt="Logo" width="44" height="44">
                        <div class="flex flex-col">
                            <span class="font-heading font-bold text-lg leading-tight text-primary-700">{{ $site_settings['village_name'] ?? 'Website Desa' }}</span>
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Portal Resmi Desa</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:items-center lg:space-x-8" x-data="{ openMenu: null }">
                    <a href="/" class="relative py-2 px-1 text-sm font-bold transition-all duration-300 {{ request()->is('/') ? 'text-primary-700' : 'text-slate-600 hover:text-primary-700' }}">
                        Beranda
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 transition-all duration-300 origin-left {{ request()->is('/') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                    </a>

                    <!-- Profil Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'profil'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'text-primary-700' : 'text-slate-600 hover:text-primary-700' }}">
                            Profil <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 transition-all duration-300 origin-left {{ request()->is('profil*') || request()->is('aparatur*') || request()->is('lembaga*') || request()->is('potensi*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'profil'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-48 bg-white/95 backdrop-blur-xl border border-slate-200/50 shadow-2xl shadow-slate-200/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/profil" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Profil Desa</a>
                            <a href="/aparatur" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Aparatur Desa</a>
                            <a href="/lembaga" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Lembaga Desa</a>
                            <a href="/potensi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Potensi Desa</a>
                        </div>
                    </div>

                    <!-- Informasi Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'info'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'text-primary-700' : 'text-slate-600 hover:text-primary-700' }}">
                            Informasi <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 transition-all duration-300 origin-left {{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('dokumen*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'info'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full left-0 w-48 bg-white/95 backdrop-blur-xl border border-slate-200/50 shadow-2xl shadow-slate-200/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/berita" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Berita</a>
                            <a href="/pengumuman" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Pengumuman</a>
                            <a href="/galeri" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Galeri</a>
                            <a href="/dokumen" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Dokumen</a>
                        </div>
                    </div>

                    <!-- Transparansi Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'transparansi'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'text-primary-700' : 'text-slate-600 hover:text-primary-700' }}">
                            Transparansi <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 transition-all duration-300 origin-left {{ request()->is('apbdes*') || request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'transparansi'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full right-0 w-56 bg-white/95 backdrop-blur-xl border border-slate-200/50 shadow-2xl shadow-slate-200/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/apbdes" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">APBDes</a>
                            <a href="/statistik" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Statistik</a>
                            <a href="/dataset" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Open Data</a>
                            <a href="/publikasi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-700 active:scale-95 transition-all">Publikasi Data</a>
                        </div>
                    </div>

                    <!-- Layanan Dropdown -->
                    <div class="relative py-2" @mouseenter="openMenu = 'layanan'" @mouseleave="openMenu = null">
                        <button class="relative py-1 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('layanan*') || request()->is('kontak*') || request()->is('buku-tamu*') || request()->is('pengaduan*') ? 'text-primary-700' : 'text-slate-600 hover:text-primary-700' }}">
                            Layanan <i class="fa-solid fa-chevron-down text-[9px] opacity-60"></i>
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-700 transition-all duration-300 origin-left {{ request()->is('layanan*') || request()->is('kontak*') || request()->is('buku-tamu*') || request()->is('pengaduan*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                        </button>
                        <div x-show="openMenu === 'layanan'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute top-full right-0 w-52 bg-white/95 backdrop-blur-xl border border-slate-200/50 shadow-2xl shadow-slate-200/50 rounded-2xl p-2 z-50" x-cloak>
                            <a href="/layanan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-600 active:scale-95 transition-all">Layanan Mandiri</a>
                            <a href="/pengaduan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-600 active:scale-95 transition-all">Pengaduan</a>
                            <a href="/buku-tamu" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-600 active:scale-95 transition-all">Buku Tamu</a>
                            <a href="/kontak" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-primary-50 hover:text-primary-600 active:scale-95 transition-all">Kontak</a>
                        </div>
                    </div>

                    <!-- Peta Spasial (Top-Level Link) -->
                    <a href="/peta" class="relative py-2 px-1 text-sm font-bold transition-all duration-300 flex items-center gap-1 focus:outline-none {{ request()->is('peta*') ? 'text-primary-600' : 'text-slate-600 hover:text-primary-600' }}">
                        Peta Spasial
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary-600 transition-all duration-300 origin-left {{ request()->is('peta*') ? 'scale-x-100' : 'scale-x-0' }}"></span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu Utama" title="Menu Utama" class="text-slate-600 hover:text-primary-600 p-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 active:scale-95 rounded-md transition-all duration-200" :class="mobileMenuOpen ? 'rotate-90' : ''">
                        <span class="sr-only">Menu Utama Navigasi</span>
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
                <a href="/" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('/') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Beranda</a>

                <!-- Profil Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Profil</span>
                    <a href="/profil" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('profil*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Profil Desa</a>
                    <a href="/aparatur" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('aparatur*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Aparatur Desa</a>
                    <a href="/lembaga" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('lembaga*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Lembaga Desa</a>
                    <a href="/potensi" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('potensi*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Potensi Desa</a>
                </div>

                <!-- Informasi Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Informasi</span>
                    <a href="/berita" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('berita*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Berita</a>
                    <a href="/pengumuman" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('pengumuman*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Pengumuman</a>
                    <a href="/galeri" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('galeri*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Galeri</a>
                    <a href="/dokumen" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('dokumen*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Dokumen</a>
                </div>

                <!-- Transparansi Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Transparansi</span>
                    <a href="/apbdes" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('apbdes*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">APBDes</a>
                    <a href="/statistik" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('statistik*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Statistik</a>
                    <a href="/dataset" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('dataset*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Open Data</a>
                    <a href="/publikasi" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('publikasi*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Publikasi Data</a>
                </div>

                <!-- Layanan Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Layanan</span>
                    <a href="/layanan" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('layanan*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Layanan Mandiri</a>
                    <a href="/pengaduan" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('pengaduan*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Pengaduan</a>
                    <a href="/buku-tamu" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('buku-tamu*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Buku Tamu</a>
                    <a href="/kontak" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('kontak*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Kontak</a>
                </div>

                <a href="/peta" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('peta*') ? 'text-primary-600 bg-primary-50' : 'text-slate-700' }} transition">Peta Spasial</a>

                <div class="pt-6 border-t border-slate-100 mt-4">
                    @auth
                    <div class="flex flex-col gap-3">
                        <a href="/admin" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-primary-600 text-white text-center shadow-lg shadow-primary-200">
                            <i class="fa-solid fa-table-cells-large"></i>
                            Panel Admin
                        </a>
                        <form method="POST" action="/admin/logout" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-slate-100 text-slate-600 text-center hover:bg-slate-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 transition-all duration-200">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="/admin/login" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-slate-900 text-white text-center shadow-lg hover:shadow-xl active:scale-95 transition-all duration-300">
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
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-primary-500 to-transparent"></div>
        <div class="absolute -top-px left-0 right-0 h-24 bg-gradient-to-b from-primary-500/5 to-transparent pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8">

                <!-- Brand col -->
                <div class="lg:col-span-2">
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
                        <a href="{{ $site_settings['social_facebook'] }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-500 hover:bg-blue-600 hover:border-blue-600 hover:text-white transition-all duration-200 active:scale-95" title="Facebook">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </a>
                        @else
                        <span class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-500 opacity-40 cursor-not-allowed select-none" title="Facebook (Belum diatur)">
                            <i class="fa-brands fa-facebook-f text-sm"></i>
                        </span>
                        @endif

                        @if(!empty($site_settings['social_instagram']))
                        <a href="{{ $site_settings['social_instagram'] }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-pink-500 hover:bg-pink-600 hover:border-pink-600 hover:text-white transition-all duration-200 active:scale-95" title="Instagram">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </a>
                        @else
                        <span class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-500 opacity-40 cursor-not-allowed select-none" title="Instagram (Belum diatur)">
                            <i class="fa-brands fa-instagram text-sm"></i>
                        </span>
                        @endif

                        @if(!empty($site_settings['social_youtube']))
                        <a href="{{ $site_settings['social_youtube'] }}" target="_blank" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-red-500 hover:bg-red-600 hover:border-red-600 hover:text-white transition-all duration-200 active:scale-95" title="YouTube">
                            <i class="fa-brands fa-youtube text-sm"></i>
                        </a>
                        @else
                        <span class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-slate-500 opacity-40 cursor-not-allowed select-none" title="YouTube (Belum diatur)">
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
                    Dikembangkan oleh <a href="https://github.com/kalamangna" target="_blank" class="text-primary-400 hover:text-primary-300 underline underline-offset-2 transition">kalamangna</a> &bull; v{{ config('app.version', '1.8.5') }}
                </p>
            </div>
        </div>
    </footer>

    @if(!empty($site_settings['userway_widget_id']))
    <!-- UserWay Accessibility Widget (Lazy Loaded on User Interaction) -->
    <script>
    (function() {
        let loaded = false;
        function loadUserWay() {
            if (loaded) return;
            loaded = true;
            var s = document.createElement("script");
            s.setAttribute("data-account", "{{ $site_settings['userway_widget_id'] }}");
            s.setAttribute("src", "https://cdn.userway.org/widget.js");
            (document.body || document.head).appendChild(s);
        }
        const events = ['mousemove', 'touchstart', 'keydown', 'scroll'];
        events.forEach(function(e) {
            window.addEventListener(e, loadUserWay, { once: true, passive: true });
        });
        setTimeout(loadUserWay, 4000);
    })();
    </script>
    @endif

    @stack('scripts')
</body>

</html>