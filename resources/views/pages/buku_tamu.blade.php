@extends('layouts.app')

@section('title', 'Buku Tamu | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Formulir pencatatan kunjungan resmi dan tamu umum pada Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@section('content')
{{-- ===================== HERO ===================== --}}
<div class="relative bg-slate-900 dark:bg-slate-950 py-16 md:py-24 lg:py-28 overflow-hidden transition-colors duration-500">
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900 dark:via-slate-950 dark:to-slate-950 transition-colors duration-500"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/5 rounded-full blur-3xl transition-colors duration-500"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 dark:bg-primary-600/5 rounded-full blur-3xl transition-colors duration-500"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Buku Tamu</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-4 sm:mb-6 drop-shadow-2xl">
                Buku <span class="text-primary-500 italic">Tamu</span>
            </h1>
            <p class="text-slate-300 text-base sm:text-lg mt-2 leading-relaxed">
                Pencatatan kunjungan resmi dan tamu umum Kantor Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== FORM SECTION ===================== --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-8 md:p-12 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50">
            
            {{-- Success Modal Popup --}}
            @if(session('success'))
                <x-success-modal
                    :title="session('success')"
                    description="Data kunjungan Anda telah dicatat."
                />
            @endif

            @error('form_error')
            <div class="mb-6 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 rounded-2xl p-4 flex gap-3 items-start text-rose-700 dark:text-rose-300 text-xs font-medium">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <span>{{ $message }}</span>
            </div>
            @enderror

            <div class="mb-8">
                <h3 class="text-2xl md:text-3xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Catat Kunjungan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Isi formulir berikut dengan data yang valid.</p>
            </div>

            <form action="{{ route('guest-book.store') }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true" class="space-y-6">
                @csrf
                <x-honeypot />
                <div>
                    <label for="name" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Budi Santoso" required
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('name') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-base sm:text-sm">
                    @error('name')
                        <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="institution_address" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Instansi atau Alamat</label>
                        <input type="text" id="institution_address" name="institution_address" value="{{ old('institution_address') }}" placeholder="Dinas Pertanian Kabupaten Sinjai" required
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('institution_address') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-base sm:text-sm">
                        @error('institution_address')
                            <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Nomor WhatsApp</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="081234567890" required
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('phone') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none transition text-base sm:text-sm">
                        @error('phone')
                            <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="purpose" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Keperluan Kunjungan</label>
                    <textarea id="purpose" name="purpose" rows="5" placeholder="Koordinasi program pendampingan pertanian" required
                              class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('purpose') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none transition resize-none text-base sm:text-sm">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-1.5 ml-1 text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="submitting"
                        class="w-full flex items-center justify-center gap-3 bg-primary-600 text-white py-4.5 rounded-2xl font-bold text-base hover:bg-primary-700 disabled:opacity-60 disabled:cursor-not-allowed active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer">
                    <template x-if="submitting">
                        <i class="fa-solid fa-spinner animate-spin text-sm"></i>
                    </template>
                    <template x-if="!submitting">
                        <i class="fa-solid fa-pen-nib text-sm"></i>
                    </template>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Buku Tamu'"></span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
