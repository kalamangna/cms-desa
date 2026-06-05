@extends('layouts.app')

@section('title', 'Dashboard Statistik - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
<!-- Standardized Dark Hero -->
<div class="relative bg-slate-900 py-32 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-sm font-bold uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-emerald-400 transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-4 h-4 mx-1" />
                        <span class="text-white">Statistik Desa</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Pusat <span class="text-emerald-500 italic">Statistik</span>
            </h1>
            <p class="text-xl text-slate-400 leading-relaxed font-medium">
                Visualisasi data kependudukan, sosial, dan ekonomi Desa {{ $site_settings['village_name'] ?? '' }} untuk transparansi berbasis bukti.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        @foreach($categories as $category)
            <div class="bg-white rounded-[48px] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden group hover:border-emerald-200 transition duration-500">
                <div class="p-10 md:p-12">
                    <div class="flex justify-between items-start mb-12">
                        <div>
                            <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.3em] mb-2 block">Kategori Data</span>
                            <h2 class="text-3xl font-heading font-bold text-slate-900 mb-3">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-slate-500 text-sm italic font-medium">{{ $category->description }}</p>
                            @endif
                        </div>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest px-5 py-2 rounded-full border border-emerald-100 shadow-sm">
                            Live Data
                        </span>
                    </div>
                    
                    <div class="h-96 relative">
                        <canvas id="chart-{{ $category->slug }}"></canvas>
                    </div>
                    
                    <div class="mt-12 pt-8 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Pembaruan: {{ date('d M Y') }}</span>
                        </div>
                        <button class="bg-slate-900 text-white px-8 py-3 rounded-full font-bold text-sm hover:bg-emerald-600 transition shadow-lg shadow-slate-900/10 flex items-center gap-3">
                            Unduh Data CSV
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#94a3b8';
        
        @foreach($categories as $category)
            (function() {
                const ctx = document.getElementById('chart-{{ $category->slug }}').getContext('2d');
                
                const datasets = [
                    @foreach($category->indicators as $index => $indicator)
                    {
                        label: '{{ $indicator->name }} ({{ $indicator->unit }})',
                        data: [
                            @foreach($indicator->data as $data)
                                { x: '{{ $data->year }}', y: {{ $data->value }} },
                            @endforeach
                        ],
                        backgroundColor: '{{ $index % 2 == 0 ? "rgba(16, 185, 129, 0.1)" : "rgba(14, 165, 233, 0.1)" }}',
                        borderColor: '{{ $index % 2 == 0 ? "#10b981" : "#0ea5e9" }}',
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
