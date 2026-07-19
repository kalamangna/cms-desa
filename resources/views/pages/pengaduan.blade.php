@extends('layouts.app')

@section('title', 'Pengaduan | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Portal pengaduan dan aspirasi warga Desa ' . ($site_settings['village_name'] ?? '') . ' secara aman, cepat, dan transparan.')
@section('meta_image', asset('img/meta.png'))

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
                    <span class="text-white">Pengaduan</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Pengaduan <span class="text-emerald-500 italic">Warga</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 font-medium">
                Layanan pelaporan keluhan dan aspirasi warga.
            </p>
        </div>
    </div>
</div>

{{-- ===================== TABS WRAPPER ===================== --}}
<div class="max-w-4xl mx-auto px-4 py-16 md:py-24" x-data="{ activeTab: '{{ isset($complaint) || isset($searched_ticket) ? 'lacak' : 'kirim' }}' }">
    
    {{-- Tab Buttons --}}
    <div class="flex border-b border-slate-200 mb-12 gap-6">
        <button 
            @click="activeTab = 'kirim'" 
            :class="activeTab === 'kirim' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
            class="pb-4 font-heading font-bold text-lg border-b-2 transition duration-300 focus:outline-none flex items-center gap-2"
        >
            <i class="fa-solid fa-bullhorn text-sm"></i> Kirim Laporan
        </button>
        <button 
            @click="activeTab = 'lacak'" 
            :class="activeTab === 'lacak' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
            class="pb-4 font-heading font-bold text-lg border-b-2 transition duration-300 focus:outline-none flex items-center gap-2"
        >
            <i class="fa-solid fa-magnifying-glass text-sm"></i> Lacak Pengaduan
        </button>
    </div>

    {{-- ===================== TAB: KIRIM PENGADUAN ===================== --}}
    <div x-show="activeTab === 'kirim'" x-transition:enter="transition ease-out duration-300" x-cloak>
        <div class="bg-white rounded-[40px] p-8 md:p-12 border border-slate-100 shadow-2xl shadow-slate-200/50">
            
            {{-- Success Notification & Ticket Display --}}
            @if(session('success') && session('ticket_number'))
            <div class="mb-10 bg-emerald-50 border border-emerald-200 rounded-3xl p-8 text-slate-700 animate-in fade-in duration-300">
                <div class="flex gap-4 items-start mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-heading font-extrabold text-slate-900 text-lg mb-1">{{ session('success') }}</h4>
                        <p class="text-sm text-slate-500">Laporan Anda telah terdaftar. Catat nomor tiket berikut untuk melacak status tanggapan admin secara berkala.</p>
                    </div>
                </div>
                
                {{-- Ticket Number Box --}}
                <div class="bg-white border border-emerald-200/60 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Nomor Tiket Anda</span>
                        <h3 class="text-2xl font-mono font-black text-emerald-600 mt-1 select-all">{{ session('ticket_number') }}</h3>
                    </div>
                    <button 
                        onclick="navigator.clipboard.writeText('{{ session('ticket_number') }}'); alert('Nomor tiket berhasil disalin!');"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold px-5 py-3 rounded-xl text-sm transition"
                    >
                        <i class="fa-solid fa-copy"></i> Salin Tiket
                    </button>
                </div>
            </div>
            @endif

            {{-- Form Header --}}
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-8 bg-emerald-500"></div>
                    <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Saluran Aspirasi</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900">Buat Laporan Baru</h2>
                <p class="text-slate-400 text-sm mt-1">Laporan Anda akan ditinjau dan ditindaklanjuti secara privat oleh pihak desa.</p>
            </div>

            <form action="{{ route('complaints.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Nama Pelapor / Pengirim</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama Anda" required
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Nomor WhatsApp</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08xx-xxxx-xxxx" required
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Judul Laporan</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Tuliskan subjek / judul keluhan Anda" required
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div>
                    <label for="content" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Rincian Laporan</label>
                    <textarea id="content" name="content" rows="6" placeholder="Ceritakan permasalahan secara mendalam (sebutkan waktu, lokasi, dan kronologi jika ada)..." required
                              class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition resize-none">{{ old('content') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-3 bg-emerald-600 text-white py-5 rounded-2xl font-bold text-base hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/20 hover:shadow-emerald-200/60 active:scale-[0.98]">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim Pengaduan
                </button>
            </form>
        </div>
    </div>

    {{-- ===================== TAB: LACAK PENGADUAN ===================== --}}
    <div x-show="activeTab === 'lacak'" x-transition:enter="transition ease-out duration-300" x-cloak>
        <div class="bg-white rounded-[40px] p-8 md:p-12 border border-slate-100 shadow-2xl shadow-slate-200/50 mb-8">
            <div class="mb-8">
                <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-2">Pelacakan Status Pengaduan</h3>
                <p class="text-slate-400 text-sm">Masukkan nomor tiket pengaduan yang Anda miliki.</p>
            </div>

            <form action="{{ route('complaints.track') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-grow">
                    <input type="text" name="ticket_number" value="{{ $searched_ticket ?? '' }}" placeholder="Contoh: ADV-20260716-XXXX" required
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-mono font-bold text-slate-800 placeholder-slate-300 outline-none transition">
                </div>
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-4.5 rounded-2xl font-bold transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Lacak
                </button>
            </form>
        </div>

        {{-- Tracking Results --}}
        @if(isset($searched_ticket))
            @if($complaint)
                <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-8 md:p-12 space-y-8 animate-in fade-in duration-300">
                    
                    {{-- Status Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Nomor Tiket</span>
                            <h4 class="text-xl font-mono font-black text-slate-900 mt-0.5">{{ $complaint->ticket_number }}</h4>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-400 text-xs font-semibold">Status:</span>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider
                                @if($complaint->status === 'Menunggu') bg-slate-100 text-slate-600
                                @elseif($complaint->status === 'Diproses') bg-amber-50 border border-amber-200 text-amber-700
                                @else bg-emerald-50 border border-emerald-200 text-emerald-700 @endif"
                            >
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75
                                        @if($complaint->status === 'Menunggu') bg-slate-400
                                        @elseif($complaint->status === 'Diproses') bg-amber-400
                                        @else bg-emerald-400 @endif"
                                    ></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2
                                        @if($complaint->status === 'Menunggu') bg-slate-500
                                        @elseif($complaint->status === 'Diproses') bg-amber-500
                                        @else bg-emerald-500 @endif"
                                    ></span>
                                </span>
                                {{ $complaint->status }}
                            </span>
                        </div>
                    </div>

                    {{-- Complaint Content --}}
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Laporan / Pengaduan Anda</span>
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100/50">
                            <h4 class="font-bold text-slate-900 mb-2">{{ $complaint->title }}</h4>
                            <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $complaint->content }}</p>
                            <span class="text-[10px] text-slate-400 font-medium block mt-4"><i class="fa-regular fa-clock mr-1"></i> Dikirim pada {{ $complaint->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Admin Response --}}
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Tanggapan / Tindak Lanjut dari Admin</span>
                        <div class="bg-emerald-50/50 rounded-2xl p-6 border border-emerald-100">
                            @if($complaint->response)
                                <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">{{ $complaint->response }}</p>
                                <span class="text-[10px] text-emerald-600/70 font-semibold block mt-4"><i class="fa-regular fa-clock mr-1"></i> Ditanggapi pada {{ $complaint->updated_at->translatedFormat('d M Y, H:i') }}</span>
                            @else
                                <p class="text-slate-400 italic text-sm">Laporan Anda sedang dikaji dan belum mendapatkan tanggapan tertulis dari petugas admin desa. Harap periksa kembali secara berkala.</p>
                            @endif
                        </div>
                    </div>

                </div>
            @else
                {{-- Ticket Not Found --}}
                <div class="text-center py-16 animate-in fade-in duration-300">
                    <i class="fa-solid fa-circle-xmark text-rose-400 text-3xl mb-3 block"></i>
                    <h3 class="text-slate-400 font-bold text-sm">Tiket Tidak Ditemukan</h3>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
