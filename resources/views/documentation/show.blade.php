<x-layouts.app :title="$doc['title'] . ' - Dokumentasi Desa'">
    <div class="bg-slate-50 min-h-screen py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8 bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center gap-3">
                    <a href="{{ route('documentation.index') }}" 
                       class="p-2.5 text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                    <div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-sky-100 text-sky-800 rounded">
                            Dokumen Web
                        </span>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
                            {{ $doc['title'] }}
                        </h1>
                    </div>
                </div>
                <div>
                    <a href="{{ route('documentation.pdf', $docKey) }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl shadow-sm hover:shadow transition-all">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Berkas PDF
                    </a>
                </div>
            </div>

            <!-- Content Container -->
            <div class="bg-white p-6 sm:p-12 rounded-3xl border border-slate-200/80 shadow-sm prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-sky-600 prose-img:rounded-2xl">
                {!! $htmlContent !!}
            </div>
        </div>
    </div>
</x-layouts.app>
