@extends('layouts.app')

@section('title', 'Aparatur | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Susunan jajaran aparatur dan perangkat desa yang bertugas dalam penyelenggaraan urusan pemerintahan di bawah Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

@push('head')
<script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@graph": [{
            "@@type": "GovernmentOrganization",
            "@@id": "{{ url('/aparatur') }}#organization",
            "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('img/sinjai.png') }}",
            "employee": [
                @foreach($officials as $idx => $official) {
                    "@@type": "Person",
                    "name": "{{ $official->name }}",
                    "jobTitle": "{{ $official->position }}",
                    "image": "{{ $official->photo ? asset('storage/' . $official->photo) : asset('img/meta.png') }}"
                }{{ $idx < count($officials) - 1 ? ',' : '' }}
                @endforeach
            ]
        }]
    }
</script>

<style id="sotk-custom-css">

    /* ─── SOTK Modal ─── */
    .sotk-modal-backdrop { position: fixed; inset: 0; z-index: 9999; background: rgba(15,23,42,0.8); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; padding: 16px; }
    .sotk-modal { background: #fff; border-radius: 28px; box-shadow: 0 40px 100px rgba(15,23,42,0.3); width: 100%; max-width: 1240px; height: 90vh; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
    .sotk-modal:fullscreen, .sotk-modal:-webkit-full-screen { border-radius: 0; max-height: 100vh; height: 100vh; }
    .sotk-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 22px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; background: #fff; border-radius: 28px 28px 0 0; }
    .sotk-modal:fullscreen .sotk-modal-header, .sotk-modal:-webkit-full-screen .sotk-modal-header { border-radius: 0; }
    .sotk-modal-actions { display: flex; align-items: center; gap: 5px; }
    .sotk-modal-btn { width: 36px; height: 36px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b; font-size: 12px; flex-shrink: 0; transition: background 0.2s, color 0.2s, box-shadow 0.2s, border-color 0.2s; }
    .sotk-modal-btn:hover { background: #ecfdf5; color: #059669; border-color: #6ee7b7; box-shadow: 0 4px 12px rgba(5,150,105,0.15); }
    .sotk-modal-btn.btn-close:hover { background: #fef2f2; color: #dc2626; border-color: #fca5a5; }
    .sotk-modal-body { flex: 1; min-height: 0; padding: 16px 22px 22px; display: flex; flex-direction: column; gap: 10px; }
    .sotk-hint { font-size: 11px; color: #94a3b8; font-weight: 500; display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
    .sotk-zoom-wrapper { flex: 1; min-height: 0; overflow: auto; border: 1px solid #e2e8f0; border-radius: 16px; background: #f8fafc; display: flex; align-items: flex-start; justify-content: center; padding: 20px 16px; cursor: grab; }

    .sotk-zoom-wrapper:active { cursor: grabbing; }
    .sotk-zoom-content { transform-origin: top center; transition: transform 0.15s ease; }
    .sotk-trigger-btn { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #059669; font-weight: 700; font-size: 13px; padding: 10px 22px; border-radius: 999px; border: 1.5px solid #059669; cursor: pointer; transition: background 0.2s, color 0.2s, box-shadow 0.2s; }
    .sotk-trigger-btn:hover { background: #059669; color: #fff; box-shadow: 0 4px 16px rgba(5,150,105,0.25); }
    .sotk-trigger-btn:active { transform: scale(0.97); }
    @media (max-width: 767px) {
        .sotk-trigger-btn { display: none !important; }
    }
    body.sotk-modal-open { overflow: hidden; }


    /* ─── Org Chart Tree ─── */
    /* ─── Tree layout ─── */
    .oc-tree { display: flex; min-width: max-content; justify-content: center; margin: 0 auto; }
    .oc-tree ul { display: flex; align-items: flex-start; position: relative; padding: 0; margin: 0; list-style: none; }

    /* ─── Connector Lines ─── */

    /*
     * Diagram vertikal (bukan skala):
     *
     *   ┌──────────┐
     *   │  parent  │  ← .oc-card
     *   └──────────┘
     *        │  ← height:32px (.oc-item.has-children > .oc-card::after)
     *  ──────┼──────  ← .oc-children > .oc-item::before (top:0 of .oc-item)
     *        │  ← height:32px (.oc-children > .oc-item > .oc-card::before)
     *   ┌──────────┐
     *   │  child   │  ← .oc-card, top:32px dalam .oc-item (karena padding-top)
     *   └──────────┘
     */

    /* Jarak parent card → bar = 32px */
    /* Margin negatif menetralkan padding horizontal .oc-item parent agar lebar kolom simetris */
    .oc-tree ul.oc-children { padding-top: 32px; margin-left: -20px; margin-right: -20px; }

    /* Jarak bar → child card = 32px (padding-top pada .oc-item) */
    .oc-item { display: flex; flex-direction: column; align-items: center; position: relative; padding: 32px 20px 0; }

    /* Garis vertikal TURUN dari parent card ke bar (dengan overlap 2px) */
    .oc-item.has-children > .oc-card::after {
        content: ''; position: absolute;
        left: 50%; top: 100%;
        width: 2px; height: 34px;
        background: #cbd5e1;
        transform: translateX(-50%);
        z-index: 2;
    }

    /* Garis HORIZONTAL — bar di top:0 tiap .oc-item dalam .oc-children (overlap 1px kiri-kanan) */
    .oc-children > .oc-item::before {
        content: ''; position: absolute;
        top: 0; left: -1px; right: -1px;
        height: 2px;
        background: #cbd5e1;
    }
    .oc-children > .oc-item:first-child::before { left: 50%; }
    .oc-children > .oc-item:last-child::before  { right: 50%; }
    .oc-children > .oc-item:only-child::before  { display: none; }

    /* Garis vertikal NAIK dari child card ke bar (dengan overlap 2px) */
    .oc-children > .oc-item > .oc-card::before {
        content: ''; position: absolute;
        left: 50%; bottom: 100%;
        width: 2px; height: 34px;
        background: #cbd5e1;
        transform: translateX(-50%);
        z-index: 0;
    }



    /* ─── Node Card ─── */
    .oc-card { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 5px; background: #fff; border: 2px solid #e2e8f0; border-radius: 20px; padding: 14px 10px 10px; width: 148px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: box-shadow 0.2s, transform 0.2s; cursor: default; }
    .oc-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }
    .oc-photo { width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 2px solid #e2e8f0; flex-shrink: 0; background-size: cover; background-position: center; background-repeat: no-repeat; }

    .oc-name { font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.15; font-family: 'Poppins', sans-serif; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word; }
    .oc-pos { font-size: 11px; font-weight: 600; color: #475569; line-height: 1.15; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; word-break: break-word; }





    /* Level colors */
    .oc-lv1 { border-color: #059669; background: linear-gradient(160deg, #ecfdf5 0%, #fff 55%); }
    .oc-lv1 .oc-name { color: #059669; }
    .oc-lv1 .oc-photo { border-color: #6ee7b7; }
    .oc-lv2 { border-color: #0ea5e9; background: linear-gradient(160deg, #f0f9ff 0%, #fff 55%); }
    .oc-lv2 .oc-name { color: #0369a1; }
    .oc-lv2 .oc-photo { border-color: #bae6fd; }
    .oc-lv3 { border-color: #8b5cf6; background: linear-gradient(160deg, #f5f3ff 0%, #fff 55%); }
    .oc-lv3 .oc-name { color: #6d28d9; }
    .oc-lv3 .oc-photo { border-color: #ddd6fe; }
    .oc-lv4 { border-color: #f59e0b; background: linear-gradient(160deg, #fffbeb 0%, #fff 55%); }
    .oc-lv4 .oc-name { color: #b45309; }
    .oc-lv4 .oc-photo { border-color: #fde68a; }
    .oc-lv5 { border-color: #94a3b8; }
</style>
@endpush

@section('content')
<div x-data="sotkModal()">

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
                <li><a href="/" class="hover:text-emerald-400 transition-colors duration-200 flex items-center gap-1.5"><i class="fa-solid fa-house text-[10px]"></i> Beranda</a></li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-chevron-right text-[9px] text-emerald-500/40"></i><span class="text-white">Aparatur Desa</span></li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">Aparatur <span class="text-emerald-500 italic">Desa</span></h1>
            <p class="text-slate-300 text-lg mt-2 mb-6">Jajaran pelayan masyarakat Desa {{ $site_settings['village_name'] ?? '' }}.</p>
            <button type="button" @click="open()" class="sotk-trigger-btn" aria-haspopup="dialog">
                <i class="fa-solid fa-sitemap text-sm"></i> Struktur Organisasi
            </button>
        </div>
    </div>
</div>


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28">

    @if($officials->isEmpty())
    <div class="text-center py-16">
        <i class="fa-solid fa-users text-slate-300 text-3xl mb-3 block"></i>
        <h3 class="text-slate-400 font-bold text-sm">Belum Ada Aparatur</h3>
    </div>
    @else

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
        @foreach($officials as $official)
        <div class="group flex flex-col items-center bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300">
            <div class="relative mb-6">
                <div class="w-28 h-28 md:w-36 md:h-36 rounded-full overflow-hidden ring-4 ring-white shadow-xl shadow-slate-300/40 group-hover:ring-emerald-200 transition-all duration-300">
                    <img src="{{ $official->photo ? asset('storage/' . $official->photo) : asset('img/meta.png') }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $official->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('img/meta.png') }}'">
                </div>
                <div class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow-md"></div>
            </div>
            <div class="text-center flex-1 flex flex-col items-center">
                <h3 class="text-base md:text-lg font-heading font-bold text-slate-900 mb-3 leading-tight">{{ $official->name }}</h3>
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide">
                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                    {{ $official->position }}
                </span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12 text-center">

        <div x-show="isOpen" x-cloak
             @keydown.escape.window="close()"
             @click.self="close()"
             class="sotk-modal-backdrop"
             role="dialog" aria-modal="true" aria-labelledby="sotk-modal-title">
            <div x-ref="modal" class="sotk-modal" @click.stop>

                {{-- Header --}}

                <div class="sotk-modal-header">
                    <h2 id="sotk-modal-title" class="flex items-center gap-2 text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-sitemap text-emerald-500"></i> Struktur Organisasi
                    </h2>
                    <div class="sotk-modal-actions">
                        <span class="text-xs font-bold text-slate-400 tabular-nums mr-1" x-text="Math.round(scale * 100) + '%'"></span>
                        <button type="button" class="sotk-modal-btn" @click="zoomOut()" title="Perkecil"><i class="fa-solid fa-minus"></i></button>
                        <button type="button" class="sotk-modal-btn" @click="zoomIn()" title="Perbesar"><i class="fa-solid fa-plus"></i></button>
                        <button type="button" class="sotk-modal-btn" @click="resetZoom()" title="Reset ukuran"><i class="fa-solid fa-arrows-to-circle"></i></button>
                        <button type="button" class="sotk-modal-btn" @click="toggleFullscreen()" title="Layar penuh">
                            <i :class="isFullscreen ? 'fa-solid fa-compress' : 'fa-solid fa-expand'"></i>
                        </button>
                        <button type="button" class="sotk-modal-btn text-rose-600 hover:text-rose-700 hover:bg-rose-50 hover:border-rose-300" @click="downloadChart()" title="Download PDF Struktur Organisasi">
                            <i class="fa-solid fa-file-pdf text-sm"></i>
                        </button>
                        <button type="button" class="sotk-modal-btn btn-close" @click="close()" title="Tutup">
                            <i class="fa-solid fa-xmark"></i>
                        </button>



                    </div>
                </div>

                {{-- Body --}}
                <div class="sotk-modal-body">
                    <p class="sotk-hint">
                        <i class="fa-solid fa-hand-pointer"></i>
                        Scroll untuk zoom &bull; Tombol +/&minus; untuk mengatur ukuran
                    </p>
                    <div class="sotk-zoom-wrapper" @wheel.prevent="onWheel($event)">
                        <div class="sotk-zoom-content" :style="'transform: scale(' + scale + ')'">
                            <div class="oc-tree">
                                <ul id="oc-container"></ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    @endif

    <div class="mt-24 md:mt-32 bg-emerald-600 rounded-[48px] md:rounded-[60px] p-12 md:p-24 text-white text-center relative overflow-hidden shadow-2xl shadow-emerald-200/50">
        <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-800/30 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mx-auto mb-8"><i class="fa-solid fa-handshake"></i></div>
            <h2 class="text-3xl md:text-5xl font-heading font-extrabold mb-6 italic">"Profesional, Akuntabel, dan Transparan"</h2>
            <p class="text-emerald-100 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">Kami berkomitmen memberikan pelayanan terbaik bagi seluruh warga desa tanpa pengecualian, didukung oleh data statistik yang akurat untuk setiap keputusan kebijakan.</p>
            <div class="mt-10 flex flex-wrap gap-4 justify-center">
                <a href="/layanan" class="inline-flex items-center gap-2 bg-white text-emerald-700 px-8 py-4 rounded-2xl font-bold text-sm hover:bg-emerald-50 transition shadow-xl shadow-emerald-800/20"><i class="fa-solid fa-clipboard-list"></i> Lihat Layanan</a>
                <a href="/kontak" class="inline-flex items-center gap-2 bg-emerald-700/60 border border-white/30 text-white px-8 py-4 rounded-2xl font-bold text-sm hover:bg-emerald-50 transition"><i class="fa-solid fa-phone"></i> Hubungi Kami</a>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>

document.addEventListener('alpine:init', function () {

    Alpine.data('sotkModal', function () {
        return {
            isOpen: false,
            isFullscreen: false,
            scale: 1,
            villageName: '{{ $site_settings["village_name"] ?? "" }}',

            open: function () {
                this.isOpen = true;
                document.body.classList.add('sotk-modal-open');
                var self = this;
                this.$nextTick(function () {
                    if (!window._ocRendered) {
                        window.renderOcTree();
                        window._ocRendered = true;
                    }
                    self.$nextTick(function () {
                        self.recalcFitScale();
                    });
                });
            },
            close: function () {
                this.isOpen = false;
                document.body.classList.remove('sotk-modal-open');
                if (this.isFullscreen) this.exitFullscreen();
            },
            zoomIn: function () { this.scale = Math.min(+(this.scale + 0.15).toFixed(2), 3); },
            zoomOut: function () { this.scale = Math.max(+(this.scale - 0.15).toFixed(2), 0.2); },
            resetZoom: function () { this.scale = window._ocFitScale || 1; },
            onWheel: function (e) {
                var delta = e.deltaY > 0 ? -0.1 : 0.1;
                this.scale = Math.min(Math.max(+(this.scale + delta).toFixed(2), 0.2), 3);
            },
            recalcFitScale: function () {
                var wrapper = document.querySelector('.sotk-zoom-wrapper');
                var content = document.querySelector('.sotk-zoom-content');
                if (!wrapper || !content) return;
                var wW = wrapper.clientWidth - 32;
                var wH = wrapper.clientHeight - 40;
                var cW = content.scrollWidth;
                var cH = content.scrollHeight;
                if (cW <= 0 || cH <= 0) return;
                var fit = Math.min(wW / cW, wH / cH, 1);
                window._ocFitScale = Math.max(+(fit.toFixed(2)), 0.2);
                this.scale = window._ocFitScale;
            },
            downloadChart: function () {
                var content = document.querySelector('.sotk-zoom-content');
                if (!content) return;

                // Buka tab kosong baru secara sinkron terlebih dahulu untuk menghindari blokir popup browser
                var pdfWindow = window.open('', '_blank');
                if (pdfWindow) {
                    pdfWindow.document.write('<title>Generating PDF...</title><body style="margin:0;display:flex;align-items:center;justify-content:center;height:100vh;background:#f8fafc;font-family:sans-serif;color:#64748b;"><div style="text-align:center;"><div style="border: 4px solid #e2e8f0; border-top: 4px solid #f43f5e; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div><p style="font-size:14px;font-weight:600;">Membuat dokumen PDF...</p></div><style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style></body>');
                }

                // Cari dan lepas semua <link rel="stylesheet"> dan <style> selain style kustom SOTK kita
                // Ini mencegah parser CSS html2canvas mengalami crash akibat fungsi warna "oklch" pada Tailwind v4
                var detachedSheets = [];
                var head = document.head;
                var sheets = Array.from(head.querySelectorAll('link[rel="stylesheet"], style'));
                
                sheets.forEach(function (sheet) {
                    if (sheet.id !== 'sotk-custom-css') {
                        detachedSheets.push({
                            element: sheet,
                            nextSibling: sheet.nextSibling
                        });
                        sheet.remove(); // Lepas sementara dari DOM
                    }
                });

                // Buat kontainer off-screen sementara agar proses rendering skala 1 terjadi di latar belakang (tanpa visual zoom kedip)
                var offscreenContainer = document.createElement('div');
                offscreenContainer.style.position = 'absolute';
                offscreenContainer.style.left = '-9999px';
                offscreenContainer.style.top = '-9999px';
                offscreenContainer.style.width = content.scrollWidth + 'px';
                offscreenContainer.style.height = content.scrollHeight + 'px';
                offscreenContainer.style.background = '#ffffff';
                document.body.appendChild(offscreenContainer);

                // Kloning elemen konten bagan SOTK
                var clone = content.cloneNode(true);
                clone.style.transition = 'none';
                clone.style.transform = 'scale(1)'; // Paksa skala klon ke 1 untuk capture tajam
                clone.style.transformOrigin = 'top center';
                offscreenContainer.appendChild(clone);

                html2canvas(clone, {
                    backgroundColor: '#ffffff',
                    scale: 3, // 3x density untuk resolusi ultra-tinggi yang sangat tajam
                    useCORS: false,
                    logging: false
                }).then(function (canvas) {
                    // Kembalikan semua stylesheet segera setelah clone DOM html2canvas selesai
                    detachedSheets.forEach(function (item) {
                        head.insertBefore(item.element, item.nextSibling);
                    });

                    // Hapus kontainer off-screen dari DOM
                    offscreenContainer.remove();

                    var imgData = canvas.toDataURL('image/jpeg', 0.95); // Kualitas 95% untuk hasil yang bersih dan minim kompresi
                    
                    // Ukuran asli dalam piksel
                    var imgWidth = canvas.width;
                    var imgHeight = canvas.height;
                    
                    // Margin padding sekeliling PDF (dalam piksel)
                    var padding = 40;
                    
                    // Hitung dimensi 1x (bagi 3 karena scale:3 di html2canvas) + tambah padding kiri-kanan
                    var pdfWidth = (imgWidth / 3) + (padding * 2);
                    var pdfHeight = (imgHeight / 3) + 160 + padding; // Tambah tinggi 160px untuk judul + padding bawah

                    var { jsPDF } = window.jspdf;
                    // Buat PDF lanskap dengan ukuran custom yang sudah ditinggikan
                    var doc = new jsPDF('l', 'px', [pdfWidth, pdfHeight]);
                    
                    // 1. Tulis Judul Baris Pertama (Struktur Organisasi)
                    doc.setFont('Helvetica', 'bold');
                    doc.setFontSize(48); // Ubah ke 48pt agar seimbang
                    doc.setTextColor(15, 23, 42); // slate-900
                    doc.text('Struktur Organisasi', pdfWidth / 2, 60, { align: 'center' });

                    // 2. Tulis Judul Baris Kedua (Pemerintah Desa ...)
                    doc.setFont('Helvetica', 'normal');
                    doc.setFontSize(32); // Ubah ke 32pt
                    doc.setTextColor(71, 85, 105); // slate-600
                    doc.text('Pemerintah Desa ' + this.villageName, pdfWidth / 2, 105, { align: 'center' });

                    // 3. Masukkan gambar bagan dengan offset padding (x: padding, y: 160)
                    doc.addImage(imgData, 'JPEG', padding, 160, imgWidth / 3, imgHeight / 3, undefined, 'FAST');
                    
                    // Generate Blob URL untuk PDF
                    var blobUrl = doc.output('bloburl');
                    
                    // Buka Blob URL di tab yang sudah dibuka sebelumnya
                    if (pdfWindow) {
                        pdfWindow.location.href = blobUrl;
                    } else {
                        window.open(blobUrl, '_blank');
                    }
                }.bind(this)).catch(function (err) {
                    // Kembalikan semua stylesheet jika terjadi error
                    detachedSheets.forEach(function (item) {
                        head.insertBefore(item.element, item.nextSibling);
                    });
                    
                    if (offscreenContainer.parentNode) {
                        offscreenContainer.remove();
                    }

                    if (pdfWindow) {
                        pdfWindow.close();
                    }

                    console.error('Gagal membuat PDF:', err);
                    alert('Gagal mendownload PDF bagan SOTK: ' + (err.message || err));
                }.bind(this));
            },





            toggleFullscreen: function () {

                var el = this.$refs.modal;
                if (this.isFullscreen) {
                    this.exitFullscreen();
                } else {
                    if (el.requestFullscreen) el.requestFullscreen();
                    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                }
            },
            exitFullscreen: function () {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            },
            init: function () {
                var self = this;
                var onFsChange = function () {
                    self.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
                    self.$nextTick(function () {
                        // Beri jeda kecil agar browser menyelesaikan animasi/transisi resize ke fullscreen
                        setTimeout(function () {
                            self.recalcFitScale();
                        }, 150);
                    });
                };
                document.addEventListener('fullscreenchange', onFsChange);
                document.addEventListener('webkitfullscreenchange', onFsChange);
            }

        };
    });
});
</script>

<script>
(function () {
    var treeData    = {!! json_encode($tree) !!};
    var storageBase = '/storage';
    var defaultPhoto = '/img/meta.png';


    function buildNode(node) {
        var li = document.createElement('li');
        var hasChildren = Array.isArray(node.children) && node.children.length > 0;
        li.className = 'oc-item' + (hasChildren ? ' has-children' : '');

        var lv = parseInt(node.level) || 5;
        var lvClass = lv >= 1 && lv <= 4 ? 'oc-lv' + lv : 'oc-lv5';
        var photo = node.photo ? storageBase + '/' + node.photo : defaultPhoto;

        var card = document.createElement('div');
        card.className = 'oc-card ' + lvClass;

        var imgWrap = document.createElement('div');
        imgWrap.className = 'oc-photo';
        imgWrap.style.backgroundImage = "url('" + photo + "')";
        
        // Penanganan error jika gambar gagal dimuat (fallback ke defaultPhoto)
        var testImg = new Image();
        testImg.src = photo;
        testImg.onerror = function () {
            imgWrap.style.backgroundImage = "url('" + defaultPhoto + "')";
        };



        var nameEl = document.createElement('div');
        nameEl.className = 'oc-name';
        nameEl.title = node.name;
        nameEl.textContent = node.name;

        var posEl = document.createElement('div');
        posEl.className = 'oc-pos';
        posEl.title = node.position || '';
        posEl.textContent = node.position || '';

        card.appendChild(imgWrap);
        card.appendChild(nameEl);
        card.appendChild(posEl);
        li.appendChild(card);

        if (hasChildren) {
            var ul = document.createElement('ul');
            ul.className = 'oc-children';
            node.children.forEach(function (child) {
                ul.appendChild(buildNode(child));
            });
            li.appendChild(ul);
        }

        return li;
    }

    window._ocRendered = false;
    window.renderOcTree = function () {
        var container = document.getElementById('oc-container');
        if (!container || !treeData || treeData.length === 0) return;
        container.innerHTML = '';
        container.appendChild(buildNode(treeData[0]));
    };
}());
</script>
@endpush

