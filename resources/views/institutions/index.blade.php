@extends('layouts.app')

@section('title', 'Lembaga | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Profil dan direktori lembaga kemasyarakatan di wilayah Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . ' yang bermitra dalam pembangunan daerah.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@graph": [
        {
            "@type": "GovernmentOrganization",
            "@id": "{{ url('/lembaga') }}#organization",
            "name": "Pemerintah Desa {{ $site_settings['village_name'] ?? '' }}",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('img/sinjai.webp') }}",
            "subOrganization": [
                @foreach($institutions as $idx => $institution)
                {
                    "@type": "Organization",
                    "name": "{{ $institution->name }}",
                    "logo": "{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('img/meta.webp') }}"
                }{{ $idx < count($institutions) - 1 ? ',' : '' }}
                @endforeach
            ]
        }
    ]
}
</script>
@endpush

@section('content')

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
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Lembaga Desa</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Lembaga <span class="text-primary-500 italic">Desa</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2">
                Lembaga kemasyarakatan Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== INSTITUTIONS GRID ===================== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-28"
     x-data="{ 
        selectedInstitution: null,
        modalOpen: false,
        openModal(institution) {
            this.selectedInstitution = institution;
            this.modalOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.modalOpen = false;
            document.body.classList.remove('overflow-hidden');
        }
     }">

    @if($institutions->isEmpty())
    <x-empty-state
        icon="fa-solid fa-building-columns"
        title="Data Lembaga Belum Diisi"
        description="Belum ada data lembaga yang diinput."
    />
    @else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        @foreach($institutions as $institution)
        <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left bg-white rounded-3xl p-6 md:p-8 border border-slate-200 shadow-sm overflow-hidden gap-6 h-full">

            {{-- Logo --}}
            <div class="flex-shrink-0">
                <div class="w-28 h-28 md:w-32 md:h-32 rounded-full overflow-hidden ring-4 ring-white shadow-xl shadow-slate-300/40 mx-auto sm:mx-0">
                    <img
                        src="{{ $institution->logo ? asset('storage/' . $institution->logo) : asset('img/meta.webp') }}"
                        class="w-full h-full object-cover"
                        alt="{{ $institution->name }}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 w-full flex flex-col justify-between h-full">
                <div class="mb-5">
                    <h3 class="text-xl md:text-2xl font-heading font-bold text-slate-900 mb-3">{{ $institution->name }}</h3>
                    @if($institution->description)
                    <div class="prose prose-sm prose-slate max-w-none text-slate-500 text-sm leading-relaxed line-clamp-3">
                        {!! strip_tags($institution->description) !!}
                    </div>
                    @endif
                </div>

                {{-- Action Button --}}
                <button type="button"
                        @click="openModal({
                            name: {{ json_encode($institution->name) }},
                            logo: {{ json_encode($institution->logo ? asset('storage/' . $institution->logo) : asset('img/meta.webp')) }},
                            description: {{ json_encode($institution->description) }},
                            management: {{ json_encode($institution->management ?? []) }}
                        })"
                        class="w-full sm:w-auto mt-auto inline-flex items-center justify-center gap-2 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 py-3 px-5 rounded-2xl text-xs font-bold border border-slate-200 hover:border-primary-100 transition-all duration-300 cursor-pointer active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fa-solid fa-sitemap"></i> Lihat Detail
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ===================== DETAIL MODAL ===================== -->
    <div x-show="modalOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="closeModal()"
         @click="closeModal()"
         class="fixed inset-0 z-[9999] bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 md:p-10 cursor-pointer select-none"
         role="dialog"
         aria-modal="true">
         
         <!-- Tombol Tutup -->
         <button
             type="button"
             @click.stop="closeModal()"
             class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900"
             title="Tutup (Esc)">
             <i class="fa-solid fa-xmark text-xl"></i>
         </button>

         <!-- Modal Box -->
         <div class="relative w-[92vw] sm:w-[85vw] md:w-[75vw] lg:w-[60vw] max-w-4xl bg-white rounded-2xl md:rounded-[28px] overflow-hidden shadow-2xl border border-slate-200 flex flex-col max-h-[90vh] cursor-default"
              x-show="selectedInstitution !== null"
              @click.stop
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95 translate-y-4"
              x-transition:enter-end="opacity-100 scale-100 translate-y-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 scale-100 translate-y-0"
              x-transition:leave-end="opacity-0 scale-95 translate-y-4">
              
              <!-- Modal Header (Logo + Title) -->
              <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-center gap-6 sm:gap-8 flex-shrink-0">
                  <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full overflow-hidden ring-4 ring-white shadow-lg flex-shrink-0 bg-white">
                      <img :src="selectedInstitution?.logo"
                           :alt="selectedInstitution?.name"
                           class="w-full h-full object-cover">
                  </div>
                  <div class="text-center sm:text-left flex-1">
                      <h2 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 leading-tight"
                          x-text="selectedInstitution?.name"></h2>
                  </div>
              </div>

              <!-- Modal Content (Description + Management) -->
              <div class="p-6 sm:p-8 bg-white overflow-y-auto custom-scrollbar flex-1">
                  <div class="prose prose-sm sm:prose-base prose-slate max-w-none text-slate-600 leading-relaxed"
                       x-html="selectedInstitution?.description">
                  </div>

                  <template x-if="selectedInstitution?.management && selectedInstitution.management.length > 0">
                      <div class="mt-10 border-t border-slate-200 pt-8">
                          <h4 class="text-sm font-black uppercase tracking-widest text-primary-600 mb-6">
                              Struktur Pengurus
                          </h4>
                          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                              <template x-for="member in selectedInstitution.management" :key="member.name + member.position">
                                  <div class="flex flex-col p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                      <span class="text-[10px] font-black uppercase tracking-wider text-primary-600/80 mb-1" x-text="member.position"></span>
                                      <span class="text-sm font-bold text-slate-800" x-text="member.name"></span>
                                  </div>
                              </template>
                          </div>
                      </div>
                  </template>
              </div>
         </div>
    </div>
    @endif

</div>
@endsection
