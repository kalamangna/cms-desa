@extends('layouts.app')

@section('title', 'Potensi | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Katalog potensi unggulan, komoditas, kebudayaan, dan pariwisata pada Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.webp'))

@push('head')
@if(!$potentials->isEmpty())
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@graph": [
        @foreach($potentials as $pot)
        {
            "@@type": "TouristAttraction",
            "@@id": "{{ url('/potensi') }}#potensi-{{ $pot->id }}",
            "name": {!! json_encode($pot->title) !!},
            "description": {!! json_encode(strip_tags($pot->description)) !!},
            "image": "{{ $pot->image ? asset('storage/' . $pot->image) : asset('img/meta.webp') }}",
            "location": {
                "@@type": "Place",
                "name": "Desa {{ $site_settings['village_name'] ?? '' }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
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
        <nav class="flex mb-8 text-xs font-black uppercase tracking-widest text-primary-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-primary-400 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all duration-200 flex items-center gap-1.5 rounded-md px-1">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-primary-500/40"></i>
                    <span class="text-white">Potensi Desa</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Potensi <span class="text-primary-500 italic">Desa</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 font-medium">
                Sektor unggulan, komoditas utama, pariwisata, dan seni budaya Desa {{ $site_settings['village_name'] ?? '' }}.
            </p>
        </div>
    </div>
</div>

{{-- ===================== GRID SECTION (With Alpine.js Filters) ===================== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24"
     x-data="{ 
        activeCategory: 'Semua',
        selectedPotential: null,
        modalOpen: false,
        categoriesWithData: {{ json_encode($potentials->pluck('category')->unique()->values()->toArray()) }},
        openModal(potential) {
            this.selectedPotential = potential;
            this.modalOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.modalOpen = false;
            document.body.classList.remove('overflow-hidden');
        }
     }">
     
     <!-- Category Tabs Filter -->
     <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
        <template x-for="cat in ['Semua', 'Pariwisata', 'Pertanian & Perkebunan', 'Peternakan', 'Industri Kreatif', 'UMKM', 'Seni & Budaya']">
            <button type="button"
                    @click="activeCategory = cat"
                    :class="activeCategory === cat ? 'bg-primary-600 text-white shadow-xl shadow-primary-600/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all duration-300 cursor-pointer active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    x-text="cat">
            </button>
        </template>
     </div>

     <!-- Potentials Grid -->
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($potentials as $pot)
            <div x-show="activeCategory === 'Semua' || activeCategory === '{{ $pot->category }}'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-3xl border border-slate-200 shadow-md shadow-slate-200/50 overflow-hidden flex flex-col h-full transition-all duration-300">
                 
                 <!-- Image Header -->
                 <div class="relative aspect-video w-full overflow-hidden bg-slate-100 flex-shrink-0">
                    <img src="{{ $pot->image ? asset('storage/' . $pot->image) : asset('img/meta.webp') }}"
                         class="w-full h-full object-cover"
                         alt="{{ $pot->title }}"
                         onerror="this.onerror=null;this.src='{{ asset('img/meta.webp') }}'">
                    
                    <!-- Floating category badge -->
                    <span class="absolute top-4 left-4 inline-flex items-center text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-xl border backdrop-blur-md shadow-sm
                        @if($pot->category === 'Pariwisata') bg-blue-50/90 text-blue-700 border-blue-100
                        @elseif($pot->category === 'Pertanian & Perkebunan') bg-emerald-50/90 text-emerald-700 border-emerald-100
                        @elseif($pot->category === 'Peternakan') bg-amber-50/90 text-amber-700 border-amber-100
                        @elseif($pot->category === 'Industri Kreatif') bg-violet-50/90 text-violet-700 border-violet-100
                        @elseif($pot->category === 'UMKM') bg-orange-50/90 text-orange-700 border-orange-100
                        @else bg-rose-50/90 text-rose-700 border-rose-100 @endif">
                        {{ $pot->category }}
                    </span>
                 </div>

                 <!-- Content Body -->
                 <div class="p-6 flex-1 flex flex-col justify-between">
                    <div class="mb-5">
                        <h3 class="text-lg font-heading font-extrabold text-slate-900 transition-colors duration-200 line-clamp-2 leading-snug">
                            {{ $pot->title }}
                        </h3>
                        <div class="text-slate-500 text-xs font-medium leading-relaxed mt-2.5 line-clamp-3">
                            {!! strip_tags($pot->description) !!}
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button type="button"
                            @click="openModal({
                                title: {{ json_encode($pot->title) }},
                                category: {{ json_encode($pot->category) }},
                                image: {{ json_encode($pot->image ? asset('storage/' . $pot->image) : asset('img/meta.webp')) }},
                                description: {{ json_encode($pot->description) }}
                            })"
                            class="w-full inline-flex items-center justify-center gap-2 bg-slate-50 hover:bg-primary-50 text-slate-600 hover:text-primary-700 py-3.5 rounded-2xl text-xs font-bold border border-slate-200 hover:border-primary-100 transition-all duration-300 cursor-pointer active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <i class="fa-solid fa-circle-info"></i> Selengkapnya
                    </button>
                 </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state
                    icon="fa-solid fa-box-open"
                    title="Belum Ada Potensi Desa"
                    description="Belum ada data potensi yang diinput."
                />
            </div>
        @endforelse

         <!-- Empty category state -->
         <div x-show="activeCategory !== 'Semua' && !categoriesWithData.includes(activeCategory)"
              class="col-span-full"
              x-cloak>
             <x-empty-state
                 icon="fa-solid fa-box-open"
                 title="Tidak Ada di Kategori Ini"
                 description="Belum ada data untuk kategori ini."
             />
         </div>
     </div>

     <!-- ===================== DETAIL MODAL (Dark Lightbox Style) ===================== -->
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
          class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 md:p-10 cursor-pointer select-none"
          role="dialog"
          aria-modal="true">
          
          <!-- Tombol Tutup (Di Luar Modal) -->
          <button
              type="button"
              @click.stop="closeModal()"
              class="fixed top-5 right-5 sm:top-8 sm:right-8 text-white/80 hover:text-white bg-slate-900/80 hover:bg-slate-900 w-12 h-12 rounded-full flex items-center justify-center transition z-50 backdrop-blur-md border border-white/20 shadow-2xl cursor-pointer hover:scale-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-900"
              title="Tutup (Esc)">
              <i class="fa-solid fa-xmark text-xl"></i>
          </button>

          <!-- Modal Box -->
          <div class="relative w-11/12 sm:w-5/6 md:w-3/4 lg:w-3/5 max-w-4xl bg-white rounded-2xl md:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 flex flex-col max-h-[90vh] cursor-default"
               x-show="selectedPotential !== null"
               @click.stop
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 scale-95 translate-y-4"
               x-transition:enter-end="opacity-100 scale-100 translate-y-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100 scale-100 translate-y-0"
               x-transition:leave-end="opacity-0 scale-95 translate-y-4">
               
               <!-- Modal Image -->
               <div class="relative w-full h-48 sm:h-64 md:h-80 bg-slate-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                   <img :src="selectedPotential?.image"
                        :alt="selectedPotential?.title"
                        class="w-full h-full object-cover">
               </div>

               <!-- Modal Content -->
               <div class="p-6 sm:p-8 bg-white border-t border-slate-200 overflow-y-auto custom-scrollbar">
                   <span class="inline-flex items-center text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-xl border mb-4"
                         :class="{
                            'bg-blue-50 text-blue-700 border-blue-100': selectedPotential?.category === 'Pariwisata',
                            'bg-emerald-50 text-emerald-700 border-emerald-100': selectedPotential?.category === 'Pertanian & Perkebunan',
                            'bg-amber-50 text-amber-700 border-amber-100': selectedPotential?.category === 'Peternakan',
                            'bg-violet-50 text-violet-700 border-violet-100': selectedPotential?.category === 'Industri Kreatif',
                            'bg-orange-50 text-orange-700 border-orange-100': selectedPotential?.category === 'UMKM',
                            'bg-rose-50 text-rose-700 border-rose-100': selectedPotential?.category !== 'Pariwisata' && selectedPotential?.category !== 'Pertanian & Perkebunan' && selectedPotential?.category !== 'Peternakan' && selectedPotential?.category !== 'Industri Kreatif' && selectedPotential?.category !== 'UMKM'
                         }"
                         x-text="selectedPotential?.category">
                   </span>
                   <h2 class="text-xl sm:text-2xl md:text-3xl font-heading font-extrabold text-slate-900 mb-4 sm:mb-5 leading-tight"
                       x-text="selectedPotential?.title"></h2>
                   
                   <div class="prose prose-sm sm:prose-base prose-slate max-w-none text-slate-600 leading-relaxed"
                        x-html="selectedPotential?.description">
                   </div>
               </div>
          </div>
     </div>
</div>

@endsection
