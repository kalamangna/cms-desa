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
            <div class="absolute inset-0 bg-gradient-to-br from-primary-600/20 via-slate-900 to-slate-900"></div>
            <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -left-24 w-80 h-80 bg-primary-600/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-primary-500/60" aria-label="Breadcrumb">
                <ol class="inline-flex items-center gap-2">
                    <li>
                        <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1 py-0.5">
                            <i class="fa-solid fa-house text-[10px]"></i> Beranda
                        </a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                        <span class="text-white">Layanan</span>
                    </li>
                </ol>
            </nav>
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                    Layanan <span class="text-primary-500 italic">Mandiri</span>
                </h1>
                <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                    Panduan persyaratan dan pengajuan surat administrasi warga.
                </p>
            </div>
        </div>
    </div>

    {{-- ===================== SERVICES GRID ===================== --}}
    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20">

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
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md cursor-pointer select-none"
                 style="display: none;"
                 @click.self="showSuccessModal = false">
                
                <!-- Tombol Tutup (Di Luar Modal) -->
                <button
                    type="button"
                    @click.stop="showSuccessModal = false"
                    class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-[10000] backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110"
                    title="Tutup (Esc)">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <div class="bg-white rounded-[28px] shadow-2xl p-8 md:px-12 w-fit min-w-[300px] max-w-md mx-auto border border-slate-100 relative text-center cursor-default">
                    
                    <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-5">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <h3 class="font-heading font-extrabold text-slate-900 text-xl mb-2">{{ session('success') }}</h3>
                    <p class="text-sm text-slate-500 mb-6">Pengajuan layanan Anda telah terkirim.</p>

                    <div class="mb-8">
                        <span class="text-xs font-bold text-slate-400 block mb-1">Nomor Tiket Anda:</span>
                        <div class="flex items-center justify-center gap-3">
                            <h4 class="text-xl font-mono font-black text-slate-800 select-all">{{ session('ticket_number') }}</h4>
                            <button @click="navigator.clipboard.writeText('{{ session('ticket_number') }}'); copied = true; setTimeout(() => copied = false, 2000);"
                                    class="text-slate-400 hover:text-primary-600 transition p-2 cursor-pointer" title="Salin Nomor">
                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                            </button>
                        </div>
                    </div>

                    <button @click="showSuccessModal = false" class="w-full bg-slate-900 hover:bg-slate-800 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 text-white py-3.5 rounded-2xl font-bold text-sm transition-all duration-200 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
            @endif

            {{-- Tabs Navigation --}}
            <div class="flex justify-center mb-12 md:mb-16">
                <div class="flex space-x-8 border-b border-slate-200">
                    <button 
                        @click="showLacak = false"
                        :class="!showLacak ? 'border-primary-600 text-primary-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-semibold'"
                        class="pb-4 px-2 text-sm border-b-2 transition-all duration-200 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 cursor-pointer"
                    >
                        <i class="fa-solid fa-list-check"></i> Daftar Layanan
                    </button>
                    <button 
                        @click="showLacak = true"
                        :class="showLacak ? 'border-primary-600 text-primary-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-semibold'"
                        class="pb-4 px-2 text-sm border-b-2 transition-all duration-200 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 cursor-pointer"
                    >
                        <i class="fa-solid fa-magnifying-glass"></i> Lacak Pengajuan
                    </button>
                </div>
            </div>

            {{-- PANEL: DAFTAR LAYANAN --}}
            <div x-show="!showLacak" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @forelse($services as $service)
                <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">

                    {{-- Card Top --}}
                    <div class="p-8 md:p-10 flex-1">
                        {{-- Icon --}}
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-xl mb-8 shadow-xs">
                            <i class="fa-solid {{ $service->icon }}"></i>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-3">{{ $service->title }}</h3>

                        {{-- Description --}}
                        <p class="text-slate-600 text-sm leading-relaxed font-medium mb-6">
                            {{ Str::limit($service->description, 120) }}
                        </p>

                        {{-- Collapsible Requirements Preview --}}
                        <div x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-primary-600 transition mb-0 cursor-pointer">
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
                                class="mt-3 prose prose-sm max-w-none text-slate-600 bg-slate-50 border border-slate-100 rounded-2xl p-4 text-xs leading-relaxed"
                                x-cloak>
                                <div>{!! $service->requirements ?? '<p class="text-slate-400 italic">Persyaratan belum diisi.</p>' !!}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="border-t border-slate-100 px-8 md:px-10 py-5 flex items-center justify-center bg-slate-50/50">
                        <button
                            type="button"
                            @click="showApplyModal = true; applyServiceId = {{ $service->id }}; applyServiceTitle = '{{ addslashes($service->title) }}'"
                            class="w-full inline-flex items-center justify-center gap-2 bg-primary-600 text-white px-5 py-3 rounded-full font-bold text-sm hover:bg-primary-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            Ajukan Layanan
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-8">
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
                <div class="bg-white rounded-[32px] p-8 md:p-12 border border-slate-200/80 shadow-lg shadow-slate-200/50 mb-8 max-w-4xl mx-auto">
                    <div class="mb-8">
                        <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-2">Lacak Status Permohonan</h3>
                        <p class="text-slate-500 text-sm font-medium">Masukkan nomor tiket pengajuan Anda di bawah ini.</p>
                    </div>
                    <form @submit.prevent="fetchStatus()" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-grow relative">
                            <input type="text" x-model="ticket" placeholder="SRV-XXXXXXXX-XXXX" required
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono font-bold text-slate-800 placeholder-slate-300 outline-none transition text-sm">
                        </div>
                        <button type="submit" :disabled="loading" class="bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white px-8 py-4 rounded-2xl font-bold transition flex items-center justify-center gap-2 text-sm cursor-pointer active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 shadow-xs">
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
                        <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-lg shadow-slate-200/50 p-8 md:p-12 space-y-6 max-w-4xl mx-auto">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Nomor Tiket</span>
                                    <h4 class="text-lg font-mono font-black text-slate-900 mt-0.5" x-text="result.ticket_number"></h4>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 text-xs font-semibold">Status:</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider"
                                          :class="{
                                              'bg-slate-100 text-slate-600 border border-slate-200': result.status === 'Menunggu',
                                              'bg-amber-50 border border-amber-200 text-amber-700': result.status === 'Diproses',
                                              'bg-emerald-50 border border-emerald-200 text-emerald-700': result.status === 'Selesai'
                                          }"
                                    >
                                        <span x-text="result.status"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-medium">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">Nama Pemohon</span>
                                    <span class="text-slate-800 font-bold" x-text="result.name"></span>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-0.5">NIK Pemohon</span>
                                    <span class="text-slate-800 font-mono font-bold" x-text="result.nik_masked"></span>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block mb-1">Layanan Pengajuan</span>
                                <h4 class="font-extrabold text-slate-900 text-base" x-text="result.service_title"></h4>
                                <p class="text-xs text-slate-500 font-medium mt-2">
                                    <i class="fa-regular fa-clock mr-1"></i> Diajukan pada <span x-text="result.created_at"></span>
                                </p>
                            </div>

                            <template x-if="result.status === 'Selesai'">
                                <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-2xl text-slate-700 text-sm">
                                    <h5 class="font-extrabold text-emerald-800 mb-1"><i class="fa-solid fa-circle-check"></i> Pengajuan Selesai Diproses</h5>
                                    <p class="text-xs leading-relaxed text-slate-600 font-medium">Surat keterangan / berkas fisik administrasi Anda telah selesai diproses oleh Pemerintah Desa. Silakan mengambil berkas fisik tersebut di <strong>Kantor Desa</strong> pada jam operasional kerja dengan membawa dokumen persyaratan terkait.</p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!loading && searched && (!result || !result.found)">
                        <div class="text-center py-16 animate-in fade-in duration-300">
                            <i class="fa-solid fa-circle-xmark text-rose-400 text-3xl mb-3 block"></i>
                            <h3 class="text-slate-500 font-bold text-sm">Nomor Tiket Tidak Ditemukan</h3>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- ===================== APPLY LAYANAN MODAL ===================== --}}
    <div x-show="showApplyModal"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md cursor-pointer select-none"
         style="display: none;"
         @keydown.escape.window="showApplyModal = false"
         @click="showApplyModal = false">

        <!-- Tombol Tutup (Di Luar Modal) -->
        <button
            type="button"
            @click.stop="showApplyModal = false"
            class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-[10000] backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110"
            title="Tutup (Esc)">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <div x-show="showApplyModal"
             @click.stop
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-[28px] shadow-2xl p-8 md:p-10 max-w-lg w-full max-h-[85vh] overflow-y-auto border border-slate-100 relative cursor-default">

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
                           class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-sm text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div>
                    <label for="apply-name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Lengkap Pemohon</label>
                    <input type="text" id="apply-name" name="name" placeholder="Nama lengkap sesuai KK" required
                           class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-sm text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div>
                    <label for="apply-phone" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nomor WhatsApp</label>
                    <input type="tel" id="apply-phone" name="phone" placeholder="Contoh: 08xx-xxxx-xxxx" required
                           class="w-full px-4 py-3.5 rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-medium text-sm text-slate-800 placeholder-slate-300 outline-none transition">
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-primary-600 text-white px-6 py-3.5 rounded-full font-bold text-sm hover:bg-primary-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-all duration-200 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        Kirim Pengajuan
                    </button>
                    <button type="button" @click="showApplyModal = false" class="flex-1 flex items-center justify-center gap-2 bg-slate-100 text-slate-700 px-6 py-3.5 rounded-full font-bold text-sm hover:bg-slate-200 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 transition-all duration-200 cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
