<x-filament-widgets::widget>
    {{-- Inline CSS for absolute styling reliability in Filament --}}
    <style>
        .qa-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }
        @media (min-width: 768px) {
            .qa-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .qa-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
            padding: 24px;
            border-radius: 20px;
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .qa-card {
            background-color: #0f172a;
            border-color: #1e293b;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
        }
        
        /* Emerald theme */
        .qa-card-emerald:hover {
            transform: translateY(-4px);
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.05), 0 4px 6px -4px rgba(16, 185, 129, 0.05);
        }
        .qa-icon-emerald {
            background-color: #ecfdf5;
            color: #059669;
        }
        .dark .qa-icon-emerald {
            background-color: rgba(6, 78, 59, 0.3);
            color: #34d399;
        }
        .qa-card-emerald:hover .qa-icon-emerald {
            background-color: #10b981;
            color: #ffffff;
        }
        .qa-card-emerald:hover .qa-title {
            color: #10b981;
        }
        .qa-card-emerald:hover .qa-arrow {
            color: #10b981;
            transform: translateX(4px);
        }

        /* Sky theme */
        .qa-card-sky:hover {
            transform: translateY(-4px);
            border-color: rgba(14, 165, 233, 0.3);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.05), 0 4px 6px -4px rgba(14, 165, 233, 0.05);
        }
        .qa-icon-sky {
            background-color: #f0f9ff;
            color: #0284c7;
        }
        .dark .qa-icon-sky {
            background-color: rgba(8, 47, 73, 0.3);
            color: #38bdf8;
        }
        .qa-card-sky:hover .qa-icon-sky {
            background-color: #0ea5e9;
            color: #ffffff;
        }
        .qa-card-sky:hover .qa-title {
            color: #0ea5e9;
        }
        .qa-card-sky:hover .qa-arrow {
            color: #0ea5e9;
            transform: translateX(4px);
        }

        /* Violet theme */
        .qa-card-violet:hover {
            transform: translateY(-4px);
            border-color: rgba(139, 92, 246, 0.3);
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.05), 0 4px 6px -4px rgba(139, 92, 246, 0.05);
        }
        .qa-icon-violet {
            background-color: #f5f3ff;
            color: #7c3aed;
        }
        .dark .qa-icon-violet {
            background-color: rgba(46, 16, 101, 0.3);
            color: #a78bfa;
        }
        .qa-card-violet:hover .qa-icon-violet {
            background-color: #8b5cf6;
            color: #ffffff;
        }
        .qa-card-violet:hover .qa-title {
            color: #8b5cf6;
        }
        .qa-card-violet:hover .qa-arrow {
            color: #8b5cf6;
            transform: translateX(4px);
        }

        .qa-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .qa-arrow {
            color: #cbd5e1;
            transition: all 0.3s ease;
        }
        .dark .qa-arrow {
            color: #475569;
        }
        .qa-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            transition: color 0.3s ease;
        }
        .dark .qa-title {
            color: #f1f5f9;
        }
        .qa-desc {
            font-size: 12px;
            color: #64748b;
            margin: 4px 0 0 0;
        }
        .dark .qa-desc {
            color: #94a3b8;
        }
        .qa-row-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
    </style>

    <div class="qa-grid">
        
        {{-- Card 1: Import Data Keluarga --}}
        <a href="/admin/families" class="qa-card qa-card-emerald">
            <div class="qa-row-top">
                <div class="qa-icon-box qa-icon-emerald">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="qa-arrow">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </div>
            </div>
            <div style="margin-top: 16px;">
                <h4 class="qa-title">Import Data Keluarga</h4>
                <p class="qa-desc">Unggah hasil kuesioner keluarga (.csv)</p>
            </div>
        </a>

        {{-- Card 2: Import Data Penduduk --}}
        <a href="/admin/citizens" class="qa-card qa-card-sky">
            <div class="qa-row-top">
                <div class="qa-icon-box qa-icon-sky">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div class="qa-arrow">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </div>
            </div>
            <div style="margin-top: 16px;">
                <h4 class="qa-title">Import Data Penduduk</h4>
                <p class="qa-desc">Unggah kuesioner warga/individu (.csv)</p>
            </div>
        </a>

        {{-- Card 3: Pengaturan Aplikasi --}}
        <a href="/admin/manage-settings" class="qa-card qa-card-violet">
            <div class="qa-row-top">
                <div class="qa-icon-box qa-icon-violet">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.936 6.936 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128c.332-.183.582-.495.644-.869l.214-1.28z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="qa-arrow">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </div>
            </div>
            <div style="margin-top: 16px;">
                <h4 class="qa-title">Pengaturan Aplikasi</h4>
                <p class="qa-desc">Identitas desa, logo, kontak, maps, dll.</p>
            </div>
        </a>

    </div>
</x-filament-widgets::widget>
