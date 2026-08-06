@extends('layouts.app')

@section('title', 'APBDes | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Laporan transparansi APBDes (Anggaran Pendapatan, Belanja, dan Pembiayaan Desa) yang dikelola oleh Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' sebagai perwujudan tata kelola keuangan yang bersih.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@graph": [
        {
            "@type": "GovernmentService",
            "@id": "{{ url('/apbdes') }}#service",
            "name": "Transparansi APBDes Desa {{ $site_settings['village_name'] ?? '' }}",
            "provider": {
                "@type": "GovernmentOrganization",
                "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}",
                "url": "{{ url('/') }}"
            },
            "description": "Laporan transparansi Anggaran Pendapatan, Belanja, dan Pembiayaan Desa (APBDes) Desa {{ $site_settings['village_name'] ?? '' }} tahun berjalan."
        }
    ]
}
</script>
@endpush

@section('content')
{{-- ═══════════════════════════════════════════════════════
     DARK HERO
═══════════════════════════════════════════════════════ --}}
<div class="relative bg-slate-900 py-16 md:py-24 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">APBDes</span>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-8">
            <div class="max-w-3xl">
                <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                    APBDes <span class="text-primary-500 italic">{{ date('Y') }}</span>
                </h1>
                <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                    Laporan transparansi realisasi anggaran pendapatan, belanja, dan pembiayaan desa.
                </p>
            </div>
            <div class="flex items-center gap-3 bg-white/5 backdrop-blur border border-white/10 rounded-2xl px-5 py-3 w-fit mb-2">
                <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                <span class="text-white text-xs font-black uppercase tracking-widest">Data Terverifikasi</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     SUMMARY DASHBOARD CARDS (overlap hero)
═══════════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 md:-mt-16 mb-16 md:mb-24 relative z-10">
    @if($categories->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 p-4">
            <x-empty-state
                icon="fa-solid fa-scale-balanced"
                title="Data APBDes Belum Tersedia"
                description="Belum ada data anggaran yang diinput."
            />
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach($categories as $category)
        @php
            $totalBudget      = $category->realizations->sum('budget_amount');
            $totalRealization = $category->realizations->sum('realization_amount');
            $percentage       = $totalBudget > 0 ? ($totalRealization / $totalBudget) * 100 : 0;

            // Catatan: Warna statis khusus visualisasi data APBDes sesuai Poin 2 DESIGN.md
            $theme = match($category->name) {
                'Pendapatan' => [
                    'text'     => 'text-emerald-600',
                    'bg'       => 'bg-emerald-500',
                    'bar'      => 'bg-emerald-500',
                    'light-bg' => 'bg-emerald-50',
                    'shadow'   => 'shadow-emerald-200/40',
                    'ring'     => 'ring-emerald-500/30',
                    'icon'     => 'fa-solid fa-coins',
                    'icon-bg'  => 'bg-emerald-100 text-emerald-700',
                    'gradient' => 'from-emerald-500 to-emerald-700',
                ],
                'Belanja' => [
                    'text'     => 'text-sky-600',
                    'bg'       => 'bg-sky-500',
                    'bar'      => 'bg-sky-500',
                    'light-bg' => 'bg-sky-50',
                    'shadow'   => 'shadow-sky-200/40',
                    'ring'     => 'ring-sky-500/30',
                    'icon'     => 'fa-solid fa-arrow-trend-down',
                    'icon-bg'  => 'bg-sky-100 text-sky-700',
                    'gradient' => 'from-sky-500 to-sky-700',
                ],
                default => [
                    'text'     => 'text-amber-600',
                    'bg'       => 'bg-amber-500',
                    'bar'      => 'bg-amber-500',
                    'light-bg' => 'bg-amber-50',
                    'shadow'   => 'shadow-amber-200/40',
                    'ring'     => 'ring-amber-500/30',
                    'icon'     => 'fa-solid fa-building-columns',
                    'icon-bg'  => 'bg-amber-100 text-amber-700',
                    'gradient' => 'from-amber-500 to-amber-700',
                ],
            };
        @endphp

        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 border border-slate-200/80 dark:border-slate-800 overflow-hidden">
            {{-- Coloured top accent bar --}}
            <div class="h-1.5 w-full bg-gradient-to-r {{ $theme['gradient'] }}"></div>

            <div class="p-8 md:p-10">
                {{-- Icon + badge row --}}
                <div class="flex justify-between items-center mb-8">
                    <div class="w-14 h-14 rounded-2xl {{ $theme['icon-bg'] }} flex items-center justify-center text-xl shadow-xs">
                        <i class="{{ $theme['icon'] }}"></i>
                    </div>
                    <span class="px-3 py-1.5 rounded-full {{ $theme['light-bg'] }} {{ $theme['text'] }}
                                 text-[10px] font-black uppercase tracking-widest border border-current opacity-80">
                        Terverifikasi
                    </span>
                </div>

                <h3 class="text-2xl md:text-3xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-6">{{ $category->name }}</h3>

                <div class="space-y-5">
                    <div>
                        <p class="text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Anggaran</p>
                        <p class="text-xl md:text-2xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($totalBudget, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-400 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Realisasi</p>
                        <p class="text-3xl md:text-4xl font-heading font-black tracking-tight {{ $theme['text'] }}">
                            Rp {{ number_format($totalRealization, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Progress --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Persentase Capaian</span>
                            <span class="text-lg font-black {{ $theme['text'] }}">{{ number_format($percentage, 1, ',', '.') }}%</span>
                        </div>
                        {{-- Segmented progress bar --}}
                        <div class="w-full h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full {{ $theme['bar'] }} rounded-full transition-all duration-1000 relative"
                                 style="width: {{ min($percentage, 100) }}%">
                                <div class="absolute inset-0 bg-white/20 animate-pulse rounded-full"></div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-2">
                            {{ $category->realizations->count() }} pos anggaran
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     DETAIL PER CATEGORY – accordion style with Alpine.js
═══════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
        <div class="mb-12 text-center md:text-left">
            <div class="flex items-center gap-3 mb-4 md:justify-start justify-center">
                <div class="h-px w-8 bg-primary-600"></div>
                <span class="text-primary-700 font-black text-xs uppercase tracking-[0.25em]">Rincian Anggaran</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100">Detail Transparansi Dana</h2>
        </div>

        <div class="space-y-12 md:space-y-16">
            @foreach($categories as $category)
            @php
                // Warna visualisasi grafik & ring per kategori (statis sesuai Poin 2 DESIGN.md)
                $theme = match($category->name) {
                    'Pendapatan' => [
                        'text'   => 'text-emerald-600',
                        'border' => 'border-emerald-500',
                        'bar'    => 'bg-emerald-500',
                        'chart'  => '#10b981',
                        'label'  => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    ],
                    'Belanja' => [
                        'text'   => 'text-sky-600',
                        'border' => 'border-sky-500',
                        'bar'    => 'bg-sky-500',
                        'chart'  => '#0ea5e9',
                        'label'  => 'bg-sky-50 text-sky-700 border-sky-100',
                    ],
                    default => [
                        'text'   => 'text-amber-600',
                        'border' => 'border-amber-500',
                        'bar'    => 'bg-amber-500',
                        'chart'  => '#f59e0b',
                        'label'  => 'bg-amber-50 text-amber-700 border-amber-100',
                    ],
                };
                $totalBudget      = $category->realizations->sum('budget_amount');
                $totalRealization = $category->realizations->sum('realization_amount');
                $pct              = $totalBudget > 0 ? ($totalRealization / $totalBudget) * 100 : 0;
            @endphp

            <div class="relative" x-data="{ open: true }">
                {{-- Category header --}}
                <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">

                    {{-- LEFT: sticky sidebar --}}
                    <div class="lg:w-80 w-full lg:sticky lg:top-28 flex-shrink-0">
                        <div class="mb-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="h-px w-8 bg-primary-600"></div>
                                <span class="text-primary-700 font-black text-xs uppercase tracking-[0.25em]">Detail Transparansi</span>
                            </div>
                            <h3 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-4 leading-tight">
                                Rincian <br class="hidden lg:block">{{ $category->name }}
                            </h3>
                            <p class="text-slate-600 dark:text-slate-300 leading-relaxed font-medium text-sm">
                                Alokasi dana dan sumber {{ strtolower($category->name) }} yang dikelola secara profesional
                                untuk kemajuan Desa {{ $site_settings['village_name'] ?? '' }}.
                            </p>
                        </div>

                        {{-- Summary pill --}}
                        <div class="flex items-center gap-3 mb-6 bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                            <div class="flex-1">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Total Realisasi</p>
                                <p class="font-extrabold text-slate-900 dark:text-slate-100 text-lg">Rp {{ number_format($totalRealization, 0, ',', '.') }}</p>
                            </div>
                            <span class="text-2xl font-black {{ $theme['text'] }}">{{ number_format($pct, 0) }}%</span>
                        </div>

                        {{-- Doughnut Chart card --}}
                        <div class="bg-white dark:bg-slate-900 p-6 md:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 dark:bg-slate-800/60 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl group-hover:scale-150 transition duration-1000"></div>
                            <h4 class="text-center font-heading font-bold text-slate-900 dark:text-slate-100 mb-6 relative z-10 text-sm">
                                Distribusi Dana
                            </h4>
                            @if($category->realizations->sum('realization_amount') > 0)
                            <div class="h-56 relative z-10">
                                <div id="chart-{{ $category->id }}"></div>
                            </div>
                            @else
                            <div class="h-56 relative z-10 flex flex-col items-center justify-center text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-300 dark:text-slate-600 mb-3">
                                    <i class="fa-solid fa-chart-pie text-xl"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Grafik Tidak Tersedia</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Belum ada realisasi dana diisi</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- RIGHT: realization cards --}}
                    <div class="flex-1 w-full">

                        {{-- Section subheader with toggle --}}
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                <i class="fa-solid fa-list-ul text-primary-600"></i>
                                {{ $category->realizations->count() }} Pos Anggaran
                            </span>
                            @if($category->realizations->count() > 0)
                            <button @click="open = !open"
                                    :aria-expanded="open"
                                    class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-primary-700 dark:hover:text-primary-400 transition flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <span x-text="open ? 'Sembunyikan' : 'Tampilkan'"></span>
                                <i class="fa-solid fa-chevron-up transition-transform duration-300 text-[10px]"
                                   :class="open ? '' : 'rotate-180'"></i>
                            </button>
                            @endif
                        </div>

                        <div class="space-y-4" x-show="open" x-transition>
                            @forelse($category->realizations as $realization)
                            @php
                                $rPct = $realization->budget_amount > 0
                                    ? ($realization->realization_amount / $realization->budget_amount) * 100
                                    : 0;
                                $status = $rPct >= 90
                                    ? ['label' => 'Sangat Baik', 'cls' => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900']
                                    : ($rPct >= 60
                                        ? ['label' => 'Sedang', 'cls' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-900']
                                        : ['label' => 'Rendah', 'cls' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-900']);
                            @endphp
                            <div class="bg-white dark:bg-slate-900 rounded-2xl md:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                                {{-- Progress accent top --}}
                                <div class="h-1 w-full bg-slate-100 dark:bg-slate-800">
                                    <div class="{{ $theme['bar'] }} h-full transition-all duration-700 rounded-full"
                                         style="width: {{ min($rPct, 100) }}%"></div>
                                </div>

                                <div class="p-6 md:p-7">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                        {{-- Left: title + amounts --}}
                                        <div class="flex-grow">
                                            <div class="flex items-start gap-4 mb-4">
                                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 group-hover:bg-primary-50 dark:group-hover:bg-primary-950/40 flex items-center justify-center flex-shrink-0 transition">
                                                    <i class="fa-solid fa-file-invoice-dollar text-slate-400 dark:text-slate-500 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-sm transition"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-base md:text-lg font-heading font-bold text-slate-900 dark:text-slate-100 group-hover:text-primary-700 dark:group-hover:text-primary-400 transition leading-snug">
                                                        {{ $realization->title }}
                                                    </h4>
                                                    <span class="mt-1 inline-block text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full border {{ $status['cls'] }}">
                                                        {{ $status['label'] }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Budget vs realization row --}}
                                            <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-100 dark:border-slate-700">
                                                <div>
                                                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Anggaran</p>
                                                    <p class="font-bold text-slate-800 dark:text-slate-100 text-sm md:text-base">
                                                        Rp {{ number_format($realization->budget_amount, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Realisasi</p>
                                                    <p class="font-extrabold {{ $theme['text'] }} text-sm md:text-base">
                                                        Rp {{ number_format($realization->realization_amount, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right: percentage ring --}}
                                        <div class="flex-shrink-0 flex flex-col items-center gap-1.5 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800 pt-5 md:pt-0 md:pl-7">
                                            <div class="relative w-18 h-18 md:w-20 md:h-20">
                                                <svg class="w-full h-full -rotate-90" viewBox="0 0 80 80">
                                                    <circle cx="40" cy="40" r="34" fill="none" stroke="currentColor" class="text-slate-200 dark:text-slate-700" stroke-width="8"/>
                                                    <circle cx="40" cy="40" r="34" fill="none"
                                                            stroke="{{ $theme['chart'] }}"
                                                            stroke-width="8"
                                                            stroke-linecap="round"
                                                            stroke-dasharray="{{ $rPct * 2.136 }}, 213.6"
                                                            style="transition: stroke-dasharray 1s ease;"/>
                                                </svg>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <span class="text-sm md:text-base font-black text-slate-900 dark:text-slate-100">{{ round($rPct) }}%</span>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Capaian</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4">
                                <x-empty-state 
                                    icon="fa-solid fa-coins" 
                                    title="Belum Ada Rincian Pos Anggaran" 
                                    description="Pos rincian anggaran untuk {{ strtolower($category->name) }} belum diinput." 
                                    compact="true"
                                />
                            </div>
                            @endforelse
                        </div>

                    </div>{{-- /realization cards --}}
                </div>
            </div>{{-- /category block --}}
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Warna teks & tooltip grafik mengikuti mode gelap yang aktif saat halaman dimuat.
        const isDark = document.documentElement.classList.contains('dark');
        const chartValueColor = isDark ? '#f1f5f9' : '#0f172a';
        const chartTheme = isDark ? 'dark' : 'light';

        @foreach($categories as $category)
        (function() {
            const element = document.getElementById('chart-{{ $category->id }}');
            if (!element) return;

            const options = {
                chart: {
                    type: 'donut',
                    height: '100%',
                    fontFamily: 'Inter, sans-serif'
                },
                dataLabels: {
                    enabled: false
                },
                series: {!! json_encode($category->realizations->pluck('realization_amount')->map(fn($v) => (float)$v)) !!},
                labels: {!! json_encode($category->realizations->pluck('title')) !!},
                colors: [
                    '#10b981', '#0ea5e9', '#f59e0b', '#6366f1', '#ec4899', '#8b5cf6',
                    '#06b6d4', '#f97316', '#14b8a6', '#3b82f6'
                ],
                stroke: { show: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: { show: false },
                                value: {
                                    show: true,
                                    fontFamily: 'Inter, sans-serif',
                                    fontWeight: 800,
                                    fontSize: '15px',
                                    color: chartValueColor,
                                    offsetY: 5,
                                    formatter: function(val) {
                                        if (val >= 1000000000) {
                                            return 'Rp ' + parseFloat((val / 1000000000).toFixed(1)) + ' M';
                                        } else if (val >= 1000000) {
                                            return 'Rp ' + parseFloat((val / 1000000).toFixed(1)) + ' Jt';
                                        }
                                        return 'Rp ' + parseInt(val).toLocaleString('id-ID');
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        let val = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        if (val >= 1000000000) {
                                            return 'Rp ' + parseFloat((val / 1000000000).toFixed(1)) + ' M';
                                        } else if (val >= 1000000) {
                                            return 'Rp ' + parseFloat((val / 1000000).toFixed(1)) + ' Jt';
                                        }
                                        return 'Rp ' + parseInt(val).toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                },
                legend: { show: false },
                tooltip: {
                    theme: chartTheme,
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chart = new ApexCharts(element, options);
            chart.render();
        })();
        @endforeach
    });
</script>
@endpush
