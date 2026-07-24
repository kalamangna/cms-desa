<?php

namespace App\Filament\Resources\Families\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
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
                        Tab::make('Identitas Keluarga')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('kk_number')->label('Nomor Kartu Keluarga (KK)')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('head_name')->label('Nama Kepala Keluarga')
                                            ->required(),
                                        TextInput::make('head_nik')->label('NIK Kepala Keluarga'),
                                        Select::make('dusun_id')->label('Dusun')
                                            ->relationship('dusun', 'name')
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('rt')->label('RT'),
                                        TextInput::make('rw')->label('RW'),
                                        Select::make('address_matches_kk')->label('Alamat Sesuai KK?')
                                            ->options([
                                                1 => 'Ya Sesuai KK',
                                                0 => 'Tidak Sesuai KK',
                                            ])
                                            ->formatStateUsing(fn ($state) => $state ? 1 : 0),
                                        TextInput::make('assistance_type')->label('Jenis Bantuan Diterima (e.g. PKH, BPNT)'),
                                        TextInput::make('family_member_count')->label('Jumlah Anggota Keluarga (Tinggal Bersama)')
                                            ->numeric()
                                            ->default(1),
                                    ]),
                                Textarea::make('address')->label('Alamat Lengkap')
                                    ->columnSpanFull(),
                            ]),
                        
                        Tab::make('Karakteristik Rumah')
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
                                        Select::make('floor_condition')->label('Kondisi Lantai')
                                            ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                        Select::make('wall_condition')->label('Kondisi Dinding')
                                            ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                        Select::make('roof_condition')->label('Kondisi Atap')
                                            ->options(['Baik' => 'Baik', 'Rusak Ringan' => 'Rusak Ringan', 'Rusak Sedang' => 'Rusak Sedang', 'Rusak Berat' => 'Rusak Berat']),
                                    ]),
                            ]),
                        
                        Tab::make('Sanitasi & Listrik')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('toilet_facility')->label('Fasilitas Buang Air Besar'),
                                        TextInput::make('closet_type')->label('Jenis Kloset'),
                                        TextInput::make('feces_disposal')->label('Tempat Pembuangan Akhir Tinja'),
                                        TextInput::make('water_source')->label('Sumber Air Minum Utama'),
                                        TextInput::make('lighting_source')->label('Sumber Penerangan Utama'),
                                        TextInput::make('electricity_power_meter_1')->label('Daya Listrik Meteran 1'),
                                        TextInput::make('electricity_power_meter_2')->label('Daya Listrik Meteran 2'),
                                        TextInput::make('electricity_power_meter_3')->label('Daya Listrik Meteran 3'),
                                        TextInput::make('electricity_id')->label('ID Pelanggan PLN'),
                                        TextInput::make('electricity_cost')->label('Nilai Pengeluaran Listrik Sebulan (Rp)')
                                            ->numeric(),
                                        TextInput::make('internet_cost')->label('Nilai Pengeluaran Pulsa/Internet Sebulan (Rp)')
                                            ->numeric(),
                                    ]),
                            ]),
                        
                        Tab::make('Aset & Kepemilikan')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('gas_3kg_count')->label('Tabung Gas 3 kg (Jumlah)')->numeric()->default(0),
                                        TextInput::make('gas_5kg_count')->label('Tabung Gas >5.5 kg (Jumlah)')->numeric()->default(0),
                                        TextInput::make('refrigerator_count')->label('Lemari Es/Kulkas (Jumlah)')->numeric()->default(0),
                                        TextInput::make('ac_count')->label('AC (Jumlah)')->numeric()->default(0),
                                        TextInput::make('jewelry_count')->label('Emas/Perhiasan (Jumlah)')->numeric()->default(0),
                                        TextInput::make('computer_count')->label('Laptop/PC/Tablet (Jumlah)')->numeric()->default(0),
                                        TextInput::make('motorcycle_count')->label('Sepeda Motor (Jumlah)')->numeric()->default(0),
                                        TextInput::make('motorcycle_value')->label('Total Nilai Aset Motor (Rp)')->numeric()->default(0),
                                        TextInput::make('car_count')->label('Mobil (Jumlah)')->numeric()->default(0),
                                        TextInput::make('car_value')->label('Total Nilai Aset Mobil (Rp)')->numeric()->default(0),
                                        TextInput::make('other_land_count')->label('Tanah Lain Dimiliki (Jumlah)')->numeric()->default(0),
                                        TextInput::make('other_land_value')->label('Total Nilai Jual Tanah (Rp)')->numeric()->default(0),
                                        TextInput::make('other_building_count')->label('Bangunan Lain Dimiliki (Jumlah)')->numeric()->default(0),
                                        TextInput::make('other_building_value')->label('Total Nilai Jual Bangunan (Rp)')->numeric()->default(0),
                                        TextInput::make('cow_count')->label('Jumlah Sapi')->numeric()->default(0),
                                        TextInput::make('goat_count')->label('Jumlah Kambing/Domba')->numeric()->default(0),
                                        TextInput::make('buffalo_count')->label('Jumlah Kerbau')->numeric()->default(0),
                                    ]),
                            ]),
                        
                        Tab::make('Foto & Berkas')
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

                                        FileUpload::make('photo_front')->label('Unggah/Ganti Foto Rumah Tampak Depan')
                                            ->directory('families/photos')
                                            ->image()
                                            ->imageResizeTargetWidth(1200),
                                        FileUpload::make('photo_living_room')->label('Unggah/Ganti Foto Ruang Tamu')
                                            ->directory('families/photos')
                                            ->image()
                                            ->imageResizeTargetWidth(1200),
                                        FileUpload::make('photo_bathroom')->label('Unggah/Ganti Foto Kamar Mandi')
                                            ->directory('families/photos')
                                            ->image()
                                            ->imageResizeTargetWidth(1200),
                                        FileUpload::make('photo_kk')->label('Unggah/Ganti Foto Kartu Keluarga')
                                            ->directory('families/photos')
                                            ->image()
                                            ->imageResizeTargetWidth(1200),
                                    ]),
                                Textarea::make('notes')->label('Catatan Lainnya')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
