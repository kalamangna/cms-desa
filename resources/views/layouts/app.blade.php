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

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta property="og:description" content="@yield('meta_description', 'Website Resmi Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta property="og:image" content="@yield('meta_image', isset($site_settings['village_logo']) ? asset('storage/' . $site_settings['village_logo']) : asset('logo.png'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', ($site_settings['village_name'] ?? 'Website Desa'))">
    <meta property="twitter:description" content="@yield('meta_description', 'Website Resmi Desa ' . ($site_settings['village_name'] ?? '') . '.')">
    <meta property="twitter:image" content="@yield('meta_image', isset($site_settings['village_logo']) ? asset('storage/' . $site_settings['village_logo']) : asset('logo.png'))">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Scripts & Styles -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        .font-heading { font-family: 'Poppins', sans-serif; }
        body { font-family: 'Inter', sans-serif; }
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
            </div>
            <div class="flex gap-6 items-center">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar text-emerald-400"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
                <div class="h-3 w-px bg-white/20"></div>
                <a href="/admin/login" class="hover:text-emerald-400 transition flex items-center gap-2">
                    <i class="fa-solid fa-microchip text-[10px]"></i>
                    Sistem Informasi Desa &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-3">
                        @if(isset($site_settings['village_logo']) && $site_settings['village_logo'])
                            <img class="h-12 w-auto" src="{{ asset('storage/' . $site_settings['village_logo']) }}" alt="Logo">
                        @endif
                        <div class="flex flex-col">
                            <span class="font-heading font-bold text-xl leading-tight text-emerald-600">{{ $site_settings['village_name'] ?? 'Website Desa' }}</span>
                            <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Portal Resmi Desa</span>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden lg:flex lg:items-center lg:space-x-1">
                    <a href="/" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">Beranda</a>
                    <a href="/pemerintahan" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">Pemerintahan</a>
                    <a href="/berita" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">Berita</a>
                    <a href="/statistik" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">Statistik</a>
                    <a href="/dataset" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">Open Data</a>
                    <a href="/publikasi" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">Publikasi</a>
                    <a href="/apbdes" class="text-slate-600 hover:text-emerald-600 px-3 py-2 rounded-md text-sm font-semibold transition">APBDes</a>
                    
                    <div class="h-6 w-px bg-slate-200 mx-4"></div>
                    
                    @auth
                        <div class="flex items-center space-x-4">
                            <a href="/admin" class="bg-emerald-600 text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-emerald-700 transition shadow-sm shadow-emerald-200 flex items-center gap-2">
                                <i class="fa-solid fa-table-cells-large text-xs"></i>
                                Panel Admin
                            </a>
                            <form method="POST" action="/admin/logout">
                                @csrf
                                <button type="submit" class="text-slate-500 hover:text-rose-600 text-sm font-semibold transition flex items-center gap-1">
                                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="/admin/login" class="bg-slate-900 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-slate-800 transition shadow-sm flex items-center gap-2">
                            <i class="fa-solid fa-user text-xs"></i>
                            Login
                        </a>
                    @endauth
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
             class="lg:hidden bg-white border-b border-slate-200" x-cloak>
            <div class="px-4 pt-2 pb-8 space-y-1">
                <a href="/" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">Beranda</a>
                <a href="/pemerintahan" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">Pemerintahan</a>
                <a href="/berita" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">Berita</a>
                <a href="/statistik" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">Statistik</a>
                <a href="/dataset" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">Open Data</a>
                <a href="/publikasi" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">Publikasi</a>
                <a href="/apbdes" class="block px-4 py-4 rounded-2xl text-base font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-600 transition">APBDes</a>
                
                <div class="pt-6 border-t border-slate-100 mt-4">
                    @auth
                        <a href="/admin" class="flex items-center justify-center gap-2 px-4 py-4 rounded-2xl text-base font-bold bg-emerald-600 text-white text-center shadow-lg shadow-emerald-200">
                            <i class="fa-solid fa-table-cells-large"></i>
                            Panel Admin
                        </a>
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
    <footer class="bg-slate-900 text-white pt-32 pb-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-16">
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-8">
                    @if(isset($site_settings['village_logo']) && $site_settings['village_logo'])
                        <img class="h-10 w-auto brightness-0 invert" src="{{ asset('storage/' . $site_settings['village_logo']) }}" alt="Logo">
                    @endif
                    <h3 class="text-xl font-heading font-bold tracking-tight">{{ $site_settings['village_name'] ?? 'Website Desa' }}</h3>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-8 font-medium">
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
                    <li><a href="/berita" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Berita Terbaru</a></li>
                    <li><a href="/statistik" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Statistik Desa</a></li>
                    <li><a href="/dataset" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> Open Data</a></li>
                    <li><a href="/apbdes" class="hover:text-white transition flex items-center gap-2 group"><i class="fa-solid fa-chevron-right text-[10px] text-emerald-600 group-hover:translate-x-1 transition"></i> APBDes</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.2em] mb-10 text-emerald-400">Statistik</h3>
                <div class="space-y-4">
                    <div class="bg-white/5 p-6 rounded-3xl border border-white/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Hari Ini</span>
                            <span class="text-emerald-400 font-bold">142</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Total</span>
                            <span class="text-white font-bold">12.842</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        8 Online
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.2em] mb-10 text-emerald-400">Lokasi</h3>
                <div class="rounded-3xl overflow-hidden bg-white/5 h-36 relative group cursor-pointer border border-white/5 shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover opacity-20 group-hover:scale-110 transition duration-700" alt="Map">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="bg-emerald-600 text-white p-3 rounded-2xl shadow-xl opacity-0 group-hover:opacity-100 transition scale-90 group-hover:scale-100 duration-500">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 pt-12 border-t border-white/5 text-center text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
            &copy; {{ date('Y') }} Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}. Dikembangkan untuk Program Desa Cantik.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
