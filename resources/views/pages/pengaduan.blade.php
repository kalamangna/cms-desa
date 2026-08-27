@extends('layouts.app')

@section('title', 'Pengaduan Warga | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Portal pengaduan dan aspirasi warga Desa ' . ($site_settings['village_name'] ?? '') . ' secara aman, cepat, dan transparan.')
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
                    <span class="text-white">Pengaduan</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-heading font-black tracking-tight text-white leading-tight mb-6 drop-shadow-2xl">
                Pengaduan <span class="text-primary-500 italic">Warga</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 leading-relaxed">
                Layanan pelaporan keluhan dan aspirasi warga.
            </p>
        </div>
    </div>
</div>

{{-- ===================== TABS WRAPPER ===================== --}}
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 lg:py-20"
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
            <div class="flex space-x-8 border-b border-slate-200 dark:border-slate-800">
                <button
                    @click="activeTab = 'kirim'"
                    :class="activeTab === 'kirim' ? 'border-primary-600 text-primary-700 dark:text-primary-400 font-extrabold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 font-semibold'"
                    class="pb-4 px-2 text-sm border-b-2 transition-all duration-200 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 cursor-pointer"
                >
                    <i class="fa-solid fa-bullhorn"></i> Kirim Laporan
                </button>
                <button
                    @click="activeTab = 'lacak'"
                    :class="activeTab === 'lacak' ? 'border-primary-600 text-primary-700 dark:text-primary-400 font-extrabold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 font-semibold'"
                    class="pb-4 px-2 text-sm border-b-2 transition-all duration-200 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 cursor-pointer"
                >
                    <i class="fa-solid fa-magnifying-glass"></i> Lacak Pengaduan
                </button>
            </div>
        </div>

        {{-- ===================== TAB: KIRIM PENGADUAN ===================== --}}
        <div x-show="activeTab === 'kirim'" x-transition:enter="transition ease-out duration-300" x-cloak>
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 md:p-12 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50">
                
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
                     @click.self="showSuccessModal = false">
                    
                    <!-- Tombol Tutup (Di Luar Modal) -->
                    <button
                        type="button"
                        @click.stop="showSuccessModal = false"
                        class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-[10000] backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900"
                        title="Tutup (Esc)">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>

                    <div @click.stop class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 md:px-12 w-fit min-w-[300px] max-w-md mx-auto border border-slate-100 dark:border-slate-800 relative text-center cursor-default">
                        <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-5 shadow-xs">
                            <i class="fa-solid fa-check"></i>
                        </div>

                        <h3 class="font-heading font-extrabold text-slate-900 dark:text-slate-100 text-xl mb-2">{{ session('success') }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 font-medium">Pengaduan Anda telah terdaftar dalam sistem.</p>

                        <div class="mb-8">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 block mb-1">Nomor Tiket Anda:</span>
                            <div class="flex items-center justify-center gap-3">
                                <h4 class="text-xl font-mono font-black text-slate-800 dark:text-slate-100 select-all">{{ session('ticket_number') }}</h4>
                                <button @click="navigator.clipboard.writeText('{{ session('ticket_number') }}'); copied = true; setTimeout(() => copied = false, 2000);"
                                        class="text-slate-400 dark:text-slate-500 hover:text-primary-600 dark:hover:text-primary-400 transition p-2 cursor-pointer" title="Salin Nomor">
                                    <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                                </button>
                            </div>
                        </div>

                        <button @click="showSuccessModal = false" class="w-full bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 text-white py-3.5 rounded-2xl font-bold text-sm transition-all duration-200 cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
                @endif

                {{-- Form Header --}}
                <div class="mb-8">
                    <h3 class="text-xl font-heading font-extrabold text-slate-900 dark:text-slate-100 mb-2">Buat Laporan Baru</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Laporan Anda akan ditindaklanjuti oleh pihak desa.</p>
                </div>

                <form action="{{ route('complaints.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Nama Pelapor / Pengirim</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama Anda" required
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('name') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-300 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Nomor WhatsApp</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08xx-xxxx-xxxx" required
                                   class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('phone') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-300 dark:placeholder-slate-500 outline-none transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Judul Laporan</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Tuliskan subjek / judul keluhan Anda" required
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('title') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-300 dark:placeholder-slate-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label for="content" class="block text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2.5 ml-1">Rincian Laporan</label>
                        <textarea id="content" name="content" rows="6" placeholder="Ceritakan permasalahan secara mendalam (sebutkan waktu, lokasi, dan kronologi jika ada)..." required
                                  class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border @error('content') border-rose-500 dark:border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-primary-500 @enderror focus:outline-none focus:ring-2 focus:border-transparent font-medium text-slate-800 dark:text-slate-100 placeholder-slate-300 dark:placeholder-slate-500 outline-none transition resize-none text-sm">{{ old('content') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-3 bg-primary-600 text-white py-4.5 rounded-2xl font-bold text-base hover:bg-primary-700 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-all duration-200 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                        Kirim Pengaduan
                    </button>
                </form>
            </div>
        </div>

        {{-- ===================== TAB: LACAK PENGADUAN ===================== --}}
        <div x-show="activeTab === 'lacak'" x-transition:enter="transition ease-out duration-300" x-cloak>
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 md:p-12 border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 mb-8">
                <div class="mb-8">
                    <h3 class="text-2xl md:text-3xl font-heading font-black tracking-tight text-slate-900 dark:text-slate-100 mb-2">Lacak Status Pengaduan</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Masukkan nomor tiket pengaduan Anda di bawah ini.</p>
                </div>

                <form @submit.prevent="fetchStatus()" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-grow relative">
                        <input type="text" x-model="ticket" placeholder="Contoh: ADV-20260716-XXXX" required
                               class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono font-bold text-slate-800 dark:text-slate-100 placeholder-slate-300 dark:placeholder-slate-500 outline-none transition text-sm">
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
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-lg shadow-slate-200/50 dark:shadow-slate-950/50 p-8 md:p-12 space-y-8">

                        {{-- Status Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Nomor Tiket</span>
                                <h4 class="text-xl font-mono font-black text-slate-900 dark:text-slate-100 mt-0.5" x-text="result.ticket_number"></h4>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-slate-500 dark:text-slate-400 text-xs font-semibold">Status:</span>
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider"
                                      :class="{
                                          'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700': result.status === 'Menunggu',
                                          'bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900 text-amber-700 dark:text-amber-300': result.status === 'Diproses',
                                          'bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900 text-emerald-700 dark:text-emerald-300': result.status === 'Selesai'
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
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2">Laporan / Pengaduan Anda</span>
                            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-6 border border-slate-100 dark:border-slate-700">
                                <h4 class="font-extrabold text-slate-900 dark:text-slate-100 mb-2" x-text="result.title"></h4>
                                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed whitespace-pre-line font-medium" x-text="result.content"></p>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block mt-4">
                                    <i class="fa-regular fa-clock mr-1"></i> Dikirim pada <span x-text="result.created_at"></span>
                                </span>
                            </div>
                        </div>

                        {{-- Admin Response --}}
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2">Tanggapan / Tindak Lanjut dari Admin</span>
                            <div class="bg-primary-50/60 dark:bg-primary-950/30 rounded-2xl p-6 border border-primary-100/80 dark:border-primary-900/60">
                                <template x-if="result.response">
                                    <div>
                                        <p class="text-slate-800 dark:text-slate-100 text-sm leading-relaxed whitespace-pre-line font-medium" x-text="result.response"></p>
                                        <span class="text-[10px] text-primary-700 dark:text-primary-400 font-bold uppercase tracking-wider block mt-4">
                                            <i class="fa-regular fa-clock mr-1"></i> Ditanggapi pada <span x-text="result.updated_at"></span>
                                        </span>
                                    </div>
                                </template>
                                <template x-if="!result.response">
                                    <p class="text-slate-500 dark:text-slate-400 italic text-sm font-medium">Laporan Anda sedang dikaji dan belum mendapatkan tanggapan tertulis dari petugas admin desa. Harap periksa kembali secara berkala.</p>
                                </template>
                            </div>
                        </div>

                    </div>
                </template>

                <template x-if="!loading && searched && (!result || !result.found)">
                    <div class="text-center py-16 animate-in fade-in duration-300">
                        <i class="fa-solid fa-circle-xmark text-rose-400 text-3xl mb-3 block"></i>
                        <h3 class="text-slate-500 dark:text-slate-400 font-bold text-sm">Nomor Tiket Tidak Ditemukan</h3>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
