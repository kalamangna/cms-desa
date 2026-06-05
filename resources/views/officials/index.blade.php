@extends('layouts.app')

@section('title', 'Pemerintah Desa - ' . ($site_settings['village_name'] ?? 'Website Desa'))

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
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-white">Pemerintahan</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl text-center md:text-left">
            <h1 class="text-4xl md:text-7xl font-heading font-extrabold text-white leading-tight mb-6">
                Struktur <span class="text-emerald-500 italic">Organisasi</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 leading-relaxed font-medium">
                Mengenal jajaran pelayan masyarakat yang berdedikasi tinggi untuk kemajuan Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
        @foreach($officials as $official)
        <div class="group">
            <div class="relative rounded-[40px] overflow-hidden aspect-[3/4] mb-8 shadow-2xl shadow-slate-200/50">
                @if($official->photo)
                    <img src="{{ asset('storage/' . $official->photo) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="{{ $official->name }}">
                @else
                    <div class="w-full h-full bg-slate-100 flex flex-col items-center justify-center text-slate-300">
                        <x-heroicon-o-user class="w-20 h-20 mb-4 opacity-20" />
                        <span class="text-xs font-bold uppercase tracking-widest">Foto Tidak Tersedia</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-500 flex items-end p-10">
                    <div class="text-white">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400 mb-2">Kontak Publik</p>
                        <p class="text-sm font-medium opacity-80 italic">"Melayani dengan Hati, Membangun dengan Data"</p>
                    </div>
                </div>
            </div>
            <div class="text-center px-4">
                <h3 class="text-2xl font-heading font-bold text-slate-900 mb-2">{{ $official->name }}</h3>
                <p class="text-emerald-600 font-black text-xs uppercase tracking-widest mb-4">{{ $official->position }}</p>
                <div class="inline-block px-4 py-1.5 rounded-full bg-slate-50 text-slate-400 text-[10px] font-bold border border-slate-100 uppercase tracking-wider">
                    NIP: {{ $official->nip ?? '-' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Motto Section -->
    <div class="mt-40 bg-emerald-600 rounded-[60px] p-12 md:p-24 text-white text-center relative overflow-hidden shadow-2xl shadow-emerald-200/50">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        <div class="relative z-10">
            <h2 class="text-3xl md:text-5xl font-heading font-extrabold mb-8 italic">"Profesional, Akuntabel, dan Transparan"</h2>
            <p class="text-emerald-100 text-xl max-w-3xl mx-auto leading-relaxed">
                Kami berkomitmen memberikan pelayanan terbaik bagi seluruh warga desa tanpa pengecualian, didukung oleh data statistik yang akurat untuk setiap keputusan kebijakan.
            </p>
        </div>
    </div>
</div>
@endsection
