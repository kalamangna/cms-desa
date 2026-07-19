@extends('layouts.app')

@section('title', 'Lembaga | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Profil dan direktori lembaga kemasyarakatan di wilayah Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' yang bermitra dalam pembangunan daerah.')
@section('meta_image', asset('img/meta.png'))

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@graph": [
        {
            "@type": "GovernmentOrganization",
            "@id": "{{ url('/lembaga') }}#organization",
            "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('img/sinjai.png') }}",
            "subOrganization": [
                @foreach($institutions as $idx => $institution)
                {
                    "@type": "Organization",
                    "name": "{{ $institution->name }}",
                    "logo": "{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('img/meta.png') }}"
                }{{ $idx < count($institutions) - 1 ? ',' : '' }}
                @endforeach
            ]
        }
    ]
}
</script>
@endpush

@section('content')

{{-- ===================== HERO ===================== --}}
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
                    <span class="text-white">Lembaga Desa</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Lembaga <span class="text-emerald-500 italic">Desa</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Lembaga kemasyarakatan Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== INSTITUTIONS GRID ===================== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

    @if($institutions->isEmpty())
    <div class="text-center py-16">
        <i class="fa-solid fa-building-columns text-slate-300 text-3xl mb-3 block"></i>
        <h3 class="text-slate-400 font-bold text-sm">Belum Ada Lembaga</h3>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
        @foreach($institutions as $institution)
        <div class="group flex flex-col items-center bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300">

            {{-- Logo --}}
            <div class="relative mb-6">
                <div class="w-28 h-28 md:w-36 md:h-36 rounded-full overflow-hidden ring-4 ring-white shadow-xl shadow-slate-300/40 group-hover:ring-emerald-200 transition-all duration-300">
                    <img
                        src="{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('img/meta.png') }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                        alt="{{ $institution->name }}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('img/meta.png') }}'">
                </div>
                {{-- Active indicator dot --}}
                <div class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md"></div>
            </div>

            {{-- Info --}}
            <div class="text-center flex-1 flex flex-col items-center">
                <h3 class="text-base md:text-lg font-heading font-bold text-slate-900 mb-3 leading-tight">{{ $institution->name }}</h3>
                @if($institution->description)
                <p class="text-slate-500 text-sm leading-relaxed line-clamp-3">{{ $institution->description }}</p>
                @else
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide">
                    <i class="fa-solid fa-landmark text-[10px]"></i>
                    Lembaga Desa
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ===================== MOTTO BANNER ===================== --}}
    <div class="mt-24 md:mt-32 bg-emerald-600 rounded-[48px] md:rounded-[60px] p-12 md:p-24 text-white text-center relative overflow-hidden shadow-2xl shadow-emerald-200/50">
        <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-800/30 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mx-auto mb-8">
                <i class="fa-solid fa-people-group"></i>
            </div>
            <h2 class="text-3xl md:text-5xl font-heading font-extrabold mb-6 italic">
                "Bersama Membangun Desa"
            </h2>
            <p class="text-emerald-100 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">
                Lembaga kemasyarakatan desa adalah mitra strategis pemerintah desa dalam mewujudkan pembangunan yang partisipatif, transparan, dan berkelanjutan.
            </p>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="/aparatur" class="inline-flex items-center gap-2 bg-white text-emerald-700 px-8 py-4 rounded-2xl font-bold text-sm hover:bg-emerald-50 transition shadow-xl shadow-emerald-800/20">
                    <i class="fa-solid fa-users"></i>
                    Aparatur Desa
                </a>
                <a href="/kontak" class="inline-flex items-center gap-2 bg-emerald-700/60 border border-white/30 text-white px-8 py-4 rounded-2xl font-bold text-sm hover:bg-emerald-700 transition">
                    <i class="fa-solid fa-phone"></i>
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
