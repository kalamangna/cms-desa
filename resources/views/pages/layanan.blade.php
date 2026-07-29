@extends('layouts.app')

@section('title', 'Layanan Mandiri | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Panduan standar operasional prosedur, persyaratan berkas, dan jenis layanan administrasi kependudukan pada Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
@if(!$services->isEmpty())
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        @foreach($services as $idx => $service)
        {
            "@@type": "GovernmentService",
            "@@id": "{{ url('/layanan') }}#service-{{ $service->id }}",
            "name": {!! json_encode($service->title) !!},
            "description": {!! json_encode(strip_tags($service->description)) !!},
            "serviceOperator": {
                "@@type": "GovernmentOrganization",
                "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}",
                "url": "{{ url('/') }}"
            },
            "provider": {
                "@@type": "GovernmentOffice",
                "name": "Kantor Desa {{ $site_settings['village_name'] ?? 'Website Desa' }}",
                "url": "{{ url('/kontak') }}"
            }
        }{{ $idx < count($services) - 1 ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
@endpush

@section('content')
{{-- Wrapper with Alpine JS state --}}
<div x-data="{ 
        activeService: null,
        showApplyModal: false,
        applyServiceId: null,
        applyServiceTitle: '',
        showLacak: {{ isset($serviceRequest) || isset($searched_ticket) ? 'true' : 'false' }},
        ticket: '{{ $searched_ticket ?? '' }}',
        loading: false,
        searched: {{ isset($searched_ticket) ? 'true' : 'false' }},
        result: {{ isset($serviceRequest) ? json_encode([
            'found' => true,
            'ticket_number' => $serviceRequest->ticket_number,
            'name' => $serviceRequest->name,
            'nik_masked' => substr($serviceRequest->nik, 0, 4) . '**********',
            'service_title' => $serviceRequest->service?->title ?? 'Layanan Umum',
            'status' => $serviceRequest->status,
            'created_at' => $serviceRequest->created_at->translatedFormat('d M Y, H:i'),
        ]) : 'null' }},
        async fetchStatus(ticketNum = null) {
            if (ticketNum) this.ticket = ticketNum;
            if (!this.ticket.trim()) return;
            this.loading = true;
            this.searched = true;
            try {
                const res = await fetch('{{ route('service-requests.track') }}?ticket_number=' + encodeURIComponent(this.ticket.trim()), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    this.result = await res.json();
                } else {
                    this.result = null;
                }
            } catch (e) {
                this.result = null;
            } finally {
                this.loading = false;
            }
        }
     }" 
     class="relative"
>

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
                        <span class="text-white">Layanan</span>
                    </li>
                </ol>
            </nav>
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                    Layanan <span class="text-emerald-500 italic">Mandiri</span>
                </h1>
                <p class="text-slate-300 text-lg mt-2 font-medium">
                    Panduan persyaratan dan pengajuan surat administrasi warga.
                </p>
            </div>
        </div>
    </div>

    {{-- ===================== SERVICES GRID ===================== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

        {{-- Success Modal Popup & Ticket Display --}}
        @if(session('success') && session('ticket_number'))
        <div x-data="{ showSuccessModal: true, copied: false }"
             x-show="showSuccessModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
             style="display: none;"
             @click.self="showSuccessModal = false">
            
            <div class="bg-white rounded-[40px] shadow-2xl p-8 md:p-10 max-w-lg w-full border border-slate-100 relative text-center animate-in zoom-in-95 duration-300">
                <button @click="showSuccessModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 transition duration-300 w-10 h-10 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-6 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <h3 class="font-heading font-extrabold text-slate-900 text-2xl mb-2">{{ session('success') }}</h3>
                <p class="text-xs text-slate-500 mb-6 leading-relaxed">Permohonan surat Anda telah berhasil dikirim ke pihak desa. Catat & simpan nomor tiket berikut untuk melacak status proses pengajuan.</p>

                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 mb-6 text-center">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1">Nomor Tiket Permohonan Surat</span>
                    <h4 class="text-2xl font-mono font-black text-emerald-600 select-all tracking-wide mb-4">{{ session('ticket_number') }}</h4>

                    <button @click="navigator.clipboard.writeText('{{ session('ticket_number') }}'); copied = true; setTimeout(() => copied = false, 2500);"
                            class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3.5 rounded-xl text-sm transition shadow-md shadow-emerald-200">
                        <template x-if="!copied">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-copy"></i> Salin Nomor Tiket</span>
                        </template>
                        <template x-if="copied">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-check"></i> Berhasil Disalin!</span>
                        </template>
                    </button>
                </div>

                <div class="flex gap-3">
                    <button @click="showLacak = true; fetchStatus('{{ session('ticket_number') }}'); showSuccessModal = false;"
                            class="flex-1 bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-2xl font-bold text-xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Lacak Status Sekarang
                    </button>
                    <button @click="showSuccessModal = false" class="px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3.5 rounded-2xl font-bold text-xs transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Header & Toggle --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-16">
            <div>
                <div class="flex items-center gap-3 mb-4"><div class="h-px w-8 bg-emerald-500"></div><span class="text-emerald-600 font-black text-xs uppercase tracking-[0.25em]">Layanan Surat Online</span></div>
                <h2 class="text-3xl md:text-4xl font-heading font-extrabold text-slate-900">Persyaratan & Pengajuan Mandiri</h2>
            </div>
            
            <div class="flex gap-4">
                <button 
                    @click="showLacak = false"
                    :class="!showLacak ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:border-emerald-300'"
                    class="px-6 py-3 rounded-full text-xs font-bold transition duration-300 flex items-center gap-2 whitespace-nowrap"
                >
                    <i class="fa-solid fa-list-check"></i> Daftar Layanan
                </button>
                <button 
                    @click="showLacak = true"
                    :class="showLacak ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:border-emerald-300'"
                    class="px-6 py-3 rounded-full text-xs font-bold transition duration-300 flex items-center gap-2 whitespace-nowrap"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Lacak Pengajuan
                </button>
            </div>
        </div>

        {{-- PANEL: DAFTAR LAYANAN --}}
        <div x-show="!showLacak" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @forelse($services as $service)
            <div class="group bg-white rounded-[32px] border border-slate-100 shadow-lg shadow-slate-200/50 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-100/60 hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden">

                {{-- Card Top --}}
                <div class="p-8 md:p-10 flex-1">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-8 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm">
                        <i class="fa-solid {{ $service->icon }}"></i>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-xl font-heading font-bold text-slate-900 mb-3">{{ $service->title }}</h3>

                    {{-- Description --}}
                    <p class="text-slate-500 text-sm leading-relaxed font-medium mb-6">
                        {{ Str::limit($service->description, 120) }}
                    </p>

                    {{-- Collapsible Requirements Preview --}}
                    <div x-data="{ open: false }">
                        <button
                            @type="button"
                            @click="open = !open"
                            class="flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-emerald-600 transition mb-0">
                            <i class="fa-solid fa-list-ul text-[10px]"></i>
                            Persyaratan
                            <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-3 prose prose-sm prose-emerald max-w-none text-slate-500 bg-slate-50 rounded-2xl p-4 text-xs leading-relaxed"
                            x-cloak>
                            <div>{!! $service->requirements ?? '<p class="text-slate-400 italic">Persyaratan belum diisi.</p>' !!}</div>
                        </div>
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="border-t border-slate-100 px-8 md:px-10 py-5 flex items-center justify-between gap-4 bg-slate-50/50">
                    <button
                        @click="activeService = {{ json_encode($service) }}"
                        class="text-sm font-bold text-slate-500 hover:text-emerald-600 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-xs"></i>
                        Detail
                    </button>
                    <button
                        @click="showApplyModal = true; applyServiceId = {{ $service->id }}; applyServiceTitle = '{{ addslashes($service->title) }}'"
                        class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2.5 rounded-full font-bold text-xs hover:bg-emerald-700 transition shadow-md shadow-emerald-200">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        Ajukan
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <x-empty-state
                    icon="fa-solid fa-clipboard-list"
                    title="Layanan Mandiri Belum Tersedia"
                    description="Hubungi petugas desa untuk informasi layanan."
                    action-label="Hubungi Kami"
                    action-href="/kontak"
                />
            </div>
            @endforelse
        </div>

        {{-- PANEL: LACAK PERMOHONAN --}}
        <div x-show="showLacak" class="space-y-8 animate-in fade-in duration-300" x-cloak>
            <div class="bg-white rounded-[32px] p-8 border border-slate-100 shadow-xl max-w-2xl mx-auto">
                <h3 class="text-lg font-heading font-extrabold text-slate-900 mb-2">Lacak Status Permohonan Surat Real-time</h3>
                <p class="text-slate-400 text-xs mb-6">Masukkan nomor tiket permohonan Anda di bawah ini untuk melihat perkembangan proses tanpa perlu memuat ulang halaman.</p>
                <form @submit.prevent="fetchStatus()" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-grow">
                        <input type="text" x-model="ticket" placeholder="SRV-XXXXXXXX-XXXX" required
                               class="w-full px-5 py-3.5 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-mono font-bold text-slate-800 placeholder-slate-300 outline-none text-sm transition">
                    </div>
                    <button type="submit" :disabled="loading" class="bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white px-8 py-3.5 rounded-2xl font-bold transition text-sm flex items-center justify-center gap-2">
                        <template x-if="loading">
                            <i class="fa-solid fa-spinner animate-spin"></i>
                        </template>
                        <template x-if="!loading">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </template>
                        <span x-text="loading ? 'Memuat...' : 'Lacak'"></span>
                    </button>
                </form>
            </div>

            {{-- Tracking Results (Real-time Rendered) --}}
            <div x-show="searched" x-transition:enter="transition ease-out duration-300">
                <template x-if="result && result.found">
                    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl p-8 md:p-12 space-y-6 max-w-2xl mx-auto">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Nomor Tiket</span>
                                <h4 class="text-lg font-mono font-black text-slate-900 mt-0.5" x-text="result.ticket_number"></h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 text-xs font-semibold">Status:</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider"
                                      :class="{
                                          'bg-slate-100 text-slate-600': result.status === 'Menunggu',
                                          'bg-amber-50 border border-amber-200 text-amber-700': result.status === 'Diproses',
                                          'bg-emerald-50 border border-emerald-200 text-emerald-700': result.status === 'Selesai'
                                      }"
                                >
                                    <span x-text="result.status"></span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-xs font-medium">
                            <div class="bg-slate-50 p-4 rounded-xl">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Nama Pemohon</span>
                                <span class="text-slate-800 font-bold" x-text="result.name"></span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-xl">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">NIK Pemohon</span>
                                <span class="text-slate-800 font-mono font-bold" x-text="result.nik_masked"></span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1">Layanan Pengajuan</span>
                            <h4 class="font-bold text-slate-900 text-base" x-text="result.service_title"></h4>
                            <p class="text-xs text-slate-500 mt-2">
                                <i class="fa-regular fa-clock mr-1"></i> Diajukan pada <span x-text="result.created_at"></span>
                            </p>
                        </div>

                        <template x-if="result.status === 'Selesai'">
                            <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-2xl text-slate-700 text-sm">
                                <h5 class="font-bold text-emerald-800 mb-1"><i class="fa-solid fa-circle-check"></i> Pengajuan Selesai Diproses</h5>
                                <p class="text-xs leading-relaxed text-slate-600">Surat keterangan / berkas fisik administrasi Anda telah selesai diproses oleh Pemerintah Desa. Silakan mengambil berkas fisik tersebut di <strong>Kantor Desa</strong> pada jam operasional kerja dengan membawa dokumen persyaratan terkait.</p>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="!loading && searched && (!result || !result.found)">
                    <div class="text-center py-16 animate-in fade-in duration-300">
                        <i class="fa-solid fa-circle-xmark text-rose-400 text-3xl mb-3 block"></i>
                        <h3 class="text-slate-400 font-bold text-sm">Nomor Tiket Tidak Ditemukan</h3>
                    </div>
                </template>
            </div>
        </div>

        {{-- ===================== INFO BANNER ===================== --}}
        <div class="mt-20 md:mt-28 bg-slate-900 rounded-[40px] md:rounded-[56px] p-10 md:p-20 text-white relative overflow-hidden shadow-2xl shadow-slate-900/20">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-500/5 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="max-w-2xl text-center md:text-left">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl mb-6 mx-auto md:mx-0">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-heading font-extrabold mb-4">Butuh Bantuan Lainnya?</h2>
                    <p class="text-slate-400 text-lg leading-relaxed">
                        Jika layanan yang Anda cari tidak tersedia di atas, silakan hubungi petugas administrasi kami atau datang langsung ke Kantor Desa pada jam operasional.
                    </p>
                </div>
                <a href="/kontak" class="flex-shrink-0 bg-emerald-600 text-white px-12 py-5 rounded-full font-bold text-lg hover:bg-emerald-700 transition shadow-xl shadow-emerald-900/40 whitespace-nowrap flex items-center gap-3">
                    <i class="fa-solid fa-phone"></i>
                    Hubungi Petugas
                </a>
            </div>
        </div>
    </div>

    {{-- ===================== DETAIL MODAL OVERLAY ===================== --}}
    <div x-show="activeService"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
         style="display: none;"
         @click.self="activeService = null">

        {{-- Modal Card --}}
        <div x-show="activeService"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-[40px] shadow-2xl p-8 md:p-12 max-w-2xl w-full max-h-[85vh] overflow-y-auto border border-slate-100 relative">

            {{-- Close Button --}}
            <button @click="activeService = null" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900 transition duration-300 w-10 h-10 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            {{-- Icon & Title --}}
            <div class="flex items-center gap-6 mb-8 pr-12">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0 shadow-sm">
                    <i :class="'fa-solid ' + (activeService ? (activeService.icon ?? 'fa-file-alt') : '')"></i>
                </div>
                <div>
                    <h3 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900" x-text="activeService ? activeService.title : ''"></h3>
                    <p class="text-slate-400 text-xs mt-1 uppercase tracking-wider font-bold">Persyaratan &amp; Prosedur</p>
                </div>
            </div>

            {{-- Description --}}
            <p class="text-slate-500 text-sm leading-relaxed font-medium mb-6 pb-6 border-b border-slate-100" x-text="activeService ? activeService.description : ''"></p>

            {{-- Requirements --}}
            <div class="prose prose-emerald max-w-none text-slate-600 leading-relaxed font-medium mb-10 text-sm md:text-base">
                <div x-html="activeService ? (activeService.requirements || '<p class=\'text-slate-400 italic\'>Persyaratan belum diisi.</p>') : ''"></div>
            </div>

            {{-- Modal Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-100">
                <button 
                    @click="applyServiceId = activeService.id; applyServiceTitle = activeService.title; activeService = null; showApplyModal = true;"
                    class="flex-1 flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3.5 rounded-full font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-200"
                >
                    <i class="fa-solid fa-paper-plane"></i>
                    Ajukan Layanan Ini
                </button>
                <button @click="activeService = null" class="flex-1 flex items-center justify-center gap-2 bg-slate-100 text-slate-700 px-6 py-3.5 rounded-full font-bold text-sm hover:bg-slate-200 transition">
                    <i class="fa-solid fa-xmark"></i>
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== APPLY LAYANAN MODAL ===================== --}}
    <div x-show="showApplyModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
         style="display: none;"
         @click.self="showApplyModal = false">

        <div x-show="showApplyModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-[40px] shadow-2xl p-8 md:p-10 max-w-lg w-full border border-slate-100 relative">

            <button @click="showApplyModal = false" class="absolute top-8 right-8 text-slate-400 hover:text-slate-900 transition duration-300 w-10 h-10 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="mb-8">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Pengajuan Layanan</span>
                <h3 class="text-xl font-heading font-extrabold text-slate-900 mt-1" x-text="applyServiceTitle"></h3>
            </div>

            <form action="{{ route('service-requests.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="service_id" :value="applyServiceId">

                <div>
                    <label for="apply-nik" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">NIK Pemohon</label>
                    <input type="text" id="apply-nik" name="nik" maxlength="16" placeholder="Masukkan 16 digit NIK Anda" required
                           class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-sm text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div>
                    <label for="apply-name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Lengkap Pemohon</label>
                    <input type="text" id="apply-name" name="name" placeholder="Nama lengkap sesuai KK" required
                           class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-sm text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div>
                    <label for="apply-phone" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nomor WhatsApp</label>
                    <input type="tel" id="apply-phone" name="phone" placeholder="Contoh: 08xx-xxxx-xxxx" required
                           class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium text-sm text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-emerald-600 text-white px-6 py-3.5 rounded-full font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                        <i class="fa-solid fa-circle-check"></i>
                        Kirim Pengajuan
                    </button>
                    <button type="button" @click="showApplyModal = false" class="flex-1 flex items-center justify-center gap-2 bg-slate-100 text-slate-700 px-6 py-3.5 rounded-full font-bold text-sm hover:bg-slate-200 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
