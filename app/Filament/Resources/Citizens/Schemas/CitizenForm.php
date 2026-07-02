<?php

namespace App\Filament\Resources\Citizens\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Checkbox;

class CitizenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Identitas Warga')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nik')->label('NIK')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('kk_number')->label('No. KK'),
                                        Select::make('family_id')->label('Relasi Keluarga')
                                            ->relationship('family', 'kk_number')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Hubungkan dengan KK yang ada di database'),
                                        TextInput::make('kk_order')->label('Nomor Urut Anggota Keluarga (dari KK)')
                                            ->numeric(),
                                        TextInput::make('name')->label('Nama Lengkap')
                                            ->required(),
                                        Select::make('gender')->label('Jenis Kelamin')
                                            ->options([
                                                'Laki-laki' => 'Laki-laki',
                                                'Perempuan' => 'Perempuan',
                                            ]),
                                        TextInput::make('place_of_birth')->label('Tempat Lahir'),
                                        DatePicker::make('date_of_birth')->label('Tanggal Lahir'),
                                        Select::make('marital_status')->label('Status Perkawinan')
                                            ->options([
                                                'Belum kawin' => 'Belum kawin',
                                                'Kawin' => 'Kawin',
                                                'Cerai hidup' => 'Cerai hidup',
                                                'Cerai mati' => 'Cerai mati',
                                            ]),
                                        Select::make('family_relation')->label('Hubungan dengan Kepala Keluarga')
                                            ->options([
                                                'Kepala Keluarga' => 'Kepala Keluarga',
                                                'Istri' => 'Istri',
                                                'Anak' => 'Anak',
                                                'Menantu' => 'Menantu',
                                                'Cucu' => 'Cucu',
                                                'Orang tua' => 'Orang tua',
                                                'Mertua' => 'Mertua',
                                                'Famili Lain' => 'Famili Lain',
                                                'Pembantu' => 'Pembantu',
                                                'Lainnya' => 'Lainnya',
                                            ]),
                                        Select::make('school_participation')->label('Partisipasi Sekolah')
                                            ->options([
                                                'Tidak/belum pernah sekolah' => 'Tidak/belum pernah sekolah',
                                                'Masih sekolah' => 'Masih sekolah',
                                                'Tidak bersekolah lagi' => 'Tidak bersekolah lagi',
                                            ]),
                                        TextInput::make('education_level')->label('Ijazah Tertinggi yang Dimiliki'),
                                        TextInput::make('bpjs_status')->label('Kepesertaan JKN KIS (BPJS)'),
                                        Select::make('pip_status')->label('Menerima Bantuan PIP?')
                                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak']),
                                    ]),
                            ]),
                        
                        Tab::make('Pekerjaan & Pendapatan')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('job')->label('Profesi Pekerjaan Utama'),
                                        TextInput::make('job_status')->label('Kedudukan dalam Pekerjaan Utama'),
                                        Select::make('has_income')->label('Apakah Memiliki Pendapatan?')
                                            ->options(['Ya' => 'Ya', 'Tidak' => 'Tidak']),
                                        TextInput::make('income_salary')->label('Gaji/Upah Sebulan (Rp)')->numeric()->default(0),
                                        TextInput::make('income_allowance')->label('Tunjangan Sebulan (Rp)')->numeric()->default(0),
                                        TextInput::make('income_food')->label('Uang Makan Sebulan (Rp)')->numeric()->default(0),
                                        TextInput::make('income_honor')->label('Honor Sebulan (Rp)')->numeric()->default(0),
                                        TextInput::make('income_overtime')->label('Lembur Sebulan (Rp)')->numeric()->default(0),
                                        TextInput::make('income_business')->label('Pendapatan Usaha (Rp)')->numeric()->default(0),
                                        TextInput::make('income_passive')->label('Passive Income / Lainnya (Rp)')->numeric()->default(0),
                                        TextInput::make('income_other')->label('Pendapatan Lainnya (Rp)')->numeric()->default(0),
                                    ]),
                            ]),

                        Tab::make('Kesehatan & Disabilitas')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        // Disabilitas
                                        Select::make('disability_physical')->label('Disabilitas Fisik')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('disability_mental')->label('Disabilitas Mental')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('disability_intellectual')->label('Disabilitas Intelektual')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('disability_blind')->label('Disabilitas Sensorik Netra')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('disability_deaf')->label('Disabilitas Sensorik Rungu')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('disability_speech')->label('Disabilitas Sensorik Wicara')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                    ]),
                                
                                Grid::make(3)
                                    ->schema([
                                        // Keluhan Kesehatan Kronis
                                        Select::make('illness_hypertension')->label('Hipertensi')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_rheumatic')->label('Rematik')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_asthma')->label('Asma')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_heart')->label('Masalah Jantung')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_diabetes')->label('Diabetes')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_tbc')->label('TBC')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_stroke')->label('Stroke')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Official/Tidak'),
                                        Select::make('illness_cancer')->label('Kanker')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_kidney')->label('Gagal Ginjal')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_hemophilia')->label('Hemofilia')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_hiv')->label('HIV/AIDS')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_cholesterol')->label('Kolesterol')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_liver')->label('Sirosis Hati')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_thalassemia')->label('Talasemia')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_leukemia')->label('Leukemia')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_alzheimer')->label('Alzheimer')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                        Select::make('illness_other')->label('Penyakit Kronis Lainnya')->options(['Ya' => 'Ya', 'Tidak' => 'Tidak'])->default('Tidak'),
                                    ]),
                            ]),

                        Tab::make('Kependudukan & Keuangan')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('dusun_id')->label('Dusun')
                                            ->relationship('dusun', 'name')
                                            ->searchable()
                                            ->preload(),
                                        TextInput::make('rt')->label('RT'),
                                        TextInput::make('rw')->label('RW'),
                                        Select::make('citizenship_status')->label('Keberadaan Anggota Keluarga')
                                            ->options([
                                                'Tinggal di rumah/tempat tinggal ini' => 'Tinggal di rumah/tempat tinggal ini',
                                                'Pindah' => 'Pindah',
                                                'Meninggal' => 'Meninggal',
                                                'Tidak tinggal di rumah ini' => 'Tidak tinggal di rumah ini',
                                            ]),
                                        Select::make('has_digital_wallet')->label('Rekening / Dompet Digital Aktif')
                                            ->options([
                                                'Ya' => 'Ya',
                                                'Tidak' => 'Tidak',
                                            ]),
                                        Select::make('status')->label('Status Keaktifan')
                                            ->options([
                                                'Aktif' => 'Aktif',
                                                'Pindah' => 'Pindah',
                                                'Meninggal' => 'Meninggal',
                                            ])
                                            ->required()
                                            ->default('Aktif'),
                                    ]),
                                Textarea::make('address')->label('Alamat Domisili')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
