<?php

namespace App\Filament\Resources\StatisticCategories\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;

class StatisticCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->description('Tentukan nama kategori, sumber kuesioner data, kolom pemetaan utama (jika hanya 1 kolom), dan deskripsi kategori.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->placeholder('Contoh: Pendidikan / Pekerjaan'),
                        Select::make('mapping_table')
                            ->label('Sumber Data Kuesioner')
                            ->options([
                                'citizens' => 'Data Penduduk (Individu)',
                                'families' => 'Data Keluarga',
                            ])
                            ->required()
                            ->live(),
                        CheckboxList::make('mapping_column')
                            ->label('Kolom Pemetaan (Excel / Database)')
                            ->helperText('Pilih satu atau beberapa kolom kuesioner Excel yang ingin Anda kelompokkan dalam kategori statistik ini. Jika kolom berupa Ya/Tidak (boolean), indikator tunggal "Ya" akan dibuat otomatis. Jika berupa kategori, semua nilai unik di kolom tersebut akan dibuatkan indikator.')
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
                            ->helperText('Pilih satu atau beberapa kolom untuk mengizinkan pengunjung di halaman publik memecah/membandingkan grafik dan tabel berdasarkan opsi ini (misal: memecah data Pekerjaan berdasarkan Gender, Pendidikan, atau Status Perkawinan).')
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
                        Toggle::make('is_active')
                            ->label('Tampilkan di Halaman Publik')
                            ->default(true)
                            ->inline(false),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tuliskan deskripsi singkat mengenai kategori statistik ini...')
                            ->rows(3),
                    ])
                    ->columns(1),
            ])
            ->columns(1);
    }
}
