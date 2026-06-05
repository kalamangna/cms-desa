@extends('layouts.app')

@section('title', 'APBDes - ' . ($site_settings['village_name'] ?? 'Website Desa'))

@section('content')
<!-- Standardized Dark Hero -->
<div class="relative bg-slate-900 py-20 md:py-32 overflow-hidden">
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
                        <span class="text-white">APBDes</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                APBDes <span class="text-emerald-500 italic">{{ date('Y') }}</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Transparansi realisasi anggaran Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<!-- Summary Dashboard Cards -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 md:-mt-16 mb-20 md:mb-24 relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        @foreach($categories as $category)
        @php
            $totalBudget = $category->realizations->sum('budget_amount');
            $totalRealization = $category->realizations->sum('realization_amount');
            $percentage = $totalBudget > 0 ? ($totalRealization / $totalBudget) * 100 : 0;
            
            $theme = match($category->name) {
                'Pendapatan' => [
                    'text' => 'text-emerald-600',
                    'bg' => 'bg-emerald-500',
                    'light-bg' => 'bg-emerald-50',
                    'shadow' => 'shadow-emerald-200/40',
                    'icon' => '💰'
                ],
                'Belanja' => [
                    'text' => 'text-sky-600',
                    'bg' => 'bg-sky-500',
                    'light-bg' => 'bg-sky-50',
                    'shadow' => 'shadow-sky-200/40',
                    'icon' => '💸'
                ],
                default => [
                    'text' => 'text-amber-600',
                    'bg' => 'bg-amber-500',
                    'light-bg' => 'bg-amber-50',
                    'shadow' => 'shadow-amber-200/40',
                    'icon' => '🏦'
                ],
            };
        @endphp
        <div class="bg-white p-8 md:p-10 rounded-[40px] shadow-2xl {{ $theme['shadow'] }} border border-slate-100 group hover:-translate-y-2 transition duration-500">
            <div class="flex justify-between items-center mb-8 md:mb-10">
                <div class="w-14 h-14 md:w-16 md:h-16 rounded-[20px] md:rounded-[24px] {{ $theme['light-bg'] }} flex items-center justify-center text-2xl md:text-3xl">
                    {{ $theme['icon'] }}
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-1">Status</span>
                    <span class="px-3 md:px-4 py-1.5 rounded-full {{ $theme['light-bg'] }} {{ $theme['text'] }} text-[10px] font-black uppercase tracking-widest border border-current opacity-70">Terverifikasi</span>
                </div>
            </div>
            
            <h3 class="text-xl md:text-2xl font-heading font-bold text-slate-900 mb-6 md:mb-8">{{ $category->name }}</h3>

            <div class="space-y-6 md:space-y-8">
                <div>
                    <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-widest mb-2">Anggaran (Budget)</p>
                    <p class="text-xl md:text-2xl font-heading font-extrabold text-slate-900">Rp {{ number_format($totalBudget, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-widest mb-2">Realisasi (Actual)</p>
                    <p class="text-2xl md:text-3xl font-heading font-extrabold {{ $theme['text'] }}">Rp {{ number_format($totalRealization, 0, ',', '.') }}</p>
                </div>
                
                <div class="pt-6 border-t border-slate-50">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm font-bold text-slate-700">Persentase Capaian</span>
                        <span class="text-lg font-black {{ $theme['text'] }}">{{ number_format($percentage, 1) }}%</span>
                    </div>
                    <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden p-1">
                        <div class="h-full {{ $theme['bg'] }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Detailed Data Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
    <div class="space-y-20 md:space-y-32">
        @foreach($categories as $category)
        @php
            $theme = match($category->name) {
                'Pendapatan' => ['text' => 'text-emerald-500', 'border' => 'border-emerald-500', 'chart' => '#10b981'],
                'Belanja' => ['text' => 'text-sky-500', 'border' => 'border-sky-500', 'chart' => '#0ea5e9'],
                default => ['text' => 'text-amber-500', 'border' => 'border-amber-500', 'chart' => '#f59e0b'],
            };
        @endphp
        <div class="relative">
            <div class="flex flex-col lg:flex-row gap-12 lg:gap-20 items-start">
                <!-- Sidebar Info & Chart -->
                <div class="lg:w-1/3 w-full lg:sticky lg:top-32">
                    <div class="mb-10 md:mb-12 text-center lg:text-left">
                        <span class="text-emerald-600 font-black text-[10px] uppercase tracking-[0.3em] mb-4 block">Detail Transparansi</span>
                        <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900 mb-6 leading-tight">
                            Rincian <br class="hidden lg:block">{{ $category->name }}
                        </h2>
                        <p class="text-slate-500 leading-relaxed font-medium">
                            Alokasi dana dan sumber {{ strtolower($category->name) }} yang dikelola secara profesional untuk kemajuan Desa {{ $site_settings['village_name'] ?? '' }}.
                        </p>
                    </div>

                    <div class="bg-white p-8 md:p-12 rounded-[40px] md:rounded-[60px] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl group-hover:scale-150 transition duration-1000"></div>
                        <h4 class="text-center font-heading font-bold text-slate-900 mb-8 relative z-10">Distribusi Dana</h4>
                        <div class="h-64 relative z-10">
                            <canvas id="chart-{{ $category->id }}"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Cards List (Replaces "Standard Table") -->
                <div class="lg:w-2/3 w-full space-y-6">
                    @foreach($category->realizations as $realization)
                    <div class="bg-white p-8 md:p-10 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-xl hover:border-emerald-200 transition duration-500 group">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                            <div class="flex-grow">
                                <div class="flex items-center gap-4 mb-4">
                                    <span class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center font-black text-slate-400 text-xs border border-slate-100">{{ rand(100, 999) }}</span>
                                    <h5 class="text-xl font-heading font-bold text-slate-900 group-hover:text-emerald-600 transition">{{ $realization->title }}</h5>
                                </div>
                                <div class="grid grid-cols-2 gap-8">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Anggaran</p>
                                        <p class="font-bold text-slate-700">Rp {{ number_format($realization->budget_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Realisasi</p>
                                        <p class="font-extrabold {{ $theme['text'] }}">Rp {{ number_format($realization->realization_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-8 border-t md:border-t-0 md:border-l border-slate-50 pt-8 md:pt-0 md:pl-10">
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Capaian</p>
                                    <p class="text-2xl font-black text-slate-900">{{ round($realization->percentage) }}%</p>
                                </div>
                                <div class="w-16 h-16 rounded-full border-4 border-slate-50 relative flex items-center justify-center">
                                    <svg class="w-full h-full -rotate-90 absolute">
                                        <circle cx="32" cy="32" r="28" fill="none" stroke="currentColor" stroke-width="4" class="{{ $theme['text'] }} opacity-20" />
                                        <circle cx="32" cy="32" r="28" fill="none" stroke="currentColor" stroke-width="6" class="{{ $theme['text'] }}" stroke-dasharray="{{ $realization->percentage * 1.75 }}, 175" stroke-linecap="round" />
                                    </svg>
                                    <span class="text-[10px] font-black {{ $theme['text'] }}">OK</span>
                                </div>
                            </div>
                        </div>
                        <!-- Micro Progress Bar -->
                        <div class="mt-8 w-full h-1.5 bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full {{ $theme['text'] == 'text-emerald-500' ? 'bg-emerald-500' : ($theme['text'] == 'text-sky-500' ? 'bg-sky-500' : 'bg-amber-500') }} rounded-full" style="width: {{ min($realization->percentage, 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Info Banner -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
    <div class="bg-slate-900 rounded-[60px] p-12 md:p-20 text-white relative overflow-hidden text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-12 shadow-2xl shadow-slate-900/40">
        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10 max-w-2xl">
            <h3 class="text-3xl md:text-4xl font-heading font-extrabold mb-6">Punya Pertanyaan Mengenai Anggaran?</h3>
            <p class="text-slate-400 text-lg leading-relaxed">
                Kami sangat menghargai partisipasi warga dalam mengawasi pembangunan desa. Sampaikan masukan atau pertanyaan Anda melalui kanal aspirasi kami.
            </p>
        </div>
        <a href="/kontak" class="bg-emerald-600 text-white px-12 py-5 rounded-full font-bold text-lg hover:bg-emerald-700 transition relative z-10 shadow-xl shadow-emerald-900/20">Hubungi Kami</a>
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
            const ctx = document.getElementById('chart-{{ $category->id }}').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($category->realizations->pluck('title')) !!},
                    datasets: [{
                        data: {!! json_encode($category->realizations->pluck('realization_amount')) !!},
                        backgroundColor: [
                            '#10b981', '#0ea5e9', '#f59e0b', '#6366f1', '#ec4899', '#8b5cf6'
                        ],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        })();
        @endforeach
    });
</script>
@endpush
