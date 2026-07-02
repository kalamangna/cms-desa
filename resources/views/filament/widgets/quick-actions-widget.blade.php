<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Card 1: Import Data Keluarga --}}
        <a href="/admin/families" class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-lg hover:border-emerald-500/30 transition-all duration-300 hover:-translate-y-1 group flex flex-col justify-between min-h-[160px]">
            {{-- Top Row: Icon + Arrow --}}
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                    <svg width="24" height="24" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="text-slate-300 group-hover:text-emerald-500 transition-colors duration-300">
                    <svg width="20" height="20" style="width: 20px; height: 20px;" class="transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Bottom Row: Title + Desc --}}
            <div class="mt-6">
                <h4 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-600 transition-colors duration-300">Import Data Keluarga</h4>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Unggah hasil kuesioner keluarga (.csv)</p>
            </div>
        </a>

        {{-- Card 2: Import Data Penduduk --}}
        <a href="/admin/citizens" class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-lg hover:border-sky-500/30 transition-all duration-300 hover:-translate-y-1 group flex flex-col justify-between min-h-[160px]">
            {{-- Top Row: Icon + Arrow --}}
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center group-hover:bg-sky-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                    <svg width="24" height="24" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div class="text-slate-300 group-hover:text-sky-500 transition-colors duration-300">
                    <svg width="20" height="20" style="width: 20px; height: 20px;" class="transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Bottom Row: Title + Desc --}}
            <div class="mt-6">
                <h4 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-sky-600 transition-colors duration-300">Import Data Penduduk</h4>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Unggah kuesioner warga/individu (.csv)</p>
            </div>
        </a>

        {{-- Card 3: Pengaturan Aplikasi --}}
        <a href="/admin/manage-settings" class="relative overflow-hidden bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-lg hover:border-violet-500/30 transition-all duration-300 hover:-translate-y-1 group flex flex-col justify-between min-h-[160px]">
            {{-- Top Row: Icon + Arrow --}}
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-950/50 text-violet-600 dark:text-violet-400 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-all duration-300 flex-shrink-0">
                    <svg width="24" height="24" style="width: 24px; height: 24px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.936 6.936 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128c.332-.183.582-.495.644-.869l.214-1.28z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="text-slate-300 group-hover:text-violet-500 transition-colors duration-300">
                    <svg width="20" height="20" style="width: 20px; height: 20px;" class="transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Bottom Row: Title + Desc --}}
            <div class="mt-6">
                <h4 class="font-bold text-slate-800 dark:text-slate-100 group-hover:text-violet-600 transition-colors duration-300">Pengaturan Aplikasi</h4>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Identitas desa, logo, kontak, maps, dll.</p>
            </div>
        </a>

    </div>
</x-filament-widgets::widget>
