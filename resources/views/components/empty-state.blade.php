{{--
    ┌─────────────────────────────────────────────────────────────────┐
    │  Empty State Component                                          │
    │  resources/views/components/empty-state.blade.php              │
    │                                                                 │
    │  Props:                                                         │
    │  - icon        : FontAwesome class (default: fa-solid fa-inbox) │
    │  - title       : Judul empty state                              │
    │  - description : Deskripsi opsional                             │
    │  - actionLabel : Teks tombol CTA (opsional)                     │
    │  - actionHref  : URL tombol CTA (opsional)                      │
    │  - compact     : Gunakan padding lebih kecil (bool, default: false) │
    ├─────────────────────────────────────────────────────────────────┤
    │  Contoh penggunaan:                                             │
    │                                                                 │
    │  <x-empty-state                                                 │
    │      icon="fa-solid fa-bullhorn"                                │
    │      title="Belum Ada Pengumuman Resmi"                         │
    │      description="Pengumuman resmi akan tampil di sini."        │
    │  />                                                             │
    └─────────────────────────────────────────────────────────────────┘
--}}
@props([
    'icon'        => 'fa-solid fa-inbox',
    'title'       => 'Data Belum Tersedia',
    'description' => null,
    'actionLabel' => null,
    'actionHref'  => null,
    'compact'     => false,
])

<div class="text-center {{ $compact ? 'py-10 px-4' : 'py-16 px-6' }}">
    {{-- Icon Container --}}
    <div class="w-20 h-20 rounded-3xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-5">
        <i class="{{ $icon }} text-slate-400 dark:text-slate-500 text-3xl"></i>
    </div>

    {{-- Title --}}
    <h3 class="text-slate-700 dark:text-slate-200 font-heading font-bold text-base mb-2">{{ $title }}</h3>

    {{-- Description (optional) --}}
    @if($description)
        <p class="text-slate-400 dark:text-slate-500 text-sm max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @endif

    {{-- CTA Button (optional) --}}
    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}"
           class="mt-6 inline-flex items-center gap-2 bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-primary-700 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all duration-200 shadow-sm shadow-primary-600/20">
            {{ $actionLabel }}
        </a>
    @endif
</div>
