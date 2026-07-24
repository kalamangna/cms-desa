<x-layouts.app title="Dokumentasi Sistem & PDF - Portal Resmi Desa">
    <div class="bg-slate-50 min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-sky-100 text-sky-800 rounded-full mb-3">
                    📑 Dokumentasi Resmi & PDF
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Pusat Dokumentasi & Spesifikasi Sistem
                </h1>
                <p class="mt-4 text-base text-slate-600">
                    Akses naskah laporan teknis, spesifikasi arsitektur, panduan instalasi, dan petunjuk pengguna secara online atau unduh langsung dalam format **PDF Resmi**.
                </p>
            </div>

            <!-- Documents Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($documents as $key => $doc)
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-3xl p-2.5 bg-slate-100/80 rounded-xl group-hover:scale-110 transition-transform">
                                    {{ $doc['icon'] }}
                                </span>
                                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                                    {{ strtoupper($key) }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-sky-600 transition-colors">
                                {{ $doc['title'] }}
                            </h3>
                            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                                {{ $doc['desc'] }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('documentation.show', $key) }}" 
                               class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Baca Web
                            </a>
                            <a href="{{ route('documentation.pdf', $key) }}" 
                               class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm hover:shadow transition-all">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh PDF
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
