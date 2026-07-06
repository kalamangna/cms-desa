@extends('layouts.app')

@section('title', 'Statistik | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Penyajian visualisasi data statistik sektoral kependudukan, pekerjaan, pendidikan, dan kesehatan mikro yang dikelola Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

@section('content')
{{-- ═══════════════════════════════════════════════════════
     DARK HERO
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
                    Pusat <span class="text-emerald-500 italic">Statistik</span>
                </h1>
                <p class="text-slate-300 text-lg mt-2">
                    Data kependudukan, sosial, dan ekonomi Desa {{ $site_settings['village_name'] ?? '' }}.
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
     ALPINE.JS TAB NAVIGATION + CONTENT
═══════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28"
     x-data="{ activeTab: '{{ $categories->first()?->slug ?? '' }}' }">

    @if($categories->isEmpty())
        <div class="flex flex-col items-center justify-center text-center py-16 px-4 border border-slate-100 bg-slate-50/50 rounded-3xl">
            <div class="h-20 w-20 bg-emerald-50 rounded-full flex items-center justify-center border border-emerald-100 mb-6">
                <i class="fa-solid fa-chart-pie text-emerald-600 text-3xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mb-2">Belum Ada Kategori Statistik</h2>
            <p class="text-slate-500 max-w-md mx-auto text-sm leading-relaxed">
                Pemerintah desa belum mengonfigurasi kategori data statistik dinamis. Silakan masuk ke panel administrasi untuk menambahkan kategori statistik dan menghubungkannya dengan database kependudukan.
            </p>
        </div>
    @else
        {{-- TAB NAV --}}
    <div class="mb-12 md:mb-16">
        <div class="flex items-center gap-2 md:gap-3 overflow-x-auto pb-2 scrollbar-hide snap-x snap-mandatory">
            @foreach($categories as $category)
            <button
                @click="activeTab = '{{ $category->slug }}'"
                :class="activeTab === '{{ $category->slug }}'
                    ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 border-emerald-600'
                    : 'bg-white text-slate-600 border-slate-200 hover:border-emerald-300 hover:text-emerald-700'"
                class="snap-start flex-shrink-0 px-6 py-3 rounded-full text-sm font-bold border transition-all duration-300 flex items-center gap-2 whitespace-nowrap"
                id="tab-{{ $category->slug }}">
                <i class="fa-solid fa-chart-line text-xs"></i>
                {{ $category->name }}
                <span
                    :class="activeTab === '{{ $category->slug }}' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'"
                    class="text-[10px] font-black px-2 py-0.5 rounded-full transition-all duration-300">
                    {{ $category->indicators->count() }}
                </span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- TAB PANELS --}}
    @foreach($categories as $catIndex => $category)
    @php
        $totalIndicators = $category->indicators->count();
    @endphp
    <div x-show="activeTab === '{{ $category->slug }}'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-cloak>

        {{-- BIG STATS ROW --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            {{-- Total Indicators card --}}
            <div class="col-span-2 md:col-span-1 bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-3xl p-6 text-white shadow-xl shadow-emerald-600/25 flex flex-col justify-between min-h-[130px]">
                <span class="text-emerald-200 text-[10px] font-black uppercase tracking-[0.2em]">Total Indikator</span>
                <div>
                    <span class="text-5xl font-extrabold block leading-none">{{ $totalIndicators }}</span>
                    <span class="text-emerald-200 text-xs font-semibold mt-1 block">indikator aktif</span>
                </div>
            </div>

            {{-- Year range card --}}
            @php
                $allYears = $category->indicators->flatMap(fn($i) => $i->data->pluck('year'));
                $minYear = $allYears->min() ?? '-';
                $maxYear = $allYears->max() ?? '-';
                $totalRecords = $category->indicators->sum(fn($i) => $i->data->count());
            @endphp
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between min-h-[130px]">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Rentang Tahun</span>
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block leading-none">{{ $minYear }}–{{ $maxYear }}</span>
                    <span class="text-slate-400 text-xs font-semibold mt-1 block">periode data</span>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between min-h-[130px]">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Total Catatan</span>
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block leading-none">{{ number_format($totalRecords) }}</span>
                    <span class="text-slate-400 text-xs font-semibold mt-1 block">data poin</span>
                </div>
            </div>

            <div class="bg-slate-900 rounded-3xl p-6 text-white flex flex-col justify-between min-h-[130px]">
                <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">Kategori</span>
                <div>
                    <span class="text-lg font-extrabold block leading-snug">{{ $category->name }}</span>
                    @if($category->description)
                    <span class="text-slate-400 text-xs font-medium mt-1 block line-clamp-2">{{ $category->description }}</span>
                    @else
                    <span class="text-slate-400 text-xs font-medium mt-1 block">Data resmi desa</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- SECTION HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Visualisasi Data</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900">{{ $category->name }}</h2>
            </div>
            <div class="flex items-center gap-2 text-slate-400 text-xs font-semibold hidden md:flex">
                <i class="fa-regular fa-calendar-check text-emerald-500"></i>
                Diperbarui {{ date('d M Y') }}
            </div>
        </div>

        <!-- MAIN CHART CONTAINER (WITH INTERACTIVE TOGGLE SWITCH) -->
        <div x-data="{ currentType: 'bar' }" 
             class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden p-8 md:p-10 mb-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h3 class="text-lg font-heading font-extrabold text-slate-900">Grafik Perbandingan & Analisis Tren</h3>
                    <p class="text-slate-400 text-xs font-medium mt-1">Menggabungkan semua indikator kategori {{ $category->name }}</p>
                </div>
                
                <!-- Chart type selector buttons -->
                <div class="inline-flex bg-slate-100 p-1.5 rounded-2xl w-fit self-start sm:self-center border border-slate-200">
                    <button @click="currentType = 'line'; window.changeChartType('{{ $category->slug }}', 'line')" 
                            :class="currentType === 'line' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 focus:outline-none">
                        <i class="fa-solid fa-chart-line"></i> Garis
                    </button>
                    <button @click="currentType = 'bar'; window.changeChartType('{{ $category->slug }}', 'bar')" 
                            :class="currentType === 'bar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 focus:outline-none">
                        <i class="fa-solid fa-chart-bar"></i> Batang
                    </button>
                    <button @click="currentType = 'doughnut'; window.changeChartType('{{ $category->slug }}', 'doughnut')" 
                            :class="currentType === 'doughnut' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 focus:outline-none">
                        <i class="fa-solid fa-chart-pie"></i> Donat
                    </button>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="h-96 md:h-[420px] relative">
                <div id="chart-{{ $category->slug }}"></div>
            </div>
        </div>

    </div>{{-- /tab panel --}}
    @endforeach
    @endif

</div>{{-- /alpine wrapper --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @php
            $palette = [
                ['hex' => '#10b981', 'rgba' => 'rgba(16, 185, 129, 0.1)'],  // emerald
                ['hex' => '#0ea5e9', 'rgba' => 'rgba(14, 165, 233, 0.1)'],  // sky
                ['hex' => '#f59e0b', 'rgba' => 'rgba(245, 158, 11, 0.1)'],  // amber
                ['hex' => '#8b5cf6', 'rgba' => 'rgba(139, 92, 246, 0.1)'],  // violet
                ['hex' => '#ec4899', 'rgba' => 'rgba(236, 72, 153, 0.1)'],  // pink
                ['hex' => '#f43f5e', 'rgba' => 'rgba(244, 63, 94, 0.1)'],   // rose
                ['hex' => '#06b6d4', 'rgba' => 'rgba(6, 182, 212, 0.1)'],   // cyan
                ['hex' => '#14b8a6', 'rgba' => 'rgba(20, 184, 166, 0.1)'],  // teal
                ['hex' => '#f97316', 'rgba' => 'rgba(249, 115, 22, 0.1)'],  // orange
                ['hex' => '#3b82f6', 'rgba' => 'rgba(59, 130, 246, 0.1)'],  // blue
            ];
        @endphp

        const categoryData = {
            @foreach($categories as $category)
            '{{ $category->slug }}': {
                name: '{{ $category->name }}',
                years: {!! json_encode($category->indicators->flatMap(fn($i) => $i->data->pluck('year'))->unique()->sort()->values()->toArray()) !!},
                indicators: [
                    @foreach($category->indicators as $idx => $indicator)
                    @php
                        $c = $palette[$idx % count($palette)];
                        if (str_contains(strtolower($indicator->name), 'laki-laki')) {
                            $c = ['hex' => '#0ea5e9', 'rgba' => 'rgba(14, 165, 233, 0.15)'];
                        } elseif (str_contains(strtolower($indicator->name), 'perempuan')) {
                            $c = ['hex' => '#ec4899', 'rgba' => 'rgba(236, 72, 153, 0.15)'];
                        }
                    @endphp
                    {
                        name: '{{ $indicator->name }}',
                        unit: '{{ $indicator->unit }}',
                        colorHex: '{{ $c["hex"] }}',
                        colorRgba: '{{ $c["rgba"] }}',
                        data: {!! json_encode($indicator->data->map(fn($d) => ['year' => (int)$d->year, 'value' => (int)$d->value])->toArray()) !!}
                    },
                    @endforeach
                ]
            },
            @endforeach
        };

        const chartInstances = {};

        window.renderCategoryChart = function(slug, type) {
            const cat = categoryData[slug];
            const element = document.getElementById('chart-' + slug);
            if (!element) return;

            // Destroy existing instance if it exists
            if (chartInstances[slug]) {
                chartInstances[slug].destroy();
            }

            // Ambil tahun terbaru untuk penentuan nilai pengurutan
            const latestYearForSort = cat.years.length > 0 ? Math.max(...cat.years.map(Number)) : new Date().getFullYear();

            // Salin dan urutkan indikator berdasarkan jenis data (Ordinal, Gender, Nominal)
            let sortedIndicators = [...cat.indicators];

            if (cat.name.toLowerCase().includes('pendidikan')) {
                const educationOrder = [
                    'tidak/belum pernah sekolah',
                    'tidak sekolah',
                    'belum sekolah',
                    'paud',
                    'tk',
                    'sd',
                    'smp',
                    'sma',
                    'diploma',
                    'd1',
                    'd2',
                    'd3',
                    'd4',
                    'sarjana',
                    's1',
                    'magister',
                    's2',
                    'doktor',
                    's3'
                ];
                sortedIndicators.sort((a, b) => {
                    const nameA = a.name.toLowerCase().trim();
                    const nameB = b.name.toLowerCase().trim();
                    
                    let indexA = educationOrder.findIndex(item => nameA.includes(item));
                    let indexB = educationOrder.findIndex(item => nameB.includes(item));
                    
                    if (indexA === -1) indexA = 999;
                    if (indexB === -1) indexB = 999;
                    
                    return indexA - indexB;
                });
            } else if (cat.name.toLowerCase().includes('penduduk')) {
                // Pastikan Laki-laki selalu pertama, Perempuan kedua
                sortedIndicators.sort((a, b) => {
                    const nameA = a.name.toLowerCase();
                    const nameB = b.name.toLowerCase();
                    if (nameA.includes('laki') && !nameB.includes('laki')) return -1;
                    if (!nameA.includes('laki') && nameB.includes('laki')) return 1;
                    return 0;
                });
            } else {
                // Urutkan desc berdasarkan nilai tahun terbaru untuk kategori nominal
                sortedIndicators.sort((a, b) => {
                    const valA = a.data.find(d => Number(d.year) === Number(latestYearForSort))?.value || 0;
                    const valB = b.data.find(d => Number(d.year) === Number(latestYearForSort))?.value || 0;
                    return valB - valA;
                });
            }

            let chartConfig = {};

            if (type === 'line' || type === 'bar') {
                const datasets = sortedIndicators.map((ind) => {
                    const dataPoints = cat.years.map((yr) => {
                        const found = ind.data.find(d => Number(d.year) === Number(yr));
                        return found ? found.value : 0;
                    });
                    return {
                        name: ind.name,
                        data: dataPoints
                    };
                });

                const hasGender = sortedIndicators.some(ind => ind.name.toLowerCase().includes('laki') || ind.name.toLowerCase().includes('perempuan'));
                const chartColors = hasGender 
                    ? sortedIndicators.map(ind => {
                        if (ind.name.toLowerCase().includes('laki')) return '#0ea5e9';
                        if (ind.name.toLowerCase().includes('perempuan')) return '#ec4899';
                        return ind.colorHex;
                    })
                    : undefined;

                chartConfig = {
                    chart: {
                        type: type,
                        height: '100%',
                        fontFamily: 'Inter, sans-serif',
                        toolbar: {
                            show: true,
                            tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: datasets,
                    stroke: {
                        curve: 'smooth',
                        width: type === 'bar' ? 0 : 3
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '55%'
                        }
                    },
                    xaxis: {
                        categories: cat.years.map(String),
                        labels: {
                            rotate: -45,
                            rotateAlways: false,
                            hideOverlappingLabels: true,
                            style: { colors: '#94a3b8', fontWeight: 600, fontSize: '11px' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        labels: {
                            style: { colors: '#94a3b8', fontWeight: 600, fontSize: '11px' },
                            formatter: function(val) {
                                if (val % 1 === 0) {
                                    return val.toLocaleString('id-ID');
                                }
                                return '';
                            }
                        }
                    },
                    grid: {
                        borderColor: 'rgba(148, 163, 184, 0.08)',
                        strokeDashArray: 5,
                        xaxis: { lines: { show: false } },
                        yaxis: { lines: { show: true } }
                    },
                    legend: {
                        position: 'bottom',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 600,
                        fontSize: '12px',
                        labels: { colors: '#64748b' },
                        markers: { width: 10, height: 10, radius: 10, offsetY: -1 }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(val, opts) {
                                const indIndex = opts.seriesIndex;
                                const unit = cat.indicators[indIndex] ? cat.indicators[indIndex].unit : '';
                                return val.toLocaleString('id-ID') + ' ' + unit;
                            }
                        }
                    }
                };
                if (chartColors) {
                    chartConfig.colors = chartColors;
                }
            } else if (type === 'doughnut') {
                const latestYear = cat.years.length > 0 ? Math.max(...cat.years.map(Number)) : new Date().getFullYear();
                const dataPoints = sortedIndicators.map((ind) => {
                    const found = ind.data.find(d => Number(d.year) === Number(latestYear));
                    return found ? found.value : 0;
                });

                const hasGender = sortedIndicators.some(ind => ind.name.toLowerCase().includes('laki') || ind.name.toLowerCase().includes('perempuan'));
                const chartColors = hasGender 
                    ? sortedIndicators.map(ind => {
                        if (ind.name.toLowerCase().includes('laki')) return '#0ea5e9';
                        if (ind.name.toLowerCase().includes('perempuan')) return '#ec4899';
                        return ind.colorHex;
                    })
                    : undefined;

                chartConfig = {
                    chart: {
                        type: 'donut',
                        height: '100%',
                        fontFamily: 'Inter, sans-serif'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    labels: sortedIndicators.map(ind => ind.name),
                    series: dataPoints,
                    stroke: {
                        show: false
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '72%',
                                labels: {
                                    show: true,
                                    name: { 
                                        show: true, 
                                        fontFamily: 'Poppins, sans-serif', 
                                        fontWeight: 700,
                                        fontSize: '14px',
                                        color: '#64748b'
                                    },
                                    value: { 
                                        show: true, 
                                        fontFamily: 'Inter, sans-serif', 
                                        fontWeight: 800,
                                        fontSize: '22px',
                                        color: '#0f172a',
                                        formatter: function(val) {
                                            return parseInt(val).toLocaleString('id-ID');
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total (' + latestYear + ')',
                                        fontFamily: 'Poppins, sans-serif',
                                        fontWeight: 700,
                                        fontSize: '11px',
                                        color: '#94a3b8',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 600,
                        fontSize: '12px',
                        labels: { colors: '#64748b' },
                        markers: { width: 10, height: 10, radius: 10, offsetY: -1 }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(val, opts) {
                                const indIndex = opts.seriesIndex;
                                const unit = cat.indicators[indIndex] ? cat.indicators[indIndex].unit : '';
                                return val.toLocaleString('id-ID') + ' ' + unit;
                            }
                        }
                    }
                };

                if (chartColors) {
                    chartConfig.colors = chartColors;
                }
            }

            chartInstances[slug] = new ApexCharts(element, chartConfig);
            chartInstances[slug].render();
        };

        window.changeChartType = function(slug, type) {
            window.renderCategoryChart(slug, type);
        };

        // Initialize all charts on load
        @foreach($categories as $category)
            window.renderCategoryChart('{{ $category->slug }}', 'bar');
        @endforeach
    });
</script>
@endpush
