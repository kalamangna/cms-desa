<?php

namespace App\Filament\Resources\Families\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;

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
                                                'Ya Sesuai KK' => 'Ya Sesuai KK',
                                                'Tidak Sesuai KK' => 'Tidak Sesuai KK',
                                            ]),
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
                                        TextInput::make('building_type')->label('Jenis Bangunan Tempat Tinggal'),
                                        TextInput::make('ownership_status')->label('Status Kepemilikan Bangunan'),
                                        TextInput::make('ownership_proof')->label('Bukti Kepemilikan'),
                                        TextInput::make('floor_area')->label('Luas Lantai Bangunan (m²)')
                                            ->numeric(),
                                        TextInput::make('floor_material')->label('Bahan Lantai Utama Terluas'),
                                        TextInput::make('wall_material')->label('Bahan Dinding Utama Terluas'),
                                        TextInput::make('roof_material')->label('Bahan Atap Utama Terluas'),
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
                                        TextInput::make('electricity_power')->label('Daya Listrik (VA/Watt)'),
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
                                    ]),
                            ]),
                        
                        Tab::make('Foto & Berkas')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        FileUpload::make('photo_front')->label('Foto Rumah Tampak Depan')
                                            ->directory('families/photos')
                                            ->image(),
                                        FileUpload::make('photo_living_room')->label('Foto Ruang Tamu')
                                            ->directory('families/photos')
                                            ->image(),
                                        FileUpload::make('photo_bathroom')->label('Foto Kamar Mandi')
                                            ->directory('families/photos')
                                            ->image(),
                                        FileUpload::make('photo_kk')->label('Foto Kartu Keluarga')
                                            ->directory('families/photos')
                                            ->image(),
                                    ]),
                                Textarea::make('notes')->label('Catatan Lainnya')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
