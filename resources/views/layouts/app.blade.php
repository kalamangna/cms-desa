<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO Meta Tags -->
    <title>@yield('title', ($site_settings['village_name'] ?? 'Website Desa') . ' - ' . ($site_settings['district_name'] ?? ''))</title>
    <meta name="description" content="@yield('meta_description', 'Website Resmi Desa ' . ($site_settings['village_name'] ?? '') . '. Menyajikan informasi berita, statistik, dan transparansi anggaran desa.')">
    <meta name="keywords" content="desa, {{ $site_settings['village_name'] ?? '' }}, {{ $site_settings['district_name'] ?? '' }}, statistik desa, apbdes">
    <meta name="author" content="Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/sinjai.png') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta property="og:description" content="@yield('meta_description', 'Website Resmi Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta property="og:image" content="@yield('meta_image', asset('img/meta.png'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta property="twitter:description" content="@yield('meta_description', 'Website Resmi Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta property="twitter:image" content="@yield('meta_image', asset('img/meta.png'))">

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
</head>

<body class="bg-slate-50 flex flex-col min-h-screen font-sans text-slate-900">
    <!-- Top Bar -->
    <div class="bg-emerald-900 text-white py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.2em]">
            <div class="flex gap-8">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-emerald-400"></i> {{ $site_settings['village_address'] ?? 'Desa Tompobulu, Sinjai' }}
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
                        <a href="/admin" class="hover:text-emerald-400 transition flex items-center gap-2">
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
                    <a href="/admin/login" class="hover:text-emerald-400 transition flex items-center gap-2">
                        <i class="fa-solid fa-user text-[10px]"></i>
                        Login Sistem &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-3">
                        <img class="h-12 w-auto" src="{{ asset('img/sinjai.png') }}" alt="Logo">
                        <div class="flex flex-col">
                            <span class="font-heading font-bold text-xl leading-tight text-emerald-600">{{ $site_settings['village_name'] ?? 'Website Desa' }}</span>
                            <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Portal Resmi Desa</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:items-center lg:space-x-1" x-data="{ openMenu: null }">
                    <a href="/" class="{{ request()->is('/') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 hover:text-emerald-600 hover:bg-slate-50' }} px-4 py-2 rounded-xl text-sm font-bold transition">Beranda</a>
                    
                    <!-- Informasi Dropdown -->
                    <div class="relative" @mouseenter="openMenu = 'info'" @mouseleave="openMenu = null">
                        <button class="{{ request()->is('berita*') || request()->is('pengumuman*') || request()->is('galeri*') || request()->is('kegiatan*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600' }} px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-1">
                            Informasi <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div x-show="openMenu === 'info'" x-transition class="absolute top-full left-0 w-48 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50">
                            <a href="/berita" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Berita</a>
                            <a href="/pengumuman" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Pengumuman</a>
                            <a href="/galeri" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Galeri</a>
                            <a href="/dokumen" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Dokumen</a>
                        </div>
                    </div>

                    <!-- Profil Dropdown -->
                    <div class="relative" @mouseenter="openMenu = 'profil'" @mouseleave="openMenu = null">
                        <button class="{{ request()->is('profil*') || request()->is('pemerintahan*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600' }} px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-1">
                            Profil <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div x-show="openMenu === 'profil'" x-transition class="absolute top-full left-0 w-48 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50">
                            <a href="/profil" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Profil</a>
                            <a href="/pemerintahan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Perangkat</a>
                        </div>
                    </div>

                    <!-- Data Dropdown -->
                    <div class="relative" @mouseenter="openMenu = 'data'" @mouseleave="openMenu = null">
                        <button class="{{ request()->is('statistik*') || request()->is('dataset*') || request()->is('publikasi*') || request()->is('apbdes*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600' }} px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-1">
                            Data <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div x-show="openMenu === 'data'" x-transition class="absolute top-full left-0 w-56 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50">
                            <a href="/statistik" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Statistik</a>
                            <a href="/dataset" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Open Data</a>
                            <a href="/publikasi" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Publikasi</a>
                            <a href="/apbdes" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">APBDes</a>
                        </div>
                    </div>

                    <!-- Layanan Dropdown -->
                    <div class="relative" @mouseenter="openMenu = 'layanan'" @mouseleave="openMenu = null">
                        <button class="{{ request()->is('layanan*') || request()->is('kontak*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600' }} px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-1">
                            Layanan <i class="fa-solid fa-chevron-down text-[10px] opacity-50"></i>
                        </button>
                        <div x-show="openMenu === 'layanan'" x-transition class="absolute top-full left-0 w-48 bg-white border border-slate-100 shadow-2xl rounded-2xl p-2 z-50">
                            <a href="/layanan" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Layanan</a>
                            <a href="/kontak" class="block px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-600 transition">Kontak</a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-emerald-600 p-2 focus:outline-none">
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

                <!-- Informasi Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Informasi</span>
                    <a href="/berita" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('berita*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Berita</a>
                    <a href="/pengumuman" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('pengumuman*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Pengumuman</a>
                    <a href="/galeri" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('galeri*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Galeri</a>
                    <a href="/dokumen" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('dokumen*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Dokumen</a>
                </div>

                <!-- Profil Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Profil</span>
                    <a href="/profil" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('profil*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Profil</a>
                    <a href="/pemerintahan" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('pemerintahan*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Perangkat</a>
                </div>

                <!-- Data Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Data</span>
                    <a href="/statistik" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('statistik*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Statistik</a>
                    <a href="/dataset" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('dataset*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Open Data</a>
                    <a href="/publikasi" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('publikasi*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Publikasi</a>
                    <a href="/apbdes" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('apbdes*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">APBDes</a>
                </div>

                <!-- Layanan Section -->
                <div>
                    <span class="px-4 text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Layanan</span>
                    <a href="/layanan" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('layanan*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Layanan</a>
                    <a href="/kontak" class="block px-4 py-3 rounded-2xl text-base font-bold {{ request()->is('kontak*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-700' }} transition">Kontak</a>
                </div>

                <div class="pt-6 border-t border-slate-100 mt-4">
                    @auth
                        <div class="flex flex-col gap-3">
                            <a href="/admin" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-emerald-600 text-white text-center shadow-lg shadow-emerald-200">
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
                        <a href="/admin/login" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-slate-900 text-white text-center shadow-lg">
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
    <footer class="bg-slate-900 text-white pt-24 pb-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-12 lg:gap-16">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-8">
                    <img class="h-10 w-auto" src="{{ asset('img/sinjai.png') }}" alt="Logo">
                    <h3 class="text-xl font-heading font-bold tracking-tight">{{ $site_settings['village_name'] ?? 'Website Desa' }}</h3>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-8 font-medium max-w-md">
                    Website resmi Pemerintah Desa {{ $site_settings['village_name'] ?? '' }} untuk memberikan layanan informasi publik yang profesional dan transparan.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 hover:border-emerald-600 transition cursor-pointer text-slate-300 hover:text-white" title="Facebook">
                        <i class="fa-brands fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 hover:border-emerald-600 transition cursor-pointer text-slate-300 hover:text-white" title="Instagram">
                        <i class="fa-brands fa-instagram text-lg"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-emerald-600 hover:border-emerald-600 transition cursor-pointer text-slate-300 hover:text-white" title="YouTube">
                        <i class="fa-brands fa-youtube text-lg"></i>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.2em] mb-10 text-emerald-400">Kontak Kami</h3>
                <ul class="space-y-6 text-sm text-slate-400 font-medium">
                    <li class="flex items-start gap-4">
                        <i class="fa-solid fa-location-dot text-emerald-500 mt-1"></i>
                        <span class="leading-relaxed">{{ $site_settings['village_address'] ?? '-' }}</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <i class="fa-solid fa-envelope text-emerald-500"></i>
                        <span class="truncate">{{ $site_settings['village_email'] ?? '-' }}</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <i class="fa-solid fa-phone text-emerald-500"></i>
                        <span>{{ $site_settings['village_phone'] ?? '-' }}</span>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.2em] mb-10 text-emerald-400">Link Cepat</h3>
                <ul class="space-y-4 text-sm text-slate-400 font-bold">
                    <li><a href="/profil" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Profil</a></li>
                    <li><a href="/layanan" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Layanan</a></li>
                    <li><a href="/berita" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Berita</a></li>
                    <li><a href="/statistik" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Statistik</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 pt-12 border-t border-white/5 text-center text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
            &copy; {{ date('Y') }} Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}. Dikembangkan oleh <a href="https://github.com/kalamangna" class="hover:text-emerald-400 transition">kalamangna</a>.
        </div>
    </footer>

    @stack('scripts')
</body>

</html>