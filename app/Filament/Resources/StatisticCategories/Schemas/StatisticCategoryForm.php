<?php

namespace App\Filament\Resources\StatisticCategories\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StatisticCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->description('Pengaturan nama, sumber data, kolom pemetaan, dan deskripsi.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->placeholder('Contoh: Pendidikan / Pekerjaan')
                                    ->helperText('Nama unik kategori statistik desa.'),
                                Select::make('mapping_table')
                                    ->label('Sumber Data Kuesioner')
                                    ->placeholder('Pilih Sumber Data')
                                    ->helperText('Tabel sumber data kuesioner.')
                                    ->options([
                                        'citizens' => 'Data Penduduk (Individu)',
                                        'families' => 'Data Keluarga',
                                    ])
                                    ->required()
                                    ->live(),
                            ]),
                        CheckboxList::make('mapping_column')
                            ->label('Kolom Pemetaan (Excel / Database)')
                            ->helperText('Centang kolom kuesioner untuk statistik.')
                            ->options(function (callable $get) {
                                $table = $get('mapping_table');
                                if ($table === 'citizens') {
                                    return [
                                        'gender' => 'Jenis Kelamin',
                                        'education_level' => 'Tingkat Pendidikan Terakhir',
                                        'job' => 'Pekerjaan/Profesi',
                                        'job_status' => 'Status Kedudukan Pekerjaan Utama',
                                        'disability_physical' => 'Disabilitas Fisik',
                                        'disability_mental' => 'Disabilitas Mental',
                                        'disability_intellectual' => 'Disabilitas Intelektual',
                                        'disability_blind' => 'Disabilitas Sensorik Netra',
                                        'disability_deaf' => 'Disabilitas Sensorik Rungu',
                                        'disability_speech' => 'Disabilitas Sensorik Wicara',
                                        'illness_hypertension' => 'Penyakit Hipertensi',
                                        'illness_rheumatic' => 'Penyakit Rematik',
                                        'illness_asthma' => 'Penyakit Asma',
                                        'illness_heart' => 'Penyakit Jantung',
                                        'illness_diabetes' => 'Penyakit Diabetes',
                                        'illness_tbc' => 'Penyakit TBC',
                                        'illness_stroke' => 'Penyakit Stroke',
                                        'illness_cancer' => 'Penyakit Kanker',
                                        'illness_kidney' => 'Penyakit Gagal Ginjal',
                                        'illness_cholesterol' => 'Penyakit Kolesterol',
                                        'illness_other' => 'Penyakit Lainnya',
                                        'has_digital_wallet' => 'Kepemilikan Dompet Digital/Rekening',
                                    ];
                                } elseif ($table === 'families') {
                                    return [
                                        'assistance_type' => 'Jenis Bantuan Sosial',
                                        'ownership_status' => 'Status Kepemilikan Rumah',
                                        'building_type' => 'Jenis Bangunan',
                                        'ownership_proof' => 'Bukti Kepemilikan Rumah',
                                        'water_source' => 'Sumber Air Minum',
                                        'lighting_source' => 'Sumber Penerangan',
                                    ];
                                }

                                return [];
                            })
                            ->reactive()
                            ->columns(3)
                            ->required()
                            ->minItems(1),
                        CheckboxList::make('secondary_columns')
                            ->label('Opsi Pembanding Grafik & Tabel (Sumbu Ke-2)')
                            ->helperText('Centang kolom pembanding untuk grafik.')
                            ->options(function (callable $get) {
                                $table = $get('mapping_table');
                                if ($table === 'citizens') {
                                    return [
                                        'gender' => 'Jenis Kelamin',
                                        'education_level' => 'Pendidikan',
                                        'marital_status' => 'Status Perkawinan',
                                        'job_status' => 'Status Pekerjaan',
                                        'dusun_id' => 'Dusun',
                                        'school_participation' => 'Partisipasi Sekolah',
                                        'has_digital_wallet' => 'Dompet Digital / Rekening',
                                        'bpjs_status' => 'Kepesertaan BPJS',
                                    ];
                                } elseif ($table === 'families') {
                                    return [
                                        'dusun_id' => 'Dusun',
                                        'ownership_status' => 'Kepemilikan Rumah',
                                        'building_type' => 'Jenis Bangunan',
                                        'water_source' => 'Sumber Air Minum',
                                        'lighting_source' => 'Sumber Penerangan',
                                    ];
                                }

                                return [];
                            })
                            ->reactive()
                            ->columns(3),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tuliskan deskripsi singkat mengenai kategori statistik ini...')
                            ->helperText('Keterangan tambahan mengenai kategori ini.')
                            ->rows(3),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
