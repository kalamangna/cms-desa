<div class="flex items-center gap-2.5" style="height: 2.5rem; display: flex; align-items: center;">
    <img src="{{ asset('img/sinjai.png') }}" alt="Logo" style="height: 2rem !important; width: auto !important; max-height: 2rem !important; object-fit: contain;">
    <div class="flex flex-col group-[.collapsed]:hidden group-data-[collapsed=true]:hidden group-data-[collapsible=true]:hidden lg:group-data-[collapsed=true]:hidden">
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 leading-none">
            Pemerintah Desa
        </span>
        <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 leading-none">
            {{ \App\Models\Setting::where('key', 'village_name')->value('value') ?? '' }}
        </span>
    </div>
</div>
