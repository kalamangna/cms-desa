@extends('layouts.app')

@section('title', 'Statistik - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
{{-- ═══════════════════════════════════════════════════════
     DARK HERO
═══════════════════════════════════════════════════════ --}}
<div class="relative bg-slate-900 py-20 md:py-32 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-400 transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
                        <span class="text-white">Statistik</span>
                    </div>
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
            <div class="flex items-center gap-3 bg-white/5 backdrop-blur border border-white/10 rounded-2xl px-5 py-3 w-fit">
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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-36"
     x-data="{ activeTab: '{{ $categories->first()?->slug ?? '' }}' }">

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

        {{-- CHARTS GRID: 2-col, full-width if only 1 chart --}}
        @if($totalIndicators === 1)
        <div class="grid grid-cols-1 gap-8">
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @endif
            @foreach($category->indicators as $indicatorIndex => $indicator)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 overflow-hidden group
                        {{ $totalIndicators === 1 ? '' : ($totalIndicators % 2 !== 0 && $loop->last ? 'lg:col-span-2' : '') }}">
                <div class="p-8 md:p-10">
                    {{-- Chart header --}}
                    <div class="flex items-start justify-between mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-heading font-bold text-slate-900 group-hover:text-emerald-700 transition">{{ $indicator->name }}</h3>
                            <p class="text-slate-400 text-xs font-medium mt-1">Satuan: <span class="font-bold text-slate-600">{{ $indicator->unit }}</span></p>
                        </div>
                        <span class="bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full border border-emerald-100 flex-shrink-0">
                            {{ $indicator->data->count() }} data poin
                        </span>
                    </div>

                    {{-- Latest value highlight --}}
                    @php $latestData = $indicator->data->sortByDesc('year')->first(); @endphp
                    @if($latestData)
                    <div class="flex items-center gap-4 mb-6 bg-slate-50 rounded-2xl p-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-arrow-trend-up text-emerald-600 text-sm"></i>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] font-black uppercase tracking-wider block">Nilai Terkini ({{ $latestData->year }})</span>
                            <span class="text-2xl font-extrabold text-slate-900">{{ number_format($latestData->value, 0, ',', '.') }}
                                <span class="text-sm font-semibold text-slate-500">{{ $indicator->unit }}</span>
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Canvas --}}
                    <div class="h-72 md:h-80 relative">
                        <canvas id="chart-{{ $category->slug }}-ind-{{ $indicatorIndex }}"></canvas>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Download action --}}
        @php
            $tabDownloadUrl = in_array($category->name, ['Bantuan Sosial', 'Kepemilikan Rumah'])
                ? route('dataset.download', ['type' => 'keluarga'])
                : route('dataset.download', ['type' => 'penduduk']);
        @endphp
        <div class="mt-8 flex justify-end">
            <a href="{{ $tabDownloadUrl }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-4 rounded-2xl transition duration-300 shadow-lg shadow-emerald-600/20" download>
                <i class="fa-solid fa-download"></i>
                Unduh Dataset Riil (CSV)
            </a>
        </div>

    </div>{{-- /tab panel --}}
    @endforeach

</div>{{-- /alpine wrapper --}}

    {{-- INTEGRASI OPEN DATA --}}
    <div class="mt-24 border-t border-slate-100 pt-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left column: Call to action --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Transparansi & Open Data</span>
                </div>
                <h3 class="text-3xl font-heading font-extrabold text-slate-900 leading-tight mb-4">
                    Ingin Mengolah Data Sendiri?
                </h3>
                <p class="text-slate-500 font-medium leading-relaxed mb-6">
                    Kami menyediakan seluruh data kependudukan, statistik, dan publikasi dalam format terbuka (Open Data) seperti CSV, XLSX, dan PDF agar Anda dapat memanfaatkannya secara bebas, akurat, dan transparan.
                </p>
                <a href="/dataset" class="group inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm px-6 py-3 rounded-2xl shadow-lg shadow-emerald-600/20 transition-all duration-300 hover:-translate-y-0.5">
                    <i class="fa-solid fa-database group-hover:rotate-12 transition-transform"></i>
                    Kunjungi Open Data Portal
                </a>
            </div>

            {{-- Right column: Latest Datasets --}}
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <span class="text-slate-400 text-xs font-black uppercase tracking-[0.2em]">Dataset Terkait & Terbaru</span>
                    <span class="text-emerald-600 text-xs font-bold">{{ $datasets->count() }} Dataset Tersedia</span>
                </div>

                <div class="space-y-4">
                    @forelse($datasets as $dataset)
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 hover:border-emerald-200 hover:shadow-md transition duration-300 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 font-black text-[9px] uppercase tracking-wider mb-2 inline-block">{{ $dataset->year }}</span>
                            <h4 class="font-heading font-bold text-slate-900 text-base leading-snug">{{ $dataset->title }}</h4>
                            <p class="text-slate-400 text-xs mt-1 line-clamp-1 font-medium">{{ $dataset->description }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if($dataset->file_csv)
                            @php
                                $csvUrl = $dataset->file_csv === 'dynamic' 
                                    ? route('dataset.download', ['type' => ($dataset->slug === 'data-penduduk' ? 'penduduk' : 'keluarga')])
                                    : asset('storage/' . $dataset->file_csv);
                            @endphp
                            <a href="{{ $csvUrl }}" class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 py-2 rounded-xl text-xs font-bold border border-emerald-100 hover:bg-emerald-100 transition" download>
                                <i class="fa-solid fa-download"></i> CSV
                            </a>
                            @endif
                            @if($dataset->file_xlsx)
                            <a href="{{ asset('storage/' . $dataset->file_xlsx) }}" class="inline-flex items-center gap-1.5 bg-sky-50 text-sky-700 px-3 py-2 rounded-xl text-xs font-bold border border-sky-100 hover:bg-sky-100 transition" download>
                                <i class="fa-solid fa-download"></i> XLSX
                            </a>
                            @endif
                            @if($dataset->file_pdf)
                            <a href="{{ asset('storage/' . $dataset->file_pdf) }}" class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 px-3 py-2 rounded-xl text-xs font-bold border border-rose-100 hover:bg-rose-100 transition" download>
                                <i class="fa-solid fa-download"></i> PDF
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-3xl p-8 text-center text-slate-400 border border-dashed border-slate-200 italic font-medium">
                        Belum ada dataset terbuka yang dipublikasikan.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#94a3b8';

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

        // ── Per-indicator charts (new IDs: chart-{slug}-ind-{index}) ──────────
        @foreach($categories as $category)
            @foreach($category->indicators as $indicatorIndex => $indicator)
            (function() {
                const el = document.getElementById('chart-{{ $category->slug }}-ind-{{ $indicatorIndex }}');
                if (!el) return;
                const ctx = el.getContext('2d');

                @php
                    $c = $palette[$indicatorIndex % count($palette)];
                @endphp

                const rawData = [
                    @foreach($indicator->data as $data)
                        { x: '{{ $data->year }}', y: {{ $data->value }} },
                    @endforeach
                ];

                const labels = [...new Set(rawData.map(d => d.x))].sort();
                const chartType = '{{ in_array($category->name, ["Pekerjaan", "Pendidikan"]) ? "bar" : "line" }}';

                new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: '{{ $indicator->name }} ({{ $indicator->unit }})',
                            data: labels.map(label => {
                                const found = rawData.find(d => d.x === label);
                                return found ? found.y : 0;
                            }),
                            backgroundColor: '{{ $c["rgba"] }}',
                            borderColor: '{{ $c["hex"] }}',
                            borderWidth: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            tension: 0.4,
                            fill: true,
                            borderRadius: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 24,
                                    font: { weight: 'bold', size: 11, family: 'Inter' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 16,
                                titleFont: { family: 'Poppins', size: 14, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 13 },
                                cornerRadius: 24,
                                displayColors: true,
                                boxPadding: 6
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f8fafc', borderDash: [5, 5] },
                                ticks: { font: { weight: 'bold', size: 11 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: 'bold', size: 11 } }
                            }
                        }
                    }
                });
            })();
            @endforeach
        @endforeach

        // ── Legacy chart IDs preserved (chart-{slug}) for backward compat ──────
        // The original per-category combined chart is recreated below so any
        // external JS referencing the old canvas IDs still works.
        @foreach($categories as $category)
            // NOTE: canvas id="chart-{{ $category->slug }}" is no longer rendered
            // in the new layout (replaced by per-indicator canvases above).
            // If your template elsewhere renders <canvas id="chart-{{ $category->slug }}">,
            // the block below will initialize it automatically.
            (function() {
                const el = document.getElementById('chart-{{ $category->slug }}');
                if (!el) return; // silently skip if canvas not present
                const ctx = el.getContext('2d');

                const datasets = [
                    @foreach($category->indicators as $index => $indicator)
                    @php $c = $palette[$index % count($palette)]; @endphp
                    {
                        label: '{{ $indicator->name }} ({{ $indicator->unit }})',
                        data: [
                            @foreach($indicator->data as $data)
                                { x: '{{ $data->year }}', y: {{ $data->value }} },
                            @endforeach
                        ],
                        backgroundColor: '{{ $c["rgba"] }}',
                        borderColor: '{{ $c["hex"] }}',
                        borderWidth: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.4,
                        fill: true
                    },
                    @endforeach
                ];

                const labels = [...new Set(datasets.flatMap(ds => ds.data.map(d => d.x)))].sort();

                new Chart(ctx, {
                    type: '{{ in_array($category->name, ["Pekerjaan", "Pendidikan"]) ? "bar" : "line" }}',
                    data: {
                        labels: labels,
                        datasets: datasets.map(ds => ({
                            ...ds,
                            data: labels.map(label => {
                                const found = ds.data.find(d => d.x === label);
                                return found ? found.y : 0;
                            }),
                            borderRadius: 12
                        }))
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 30,
                                    font: { weight: 'bold', size: 11, family: 'Inter' }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                padding: 16,
                                titleFont: { family: 'Poppins', size: 14, weight: 'bold' },
                                bodyFont: { family: 'Inter', size: 13 },
                                cornerRadius: 24,
                                displayColors: true,
                                boxPadding: 6
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f8fafc', borderDash: [5, 5] },
                                ticks: { font: { weight: 'bold', size: 11 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: 'bold', size: 11 } }
                            }
                        }
                    }
                });
            })();
        @endforeach
    });
</script>
@endpush
