<?php

namespace App\Filament\Resources\StatisticData\Pages;

use App\Filament\Resources\StatisticData\StatisticDataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use App\Models\StatisticCategory;
use App\Models\StatisticIndicator;
use App\Models\StatisticData;
use Illuminate\Support\Str;

class ListStatisticData extends ListRecords
{
    protected static string $resource = StatisticDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importCsv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Placeholder::make('info')
                        ->label('Panduan Format CSV')
                        ->content('File CSV wajib memiliki baris header dengan kolom berikut (tidak harus berurutan):
                                  "Kategori", "Indikator", "Tahun", "Nilai" (atau "Jumlah").
                                  Contoh baris data:
                                  "Kependudukan", "Jumlah Laki-laki", "2024", "1540"'),
                    FileUpload::make('csv_file')
                        ->label('Pilih File CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->required()
                        ->directory('temp'),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/public/' . $data['csv_file']);
                    
                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('File tidak ditemukan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $file = fopen($filePath, 'r');
                    $header = fgetcsv($file); // Read headers

                    if (!$header) {
                        Notification::make()
                            ->title('File CSV Kosong.')
                            ->danger()
                            ->send();
                        fclose($file);
                        return;
                    }

                    // Normalize header names: lowercase, trim
                    $header = array_map(function($h) {
                        return trim(strtolower($h));
                    }, $header);

                    // Find positions of columns: category, indicator, year, value
                    $colCategory = array_search('kategori', $header) !== false ? array_search('kategori', $header) : array_search('category', $header);
                    $colIndicator = array_search('indikator', $header) !== false ? array_search('indikator', $header) : array_search('indicator', $header);
                    $colYear = array_search('tahun', $header) !== false ? array_search('tahun', $header) : array_search('year', $header);
                    $colValue = array_search('nilai', $header) !== false ? array_search('nilai', $header) : (array_search('jumlah', $header) !== false ? array_search('jumlah', $header) : array_search('value', $header));

                    if ($colCategory === false || $colIndicator === false || $colYear === false || $colValue === false) {
                        Notification::make()
                            ->title('Format CSV salah.')
                            ->body('CSV wajib memiliki header kolom: Kategori, Indikator, Tahun, Nilai.')
                            ->danger()
                            ->send();
                        fclose($file);
                        return;
                    }

                    $rowCount = 0;
                    while (($row = fgetcsv($file)) !== false) {
                        // Skip empty rows
                        if (count($row) <= max($colCategory, $colIndicator, $colYear, $colValue)) {
                            continue;
                        }

                        $catName = trim($row[$colCategory]);
                        $indName = trim($row[$colIndicator]);
                        $yearVal = trim($row[$colYear]);
                        $valStr = trim($row[$colValue]);

                        if ($catName === '' || $indName === '' || $yearVal === '') {
                            continue;
                        }

                        $year = intval($yearVal);
                        $value = floatval($valStr);

                        // Find or create Category
                        $category = StatisticCategory::firstOrCreate(
                            ['name' => $catName],
                            ['slug' => Str::slug($catName)]
                        );

                        // Find or create Indicator
                        $indicator = StatisticIndicator::firstOrCreate(
                            [
                                'statistic_category_id' => $category->id,
                                'name' => $indName
                            ],
                            ['unit' => 'Orang'] // default unit
                        );

                        // Update or create Statistic Data value
                        StatisticData::updateOrCreate(
                            [
                                'statistic_indicator_id' => $indicator->id,
                                'year' => $year
                            ],
                            ['value' => $value]
                        );

                        $rowCount++;
                    }

                    fclose($file);
                    
                    // Delete temp file
                    @unlink($filePath);

                    Notification::make()
                        ->title('Import Berhasil!')
                        ->body("Berhasil mengimpor $rowCount baris data statistik.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
