@extends('layouts.app')

@section('title', 'Statistik | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Penyajian visualisasi data statistik sektoral kependudukan, pekerjaan, pendidikan, dan kesehatan mikro yang dikelola Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

@section('content')

{{-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ --}}
{{-- ═══════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════ --}}
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
                    <span class="text-white">Statistik</span>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                    Statistik <span class="text-emerald-500 italic">Desa</span>
                </h1>
                <p class="text-slate-300 text-lg mt-2">
                    Data kependudukan, sosial, dan ekonomi Desa.
                </p>
            </div>
            {{-- Live indicator --}}
            <div class="flex items-center gap-3 bg-white/5 backdrop-blur border border-white/10 rounded-2xl px-5 py-3 w-fit mb-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-emerald-400 text-xs font-black uppercase tracking-widest">Live Data</span>
                <span class="text-slate-400 text-xs font-medium">{{ date('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20"
         x-data="statistikApp()"
         x-init="init()"
         id="statistik-main">

        @if($isEmptyDb)
            <div class="text-center py-16 bg-white rounded-[32px] border border-slate-100 shadow-sm max-w-2xl mx-auto">
                <i class="fa-solid fa-users-slash text-slate-300 text-3xl mb-3 block"></i>
                <h3 class="text-slate-400 font-bold text-sm">Data Kependudukan Belum Tersedia</h3>
            </div>
        @else
            {{-- ─── SUMMARY CARDS ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                {{-- Total Penduduk --}}
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-6 text-white shadow-xl shadow-emerald-600/30 flex flex-col justify-between min-h-[120px]">
                    <span class="text-emerald-200 text-[9px] font-black uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <i class="fa-solid fa-users text-xs"></i> Total Penduduk
                    </span>
                    <div>
                        <span class="text-3xl md:text-4xl font-extrabold block leading-none">{{ number_format($summaryCards['total_penduduk'], 0, ',', '.') }}</span>
                        <span class="text-emerald-200 text-[10px] font-semibold mt-2 block">jiwa aktif {{ $summaryCards['latest_year'] }}</span>
                    </div>
                </div>

                {{-- YoY Growth --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[120px]">
                    <span class="text-slate-400 text-[9px] font-black uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-line text-emerald-500 text-xs"></i> Pertumbuhan YoY
                    </span>
                    <div>
                        @if(!is_null($summaryCards['yoy_growth']))
                            @php $yoy = $summaryCards['yoy_growth']; @endphp
                            <span class="text-3xl font-extrabold block leading-none {{ $yoy >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $yoy >= 0 ? '+' : '' }}{{ number_format($yoy, 2, ',', '.') }}%
                            </span>
                            <span class="text-slate-400 text-[10px] font-semibold mt-2 block">vs {{ $summaryCards['latest_year'] - 1 }}</span>
                        @else
                            <span class="text-2xl font-extrabold block leading-none text-slate-300">—</span>
                            <span class="text-slate-400 text-[10px] font-semibold mt-2 block">data historis belum ada</span>
                        @endif
                    </div>
                </div>

                {{-- Tahun Terkini --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[120px]">
                    <span class="text-slate-400 text-[9px] font-black uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar text-emerald-500 text-xs"></i> Tahun Data
                    </span>
                    <div>
                        <span class="text-3xl font-extrabold text-slate-900 block leading-none">{{ $summaryCards['latest_year'] }}</span>
                        <span class="text-slate-400 text-[10px] font-semibold mt-2 block">data real-time</span>
                    </div>
                </div>

                {{-- Dusun Terbanyak --}}
                <div class="bg-slate-900 rounded-2xl p-6 text-white flex flex-col justify-between min-h-[120px]">
                    <span class="text-slate-400 text-[9px] font-black uppercase tracking-[0.2em] flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-emerald-400 text-xs"></i> Dusun Terbanyak
                    </span>
                    <div>
                        <span class="text-lg font-extrabold block leading-snug">{{ $summaryCards['top_dusun'] }}</span>
                        <span class="text-slate-400 text-[10px] font-semibold mt-2 block">{{ number_format($summaryCards['top_dusun_count'], 0, ',', '.') }} jiwa</span>
                    </div>
                </div>
            </div>

    {{-- Mobile: Dropdown Category Selector --}}
    <div class="md:hidden mb-6">
        <label class="block text-xs font-black uppercase tracking-[0.2em] text-slate-500 mb-2">Pilih Kategori</label>
        <div class="relative">
            <select
                x-model="activeTab"
                @change="onCategoryChange($event.target.value)"
                class="w-full appearance-none bg-white border border-slate-200 rounded-2xl px-5 py-3.5 pr-10 text-sm font-bold text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                id="stat-category-select">
                @foreach($categories as $category)
                <option value="{{ $category->slug }}">{{ $category->name }} ({{ $category->indicators->count() }} indikator)</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </div>
        </div>
    </div>

    {{-- Layout: Sidebar (desktop) + Content --}}
    <div class="flex gap-8 items-start">

        {{-- ── SIDEBAR (desktop only) ──────────────────────────────────── --}}
        <aside class="hidden md:block w-64 flex-shrink-0 sticky top-24 self-start">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 pt-5 pb-3">
                    <span class="text-[9px] font-black uppercase tracking-[0.25em] text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-emerald-500"></i> Kategori
                    </span>
                </div>
                <nav class="pb-3">
                    @foreach($categories as $category)
                    <button
                        @click="onCategoryChange('{{ $category->slug }}')"
                        :class="activeTab === '{{ $category->slug }}'
                            ? 'bg-emerald-50 text-emerald-700 border-l-4 border-emerald-500'
                            : 'text-slate-600 border-l-4 border-transparent hover:bg-slate-50 hover:text-slate-900'"
                        class="w-full text-left flex items-center justify-between gap-2 px-5 py-3 text-sm font-semibold transition-all duration-200"
                        id="sidebar-tab-{{ $category->slug }}">
                        <span class="flex items-center gap-2 leading-snug">
                            <i class="fa-solid fa-chart-bar text-[10px] flex-shrink-0"
                               :class="activeTab === '{{ $category->slug }}' ? 'text-emerald-500' : 'text-slate-300'"></i>
                            {{ $category->name }}
                        </span>
                        <span
                            :class="activeTab === '{{ $category->slug }}' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                            class="text-[10px] font-black px-2 py-0.5 rounded-full flex-shrink-0 transition-all duration-200">
                            {{ $category->indicators->count() }}
                        </span>
                    </button>
                    @endforeach
                </nav>

                {{-- Filter Dusun & Tahun --}}
                <div class="border-t border-slate-100 px-5 py-4">
                    <form method="GET" action="/statistik" @submit.prevent>
                        <input type="hidden" name="kategori" :value="activeTab">

                        @if($dusuns->count() > 0)
                        {{-- Filter Dusun --}}
                        <div class="mb-4">
                            <label class="block text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-emerald-500"></i> Filter Dusun
                            </label>
                            <div class="relative">
                                <select
                                    name="dusun_id"
                                    @change="onFilterChange()"
                                    x-model="selectedDusun"
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 pr-8 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                                    <option value="">Semua Dusun</option>
                                    @foreach($dusuns as $dusun)
                                        <option value="{{ $dusun->id }}">{{ $dusun->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div x-show="selectedDusun" x-cloak>
                            <button type="button" @click="resetFilters()" class="inline-flex items-center gap-1 text-[10px] text-rose-400 hover:text-rose-600 font-semibold mt-2 transition-colors">
                                <i class="fa-solid fa-xmark"></i> Hapus filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ── MAIN PANEL ───────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            @foreach($categories as $catIndex => $category)
            @php
                $totalIndicators = $category->indicators->count();
                $allYears = $category->indicators->flatMap(fn($i) => $i->data->pluck('year'))->unique()->sort()->values();
                $isPendudukCategory = str_contains(strtolower($category->name), 'penduduk');
            @endphp

            <div x-show="activeTab === '{{ $category->slug }}'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 data-panel-slug="{{ $category->slug }}"
                 x-cloak>

                {{-- Section header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="h-px w-6 bg-emerald-500"></div>
                            <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.25em]">Visualisasi Data</span>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900" x-text="'{{ addslashes($category->name) }}' + getDusunTitleSuffix()">{{ $category->name }}</h2>
                        @if($category->description)
                        <p class="text-slate-500 text-sm mt-1">{{ $category->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-slate-400 text-xs font-semibold">
                        <i class="fa-regular fa-calendar-check text-emerald-500"></i>
                        Diperbarui {{ date('d M Y') }}
                    </div>
                </div>

                {{-- ── CHART CONTAINER ───────────────────────────────────── --}}
                <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm overflow-hidden p-6 md:p-8 mb-6"
                     x-data="{
                         currentType: 'bar',
                         showPercent: false,
                         filterYear: 'all',
                         compareYear: 'none',
                         showGender: false,
                         renderChart() {
                             window.renderStatChart('{{ $category->slug }}', this.currentType, this.showPercent, this.filterYear, this.compareYear, this.showGender);
                         }
                     }"
                     x-init="setTimeout(() => { if (activeTab === '{{ $category->slug }}') renderChart(); }, 150)">

                    {{-- Toolbar --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-base font-heading font-extrabold text-slate-900" x-text="'Grafik {{ addslashes($category->name) }}' + getDusunTitleSuffix()">Grafik {{ $category->name }}</h3>
                            <p class="text-slate-400 text-xs font-medium mt-0.5">{{ $totalIndicators }} indikator &mdash; {{ $allYears->count() > 1 ? $allYears->first() . '–' . $allYears->last() : ($allYears->first() ?? date('Y')) }}</p>
                        </div>

                        {{-- Controls --}}
                        <div class="flex flex-wrap items-center gap-2">

                            {{-- Compare Year --}}
                            @if($allYears->count() > 1)
                            <div class="relative">
                                <select x-model="compareYear"
                                        @change="renderChart('{{ $category->slug }}')"
                                        class="appearance-none bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 pr-7 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 cursor-pointer">
                                    <option value="none">Bandingkan...</option>
                                    @foreach($allYears as $yr)
                                    <option value="{{ $yr }}">vs {{ $yr }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-slate-400">
                                    <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                            @endif

                            {{-- Toggle Persentase --}}
                            <button @click="showPercent = !showPercent; renderChart('{{ $category->slug }}')"
                                    :class="showPercent ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold border transition-all duration-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-percent text-[10px]"></i>
                                <span x-text="showPercent ? 'Persentase' : 'Jumlah'"></span>
                            </button>

                            @if($category->mapping_table === 'citizens')
                            {{-- Toggle Jenis Kelamin --}}
                            <button @click="showGender = !showGender; renderChart('{{ $category->slug }}')"
                                    x-show="compareYear === 'none'"
                                    :class="showGender ? 'bg-sky-600 text-white border-sky-600 shadow-md shadow-sky-600/10' : 'bg-white text-slate-600 border-slate-200 hover:border-sky-300 hover:text-sky-700'"
                                    class="px-3 py-2 rounded-xl text-xs font-bold border transition-all duration-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-venus-mars text-[10px]"></i>
                                <span>Jenis Kelamin</span>
                            </button>
                            @endif

                            {{-- Tipe Grafik --}}
                            <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                                <button @click="currentType = 'bar'; renderChart('{{ $category->slug }}')"
                                        :class="currentType === 'bar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 focus:outline-none">
                                    <i class="fa-solid fa-chart-bar"></i>
                                </button>
                                <button @click="currentType = 'line'; renderChart('{{ $category->slug }}')"
                                        :class="currentType === 'line' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 focus:outline-none">
                                    <i class="fa-solid fa-chart-line"></i>
                                </button>
                                <button @click="currentType = 'doughnut'; renderChart('{{ $category->slug }}')"
                                        :class="currentType === 'doughnut' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 focus:outline-none">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Chart area --}}
                    <div class="h-80 md:h-[400px] relative">
                        <div id="chart-{{ $category->slug }}" class="w-full h-full"></div>
                    </div>
                </div>

                {{-- ── TABEL DETAIL ───────────────────────────────────────── --}}
                @if($totalIndicators > 0)
                @php
                    $isCitizens = ($category->mapping_table === 'citizens');
                    // Tentukan tahun yang ditampilkan: filter aktif atau tahun terbaru
                    $tableYear = $selectedYear
                        ? (int)$selectedYear
                        : (int)($allYears->last() ?? date('Y'));
                @endphp
                <div class="bg-white rounded-[28px] border border-slate-100 shadow-sm overflow-hidden mb-8">
                    <div class="flex items-center justify-between px-6 md:px-8 pt-6 pb-4">
                        <div>
                            <h3 class="text-base font-heading font-extrabold text-slate-900" x-text="'Tabel {{ addslashes($category->name) }}{{ $isCitizens ? ' Berdasarkan Jenis Kelamin' : '' }}' + getDusunTitleSuffix()">
                                Tabel {{ $category->name }}{{ $isCitizens ? ' Berdasarkan Jenis Kelamin' : '' }}
                            </h3>
                            <p class="text-slate-400 text-xs font-medium mt-0.5">
                                {{ $isCitizens ? 'Tahun ' . $tableYear : 'Data per indikator berdasarkan tahun' }}
                            </p>
                        </div>
                        {{-- Tombol Ekspor --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 border border-slate-200 text-slate-600 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all duration-200">
                                <i class="fa-solid fa-download text-[10px]"></i>
                                <span>Ekspor</span>
                                <i class="fa-solid fa-chevron-down text-[8px]" :class="open ? 'rotate-180' : ''"
                                   style="transition: transform .2s"></i>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 mt-1.5 w-40 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-30">
                                <button onclick="exportTableCSV('tabel-{{ $category->slug }}')"
                                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <i class="fa-solid fa-file-csv text-emerald-500 w-4 text-center"></i> CSV
                                </button>
                                <button onclick="exportTableExcel('tabel-{{ $category->slug }}', '{{ addslashes($category->name) }}')"
                                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <i class="fa-solid fa-file-excel text-emerald-600 w-4 text-center"></i> Excel
                                </button>
                                <button onclick="exportTablePDF('tabel-{{ $category->slug }}', '{{ addslashes($category->name) }}')"
                                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <i class="fa-solid fa-file-pdf text-red-500 w-4 text-center"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        @if($isCitizens)
                        {{-- ── Tabel citizens: Indikator | Laki-laki | Perempuan | Total ── --}}
                        @php
                            $firstIndicatorUnit = $category->indicators->first()->unit ?? '';
                        @endphp
                        <table id="tabel-{{ $category->slug }}" class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-t border-slate-100 border-b border-slate-100 bg-slate-50/70">
                                    <th class="text-left px-6 md:px-8 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 sticky left-0 bg-slate-50/70 z-10 min-w-[200px]">Indikator</th>
                                    <th class="text-right px-5 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-sky-500 whitespace-nowrap leading-tight">
                                        Laki-laki<br>
                                        <span class="text-[9px] font-medium text-slate-400 normal-case tracking-normal">({{ $firstIndicatorUnit }})</span>
                                    </th>
                                    <th class="text-right px-5 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-pink-500 whitespace-nowrap leading-tight">
                                        Perempuan<br>
                                        <span class="text-[9px] font-medium text-slate-400 normal-case tracking-normal">({{ $firstIndicatorUnit }})</span>
                                    </th>
                                    <th class="text-right px-6 md:px-8 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 whitespace-nowrap leading-tight">
                                        Total<br>
                                        <span class="text-[9px] font-medium text-slate-400 normal-case tracking-normal">({{ $firstIndicatorUnit }})</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody-{{ $category->slug }}" class="divide-y divide-slate-50">
                                @php
                                    $grandTotal = 0;
                                    foreach ($category->indicators as $ind) {
                                        $dp = $ind->data->firstWhere('year', $tableYear);
                                        $grandTotal += $dp ? (int)($dp->value ?? 0) : 0;
                                    }
                                @endphp
                                @foreach($category->indicators as $indicator)
                                @php
                                    $dp  = $indicator->data->firstWhere('year', $tableYear);
                                    $valL = $dp ? (int)($dp->value_male   ?? 0) : 0;
                                    $valP = $dp ? (int)($dp->value_female ?? 0) : 0;
                                    $valT = $dp ? (int)($dp->value        ?? 0) : 0;
                                    $pctT = $grandTotal > 0 ? round(($valT / $grandTotal) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors duration-100 group">
                                    <td class="px-6 md:px-8 py-3.5 font-semibold text-slate-800 sticky left-0 bg-white group-hover:bg-slate-50/50 transition-colors text-xs leading-snug">
                                        {{ $indicator->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <span class="text-xs font-bold text-sky-700">{{ number_format($valL, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <span class="text-xs font-bold text-pink-600">{{ number_format($valP, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 md:px-8 py-3.5 text-right text-xs font-extrabold text-slate-900 whitespace-nowrap">
                                        {{ number_format($valT, 0, ',', '.') }}
                                        @if($grandTotal > 0)
                                            <span class="text-[10px] text-slate-400 font-medium ml-1">({{ $pctT }}%)</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @else
                        {{-- ── Tabel non-citizens (Keluarga, dll): Indikator | Jumlah | Persentase ── --}}
                        @php
                            $firstIndicatorUnit = $category->indicators->first()->unit ?? 'Keluarga';
                        @endphp
                        <table id="tabel-{{ $category->slug }}" class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="border-t border-slate-100 border-b border-slate-100 bg-slate-50/70">
                                    <th class="text-left px-6 md:px-8 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 sticky left-0 bg-slate-50/70 z-10 min-w-[200px]">Indikator</th>
                                    <th class="text-right px-6 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 whitespace-nowrap leading-tight">
                                        Jumlah<br>
                                        <span class="text-[9px] font-medium text-slate-400 normal-case tracking-normal">({{ $firstIndicatorUnit }})</span>
                                    </th>
                                    <th class="text-right px-6 md:px-8 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 whitespace-nowrap leading-tight">
                                        Persentase<br>
                                        <span class="text-[9px] font-medium text-slate-400 normal-case tracking-normal">(%)</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody-{{ $category->slug }}" class="divide-y divide-slate-50">
                                @php
                                    $grandTotal = 0;
                                    foreach ($category->indicators as $ind) {
                                        $dp = $ind->data->firstWhere('year', $tableYear);
                                        $grandTotal += $dp ? (int)($dp->value ?? 0) : 0;
                                    }
                                @endphp
                                @foreach($category->indicators as $indicator)
                                @php
                                    $dp  = $indicator->data->firstWhere('year', $tableYear);
                                    $valT = $dp ? (int)($dp->value ?? 0) : 0;
                                    $pctT = $grandTotal > 0 ? round(($valT / $grandTotal) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors duration-100 group">
                                    <td class="px-6 md:px-8 py-3.5 font-semibold text-slate-800 sticky left-0 bg-white group-hover:bg-slate-50/50 transition-colors text-xs leading-snug">
                                        {{ $indicator->name }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                        <span class="text-xs font-bold text-emerald-600">{{ number_format($valT, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 md:px-8 py-3.5 text-right text-xs font-extrabold text-slate-900 whitespace-nowrap">
                                        {{ str_replace('.', ',', (string)$pctT) }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
                @endif

            </div>{{-- /tab panel --}}
            @endforeach

        </div>{{-- /main panel --}}
    </div>{{-- /layout --}}

    @endif
</div>{{-- /alpine wrapper --}}
</div>{{-- /bg-slate-50 --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>


    // ── Palette ────────────────────────────────────────────────────────────
    const palette = [
        '#10b981','#0ea5e9','#f59e0b','#8b5cf6','#ec4899',
        '#f43f5e','#06b6d4','#14b8a6','#f97316','#3b82f6',
    ];

    // ── Category data (server-rendered) ────────────────────────────────────
    @php
        $phpPalette = [
            '#10b981','#0ea5e9','#f59e0b','#8b5cf6','#ec4899',
            '#f43f5e','#06b6d4','#14b8a6','#f97316','#3b82f6',
        ];
    @endphp
    const categoryData = {
        @foreach($categories as $category)
        '{{ $category->slug }}': {
            name: {!! json_encode($category->name) !!},
            isCitizens: {{ $category->mapping_table === 'citizens' ? 'true' : 'false' }},
            isPenduduk: {{ str_contains(strtolower($category->name), 'penduduk') ? 'true' : 'false' }},
            isEducation: {{ str_contains(strtolower($category->name), 'pendidikan') ? 'true' : 'false' }},
            years: {!! json_encode($category->indicators->flatMap(fn($i) => $i->data ? $i->data->pluck('year') : collect())->unique()->sort()->values()->toArray()) !!},
            indicators: [
                @foreach($category->indicators as $idx => $indicator)
                @php
                    $c = $phpPalette[$idx % count($phpPalette)];
                    if (str_contains(strtolower($indicator->name), 'laki-laki') || str_contains(strtolower($indicator->name), 'laki laki')) {
                        $c = '#0ea5e9';
                    } elseif (str_contains(strtolower($indicator->name), 'perempuan')) {
                        $c = '#ec4899';
                    }
                @endphp
                {
                    name: {!! json_encode($indicator->name) !!},
                    unit: {!! json_encode($indicator->unit ?? 'Jiwa') !!},
                    color: '{{ $c }}',
                    data: {!! json_encode(($indicator->data ?? collect())->map(fn($d) => [
                        'year'         => (int)$d->year,
                        'value'        => (int)$d->value,
                        'value_male'   => (int)($d->value_male ?? 0),
                        'value_female' => (int)($d->value_female ?? 0),
                    ])->toArray()) !!}
                },
                @endforeach
            ]
        },
        @endforeach
    };

    // ── Chart instances store ───────────────────────────────────────────────
    const chartInstances = {};

    const educationOrder = [
        'tidak punya ijazah','tidak/belum pernah sekolah','tidak sekolah','belum sekolah',
        'sd','smp','sma','d1','d2','d3','d4','s1','profesi','s2','s3'
    ];

    function sortIndicators(cat, indicators) {
        let sorted = [...indicators];
        if (cat.isEducation) {
            sorted.sort((a, b) => {
                const na = a.name.toLowerCase().trim();
                const nb = b.name.toLowerCase().trim();
                let ia = educationOrder.findIndex(o => na.includes(o));
                let ib = educationOrder.findIndex(o => nb.includes(o));
                if (ia === -1) ia = 999;
                if (ib === -1) ib = 999;
                return ia - ib;
            });
        } else if (cat.isPenduduk) {
            sorted.sort((a, b) => {
                const na = a.name.toLowerCase();
                const nb = b.name.toLowerCase();
                if (na.includes('laki') && !nb.includes('laki')) return -1;
                if (!na.includes('laki') && nb.includes('laki')) return 1;
                return 0;
            });
        } else {
            const latestYear = cat.years.length ? Math.max(...cat.years.map(Number)) : new Date().getFullYear();
            sorted.sort((a, b) => {
                const va = a.data.find(d => Number(d.year) === Number(latestYear))?.value || 0;
                const vb = b.data.find(d => Number(d.year) === Number(latestYear))?.value || 0;
                return vb - va;
            });
        }
        return sorted;
    }

    // ── Main render function ────────────────────────────────────────────────
    window.renderStatChart = function(slug, type, showPercent, filterYear, compareYear, showGender) {
        const cat = categoryData[slug];
        if (!cat) return;
        const el = document.getElementById('chart-' + slug);
        if (!el || !el.isConnected) return;

        try {
            if (chartInstances[slug]) {
                chartInstances[slug].destroy();
                delete chartInstances[slug];
            }
        } catch (e) {
            delete chartInstances[slug];
        }

        let filteredIndicators = sortIndicators(cat, cat.indicators);

        // Apply year filter
        let activeYears = cat.years.map(Number);
        if (filterYear && filterYear !== 'all') {
            activeYears = [Number(filterYear)];
        }

        // Compare mode: show two specific years side by side in bar chart
        let compareYearNum = null;
        if (compareYear && compareYear !== 'none') {
            compareYearNum = Number(compareYear);
        }

        let showGenderSplit = showGender && activeYears.length >= 1 && (type === 'bar' || type === 'line');

        // Calculate grand total (for percentage)
        const grandTotals = {};
        if (showPercent) {
            activeYears.forEach(yr => {
                grandTotals[yr] = filteredIndicators.reduce((sum, ind) => {
                    const d = ind.data.find(d => Number(d.year) === yr);
                    return sum + (d ? d.value : 0);
                }, 0);
            });
        }

        const getValue = (ind, yr) => {
            const d = ind.data.find(d => Number(d.year) === yr);
            const raw = d ? d.value : 0;
            if (showPercent && grandTotals[yr] > 0) {
                return Math.round((raw / grandTotals[yr]) * 1000) / 10; // 1dp %
            }
            return raw;
        };

        const yLabel = showPercent ? '%' : '';

        // ── BAR / LINE ────────────────────────────────────────────────────
        if (type === 'bar' || type === 'line') {
            let series;

            if (showGenderSplit) {
                const yr = activeYears[0];
                series = [
                    {
                        name: 'Laki-laki',
                        data: filteredIndicators.map(ind => {
                            const d = ind.data.find(d => Number(d.year) === yr);
                            const val = d ? (d.value_male ?? 0) : 0;
                            if (showPercent && d && d.value > 0) {
                                return Math.round((val / d.value) * 1000) / 10;
                            }
                            return val;
                        })
                    },
                    {
                        name: 'Perempuan',
                        data: filteredIndicators.map(ind => {
                            const d = ind.data.find(d => Number(d.year) === yr);
                            const val = d ? (d.value_female ?? 0) : 0;
                            if (showPercent && d && d.value > 0) {
                                return Math.round((val / d.value) * 1000) / 10;
                            }
                            return val;
                        })
                    }
                ];
            } else if (compareYearNum !== null && activeYears.length === 1) {
                // Compare two years: each indicator gets 2 bars
                const yr1 = activeYears[0];
                const yr2 = compareYearNum;
                series = [
                    {
                        name: String(yr1),
                        data: filteredIndicators.map(ind => getValue(ind, yr1)),
                    },
                    {
                        name: String(yr2),
                        data: filteredIndicators.map(ind => getValue(ind, yr2)),
                    },
                ];
            } else if (filteredIndicators.length === 1 && activeYears.length > 1) {
                // Single indicator with multi years: show trend over years
                series = [{
                    name: filteredIndicators[0].name,
                    data: activeYears.map(yr => getValue(filteredIndicators[0], yr)),
                }];
            } else {
                // Multiple indicators: each as a series, years on x-axis (multi-year)
                // But if year is filtered to single year, show as single-year bar
                if (activeYears.length === 1) {
                    const yr = activeYears[0];
                    series = [{
                        name: String(yr),
                        data: filteredIndicators.map(ind => getValue(ind, yr)),
                    }];
                } else {
                    series = filteredIndicators.map(ind => ({
                        name: ind.name,
                        data: activeYears.map(yr => getValue(ind, yr)),
                    }));
                }
            }

            const hasGender = filteredIndicators.some(i =>
                i.name.toLowerCase().includes('laki') || i.name.toLowerCase().includes('perempuan')
            );
            const colors = showGenderSplit
                ? ['#0ea5e9', '#ec4899']
                : (hasGender
                    ? filteredIndicators.map((i, idx) => {
                        if (i.name.toLowerCase().includes('laki')) return '#0ea5e9';
                        if (i.name.toLowerCase().includes('perempuan')) return '#ec4899';
                        return i.color || palette[idx % palette.length];
                      })
                    : filteredIndicators.map((i, idx) => i.color || palette[idx % palette.length]));

            const xCats = (showGenderSplit || (compareYearNum !== null && activeYears.length === 1))
                ? filteredIndicators.map(i => i.name)
                : (activeYears.length === 1 ? filteredIndicators.map(i => i.name) : activeYears.map(String));

            const config = {
                chart: {
                    type: type,
                    height: '100%',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false } }
                },
                colors: colors,
                series: series,
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: type === 'line' ? 3 : 0 },
                plotOptions: { bar: { borderRadius: 6, columnWidth: filteredIndicators.length > 6 ? '80%' : '55%', distributed: (type === 'bar' && series.length === 1) } },
                xaxis: {
                    categories: xCats,
                    labels: {
                        show: type !== 'bar' || series.length > 1,
                        rotate: -35,
                        rotateAlways: false,
                        hideOverlappingLabels: true,
                        style: { colors: '#94a3b8', fontWeight: 600, fontSize: '10px' }
                    },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: {
                    min: 0,
                    labels: {
                        style: { colors: '#94a3b8', fontWeight: 600, fontSize: '11px' },
                        formatter: (val) => {
                            if (showPercent) return val + '%';
                            if (val % 1 === 0) return val.toLocaleString('id-ID');
                            return '';
                        }
                    }
                },
                grid: { borderColor: 'rgba(148,163,184,0.08)', strokeDashArray: 5, xaxis: { lines: { show: false } }, yaxis: { lines: { show: true } } },
                legend: {
                    show: true,
                    position: 'bottom',
                    fontFamily: 'Inter, sans-serif',
                    fontWeight: 600,
                    fontSize: '11px',
                    labels: { colors: '#64748b' },
                    markers: { width: 8, height: 8, radius: 8, offsetY: -1 }
                },
                tooltip: {
                    theme: 'light',
                    y: { formatter: (val, opts) => {
                        const unit = filteredIndicators[opts.seriesIndex]?.unit || '';
                        if (showPercent) return val + '%';
                        return val.toLocaleString('id-ID') + (unit ? ' ' + unit : '');
                    }}
                }
            };
            chartInstances[slug] = new ApexCharts(el, config);
            chartInstances[slug].render();
            return;
        }

        // ── DOUGHNUT ───────────────────────────────────────────────────────
        if (type === 'doughnut') {
            const latestYear = activeYears.length ? Math.max(...activeYears) : new Date().getFullYear();
            const values = filteredIndicators.map(ind => getValue(ind, latestYear));
            const colors = filteredIndicators.map((i, idx) => {
                if (i.name.toLowerCase().includes('laki')) return '#0ea5e9';
                if (i.name.toLowerCase().includes('perempuan')) return '#ec4899';
                return i.color || palette[idx % palette.length];
            });

            const config = {
                chart: { type: 'donut', height: '100%', fontFamily: 'Inter, sans-serif' },
                colors: colors,
                labels: filteredIndicators.map(i => i.name),
                series: values,
                dataLabels: { enabled: false },
                stroke: { show: false },
                plotOptions: {
                    pie: { donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: { show: true, fontFamily: 'Poppins, sans-serif', fontWeight: 700, fontSize: '13px', color: '#64748b' },
                            value: { show: true, fontFamily: 'Inter, sans-serif', fontWeight: 800, fontSize: '22px', color: '#0f172a',
                                formatter: (val) => showPercent ? val + '%' : parseInt(val).toLocaleString('id-ID') },
                            total: {
                                show: true,
                                label: 'Total (' + latestYear + ')',
                                fontFamily: 'Poppins, sans-serif', fontWeight: 700, fontSize: '11px', color: '#94a3b8',
                                formatter: (w) => {
                                    const tot = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return showPercent ? '100%' : tot.toLocaleString('id-ID');
                                }
                            }
                        }
                    }}
                },
                legend: { position: 'bottom', fontFamily: 'Inter, sans-serif', fontWeight: 600, fontSize: '11px', labels: { colors: '#64748b' }, markers: { width: 8, height: 8, radius: 8, offsetY: -1 } },
                tooltip: { theme: 'light', y: { formatter: (val) => {
                    if (showPercent) return val + '%';
                    return val.toLocaleString('id-ID');
                }}}
            };
            chartInstances[slug] = new ApexCharts(el, config);
            chartInstances[slug].render();
        }
    };



    // ── Dusuns Map ─────────────────────────────────────────────────────────
    const dusunNames = {
        @foreach($dusuns as $d)
        '{{ $d->id }}': '{{ addslashes($d->name) }}',
        @endforeach
    };

    // ── Alpine component ───────────────────────────────────────────────────
    window.statistikApp = function() {
        const defaultSlug = '{{ request()->query('kategori', $categories->first()?->slug ?? '') }}';
        return {
            activeTab: defaultSlug || Object.keys(categoryData)[0] || '',
            selectedDusun: '{{ $selectedDusunId ?? '' }}',
            selectedYear: '{{ date('Y') }}',
            isLoading: false,

            getDusunTitleSuffix() {
                if (this.selectedDusun && dusunNames[this.selectedDusun]) {
                    return ' - Dusun ' + dusunNames[this.selectedDusun];
                }
                return '';
            },

            init() {
                this.$nextTick(() => {
                    this.renderActiveChart();
                    this.updateUrl(this.activeTab);
                });
            },
            onCategoryChange(slug) {
                this.activeTab = slug;
                this.$nextTick(() => {
                    this.renderActiveChart();
                    this.updateUrl(slug);
                });
            },
            onFilterChange() {
                this.fetchData();
            },
            resetFilters() {
                this.selectedDusun = '';
                this.selectedYear = '{{ date('Y') }}';
                this.fetchData();
            },
            async fetchData() {
                this.isLoading = true;
                const params = new URLSearchParams();
                if (this.activeTab) params.set('kategori', this.activeTab);
                if (this.selectedDusun) params.set('dusun_id', this.selectedDusun);
                if (this.selectedYear) params.set('year', this.selectedYear);
                params.set('json', '1');

                try {
                    const res = await fetch(`/statistik?${params.toString()}`);
                    if (!res.ok) throw new Error('Fetch failed');
                    const json = await res.json();

                    // Update categoryData with new indicator values
                    Object.keys(json).forEach(slug => {
                        if (categoryData[slug]) {
                            const newCat = json[slug];
                            categoryData[slug].years = newCat.years || [];
                            categoryData[slug].indicators = newCat.indicators;
                            this.updateTableDOM(slug);
                        }
                    });

                    // Re-render charts
                    this.renderActiveChart();
                    this.updateUrl(this.activeTab);
                } catch (e) {
                    console.error('Error fetching stat data:', e);
                } finally {
                    this.isLoading = false;
                }
            },
            updateTableDOM(slug) {
                const cat = categoryData[slug];
                if (!cat) return;
                const tbody = document.getElementById('tbody-' + slug);
                if (!tbody) return;

                const sortedIndicators = sortIndicators(cat, cat.indicators);
                const tableYear = this.selectedYear ? Number(this.selectedYear) : (cat.years.length ? Math.max(...cat.years.map(Number)) : new Date().getFullYear());

                let html = '';
                if (cat.isCitizens) {
                    const grandTotal = sortedIndicators.reduce((sum, ind) => {
                        const d = ind.data.find(d => Number(d.year) === tableYear);
                        return sum + (d ? d.value : 0);
                    }, 0);

                    sortedIndicators.forEach(ind => {
                        const d = ind.data.find(d => Number(d.year) === tableYear);
                        const valL = d ? (d.value_male ?? 0) : 0;
                        const valP = d ? (d.value_female ?? 0) : 0;
                        const valT = d ? (d.value ?? 0) : 0;
                        const pctT = grandTotal > 0 ? (Math.round((valT / grandTotal) * 1000) / 10) : 0;

                        html += `<tr class="hover:bg-slate-50/50 transition-colors duration-100 group">
                            <td class="px-6 md:px-8 py-3.5 font-semibold text-slate-800 sticky left-0 bg-white group-hover:bg-slate-50/50 transition-colors text-xs leading-snug">
                                ${ind.name}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <span class="text-xs font-bold text-sky-700">${valL.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <span class="text-xs font-bold text-pink-600">${valP.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="px-6 md:px-8 py-3.5 text-right text-xs font-extrabold text-slate-900 whitespace-nowrap">
                                ${valT.toLocaleString('id-ID')}
                                ${grandTotal > 0 ? `<span class="text-[10px] text-slate-400 font-medium ml-1">(${pctT.toString().replace('.', ',')}%)</span>` : ''}
                            </td>
                        </tr>`;
                    });
                } else {
                    const grandTotal = sortedIndicators.reduce((sum, ind) => {
                        const d = ind.data.find(d => Number(d.year) === tableYear);
                        return sum + (d ? d.value : 0);
                    }, 0);

                    sortedIndicators.forEach(ind => {
                        const d = ind.data.find(d => Number(d.year) === tableYear);
                        const valT = d ? (d.value ?? 0) : 0;
                        const pctT = grandTotal > 0 ? (Math.round((valT / grandTotal) * 1000) / 10) : 0;

                        html += `<tr class="hover:bg-slate-50/50 transition-colors duration-100 group">
                            <td class="px-6 md:px-8 py-3.5 font-semibold text-slate-800 sticky left-0 bg-white group-hover:bg-slate-50/50 transition-colors text-xs leading-snug">
                                ${ind.name}
                            </td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                <span class="text-xs font-bold text-emerald-600">${valT.toLocaleString('id-ID')}</span>
                            </td>
                            <td class="px-6 md:px-8 py-3.5 text-right text-xs font-extrabold text-slate-900 whitespace-nowrap">
                                ${pctT.toString().replace('.', ',')}%
                            </td>
                        </tr>`;
                    });
                }

                tbody.innerHTML = html;
            },
            renderActiveChart() {
                if (!categoryData[this.activeTab]) return;
                setTimeout(() => {
                    const chartCard = document.querySelector(`[data-panel-slug="${this.activeTab}"] [x-data]`);
                    if (chartCard && chartCard._x_dataStack && chartCard._x_dataStack[0]) {
                        const state = chartCard._x_dataStack[0];
                        window.renderStatChart(this.activeTab, state.currentType || 'bar', state.showPercent || false, state.filterYear || 'all', state.compareYear || 'none', state.showGender || false);
                    } else {
                        window.renderStatChart(this.activeTab, 'bar', false, 'all', 'none', false);
                    }
                }, 50);
            },
            updateUrl(slug) {
                const url = new URL(window.location);
                url.searchParams.set('kategori', slug);
                if (this.selectedDusun) {
                    url.searchParams.set('dusun_id', this.selectedDusun);
                } else {
                    url.searchParams.delete('dusun_id');
                }
                if (this.selectedYear) {
                    url.searchParams.set('year', this.selectedYear);
                } else {
                    url.searchParams.delete('year');
                }
                window.history.replaceState({}, '', url.toString());
            }
        };
    };
</script>

<script>
// ── Ekspor Tabel ───────────────────────────────────────────────────────────
function getTableData(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return { headers: [], rows: [] };

    const headers = [];
    // Ambil semua th dari thead (satu baris header saja)
    table.querySelectorAll('thead tr:first-child th').forEach(th => {
        headers.push(th.innerText.trim());
    });

    const rows = [];
    table.querySelectorAll('tbody tr, tfoot tr').forEach(tr => {
        const row = [];
        tr.querySelectorAll('td').forEach(td => {
            row.push(td.innerText.trim().replace(/\n/g, ' '));
        });
        if (row.length) rows.push(row);
    });

    return { headers, rows };
}

function exportTableCSV(tableId) {
    const { headers, rows } = getTableData(tableId);
    const escape = v => '"' + String(v).replace(/"/g, '""') + '"';
    const lines = [headers.map(escape).join(',')];
    rows.forEach(row => lines.push(row.map(escape).join(',')));
    const blob = new Blob(['\uFEFF' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = tableId + '.csv';
    a.click();
}

function exportTableExcel(tableId, sheetName) {
    if (typeof XLSX === 'undefined') {
        alert('Library Excel belum dimuat. Coba refresh halaman.');
        return;
    }
    const { headers, rows } = getTableData(tableId);
    const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);

    // Style header (bold)
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let C = range.s.c; C <= range.e.c; C++) {
        const cell = ws[XLSX.utils.encode_cell({ r: 0, c: C })];
        if (cell) cell.s = { font: { bold: true } };
    }

    // Auto column width
    ws['!cols'] = headers.map((h, i) => ({
        wch: Math.max(h.length, ...rows.map(r => String(r[i] || '').length), 10) + 2
    }));

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, sheetName || 'Data');
    XLSX.writeFile(wb, (tableId || 'tabel') + '.xlsx');
}

function exportTablePDF(tableId, title) {
    if (typeof window.jspdf === 'undefined') {
        alert('Library PDF belum dimuat. Coba refresh halaman.');
        return;
    }
    const { jsPDF } = window.jspdf;
    const { headers, rows } = getTableData(tableId);

    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

    // Judul
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(14);
    doc.setTextColor(16, 185, 129); // emerald-500
    doc.text(title || 'Data Statistik', 14, 16);

    // Tanggal
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(100, 116, 139); // slate-500
    doc.text('Diekspor: ' + new Date().toLocaleDateString('id-ID', { dateStyle: 'long' }), 14, 22);

    doc.autoTable({
        head: [headers],
        body: rows,
        startY: 27,
        styles: {
            font: 'helvetica',
            fontSize: 9,
            cellPadding: 3,
            valign: 'middle',
        },
        headStyles: {
            fillColor: [16, 185, 129],
            textColor: 255,
            fontStyle: 'bold',
            halign: 'center',
        },
        columnStyles: { 0: { halign: 'left' } },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        foot: rows.length ? undefined : undefined,
        didParseCell(data) {
            // Baris terakhir (Total) — bold
            if (data.section === 'body' && data.row.index === rows.length - 1) {
                data.cell.styles.fontStyle = 'bold';
                data.cell.styles.fillColor = [241, 245, 249];
            }
        },
        margin: { left: 14, right: 14 },
    });

    doc.save((tableId || 'tabel') + '.pdf');
}
</script>
@endpush
