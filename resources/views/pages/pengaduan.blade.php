@extends('layouts.app')

@section('title', 'Pengaduan | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Portal pengaduan dan aspirasi warga Desa ' . ($site_settings['village_name'] ?? '') . ' secara aman, cepat, dan transparan.')
@section('meta_image', asset('img/meta.webp'))

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
{{-- ===================== TABS WRAPPER ===================== --}}
<div class="max-w-4xl mx-auto px-4 py-16 md:py-24"
     x-data="{
         activeTab: '{{ isset($complaint) || isset($searched_ticket) ? 'lacak' : 'kirim' }}',
         ticket: '{{ $searched_ticket ?? '' }}',
         loading: false,
         searched: {{ isset($searched_ticket) ? 'true' : 'false' }},
         result: {{ isset($complaint) ? json_encode([
             'found' => true,
             'ticket_number' => $complaint->ticket_number,
             'title' => $complaint->title,
             'content' => $complaint->content,
             'status' => $complaint->status,
             'response' => $complaint->response,
             'created_at' => $complaint->created_at->translatedFormat('d M Y, H:i'),
             'updated_at' => $complaint->updated_at->translatedFormat('d M Y, H:i'),
         ]) : 'null' }},
         async fetchStatus(ticketNum = null) {
             if (ticketNum) this.ticket = ticketNum;
             if (!this.ticket.trim()) return;
             this.loading = true;
             this.searched = true;
             try {
                 const res = await fetch('{{ route('complaints.track') }}?ticket_number=' + encodeURIComponent(this.ticket.trim()), {
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
>
    
    {{-- Tab Buttons --}}
    <div class="flex justify-center mb-12 md:mb-16">
        <div class="flex space-x-8 border-b border-slate-200">
            <button 
                @click="activeTab = 'kirim'" 
                :class="activeTab === 'kirim' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="pb-4 px-2 font-bold text-sm border-b-2 transition-colors duration-300 flex items-center gap-2"
            >
                <i class="fa-solid fa-bullhorn"></i> Kirim Laporan
            </button>
            <button 
                @click="activeTab = 'lacak'" 
                :class="activeTab === 'lacak' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                class="pb-4 px-2 font-bold text-sm border-b-2 transition-colors duration-300 flex items-center gap-2"
            >
                <i class="fa-solid fa-magnifying-glass"></i> Lacak Pengaduan
            </button>
        </div>
    </div>

    {{-- ===================== TAB: KIRIM PENGADUAN ===================== --}}
    <div x-show="activeTab === 'kirim'" x-transition:enter="transition ease-out duration-300" x-cloak>
        <div class="bg-white rounded-[40px] p-8 md:p-12 border border-slate-100 shadow-2xl shadow-slate-200/50">
            
            {{-- Success Modal Popup & Ticket Display --}}
            @if(session('success') && session('ticket_number'))
            <div x-data="{ showSuccessModal: true, copied: false }"
                 x-show="showSuccessModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md cursor-pointer select-none"
                 style="display: none;"
                 @keydown.escape.window="showSuccessModal = false"
                 @click="showSuccessModal = false">
                
                <div @click.stop class="bg-white rounded-[32px] shadow-2xl p-8 md:px-12 w-fit min-w-[300px] max-w-md mx-auto border border-slate-100 relative text-center cursor-default">
                    <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto mb-5">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <h3 class="font-heading font-extrabold text-slate-900 text-xl mb-2">{{ session('success') }}</h3>
                    <p class="text-sm text-slate-500 mb-6">Pengaduan Anda telah terdaftar dalam sistem.</p>

                    <div class="mb-8">
                        <span class="text-xs font-bold text-slate-400 block mb-1">Nomor Tiket Anda:</span>
                        <div class="flex items-center justify-center gap-3">
                            <h4 class="text-xl font-mono font-black text-slate-800 select-all">{{ session('ticket_number') }}</h4>
                            <button @click="navigator.clipboard.writeText('{{ session('ticket_number') }}'); copied = true; setTimeout(() => copied = false, 2000);"
                                    class="text-slate-400 hover:text-emerald-600 transition p-2" title="Salin Nomor">
                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                            </button>
                        </div>
                    </div>

                    <button @click="showSuccessModal = false" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-2xl font-bold text-sm transition">
                        Tutup
                    </button>
                </div>
            </div>
            @endif

            {{-- Form Header --}}
            <div class="mb-8">
                <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-2">Buat Laporan Baru</h3>
                <p class="text-slate-400 text-sm">Laporan Anda akan ditindaklanjuti oleh pihak desa.</p>
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
                <h3 class="text-xl font-heading font-extrabold text-slate-900 mb-2">Lacak Status Pengaduan</h3>
                <p class="text-slate-400 text-sm">Masukkan nomor tiket Anda di bawah ini.</p>
            </div>

            <form @submit.prevent="fetchStatus()" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-grow relative">
                    <input type="text" x-model="ticket" placeholder="Contoh: ADV-20260716-XXXX" required
                           class="w-full px-5 py-4 rounded-2xl bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-mono font-bold text-slate-800 placeholder-slate-300 outline-none transition">
                </div>
                <button type="submit" :disabled="loading" class="bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white px-8 py-4.5 rounded-2xl font-bold transition flex items-center justify-center gap-2">
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
                <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-8 md:p-12 space-y-8">
                    
                    {{-- Status Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Nomor Tiket</span>
                            <h4 class="text-xl font-mono font-black text-slate-900 mt-0.5" x-text="result.ticket_number"></h4>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-400 text-xs font-semibold">Status:</span>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider"
                                  :class="{
                                      'bg-slate-100 text-slate-600': result.status === 'Menunggu',
                                      'bg-amber-50 border border-amber-200 text-amber-700': result.status === 'Diproses',
                                      'bg-emerald-50 border border-emerald-200 text-emerald-700': result.status === 'Selesai'
                                  }"
                            >
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                          :class="{
                                              'bg-slate-400': result.status === 'Menunggu',
                                              'bg-amber-400': result.status === 'Diproses',
                                              'bg-emerald-400': result.status === 'Selesai'
                                          }"
                                    ></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2"
                                          :class="{
                                              'bg-slate-500': result.status === 'Menunggu',
                                              'bg-amber-500': result.status === 'Diproses',
                                              'bg-emerald-500': result.status === 'Selesai'
                                          }"
                                    ></span>
                                </span>
                                <span x-text="result.status"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Complaint Content --}}
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Laporan / Pengaduan Anda</span>
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100/50">
                            <h4 class="font-bold text-slate-900 mb-2" x-text="result.title"></h4>
                            <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line" x-text="result.content"></p>
                            <span class="text-[10px] text-slate-400 font-medium block mt-4">
                                <i class="fa-regular fa-clock mr-1"></i> Dikirim pada <span x-text="result.created_at"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Admin Response --}}
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2">Tanggapan / Tindak Lanjut dari Admin</span>
                        <div class="bg-emerald-50/50 rounded-2xl p-6 border border-emerald-100">
                            <template x-if="result.response">
                                <div>
                                    <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line" x-text="result.response"></p>
                                    <span class="text-[10px] text-emerald-600/70 font-semibold block mt-4">
                                        <i class="fa-regular fa-clock mr-1"></i> Ditanggapi pada <span x-text="result.updated_at"></span>
                                    </span>
                                </div>
                            </template>
                            <template x-if="!result.response">
                                <p class="text-slate-400 italic text-sm">Laporan Anda sedang dikaji dan belum mendapatkan tanggapan tertulis dari petugas admin desa. Harap periksa kembali secara berkala.</p>
                            </template>
                        </div>
                    </div>

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
</div>
@endsection
