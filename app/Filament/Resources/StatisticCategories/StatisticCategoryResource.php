<?php

namespace App\Filament\Resources\StatisticCategories;

use App\Filament\Resources\StatisticCategories\Pages\ListStatisticCategories;
use App\Filament\Resources\StatisticCategories\Pages\CreateStatisticCategory;
use App\Filament\Resources\StatisticCategories\Pages\EditStatisticCategory;
use App\Models\StatisticCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class StatisticCategoryResource extends Resource
{
    protected static ?string $model = StatisticCategory::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Master';

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return 'Kategori Statistik';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kategori Statistik';
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('mapping_table')
                    ->label('Sumber Data')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'citizens' => 'Data Penduduk (Individu)',
                        'families' => 'Data Keluarga',
                        default => $state,
                    }),
                IconColumn::make('is_active')
                    ->label('Publik')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('indicators_count')
                    ->label('Jumlah Indikator')
                    ->counts('indicators'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStatisticCategories::route('/'),
            'create' => CreateStatisticCategory::route('/create'),
            'edit' => EditStatisticCategory::route('/{record}/edit'),
        ];
    }
}
