@extends('layouts.app')

@section('title', 'Buku Tamu | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Formulir pencatatan kunjungan resmi dan tamu umum pada Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
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
                    <span class="text-white">Buku Tamu</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Buku <span class="text-emerald-500 italic">Tamu</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 font-medium">
                Pencatatan kunjungan resmi dan tamu umum.
            </p>
        </div>
    </div>
</div>

{{-- ===================== FORM SECTION ===================== --}}
<div class="max-w-4xl mx-auto px-4 py-16 md:py-24">
    <div class="bg-white rounded-[40px] p-8 md:p-12 border border-slate-100 shadow-2xl shadow-slate-200/50">
        
        {{-- Success Notification --}}
        @if(session('success'))
        <div class="mb-10 bg-emerald-50 border border-emerald-200 rounded-3xl p-6 flex gap-4 items-start text-slate-700 animate-in fade-in duration-300">
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-1">Berhasil Terkirim</h4>
                <p class="text-sm text-slate-600">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        {{-- Error Notification --}}
        @if($errors->any())
        <div class="mb-10 bg-rose-50 border border-rose-200 rounded-3xl p-6 flex gap-4 items-start text-slate-700">
            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-1">Pengecekan Formulir</h4>
                <ul class="list-disc pl-4 text-xs text-rose-600 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-px w-8 bg-emerald-500"></div>
                <span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Formulir Digital</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900">Catat Kunjungan</h2>
            <p class="text-slate-400 text-sm mt-1">Isi formulir berikut dengan data yang valid.</p>
        </div>

        <form action="{{ route('guest-book.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap Anda" required
                       class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="institution_address" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Instansi / Alamat</label>
                    <input type="text" id="institution_address" name="institution_address" value="{{ old('institution_address') }}" placeholder="Asal instansi atau alamat asal" required
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                </div>
                <div>
                    <label for="phone" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Nomor Kontak (WhatsApp)</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08xx-xxxx-xxxx" required
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition">
                </div>
            </div>

            <div>
                <label for="purpose" class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2.5 ml-1">Keperluan / Pesan</label>
                <textarea id="purpose" name="purpose" rows="5" placeholder="Tuliskan tujuan kunjungan Anda secara jelas..." required
                          class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-slate-800 placeholder-slate-300 outline-none transition resize-none">{{ old('purpose') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full flex items-center justify-center gap-3 bg-emerald-600 text-white py-5 rounded-2xl font-bold text-base hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/20 hover:shadow-emerald-200/60 active:scale-[0.98]">
                <i class="fa-solid fa-pen-nib"></i>
                Simpan Buku Tamu
            </button>
        </form>
    </div>
</div>
@endsection
