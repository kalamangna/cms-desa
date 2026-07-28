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
                                                    ->placeholder('Contoh: 7306011203040001')
                                                    ->helperText('Nomor KK 16 digit sesuai dokumen resmi.')
                                                    ->required()
                                                    ->unique(ignoreRecord: true),
                                                TextInput::make('head_name')->label('Nama Kepala Keluarga')
                                                    ->placeholder('Contoh: Andi Muhammad')
                                                    ->helperText('Nama lengkap Kepala Keluarga.')
                                                    ->required(),
                                                TextInput::make('head_nik')->label('NIK Kepala Keluarga')
                                                    ->placeholder('Contoh: 7306010506800001')
                                                    ->helperText('NIK 16 digit Kepala Keluarga (opsional).'),
                                                TextInput::make('family_member_count')->label('Jumlah Anggota Keluarga (Tinggal Bersama)')
                                                    ->placeholder('Contoh: 4')
                                                    ->helperText('Total anggota tinggal dalam satu rumah.')
                                                    ->numeric()
                                                    ->default(1),
                                            ]),
                                    ]),

                                Section::make('Lokasi Keluarga')
                                    ->description('Domisili dan alamat tempat tinggal.')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                Select::make('dusun_id')->label('Dusun')
                                                    ->placeholder('Pilih Dusun')
                                                    ->helperText('Wilayah dusun domisili.')
                                                    ->relationship('dusun', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                TextInput::make('rt')->label('RT')
                                                    ->placeholder('Contoh: 001')
                                                    ->helperText('Nomor RT.'),
                                                TextInput::make('rw')->label('RW')
                                                    ->placeholder('Contoh: 002')
                                                    ->helperText('Nomor RW.'),
                                                Select::make('address_matches_kk')->label('Alamat Sesuai KK?')
                                                    ->placeholder('Pilih Kesesuaian')
                                                    ->helperText('Kesesuaian domisili dengan alamat di KK.')
                                                    ->options([
                                                        1 => 'Ya Sesuai KK',
                                                        0 => 'Tidak Sesuai KK',
                                                    ])
                                                    ->formatStateUsing(fn ($state) => $state ? 1 : 0),
                                            ]),
                                        Textarea::make('address')->label('Alamat Lengkap')
                                            ->placeholder('Contoh: Jl. Poros Desa No. 12, Dusun Karawa')
                                            ->helperText('Alamat rumah/domisili tempat tinggal keluarga.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ─── Tab 2: Karakteristik Rumah ───────────────────────────────────
                        Tab::make('Karakteristik Rumah')
                            ->schema([
                                Section::make('Info & Kepemilikan Bangunan')
                                    ->description('Jenis, legalitas, dan luas hunian.')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('building_type')->label('Jenis Bangunan Tempat Tinggal')
                                                    ->placeholder('Pilih Jenis Bangunan')
                                                    ->helperText('Bentuk atau tipe fisik bangunan.')
                                                    ->options([
                                                        'Rumah Tinggal Tunggal' => 'Rumah Tinggal Tunggal',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable(),
                                                Select::make('ownership_status')->label('Status Kepemilikan Bangunan')
                                                    ->placeholder('Pilih Status Kepemilikan')
                                                    ->helperText('Status hak kepemilikan rumah.')
                                                    ->options([
                                                        'Milik Sendiri' => 'Milik Sendiri',
                                                        'Bebas Sewa' => 'Bebas Sewa',
                                                        'Sewa / Kontrak' => 'Sewa / Kontrak',
                                                    ])
                                                    ->searchable(),
                                                Select::make('ownership_proof')->label('Bukti Kepemilikan')
                                                    ->placeholder('Pilih Bukti Kepemilikan')
                                                    ->helperText('Pilih bukti kepemilikan bangunan.')
                                                    ->options([
                                                        'SHM' => 'SHM',
                                                        'Tidak Punya' => 'Tidak Punya',
                                                    ])
                                                    ->searchable(),
                                                TextInput::make('floor_area')->label('Luas Lantai Bangunan (m²)')
                                                    ->placeholder('Contoh: 72')
                                                    ->helperText('Estimasi luas lantai (meter persegi).')
                                                    ->numeric(),
                                            ]),
                                    ]),

                                Section::make('Biaya Sewa / Kontrak')
                                    ->description('Estimasi biaya sewa atau kontrak.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('rental_estimate')->label('Perkiraan Sewa Sebulan (Rp)')
                                                    ->placeholder('Contoh: 500000')
                                                    ->helperText('Biaya estimasi sewa per bulan.')
                                                    ->numeric(),
                                                TextInput::make('rental_free_estimate')->label('Estimasi Bebas Sewa / Lainnya (Rp)')
                                                    ->placeholder('Contoh: 0')
                                                    ->helperText('Estimasi nilai jika berstatus bebas sewa.')
                                                    ->numeric(),
                                                TextInput::make('rental_contract_value')->label('Nilai Kontrak Total (Rp)')
                                                    ->placeholder('Contoh: 6000000')
                                                    ->helperText('Nilai total kesepakatan kontrak.')
                                                    ->numeric(),
                                            ]),
                                    ]),

                                Section::make('Material Bangunan (Bahan Terluas)')
                                    ->description('Bahan konstruksi lantai, dinding, atap.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('floor_material')->label('Bahan Lantai Utama')
                                                    ->placeholder('Pilih Bahan Lantai')
                                                    ->helperText('Pilih bahan lantai utama.')
                                                    ->options([
                                                        'Semen / Bata Merah' => 'Semen / Bata Merah',
                                                        'Keramik' => 'Keramik',
                                                        'Kayu / Papan' => 'Kayu / Papan',
                                                        'Ubin / Tegel / Teraso' => 'Ubin / Tegel / Teraso',
                                                        'Parket / Vinil / Karpet' => 'Parket / Vinil / Karpet',
                                                        'Tanah' => 'Tanah',
                                                    ])
                                                    ->searchable(),
                                                Select::make('wall_material')->label('Bahan Dinding Utama')
                                                    ->placeholder('Pilih Bahan Dinding')
                                                    ->helperText('Material terluas yang digunakan pada dinding.')
                                                    ->options([
                                                        'Tembok' => 'Tembok',
                                                        'Kayu / Papan / Gipsum / GRC / Calciboard' => 'Kayu / Papan / Gipsum / GRC / Calciboard',
                                                        'Seng' => 'Seng',
                                                    ])
                                                    ->searchable(),
                                                Select::make('roof_material')->label('Bahan Atap Utama')
                                                    ->placeholder('Pilih Bahan Atap')
                                                    ->helperText('Material terluas yang digunakan pada atap.')
                                                    ->options([
                                                        'Seng' => 'Seng',
                                                        'Genteng' => 'Genteng',
                                                        'Asbes' => 'Asbes',
                                                    ])
                                                    ->searchable(),
                                            ]),
                                    ]),

                                Section::make('Kondisi Fisik Bangunan')
                                    ->description('Kondisi kelayakan lantai, dinding, atap.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('floor_condition')->label('Kondisi Lantai')
                                                    ->placeholder('Pilih Kondisi')
                                                    ->helperText('Kondisi fisik bangunan lantai.')
                                                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                                Select::make('wall_condition')->label('Kondisi Dinding')
                                                    ->placeholder('Pilih Kondisi')
                                                    ->helperText('Kondisi fisik bangunan dinding.')
                                                    ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                                Select::make('roof_condition')->label('Kondisi Atap')
                                                    ->placeholder('Pilih Kondisi')
                                                    ->helperText('Kondisi fisik bangunan atap.')
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
                                                    ->placeholder('Pilih Fasilitas BAB')
                                                    ->helperText('Kepemilikan dan penggunaan fasilitas MCK.')
                                                    ->options([
                                                        'Ada, digunakan oleh anggota keluarga dalam satu rumah' => 'Ada, digunakan sendiri',
                                                        'Ada, digunakan bersama oleh anggota keluarga dari beberapa rumah' => 'Ada, digunakan bersama',
                                                        'Tidak Ada' => 'Tidak Ada',
                                                    ])->searchable(),
                                                Select::make('closet_type')->label('Jenis Kloset')
                                                    ->placeholder('Pilih Jenis Kloset')
                                                    ->helperText('Pilih jenis kloset.')
                                                    ->options([
                                                        'Leher Angsa' => 'Leher Angsa',
                                                        'Plengsengan dengan Tutup' => 'Plengsengan dengan Tutup',
                                                        'Cemplung / Cubluk' => 'Cemplung / Cubluk',
                                                        'Tidak Ada' => 'Tidak Ada',
                                                    ])->searchable(),
                                                Select::make('feces_disposal')->label('Tempat Pembuangan Akhir Tinja')
                                                    ->placeholder('Pilih Tempat Pembuangan')
                                                    ->helperText('Muara pembuangan limbah jamban.')
                                                    ->options([
                                                        'Tangki Septik' => 'Tangki Septik',
                                                        'Kolam / Sawah / Sungai / Danau' => 'Kolam / Sawah / Sungai / Danau',
                                                        'Lubang Tanah' => 'Lubang Tanah',
                                                        'Pantai / Tanah Lapang' => 'Pantai / Tanah Lapang',
                                                        'Lainnya' => 'Lainnya',
                                                    ])->searchable(),
                                                Select::make('water_source')->label('Sumber Air Minum Utama')
                                                    ->placeholder('Pilih Sumber Air')
                                                    ->helperText('Sumber konsumsi air minum harian.')
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
                                    ->description('Sumber penerangan dan daya listrik.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('lighting_source')->label('Sumber Penerangan Utama')
                                                    ->placeholder('Pilih Sumber Penerangan')
                                                    ->helperText('Pilih sumber penerangan utama.')
                                                    ->options([
                                                        'Listrik PLN Dengan Meteran' => 'Listrik PLN Dengan Meteran',
                                                        'Listrik PLN Tanpa Meteran' => 'Listrik PLN Tanpa Meteran',
                                                        'Listrik Non-PLN' => 'Listrik Non-PLN',
                                                        'Bukan Listrik' => 'Bukan Listrik',
                                                    ])->searchable(),
                                                TextInput::make('electricity_id')->label('ID Pelanggan PLN')
                                                    ->placeholder('Contoh: 53123456789')
                                                    ->helperText('Nomor ID Meteran PLN.'),
                                                Select::make('electricity_power_meter_1')->label('Daya Listrik Meteran 1')
                                                    ->placeholder('Pilih Daya Listrik')
                                                    ->helperText('Pilih kapasitas daya listrik meteran 1.')
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
                                                    ->placeholder('Pilih Daya Listrik')
                                                    ->helperText('Pilih kapasitas daya listrik meteran 2.')
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
                                                    ->placeholder('Pilih Daya Listrik')
                                                    ->helperText('Pilih kapasitas daya listrik meteran 3.')
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
                                    ->description('Biaya listrik dan pulsa/internet.')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('electricity_cost')->label('Pengeluaran Listrik Sebulan (Rp)')
                                                    ->placeholder('Contoh: 150000')
                                                    ->helperText('Tagihan/token listrik rata-rata sebulan.')
                                                    ->numeric(),
                                                TextInput::make('internet_cost')->label('Pengeluaran Pulsa / Internet Sebulan (Rp)')
                                                    ->placeholder('Contoh: 100000')
                                                    ->helperText('Pulsa dan kuota internet rata-rata sebulan.')
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
                                                TextInput::make('gas_3kg_count')->label('Tabung Gas 3 kg (Jumlah)')->placeholder('0')->helperText('Jumlah tabung gas LPG 3kg.')->numeric()->default(0),
                                                TextInput::make('gas_5kg_count')->label('Tabung Gas >5.5 kg (Jumlah)')->placeholder('0')->helperText('Jumlah tabung gas >5.5kg.')->numeric()->default(0),
                                                TextInput::make('refrigerator_count')->label('Lemari Es / Kulkas (Jumlah)')->placeholder('0')->helperText('Jumlah unit kulkas.')->numeric()->default(0),
                                                TextInput::make('ac_count')->label('AC (Jumlah)')->placeholder('0')->helperText('Jumlah unit pendingin ruangan AC.')->numeric()->default(0),
                                                TextInput::make('jewelry_count')->label('Emas / Perhiasan (Jumlah)')->placeholder('0')->helperText('Jumlah gram/unit perhiasan emas.')->numeric()->default(0),
                                                TextInput::make('computer_count')->label('Laptop / PC / Tablet (Jumlah)')->placeholder('0')->helperText('Jumlah perangkat komputer.')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Kendaraan')
                                    ->description('Kepemilikan dan nilai kendaraan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('motorcycle_count')->label('Sepeda Motor (Jumlah)')->placeholder('0')->helperText('Jumlah unit sepeda motor.')->numeric()->default(0),
                                                TextInput::make('motorcycle_value')->label('Total Nilai Aset Motor (Rp)')->placeholder('Contoh: 15000000')->helperText('Estimasi nilai taksiran motor.')->numeric()->default(0),
                                                TextInput::make('car_count')->label('Mobil (Jumlah)')->placeholder('0')->helperText('Jumlah unit mobil.')->numeric()->default(0),
                                                TextInput::make('car_value')->label('Total Nilai Aset Mobil (Rp)')->placeholder('Contoh: 100000000')->helperText('Estimasi nilai taksiran mobil.')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Properti')
                                    ->description('Tanah dan bangunan lain.')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('other_land_count')->label('Tanah Lain Dimiliki (Jumlah)')->placeholder('0')->helperText('Jumlah bidang tanah lain.')->numeric()->default(0),
                                                TextInput::make('other_land_value')->label('Total Nilai Jual Tanah (Rp)')->placeholder('Contoh: 50000000')->helperText('Estimasi nilai jual tanah.')->numeric()->default(0),
                                                TextInput::make('other_building_count')->label('Bangunan Lain Dimiliki (Jumlah)')->placeholder('0')->helperText('Jumlah unit bangunan lain.')->numeric()->default(0),
                                                TextInput::make('other_building_value')->label('Total Nilai Jual Bangunan (Rp)')->placeholder('Contoh: 75000000')->helperText('Estimasi nilai jual bangunan.')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Hewan Ternak')
                                    ->description('Jumlah hewan ternak.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('cow_count')->label('Jumlah Sapi')->placeholder('0')->helperText('Ekor sapi.')->numeric()->default(0),
                                                TextInput::make('goat_count')->label('Jumlah Kambing / Domba')->placeholder('0')->helperText('Ekor kambing/domba.')->numeric()->default(0),
                                                TextInput::make('buffalo_count')->label('Jumlah Kerbau')->placeholder('0')->helperText('Ekor kerbau.')->numeric()->default(0),
                                            ]),
                                    ]),

                                Section::make('Bantuan Sosial')
                                    ->description('Program bansos yang diterima.')
                                    ->schema([
                                        CheckboxList::make('assistance_type')->label('Jenis Bantuan Sosial Diterima')
                                            ->helperText('Centang program bansos yang diterima.')
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
                                            ->columns(3)
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
                                    ->description('Upload foto baru pengganti.')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('photo_front')->label('Foto Rumah Tampak Depan')
                                                    ->helperText('Unggah foto tampak depan rumah keluarga.')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                                FileUpload::make('photo_living_room')->label('Foto Ruang Tamu')
                                                    ->helperText('Unggah foto interior ruang tamu.')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                                FileUpload::make('photo_bathroom')->label('Foto Kamar Mandi')
                                                    ->helperText('Unggah foto fasilitas mck/kamar mandi.')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                                FileUpload::make('photo_kk')->label('Foto Kartu Keluarga')
                                                    ->helperText('Foto Kartu Keluarga (KK) resmi.')
                                                    ->directory('families/photos')
                                                    ->image()
                                                    ->imageResizeTargetWidth(1200),
                                            ]),
                                    ]),

                                Section::make('Catatan')
                                    ->schema([
                                        Textarea::make('notes')->label('Catatan Lainnya')
                                            ->placeholder('Catatan khusus kondisi sosial atau ekonomi keluarga...')
                                            ->helperText('Informasi tambahan mengenai keluarga ini.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
