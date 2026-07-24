<?php

namespace App\Filament\Resources\Families\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;

class FamilyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([

                        // ─── Tab 1: Identitas Keluarga ────────────────────────────────────
                        Tab::make('Identitas Keluarga')
                            ->schema([
                                Section::make('Data Kartu Keluarga')
                                    ->description('Informasi utama kepala dan anggota keluarga')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('kk_number')->label('Nomor Kartu Keluarga (KK)')
                                                    ->required()
                                                    ->unique(ignoreRecord: true),
                                                TextInput::make('head_name')->label('Nama Kepala Keluarga')
                                                    ->required(),
                                                TextInput::make('head_nik')->label('NIK Kepala Keluarga'),
                                                TextInput::make('family_member_count')->label('Jumlah Anggota Keluarga (Tinggal Bersama)')
                                                    ->numeric()
                                                    ->default(1),
                                            ]),
                                    ]),

                                Section::make('Lokasi Keluarga')
                                    ->description('Domisili dan alamat tempat tinggal keluarga')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('dusun_id')->label('Dusun')
                                                    ->relationship('dusun', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                TextInput::make('rt')->label('RT'),
                                                TextInput::make('rw')->label('RW'),
                                            ]),
                                        Select::make('address_matches_kk')->label('Alamat Sesuai KK?')
                                            ->options([
                                                1 => 'Ya Sesuai KK',
                                                0 => 'Tidak Sesuai KK',
                                            ])
                                            ->formatStateUsing(fn ($state) => $state ? 1 : 0),
                                        Textarea::make('address')->label('Alamat Lengkap')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─── Tab 2: Karakteristik Rumah ───────────────────────────────────
                        Tab::make('Karakteristik Rumah')
                            ->schema([
                                Section::make('Info Bangunan')
                                    ->description('Jenis, kepemilikan, dan luas bangunan')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('building_type')->label('Jenis Bangunan Tempat Tinggal')
                                                    ->options([
                                                        'Rumah Tinggal Tunggal' => 'Rumah Tinggal Tunggal',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable(),
                                                Select::make('ownership_status')->label('Status Kepemilikan Bangunan')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Bebas Sewa' => 'Bebas Sewa',
                                                        'Sewa / Kontrak' => 'Sewa / Kontrak',
                                                    ])
                                                    ->searchable(),
                                                Select::make('ownership_proof')->label('Bukti Kepemilikan')
                                                    ->options([
                                                        'SHM' => 'SHM',
                                                        'Tidak Punya' => 'Tidak Punya',
                                                    ])
                                                    ->searchable(),
                                                TextInput::make('floor_area')->label('Luas Lantai Bangunan (m²)')
                                                    ->numeric(),
                                            ]),
                                    ]),

                                Section::make('Biaya Sewa')
                                    ->description('Estimasi biaya sewa / kontrak bangunan')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('rental_estimate')->label('Perkiraan Sewa Sebulan (Rp)')->numeric(),
                                                TextInput::make('rental_free_estimate')->label('Perkiraan Sewa Bebas Sewa / Lainnya (Rp)')->numeric(),
                                                TextInput::make('rental_contract_value')->label('Nilai Kontrak (Rp)')->numeric(),
                                            ]),
                                    ]),

                                Section::make('Material Bangunan')
                                    ->description('Bahan utama lantai, dinding, dan atap')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('floor_material')->label('Bahan Lantai Utama Terluas')
                                                    ->options([
                                                        'Semen / Bata Merah' => 'Semen / Bata Merah',
                                                        'Keramik' => 'Keramik',
                                                        'Kayu / Papan' => 'Kayu / Papan',
                                                        'Ubin / Tegel / Teraso' => 'Ubin / Tegel / Teraso',
                                                        'Parket / Vinil / Karpet' => 'Parket / Vinil / Karpet',
                                                        'Tanah' => 'Tanah',
                                                    ])
                                                    ->searchable(),
                                                Select::make('wall_material')->label('Bahan Dinding Utama Terluas')
                                                    ->options([
                                                        'Tembok' => 'Tembok',
                                                        'Kayu / Papan / Gipsum / GRC / Calciboard' => 'Kayu / Papan / Gipsum / GRC / Calciboard',
                                                        'Seng' => 'Seng',
                                                    ])
                                                    ->searchable(),
                                                Select::make('roof_material')->label('Bahan Atap Utama Terluas')
                                                    ->options([
                                                        'Seng' => 'Seng',
                                                        'Genteng' => 'Genteng',
                                                        'Asbes' => 'Asbes',
                                                    ])
                                                    ->searchable(),
                                            ]),
                                    ]),

                                Section::make('Kondisi Bangunan')
                                    ->description('Kondisi fisik lantai, dinding, dan atap')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('floor_condition')->label('Kondisi Lantai')
                                                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                                Select::make('wall_condition')->label('Kondisi Dinding')
                                                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                                Select::make('roof_condition')->label('Kondisi Atap')
                                                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 3: Sanitasi & Utilitas ───────────────────────────────────
                        Tab::make('Sanitasi & Utilitas')
                            ->schema([
                                Section::make('Sanitasi & Air Bersih')
                                    ->description('Fasilitas MCK dan sumber air minum')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('toilet_facility')->label('Fasilitas Buang Air Besar')
                                                    ->options([
                                                        'Ada, digunakan oleh anggota keluarga dalam satu rumah' => 'Ada, digunakan sendiri',
                                                        'Ada, digunakan bersama oleh anggota keluarga dari beberapa rumah' => 'Ada, digunakan bersama',
                                                        'Tidak Ada' => 'Tidak Ada',
                                                    ])->searchable(),
                                                Select::make('closet_type')->label('Jenis Kloset')
                                                    ->options([
                                                        'Leher angsa' => 'Leher Angsa',
                                                        'Plengsengan dengan tutup' => 'Plengsengan dengan Tutup',
                                                        'Cemplung/cubluk' => 'Cemplung / Cubluk',
                                                        'Tidak Ada' => 'Tidak Ada',
                                                    ])->searchable(),
                                                Select::make('feces_disposal')->label('Tempat Pembuangan Akhir Tinja')
                                                    ->options([
                                                        'Tangki Septik' => 'Tangki Septik',
                                                        'Kolam / Sawah / Sungai / Danau' => 'Kolam / Sawah / Sungai / Danau',
                                                        'Lubang tanah' => 'Lubang Tanah',
                                                        'Pantai / Tanah Lapang' => 'Pantai / Tanah Lapang',
                                                        'Lainnya' => 'Lainnya',
                                                    ])->searchable(),
                                                Select::make('water_source')->label('Sumber Air Minum Utama')
                                                    ->options([
                                                        'Sumur Terlindung' => 'Sumur Terlindung',
                                                        'Sumur Bor / Pompa' => 'Sumur Bor / Pompa',
                                                        'Leding' => 'Leding',
                                                        'Mata Air' => 'Mata Air',
                                                        'Air kemasan bermerek' => 'Air Kemasan Bermerek',
                                                        'Lainnya' => 'Lainnya',
                                                    ])->searchable(),
                                            ]),
                                    ]),

                                Section::make('Listrik')
                                    ->description('Sumber penerangan dan daya listrik yang digunakan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('lighting_source')->label('Sumber Penerangan Utama')
                                                    ->options([
                                                        'Listrik PLN Dengan Meteran' => 'Listrik PLN Dengan Meteran',
                                                        'Listrik PLN Tanpa Meteran' => 'Listrik PLN Tanpa Meteran',
                                                        'Listrik Non-PLN' => 'Listrik Non-PLN',
                                                        'Bukan Listrik' => 'Bukan Listrik',
                                                    ])->searchable(),
                                                TextInput::make('electricity_id')->label('ID Pelanggan PLN'),
                                                Select::make('electricity_power_meter_1')->label('Daya Listrik Meteran 1')
                                                    ->options([
                                                        '450 Watt' => '450 Watt',
                                                        '900 Watt' => '900 Watt',
                                                        '1.300 Watt' => '1.300 Watt',
                                                        '2.200 Watt' => '2.200 Watt',
                                                        '3.500 Watt' => '3.500 Watt',
                                                        '4.400 Watt' => '4.400 Watt',
                                                        '5.500 Watt' => '5.500 Watt',
                                                        '6.600 Watt' => '6.600 Watt',
                                                        '> 6.600 Watt' => '> 6.600 Watt',
                                                        'Lainnya' => 'Lainnya',
                                                    ])->searchable(),
                                                Select::make('electricity_power_meter_2')->label('Daya Listrik Meteran 2')
                                                    ->options([
                                                        '450 Watt' => '450 Watt',
                                                        '900 Watt' => '900 Watt',
                                                        '1.300 Watt' => '1.300 Watt',
                                                        '2.200 Watt' => '2.200 Watt',
                                                        '3.500 Watt' => '3.500 Watt',
                                                        '4.400 Watt' => '4.400 Watt',
                                                        '5.500 Watt' => '5.500 Watt',
                                                        '6.600 Watt' => '6.600 Watt',
                                                        '> 6.600 Watt' => '> 6.600 Watt',
                                                        'Lainnya' => 'Lainnya',
                                                    ])->searchable(),
                                                Select::make('electricity_power_meter_3')->label('Daya Listrik Meteran 3')
                                                    ->options([
                                                        '450 Watt' => '450 Watt',
                                                        '900 Watt' => '900 Watt',
                                                        '1.300 Watt' => '1.300 Watt',
                                                        '2.200 Watt' => '2.200 Watt',
                                                        '3.500 Watt' => '3.500 Watt',
                                                        '4.400 Watt' => '4.400 Watt',
                                                        '5.500 Watt' => '5.500 Watt',
                                                        '6.600 Watt' => '6.600 Watt',
                                                        '> 6.600 Watt' => '> 6.600 Watt',
                                                        'Lainnya' => 'Lainnya',
                                                    ])->searchable(),
                                            ]),
                                    ]),

                                Section::make('Pengeluaran Utilitas')
                                    ->description('Biaya listrik dan komunikasi setiap bulan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('electricity_cost')->label('Pengeluaran Listrik Sebulan (Rp)')
                                                    ->numeric(),
                                                TextInput::make('internet_cost')->label('Pengeluaran Pulsa / Internet Sebulan (Rp)')
                                                    ->numeric(),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 4: Aset & Bantuan ────────────────────────────────────────
                        Tab::make('Aset & Bantuan')
                            ->schema([
                                Section::make('Aset Rumah Tangga')
                                    ->description('Barang berharga yang dimiliki keluarga')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('gas_3kg_count')->label('Tabung Gas 3 kg (Jumlah)')->numeric()->default(0),
                                                TextInput::make('gas_5kg_count')->label('Tabung Gas >5.5 kg (Jumlah)')->numeric()->default(0),
                                                TextInput::make('refrigerator_count')->label('Lemari Es / Kulkas (Jumlah)')->numeric()->default(0),
                                                TextInput::make('ac_count')->label('AC (Jumlah)')->numeric()->default(0),
                                                TextInput::make('jewelry_count')->label('Emas / Perhiasan (Jumlah)')->numeric()->default(0),
                                                TextInput::make('computer_count')->label('Laptop / PC / Tablet (Jumlah)')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Kendaraan')
                                    ->description('Kepemilikan dan nilai kendaraan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('motorcycle_count')->label('Sepeda Motor (Jumlah)')->numeric()->default(0),
                                                TextInput::make('motorcycle_value')->label('Total Nilai Aset Motor (Rp)')->numeric()->default(0),
                                                TextInput::make('car_count')->label('Mobil (Jumlah)')->numeric()->default(0),
                                                TextInput::make('car_value')->label('Total Nilai Aset Mobil (Rp)')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Properti')
                                    ->description('Tanah dan bangunan lain yang dimiliki')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('other_land_count')->label('Tanah Lain Dimiliki (Jumlah)')->numeric()->default(0),
                                                TextInput::make('other_land_value')->label('Total Nilai Jual Tanah (Rp)')->numeric()->default(0),
                                                TextInput::make('other_building_count')->label('Bangunan Lain Dimiliki (Jumlah)')->numeric()->default(0),
                                                TextInput::make('other_building_value')->label('Total Nilai Jual Bangunan (Rp)')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Hewan Ternak')
                                    ->description('Jumlah hewan ternak yang dimiliki')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('cow_count')->label('Jumlah Sapi')->numeric()->default(0),
                                                TextInput::make('goat_count')->label('Jumlah Kambing / Domba')->numeric()->default(0),
                                                TextInput::make('buffalo_count')->label('Jumlah Kerbau')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Bantuan Sosial')
                                    ->description('Program bantuan sosial yang diterima keluarga')
                                    ->schema([
                                        CheckboxList::make('assistance_type')->label('Jenis Bantuan Sosial Diterima')
                                            ->options([
                                                'PKH' => 'PKH (Program Keluarga Harapan)',
                                                'BPNT / Sembako' => 'BPNT / Sembako',
                                                'BLT Desa' => 'BLT Desa',
                                                'Subsidi Listrik' => 'Subsidi Listrik',
                                                'Bedah Rumah' => 'Bedah Rumah',
                                                'Bantuan Lainnya' => 'Bantuan Lainnya',
                                            ])
                                            ->formatStateUsing(function ($state) {
                                                if (is_array($state)) return $state;
                                                if (empty($state) || $state === 'Tidak Ada') return [];
                                                return array_map('trim', explode(',', $state));
                                            })
                                            ->dehydrateStateUsing(function ($state) {
                                                if (empty($state) || !is_array($state)) return 'Tidak Ada';
                                                return implode(', ', $state);
                                            })
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─── Tab 5: Foto & Berkas ─────────────────────────────────────────
                        Tab::make('Foto & Berkas')
                            ->schema([
                                Section::make('Lihat Foto')
                                    ->description('Pratinjau foto yang sudah tersimpan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Placeholder::make('photo_front_drive')
                                                    ->label('Foto Rumah Tampak Depan')
                                                    ->content(function ($record) {
                                                        $url = $record?->photo_front;
                                                        if (empty($url)) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 font-normal">Tidak ada foto</span>');
                                                        if (str_starts_with($url, 'http')) {
                                                            return new \Illuminate\Support\HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 border border-primary-200 dark:border-primary-800 font-medium hover:bg-primary-100 transition-colors">🔗 Buka Foto di Google Drive ↗</a>');
                                                        }
                                                        return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-medium hover:bg-emerald-100 transition-colors">🖼️ Lihat Foto Lokal ↗</a>');
                                                    }),

                                                Placeholder::make('photo_living_room_drive')
                                                    ->label('Foto Ruang Tamu')
                                                    ->content(function ($record) {
                                                        $url = $record?->photo_living_room;
                                                        if (empty($url)) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 font-normal">Tidak ada foto</span>');
                                                        if (str_starts_with($url, 'http')) {
                                                            return new \Illuminate\Support\HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 border border-primary-200 dark:border-primary-800 font-medium hover:bg-primary-100 transition-colors">🔗 Buka Foto di Google Drive ↗</a>');
                                                        }
                                                        return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-medium hover:bg-emerald-100 transition-colors">🖼️ Lihat Foto Lokal ↗</a>');
                                                    }),

                                                Placeholder::make('photo_bathroom_drive')
                                                    ->label('Foto Kamar Mandi')
                                                    ->content(function ($record) {
                                                        $url = $record?->photo_bathroom;
                                                        if (empty($url)) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 font-normal">Tidak ada foto</span>');
                                                        if (str_starts_with($url, 'http')) {
                                                            return new \Illuminate\Support\HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 border border-primary-200 dark:border-primary-800 font-medium hover:bg-primary-100 transition-colors">🔗 Buka Foto di Google Drive ↗</a>');
                                                        }
                                                        return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-medium hover:bg-emerald-100 transition-colors">🖼️ Lihat Foto Lokal ↗</a>');
                                                    }),

                                                Placeholder::make('photo_kk_drive')
                                                    ->label('Foto Kartu Keluarga')
                                                    ->content(function ($record) {
                                                        $url = $record?->photo_kk;
                                                        if (empty($url)) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 font-normal">Tidak ada foto</span>');
                                                        if (str_starts_with($url, 'http')) {
                                                            return new \Illuminate\Support\HtmlString('<a href="' . e($url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-300 border border-primary-200 dark:border-primary-800 font-medium hover:bg-primary-100 transition-colors">🔗 Buka Foto di Google Drive ↗</a>');
                                                        }
                                                        return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $url) . '" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-medium hover:bg-emerald-100 transition-colors">🖼️ Lihat Foto Lokal ↗</a>');
                                                    }),
                                            ]),
                                    ]),

                                Section::make('Unggah / Ganti Foto')
                                    ->description('Upload foto baru untuk menggantikan foto yang sudah ada')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('photo_front')->label('Foto Rumah Tampak Depan')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                                FileUpload::make('photo_living_room')->label('Foto Ruang Tamu')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                                FileUpload::make('photo_bathroom')->label('Foto Kamar Mandi')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                                FileUpload::make('photo_kk')->label('Foto Kartu Keluarga')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                            ]),
                                    ]),

                                Section::make('Catatan')
                                    ->schema([
                                        Textarea::make('notes')->label('Catatan Lainnya')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
