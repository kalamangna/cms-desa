@extends('layouts.app')

@section('title', 'Potensi | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Katalog potensi unggulan, komoditas, kebudayaan, dan pariwisata pada Pemerintah Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

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
            "image": "{{ $pot->image ? asset('storage/' . $pot->image) : asset('img/meta.png') }}",
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
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-80 h-80 bg-emerald-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-8 text-xs font-black uppercase tracking-[0.2em] text-emerald-500/60" aria-label="Breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li>
                    <a href="/" class="hover:text-emerald-400 transition-colors duration-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-house text-[10px]"></i> Beranda
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fa-solid fa-chevron-right text-[9px] text-emerald-500/40"></i>
                    <span class="text-white">Potensi Desa</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Potensi <span class="text-emerald-500 italic">Desa</span>
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
        categoriesWithData: {{ json_encode($potentials->pluck('category')->unique()->values()->toArray()) }},
        openModal(potential) {
            this.selectedPotential = potential;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.selectedPotential = null;
            document.body.classList.remove('overflow-hidden');
        }
     }">
     
     <!-- Category Tabs Filter -->
     <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
        <template x-for="cat in ['Semua', 'Pariwisata', 'Pertanian & Perkebunan', 'Peternakan', 'Industri Kreatif', 'Seni & Budaya']">
            <button type="button"
                    @click="activeCategory = cat"
                    :class="activeCategory === cat ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-600/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'"
                    class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all duration-300 cursor-pointer"
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
                 class="group bg-white rounded-[32px] border border-slate-100 shadow-md shadow-slate-200/50 hover:shadow-xl hover:shadow-emerald-900/5 hover:border-emerald-100 overflow-hidden flex flex-col h-full transition-all duration-300">
                 
                 <!-- Image Header -->
                 <div class="relative aspect-video w-full overflow-hidden bg-slate-100 flex-shrink-0">
                    <img src="{{ $pot->image ? asset('storage/' . $pot->image) : asset('img/meta.png') }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         alt="{{ $pot->title }}"
                         onerror="this.onerror=null;this.src='{{ asset('img/meta.png') }}'">
                    
                    <!-- Floating category badge -->
                    <span class="absolute top-4 left-4 inline-flex items-center text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-xl border backdrop-blur-md shadow-sm
                        @if($pot->category === 'Pariwisata') bg-blue-50/90 text-blue-700 border-blue-100
                        @elseif($pot->category === 'Pertanian & Perkebunan') bg-emerald-50/90 text-emerald-700 border-emerald-100
                        @elseif($pot->category === 'Peternakan') bg-amber-50/90 text-amber-700 border-amber-100
                        @elseif($pot->category === 'Industri Kreatif') bg-violet-50/90 text-violet-700 border-violet-100
                        @else bg-rose-50/90 text-rose-700 border-rose-100 @endif">
                        {{ $pot->category }}
                    </span>
                 </div>

                 <!-- Content Body -->
                 <div class="p-6 flex-1 flex flex-col justify-between">
                    <div class="mb-5">
                        <h3 class="text-lg font-heading font-extrabold text-slate-900 group-hover:text-emerald-600 transition-colors duration-200 line-clamp-2 leading-snug">
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
                                image: {{ json_encode($pot->image ? asset('storage/' . $pot->image) : asset('img/meta.png')) }},
                                description: {{ json_encode($pot->description) }}
                            })"
                            class="w-full inline-flex items-center justify-center gap-2 bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 py-3.5 rounded-2xl text-xs font-bold border border-slate-100 hover:border-emerald-100 transition-all duration-300 cursor-pointer">
                        <i class="fa-solid fa-circle-info"></i> Selengkapnya
                    </button>
                 </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <i class="fa-solid fa-box-open text-slate-300 text-3xl mb-3 block"></i>
                <h3 class="text-slate-400 font-bold text-sm">Belum Ada Potensi Desa</h3>
            </div>
        @endforelse

         <!-- Empty category state -->
         <div x-show="activeCategory !== 'Semua' && !categoriesWithData.includes(activeCategory)"
              class="col-span-full text-center py-16"
              x-cloak>
             <i class="fa-solid fa-box-open text-slate-300 text-3xl mb-3 block"></i>
             <h3 class="text-slate-400 font-bold text-sm">Belum Ada Potensi Desa</h3>
         </div>
     </div>

     <!-- ===================== DETAIL MODAL (Alpine.js Overlay) ===================== -->
     <div x-show="selectedPotential !== null"
          x-cloak
          class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
          role="dialog"
          aria-modal="true">
          
          <!-- Backdrop blur -->
          <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md"
               x-show="selectedPotential !== null"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0"
               x-transition:enter-end="opacity-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100"
               x-transition:leave-end="opacity-0"
               @click="closeModal()"></div>

          <!-- Modal Box -->
          <div class="relative bg-white rounded-[40px] shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto z-10 flex flex-col border border-slate-100"
               x-show="selectedPotential !== null"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 scale-95 translate-y-4"
               x-transition:enter-end="opacity-100 scale-100 translate-y-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100 scale-100 translate-y-0"
               x-transition:leave-end="opacity-0 scale-95 translate-y-4">
               
               <!-- Close Button -->
               <button type="button"
                       @click="closeModal()"
                       class="absolute top-5 right-5 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition-all duration-200 z-20 cursor-pointer">
                   <i class="fa-solid fa-xmark"></i>
               </button>

               <!-- Modal Image Header -->
               <div class="relative aspect-video w-full bg-slate-100 flex-shrink-0">
                   <img :src="selectedPotential?.image"
                        :alt="selectedPotential?.title"
                        class="w-full h-full object-cover">
               </div>

               <!-- Modal Content -->
               <div class="p-8">
                   <span class="inline-flex items-center text-[10px] font-black uppercase tracking-wider px-3 py-1.5 rounded-xl border mb-4"
                         :class="{
                            'bg-blue-50 text-blue-700 border-blue-100': selectedPotential?.category === 'Pariwisata',
                            'bg-emerald-50 text-emerald-700 border-emerald-100': selectedPotential?.category === 'Pertanian & Perkebunan',
                            'bg-amber-50 text-amber-700 border-amber-100': selectedPotential?.category === 'Peternakan',
                            'bg-violet-50 text-violet-700 border-violet-100': selectedPotential?.category === 'Industri Kreatif',
                            'bg-rose-50 text-rose-700 border-rose-100': selectedPotential?.category !== 'Pariwisata' && selectedPotential?.category !== 'Pertanian & Perkebunan' && selectedPotential?.category !== 'Peternakan' && selectedPotential?.category !== 'Industri Kreatif'
                         }"
                         x-text="selectedPotential?.category">
                   </span>
                   <h2 class="text-2xl md:text-3xl font-heading font-extrabold text-slate-900 mb-5 leading-tight"
                       x-text="selectedPotential?.title"></h2>
                   
                   <div class="prose prose-sm prose-emerald max-w-none text-slate-600 leading-relaxed"
                        x-html="selectedPotential?.description">
                   </div>
               </div>
          </div>
     </div>
</div>

@endsection
