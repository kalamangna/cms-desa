@extends('layouts.app')

@section('title', 'Peta Spasial | Desa ' . ($site_settings['village_name'] ?? ''))
@section('meta_description', 'Peta spasial interaktif pembagian wilayah dusun, batas wilayah, serta informasi kependudukan dan statistik per dusun di Desa ' . ($site_settings['village_name'] ?? '') . '.')
@section('meta_image', asset('img/meta.png'))

@section('content')

@php
    $palette = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316', '#14b8a6', '#6366f1'];
    $mappedCount = 0;
    foreach($dusuns as $dusun) {
        if ($dusun->geojson) {
            $color = $palette[$mappedCount % count($palette)];
            $dusun->setAttribute('color', $color);
            $mappedCount++;
        } else {
            $dusun->setAttribute('color', '#cbd5e1');
        }
    }
@endphp

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
                    <span class="text-white">Peta Spasial</span>
                </li>
            </ol>
        </nav>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                Peta <span class="text-emerald-500 italic">Spasial</span>
            </h1>
            <p class="text-slate-300 text-lg mt-2 font-medium">
                Peta interaktif batas wilayah dusun, lokasi fasilitas umum, dan data kependudukan desa.
            </p>
        </div>
    </div>
</div>

{{-- ===================== MAP SECTION ===================== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Map View (Main Area) --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white rounded-[32px] p-2 border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden h-[500px] md:h-[600px] min-h-[450px] z-0">
                <div id="spatialMap" class="w-full h-full rounded-[24px] absolute inset-0 z-0"></div>
                
                {{-- Legend Overlay --}}
                <div class="absolute bottom-6 left-6 bg-white/95 backdrop-blur px-4 py-3 rounded-2xl shadow-xl border border-slate-100/50 z-[1000] hidden sm:block max-w-[220px]">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Legenda Dusun</p>
                    <div class="space-y-1.5">
                        @foreach($dusuns as $dusun)
                            @if($dusun->geojson)
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                                <span class="w-3.5 h-3.5 rounded-md border border-white shadow-sm flex-shrink-0" style="background-color: {{ $dusun->color ?? '#10b981' }}"></span>
                                <span class="truncate">Dusun {{ $dusun->name }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (Dusun list & Information Card) --}}
        <div class="lg:col-span-1 flex flex-col gap-6"
             x-data="spatialMapSidebar">
            
            {{-- Tabbed Selector Card --}}
            <div class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col gap-4">
                <!-- Tab Buttons -->
                <div class="flex border-b border-slate-100 pb-1">
                    <button type="button" 
                            @click="activeTab = 'dusun'"
                            :class="activeTab === 'dusun' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                            class="flex-1 pb-3 text-center border-b-2 font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-map-location-dot"></i>
                        Wilayah
                    </button>
                    <button type="button" 
                            @click="activeTab = 'fasilitas'"
                            :class="activeTab === 'fasilitas' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-400 hover:text-slate-600'"
                            class="flex-1 pb-3 text-center border-b-2 font-bold text-xs transition-all duration-200 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-building-circle-check"></i>
                        Fasilitas Umum
                    </button>
                </div>

                <!-- Tab Content: Dusun -->
                <div x-show="activeTab === 'dusun'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="mb-4">
                        <h3 class="text-sm font-heading font-extrabold text-slate-900 leading-tight">Daftar Wilayah</h3>
                        <p class="text-slate-400 text-[10px] font-semibold mt-1">Pilih wilayah dusun untuk memfokuskan peta.</p>
                    </div>

                    <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                        @forelse($dusuns as $dusun)
                            <button type="button"
                                    onclick="focusDusun({{ $dusun->id }})"
                                    id="btn-dusun-{{ $dusun->id }}"
                                    class="w-full flex items-center justify-between p-3.5 rounded-2xl border text-left transition-all duration-300 font-bold text-sm @if($dusun->geojson) border-slate-100 bg-slate-50/50 hover:bg-slate-50 text-slate-700 hover:border-emerald-200 @else border-slate-100 bg-slate-50/30 text-slate-400 cursor-not-allowed opacity-60 @endif">
                                <span class="flex items-center gap-2.5 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $dusun->color ?? '#cbd5e1' }}"></span>
                                    <span class="truncate">Dusun {{ $dusun->name }}</span>
                                </span>
                                @if($dusun->geojson)
                                <i class="fa-solid fa-location-crosshairs text-slate-400 text-xs transition"></i>
                                @endif
                            </button>
                        @empty
                            <div class="text-center py-6">
                                <i class="fa-solid fa-map-pin text-slate-300 text-3xl mb-2 block"></i>
                                <h3 class="text-slate-400 text-[10px] font-bold">Tidak ada data wilayah.</h3>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab Content: Fasilitas Umum -->
                <div x-show="activeTab === 'fasilitas'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                    <div class="mb-3">
                        <h3 class="text-sm font-heading font-extrabold text-slate-900 leading-tight">Cari Fasilitas</h3>
                        <p class="text-slate-400 text-[10px] font-semibold mt-1">Gunakan pencarian dan filter kategori.</p>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div class="space-y-3 mb-3">
                        <!-- Search Input -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-[10px]"></i>
                            </span>
                            <input type="text" 
                                   x-model="searchQuery" 
                                   placeholder="Cari nama atau jenis..." 
                                   class="w-full pl-8 pr-3 py-2 text-[11px] font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                        </div>

                        <!-- Category Pill Filter -->
                        <div class="flex gap-1 overflow-x-auto pb-1 scrollbar-none">
                            <template x-for="cat in ['Semua', 'Pendidikan', 'Ibadah', 'Kesehatan', 'Pemerintahan', 'Umum']">
                                <button type="button"
                                        @click="selectedCategory = cat; filterMarkers();"
                                        :class="selectedCategory === cat ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200/50' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/80'"
                                        class="px-2.5 py-1 rounded-lg text-[9px] font-bold whitespace-nowrap transition-all duration-200"
                                        x-text="cat === 'Umum' ? 'Umum/Lainnya' : cat">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Facility List -->
                    <div class="space-y-2 max-h-[200px] overflow-y-auto pr-1">
                        <template x-for="facility in filteredFacilities" :key="facility.id">
                            <button type="button"
                                    @click="focusFacility(facility)"
                                    :id="'btn-facility-' + facility.id"
                                    class="w-full flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 hover:border-emerald-200 text-left transition-all duration-300">
                                <!-- Icon based on type -->
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-white"
                                     :class="getFacilityColorClass(facility.type)">
                                    <i :class="getFacilityIconClass(facility.type) + ' text-[9px]'"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-slate-900 font-bold text-xs truncate" x-text="facility.name"></p>
                                     <p class="text-slate-400 text-[9px] font-semibold mt-0.5" x-text="facility.type"></p>
                                    <p class="text-slate-400 text-[9px] truncate mt-1" x-show="facility.address" x-text="facility.address"></p>
                                </div>
                            </button>
                        </template>
                        <div x-show="filteredFacilities.length === 0" 
                             class="text-center py-6"
                             x-cloak>
                            <i class="fa-solid fa-location-dot text-slate-300 text-3xl mb-2 block"></i>
                            <h3 class="text-slate-400 text-[10px] font-bold">Tidak ada fasilitas ditemukan.</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Information Card --}}
            <div id="dusunInfoCard" class="bg-white rounded-[32px] p-6 border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col gap-5 transition-all duration-500 relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-2 h-full bg-slate-300" id="infoCardBorder"></div>
                
                <div class="pl-2">
                    <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-500 rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-wider mb-2" id="infoBadge">
                        <i class="fa-solid fa-map"></i> Klik Area Peta
                    </span>
                    <h4 class="text-xl font-heading font-extrabold text-slate-900 leading-tight" id="infoTitle">Detail Informasi</h4>
                    <p class="text-slate-400 text-xs font-semibold mt-1" id="infoSubtitle">Pilih area Dusun atau marker Fasilitas Umum pada peta untuk melihat detail informasi.</p>
                </div>

                {{-- Stats grid, initially hidden --}}
                <div id="infoStatsGrid" class="hidden grid grid-cols-1 gap-3.5 pl-2">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Kepala Dusun</p>
                        <p class="text-slate-800 font-bold text-sm" id="infoHead">-</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Penduduk</p>
                            <p class="text-slate-950 font-heading font-black text-xl leading-none mt-1" id="infoPopulation">0</p>
                            <p class="text-slate-400 text-[10px] font-bold mt-1">Jiwa</p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Keluarga</p>
                            <p class="text-slate-950 font-heading font-black text-xl leading-none mt-1" id="infoFamilies">0</p>
                            <p class="text-slate-400 text-[10px] font-bold mt-1">KK</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Leaflet JS & CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('spatialMapSidebar', () => ({
            activeTab: 'dusun',
            searchQuery: '',
            selectedCategory: 'Semua',
            facilities: @json($facilities),
            get filteredFacilities() {
                return this.facilities.filter(f => {
                    const matchesSearch = f.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                         (f.address && f.address.toLowerCase().includes(this.searchQuery.toLowerCase()));
                    const matchesCategory = this.selectedCategory === 'Semua' || f.type === this.selectedCategory;
                    return matchesSearch && matchesCategory;
                });
            },
            getFacilityColorClass(type) {
                switch(type) {
                    case 'Pendidikan': return 'bg-blue-600 border-blue-200';
                    case 'Ibadah': return 'bg-emerald-600 border-emerald-200';
                    case 'Kesehatan': return 'bg-rose-600 border-rose-200';
                    case 'Pemerintahan': return 'bg-slate-950 border-slate-800';
                    default: return 'bg-amber-500 border-amber-300';
                }
            },
            getFacilityIconClass(type) {
                switch(type) {
                    case 'Pendidikan': return 'fa-solid fa-graduation-cap';
                    case 'Ibadah': return 'fa-solid fa-mosque';
                    case 'Kesehatan': return 'fa-solid fa-heart-pulse';
                    case 'Pemerintahan': return 'fa-solid fa-building-flag';
                    default: return 'fa-solid fa-map-pin';
                }
            },
            focusFacility(facility) {
                if (window.focusFacilityOnMap) {
                    window.focusFacilityOnMap(facility);
                }
            },
            filterMarkers() {
                if (window.filterFacilityMarkers) {
                    window.filterFacilityMarkers(this.selectedCategory, this.searchQuery);
                }
            },
            init() {
                this.$watch('searchQuery', () => this.filterMarkers());
                this.$watch('selectedCategory', () => this.filterMarkers());
            }
        }));
    });

    let map;
    const layers = {}; // Storage for geojson layers
    const dusunData = @json($dusuns);

    document.addEventListener('DOMContentLoaded', function () {
        const centerLat = {{ $site_settings['village_latitude'] ?? '-5.23' }};
        const centerLng = {{ $site_settings['village_longitude'] ?? '120.21' }};
        
        map = L.map('spatialMap').setView([centerLat, centerLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Layer Groups for control
        const boundaryLayerGroup = L.featureGroup().addTo(map);
        const dusunLayerGroup = L.featureGroup().addTo(map);
        const facilityLayerGroup = L.featureGroup().addTo(map);

        // Draw overall village boundary outline if available
        @if(!empty($site_settings['village_geojson']))
        try {
            const villageBoundaryObj = {!! $site_settings['village_geojson'] !!};
            L.geoJSON(villageBoundaryObj, {
                style: {
                    color: '#0f172a', // Slate 900
                    weight: 4,
                    dashArray: '8, 8',
                    opacity: 0.9,
                    fillColor: 'transparent',
                    fillOpacity: 0
                }
            }).addTo(boundaryLayerGroup);
        } catch (e) {
            console.error("Failed to parse village boundary GeoJSON:", e);
        }
        @endif

        // Track if we have any valid polygons
        let hasPolygons = false;

        // Loop through each dusun to draw boundaries
        dusunData.forEach(dusun => {
            if (!dusun.geojson) return;

            try {
                const geoJsonObj = JSON.parse(dusun.geojson);
                
                // Add styling
                const layer = L.geoJSON(geoJsonObj, {
                    style: {
                        color: dusun.color || '#10b981',
                        weight: 3,
                        opacity: 0.85,
                        fillColor: dusun.color || '#10b981',
                        fillOpacity: 0.25
                    }
                });

                // Bind Popup
                const popupContent = `
                    <div class="p-2 text-slate-800 font-sans">
                        <h5 class="font-heading font-black text-sm text-emerald-800 mb-1">Dusun ${dusun.name}</h5>
                        <p class="text-xs font-semibold text-slate-500 mb-2">Kepala Dusun: <strong>${dusun.head_name || '-'}</strong></p>
                        <div class="grid grid-cols-2 gap-2 text-center bg-slate-50 p-2 rounded-lg border border-slate-100">
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400">Penduduk</p>
                                <p class="text-xs font-bold text-slate-900">${dusun.citizens_count} Jiwa</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400">Keluarga</p>
                                <p class="text-xs font-bold text-slate-900">${dusun.families_count} KK</p>
                            </div>
                        </div>
                    </div>
                `;
                layer.bindPopup(popupContent);

                // Click event on polygon
                layer.on('click', function () {
                    highlightDusunInfo(dusun);
                });

                // Store layer reference
                layers[dusun.id] = layer;
                dusunLayerGroup.addLayer(layer);
                hasPolygons = true;

            } catch (error) {
                console.error("Invalid GeoJSON for Dusun: " + dusun.name, error);
            }
        });

        // Plot Public Facilities
        const facilityData = @json($facilities);
        const facilityMarkers = [];

        facilityData.forEach(facility => {
            if (!facility.latitude || !facility.longitude) return;

            let colorClass = '';
            let iconClass = '';

            switch(facility.type) {
                case 'Pendidikan':
                    colorClass = 'bg-blue-600 border-blue-200 text-white';
                    iconClass = 'fa-solid fa-graduation-cap';
                    break;
                case 'Ibadah':
                    colorClass = 'bg-emerald-600 border-emerald-200 text-white';
                    iconClass = 'fa-solid fa-mosque';
                    break;
                case 'Kesehatan':
                    colorClass = 'bg-rose-600 border-rose-200 text-white';
                    iconClass = 'fa-solid fa-heart-pulse';
                    break;
                case 'Pemerintahan':
                    colorClass = 'bg-slate-950 border-slate-800 text-white';
                    iconClass = 'fa-solid fa-building-flag';
                    break;
                default:
                    colorClass = 'bg-amber-500 border-amber-300 text-white';
                    iconClass = 'fa-solid fa-map-pin';
            }

            const customIcon = L.divIcon({
                html: `<div class="flex items-center justify-center w-8 h-8 rounded-full border-2 shadow-lg ${colorClass}"><i class="${iconClass} text-xs"></i></div>`,
                className: 'custom-facility-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            const marker = L.marker([facility.latitude, facility.longitude], { icon: customIcon })
                .bindPopup(`
                    <div class="p-2 text-slate-800 font-sans min-w-[150px]">
                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block">${facility.type}</span>
                        <h5 class="font-heading font-black text-sm text-slate-900 mt-0.5">${facility.name}</h5>
                        ${facility.address ? `<p class="text-xs text-slate-500 leading-normal mt-2"><i class="fa-solid fa-location-dot text-slate-400"></i> ${facility.address}</p>` : ''}
                        ${facility.description ? `<p class="text-xs text-slate-400 italic leading-normal mt-2 border-t border-slate-100 pt-2">${facility.description}</p>` : ''}
                    </div>
                `);

            marker.on('click', function() {
                highlightFacilityInfo(facility);
            });

            marker.facilityId = facility.id;
            marker.facilityType = facility.type;
            marker.facilityName = facility.name;
            marker.facilityAddress = facility.address || '';
            facilityMarkers.push(marker);

            facilityLayerGroup.addLayer(marker);
        });

        // Fit map bounds to show Dusuns, else Facilities, else default village center
        if (hasPolygons) {
            map.fitBounds(dusunLayerGroup.getBounds(), { padding: [40, 40] });
        } else if (facilityMarkers.length > 0) {
            map.fitBounds(facilityLayerGroup.getBounds(), { padding: [40, 40] });
        }

        // Add Layer Control
        const overlayMaps = {
            "Batas Wilayah Dusun": dusunLayerGroup,
            "Batas Luar Desa": boundaryLayerGroup,
            "Fasilitas Umum": facilityLayerGroup
        };
        L.control.layers(null, overlayMaps, { collapsed: true }).addTo(map);

        // Global functions for Alpine to call
        window.focusFacilityOnMap = function(facility) {
            const marker = facilityMarkers.find(m => m.facilityId === facility.id);
            if (marker) {
                if (!facilityLayerGroup.hasLayer(marker)) {
                    facilityLayerGroup.addLayer(marker);
                }
                const currentZoom = map.getZoom();
                const targetZoom = Math.max(currentZoom, 16);
                map.setView([facility.latitude, facility.longitude], targetZoom);
                marker.openPopup();
                highlightFacilityInfo(facility);
            }
        };

        window.filterFacilityMarkers = function(category, query) {
            facilityLayerGroup.clearLayers();
            facilityMarkers.forEach(marker => {
                const matchesCategory = category === 'Semua' || marker.facilityType === category;
                const matchesSearch = marker.facilityName.toLowerCase().includes(query.toLowerCase()) || 
                                     marker.facilityAddress.toLowerCase().includes(query.toLowerCase());
                if (matchesCategory && matchesSearch) {
                    facilityLayerGroup.addLayer(marker);
                }
            });
        };
    });

    function highlightDusunInfo(dusun) {
        // Update Card styling & values
        const borderEl = document.getElementById('infoCardBorder');
        const badgeEl = document.getElementById('infoBadge');
        const titleEl = document.getElementById('infoTitle');
        const subtitleEl = document.getElementById('infoSubtitle');
        const statsEl = document.getElementById('infoStatsGrid');
        
        const headEl = document.getElementById('infoHead');
        const popEl = document.getElementById('infoPopulation');
        const famEl = document.getElementById('infoFamilies');

        // Color mapping
        borderEl.style.backgroundColor = dusun.color || '#10b981';
        badgeEl.style.backgroundColor = (dusun.color || '#10b981') + '15'; // translucent
        badgeEl.style.color = dusun.color || '#10b981';
        badgeEl.innerHTML = `<i class="fa-solid fa-map-pin"></i> Wilayah Terpilih`;

        titleEl.textContent = `Dusun ${dusun.name}`;
        subtitleEl.innerHTML = `Berikut statistik kependudukan riil di wilayah <strong>Dusun ${dusun.name}</strong>.`;

        // Restore Dusun stats
        statsEl.innerHTML = `
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Kepala Dusun</p>
                <p class="text-slate-800 font-bold text-sm">${dusun.head_name || '-'}</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Penduduk</p>
                    <p class="text-slate-950 font-heading font-black text-xl leading-none mt-1">${Number(dusun.citizens_count).toLocaleString('id-ID')}</p>
                    <p class="text-slate-400 text-[10px] font-bold mt-1">Jiwa</p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Keluarga</p>
                    <p class="text-slate-950 font-heading font-black text-xl leading-none mt-1">${Number(dusun.families_count).toLocaleString('id-ID')}</p>
                    <p class="text-slate-400 text-[10px] font-bold mt-1">KK</p>
                </div>
            </div>
        `;

        statsEl.classList.remove('hidden');

        // Toggle buttons border highlight
        dusunData.forEach(d => {
            const btn = document.getElementById(`btn-dusun-${d.id}`);
            if (btn) {
                if (d.id === dusun.id) {
                    btn.classList.add('border-emerald-500', 'bg-emerald-50/20');
                } else {
                    btn.classList.remove('border-emerald-500', 'bg-emerald-50/20');
                }
            }
        });

        // Remove active styling from any facility items
        document.querySelectorAll('[id^="btn-facility-"]').forEach(btn => {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50/20');
        });
    }

    function highlightFacilityInfo(facility) {
        const borderEl = document.getElementById('infoCardBorder');
        const badgeEl = document.getElementById('infoBadge');
        const titleEl = document.getElementById('infoTitle');
        const subtitleEl = document.getElementById('infoSubtitle');
        const statsEl = document.getElementById('infoStatsGrid');

        let color = '#d97706'; // default amber-600
        let icon = 'fa-map-pin';

        switch(facility.type) {
            case 'Pendidikan':
                color = '#2563eb';
                icon = 'fa-graduation-cap';
                break;
            case 'Ibadah':
                color = '#059669';
                icon = 'fa-mosque';
                break;
            case 'Kesehatan':
                color = '#e11d48';
                icon = 'fa-heart-pulse';
                break;
            case 'Pemerintahan':
                color = '#0f172a';
                icon = 'fa-building-flag';
                break;
        }

        borderEl.style.backgroundColor = color;
        badgeEl.style.backgroundColor = color + '15'; // translucent
        badgeEl.style.color = color;
        badgeEl.innerHTML = `<i class="fa-solid ${icon}"></i> ${facility.type}`;

        titleEl.textContent = facility.name;
        subtitleEl.innerHTML = `Berikut informasi detail fasilitas <strong>${facility.name}</strong>.`;

        statsEl.innerHTML = `
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/50 space-y-3">
                ${facility.address ? `
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Alamat</p>
                    <p class="text-slate-800 font-bold text-xs leading-normal"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> ${facility.address}</p>
                </div>` : ''}
                ${facility.description ? `
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Deskripsi / Catatan</p>
                    <p class="text-slate-600 font-medium text-xs leading-relaxed italic">${facility.description}</p>
                </div>` : ''}
                <div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Koordinat</p>
                    <p class="text-slate-500 font-mono text-[10px]">${facility.latitude}, ${facility.longitude}</p>
                </div>
            </div>
        `;

        statsEl.classList.remove('hidden');

        // Toggle buttons border highlight on facility list
        document.querySelectorAll('[id^="btn-facility-"]').forEach(btn => {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50/20');
        });
        const currentBtn = document.getElementById(`btn-facility-${facility.id}`);
        if (currentBtn) {
            currentBtn.classList.add('border-emerald-500', 'bg-emerald-50/20');
        }

        // Remove active styling from any dusun items
        dusunData.forEach(d => {
            const btn = document.getElementById(`btn-dusun-${d.id}`);
            if (btn) btn.classList.remove('border-emerald-500', 'bg-emerald-50/20');
        });
    }

    function focusDusun(dusunId) {
        const layer = layers[dusunId];
        const dusun = dusunData.find(d => d.id === dusunId);
        
        if (layer && dusun) {
            map.fitBounds(layer.getBounds());
            layer.openPopup();
            highlightDusunInfo(dusun);
        }
    }
</script>
@endpush
