@extends('layouts.app')

@section('title', 'Pengumuman Resmi | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Daftar pengumuman resmi, keputusan, dan maklumat kedinasan yang diterbitkan oleh Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' untuk masyarakat umum.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
@if(!$announcements->isEmpty())
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@graph": [
        @foreach($announcements as $idx => $ann)
        {
            "@type": "SpecialAnnouncement",
            "@id": "{{ url('/pengumuman') }}#announcement-{{ $ann->id }}",
            "name": {!! json_encode($ann->title) !!},
            "text": {!! json_encode(Str::limit(strip_tags($ann->content), 200)) !!},
            "datePosted": "{{ $ann->published_at?->toIso8601String() }}",
            "category": "Government",
            "publisher": {
                "@type": "GovernmentOrganization",
                "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}",
                "url": "{{ url('/') }}"
            }
        }{{ $idx < count($announcements) - 1 ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
@endpush

@section('content')

{{-- =========================================================
     HERO SECTION
     ========================================================= --}}
<div class="relative bg-slate-900 dark:bg-slate-950 py-16 md:py-24 lg:py-28 overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-500"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl transition-colors duration-500"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 dark:bg-primary-600/5 rounded-full blur-3xl transition-colors duration-500"></div>
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
                    <span class="text-white">Pengumuman</span>
                </li>
            </ol>
        </nav>

        {{-- Heading --}}
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Pengumuman <span class="text-primary-500 italic">Resmi</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 max-w-2xl leading-relaxed">
                Informasi dan edaran resmi Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- =========================================================
     STATS BAR
     ========================================================= --}}
<div class="bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-wrap items-center gap-6 text-sm text-slate-600 dark:text-slate-300">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                <span class="font-semibold text-slate-700 dark:text-slate-200">Diperbarui secara berkala</span>
            </div>
            <div class="flex items-center gap-2 font-medium">
                <i class="fa-solid fa-bullhorn text-amber-500"></i>
                Total <span class="font-bold text-slate-900 dark:text-slate-100">{{ $announcements->total() }}</span> pengumuman publik
            </div>
        </div>
    </div>
</div>

{{-- =========================================================
     TIMELINE CONTENT
     ========================================================= --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

        @forelse($announcements as $index => $announcement)

        {{-- Year / Month divider --}}
        @if($index === 0 || $announcement->published_at->format('Ym') !== $announcements[$index - 1]->published_at->format('Ym'))
        <div class="flex items-center gap-4 mb-8 {{ $index > 0 ? 'mt-14 md:mt-16' : '' }}">
            <div class="bg-slate-900 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                {{ $announcement->published_at->translatedFormat('F Y') }}
            </div>
            <div class="flex-1 h-px bg-slate-200 dark:bg-slate-800"></div>
        </div>
        @endif

        {{-- ─── Timeline Item ─────────────────────────────────────── --}}
        <div class="relative flex gap-5 md:gap-8 mb-8 group">

            {{-- Timeline stem --}}
            @if(!$loop->last)
            <div class="absolute left-6 md:left-7 top-14 bottom-0 w-px bg-slate-200 dark:bg-slate-800 group-last:hidden -z-0"></div>
            @endif

            {{-- Icon dot --}}
            <div class="flex-shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-primary-50 dark:bg-primary-950/40 border border-primary-100 dark:border-primary-900/50 flex items-center justify-center shadow-xs group-hover:bg-primary-600 group-hover:border-primary-600 transition-all duration-300 z-10">
                <i class="fa-solid fa-bullhorn text-primary-600 dark:text-primary-400 group-hover:text-white transition-colors duration-300 text-base md:text-lg"></i>
            </div>

            {{-- Card --}}
            <div class="flex-1 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 hover:shadow-xl hover:shadow-slate-200 dark:hover:shadow-slate-950 hover:-translate-y-1 transition-all duration-300 overflow-hidden p-6 md:p-7">
                
                {{-- Meta Info: Date Badge --}}
                <div class="mb-4">
                    <span class="inline-flex items-center gap-1.5 bg-primary-600 text-white text-xs font-bold px-3 py-1 rounded-xl shadow-xs">
                        {{ $announcement->published_at->translatedFormat('d F Y') }}
                    </span>
                </div>

                {{-- Title --}}
                <h2 class="text-xl md:text-2xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-3 leading-snug group-hover:text-primary-700 dark:group-hover:text-primary-400 transition-colors duration-200">
                    {{ $announcement->title }}
                </h2>

                {{-- Content Preview --}}
                <div class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed line-clamp-3 mb-6">
                    {{ Str::limit(strip_tags($announcement->content), 240) }}
                </div>

                {{-- Read More Accordion (Alpine.js) --}}
                <div x-data="{ open: false }">
                    <div class="flex items-center justify-between gap-3 pt-2">
                        <button @click="open = !open"
                                :aria-expanded="open"
                                class="inline-flex items-center justify-center gap-2 text-xs font-bold px-4 py-2.5 min-h-11 rounded-xl transition-all duration-200 bg-primary-50 dark:bg-primary-950/40 text-primary-700 dark:text-primary-300 hover:bg-primary-100 dark:hover:bg-primary-900/50 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            <span x-text="open ? 'Sembunyikan Isi Pengumuman' : 'Baca Pengumuman Selengkapnya'"></span>
                            <i class="fa-solid transition-transform duration-200 text-[10px]"
                               :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>

                    {{-- Full Content --}}
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="prose prose-sm prose-emerald dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 mt-5 pt-5 border-t border-slate-100 dark:border-slate-800 leading-relaxed">
                        {!! $announcement->content !!}
                    </div>
                </div>

            </div>
        </div>

        @empty
        {{-- Empty state --}}
        <div class="py-8">
            <x-empty-state
                icon="fa-solid fa-bullhorn"
                title="Belum Ada Pengumuman Resmi"
                description="Belum ada pengumuman resmi yang diterbitkan untuk saat ini. Silakan periksa kembali di lain waktu."
            />
        </div>
        @endforelse

        {{-- ─── Pagination ──────────────────────────────────────────── --}}
        @if($announcements->hasPages())
        <div class="mt-12 md:mt-16 flex justify-center">
            {{ $announcements->links() }}
        </div>
        @endif

    </div>
</div>

@endsection
