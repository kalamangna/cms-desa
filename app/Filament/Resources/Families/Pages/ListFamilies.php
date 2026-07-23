<?php

namespace App\Filament\Resources\Families\Pages;

use App\Filament\Resources\Families\FamilyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Family;
use App\Models\Dusun;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListFamilies extends ListRecords
{
    protected static string $resource = FamilyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Placeholder::make('info')
                        ->label('Petunjuk Penggunaan')
                        ->content('Unggah respon kuesioner Google Form (Keluarga) secara langsung dalam format Excel (.xlsx / .xls).'),
                    Select::make('dusun_id')
                        ->label('Pilih Dusun')
                        ->options(Dusun::pluck('name', 'id'))
                        ->placeholder('Pilih Dusun...')
                        ->searchable()
                        ->preload()
                        ->required(),
                    FileUpload::make('excel_file')
                        ->label('File Excel')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->required()
                        ->directory('temp'),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/public/' . $data['excel_file']);
                    $selectedDusunId = $data['dusun_id'] ?? null;
                    
                    if (!file_exists($filePath)) {
                        Notification::make()->title('File tidak ditemukan.')->danger()->send();
                        return;
                    }

                    try {
                        $spreadsheet = IOFactory::load($filePath);
                        $worksheet = $spreadsheet->getActiveSheet();
                        $rows = $worksheet->toArray(null, true, true, false);
                    } catch (\Exception $e) {
                        Notification::make()->title('Gagal membaca file: ' . $e->getMessage())->danger()->send();
                        return;
                    }

                    if (empty($rows)) {
                        Notification::make()->title('File kosong atau tidak valid.')->danger()->send();
                        return;
                    }

                    $header = array_shift($rows);

                    // Normalize headers
                    $header = array_map(function($h) {
                        return trim(strtolower($h));
                    }, $header);

                    // Validate if it is a citizen sheet uploaded to family form
                    $isCitizenSheet = $this->findColumnIndex($header, ['302. nik anggota', '306. jenis kelamin']) !== false;
                    $isFamilySheet = $this->findColumnIndex($header, ['101. nama kepala keluarga', '201. jenis bangunan']) !== false;
                    
                    if ($isCitizenSheet || !$isFamilySheet) {
                        Notification::make()
                            ->title('Gagal: File Salah / Tertukar!')
                            ->body('Anda mengunggah file data PENDUDUK/INDIVIDU (atau file dengan format salah) di form Keluarga. Harap unggah file data KELUARGA.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    // Map columns dynamically by checking partial string matches
                    $colKkNumber = $this->findColumnIndex($header, ['103. nomor kk', 'nomor kk dari', 'kartu keluarga']);
                    $colHeadName = $this->findColumnIndex($header, ['101. nama kepala', 'nama kepala keluarga']);
                    $colHeadNik = $this->findColumnIndex($header, ['102. nik kepala', 'nik kepala keluarga']);
                    $colAddress = $this->findColumnIndex($header, ['104. alamat lengkap', 'alamat lengkap']);
                    $colMatchesKk = $this->findColumnIndex($header, ['105. apakah alamat', 'alamat tersebut sesuai']);
                    $colBansos = $this->findColumnIndex($header, ['jenis bantuan', 'bantuan apa yang diterima']);
                    
                    $colBuildingType = $this->findColumnIndex($header, ['201. jenis bangunan']);
                    $colOwnership = $this->findColumnIndex($header, ['202.a. status kepemilikan']);
                    $colProof = $this->findColumnIndex($header, ['202.b. bukti kepemilikan']);
                    $colFloorArea = $this->findColumnIndex($header, ['204. luas lantai']);
                    $colFloorMat = $this->findColumnIndex($header, ['205. bahan bangunan utama lantai']);
                    $colWallMat = $this->findColumnIndex($header, ['206. bahan bangunan utama dinding']);
                    $colRoofMat = $this->findColumnIndex($header, ['207. bahan bangunan utama atap']);
                    $colFloorCond = $this->findColumnIndex($header, ['kondisi lantai, dinding', '[lantai]']);
                    $colWallCond = $this->findColumnIndex($header, ['kondisi lantai, dinding', '[dinding]']);
                    $colRoofCond = $this->findColumnIndex($header, ['kondisi lantai, dinding', '[atap]']);
                    
                    $colToilet = $this->findColumnIndex($header, ['209. apakah memiliki fasilitas']);
                    $colCloset = $this->findColumnIndex($header, ['210. apa jenis kloset']);
                    $colFeces = $this->findColumnIndex($header, ['211. di manakah tempat pembuangan']);
                    $colWater = $this->findColumnIndex($header, ['212. sumber air utama', '212. apa sumber air']); // check custom
                    // If not found, manual check from header array:
                    if ($colWater === false) {
                        foreach ($header as $idx => $h) {
                            if (strpos($h, '212.') !== false || strpos($h, 'sumber air utama') !== false) {
                                $colWater = $idx;
                                        break;
                            }
                        }
                    }
                    
                    $colLight = $this->findColumnIndex($header, ['213.a. sumber penerangan']);
                    $colPlnId = $this->findColumnIndex($header, ['213.c. id pelanggan']);
                    
                    $colPlnCost = $this->findColumnIndex($header, ['214. nilai pengeluaran listrik']);
                    $colNetCost = $this->findColumnIndex($header, ['215. nilai pengeluaran pulsa']);
                    $colMemberCount = $this->findColumnIndex($header, ['106.a. berapa jumlah keluarga', 'jumlah keluarga yang tinggal dalam 1 rumah']);
                    $colPlnPower1 = $this->findColumnIndex($header, ['213.b. jika listrik pln', 'berapa daya terpasang', '[meteran 1]']);
                    $colPlnPower2 = $this->findColumnIndex($header, ['213.b. jika listrik pln', 'berapa daya terpasang', '[meteran 2]']);
                    $colPlnPower3 = $this->findColumnIndex($header, ['213.b. jika listrik pln', 'berapa daya terpasang', '[meteran 3]']);
                    
                    $colPhotoFront = $this->findColumnIndex($header, ['216.a. foto tampak depan']);
                    $colPhotoLiving = $this->findColumnIndex($header, ['216.b. foto ruang tamu']);
                    $colPhotoBath = $this->findColumnIndex($header, ['216.c. foto kamar mandi']);
                    $colPhotoKk = $this->findColumnIndex($header, ['foto kartu keluarga']);
                    
                    $colGas3 = $this->findColumnIndex($header, ['217.a. jumlah tabung gas 3']);
                    $colGas5 = $this->findColumnIndex($header, ['217.b. jumlah tabung gas 5']);
                    $colFridge = $this->findColumnIndex($header, ['217.c. jumlah lemari']);
                    $colAc = $this->findColumnIndex($header, ['217.d. jumlah ac']);
                    $colGold = $this->findColumnIndex($header, ['217.e. jumlah emas']);
                    $colComp = $this->findColumnIndex($header, ['217.f. jumlah komputer']);
                    $colMotor = $this->findColumnIndex($header, ['217.g. jumlah sepeda motor']);
                    $colMotorVal = $this->findColumnIndex($header, ['217.h. total nilai aset sepeda']);
                    $colCar = $this->findColumnIndex($header, ['217.i. jumlah mobil']);
                    $colCarVal = $this->findColumnIndex($header, ['217.j. total nilai aset mobil']);
                    $colLand = $this->findColumnIndex($header, ['217.k. jumlah tanah']);
                    $colLandVal = $this->findColumnIndex($header, ['217.l. total nilai harga jual tanah']);
                    $colBld = $this->findColumnIndex($header, ['217.m. jumlah rumah']);
                    $colBldVal = $this->findColumnIndex($header, ['217.n. total nilai harga jual rumah']);
                    $colCow = $this->findColumnIndex($header, ['217.o. jumlah sapi']);
                    $colGoat = $this->findColumnIndex($header, ['217.p. jumlah kambing']);
                    $colBuffalo = $this->findColumnIndex($header, ['217.q. jumlah kerbau']);
                    $colNotes = $this->findColumnIndex($header, ['catatan']);

                    if ($colKkNumber === false) {
                        Notification::make()->title('Format file salah.')->body('Nomor KK (Kolom 103) wajib ditemukan.')->danger()->send();
                        return;
                    }

                    \Illuminate\Support\Facades\DB::beginTransaction();

                    try {
                        $rowCount = 0;
                        foreach ($rows as $index => $row) {
                            if (count($row) <= $colKkNumber) continue;

                            $kkNumber = trim($row[$colKkNumber]);
                            if (empty($kkNumber) || strtolower($kkNumber) === 'none' || strlen($kkNumber) < 5) continue;

                            // Parse address details
                            $address = $colAddress !== false ? trim($row[$colAddress]) : '';
                            $rt = null; $rw = null; $dusunId = $selectedDusunId;

                            // Smart parse RT/RW/Dusun from address string
                            // e.g. "RT 005 RW 003 Dusun Data" or "005/003/Dusun Data"
                            if (!empty($address)) {
                                if (preg_match('/rt[\.\s]*(\d+)/i', $address, $matches)) {
                                    $rt = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                }
                                if (preg_match('/rw[\.\s]*(\d+)/i', $address, $matches)) {
                                    $rw = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                }
                                if (!$rt && !$rw && preg_match('/(\d+)\/(\d+)/', $address, $matches)) {
                                    $rt = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                    $rw = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
                                }

                                // Match Dusun from address text if not selected
                                if (!$dusunId) {
                                    $dusunList = Dusun::all();
                                    foreach ($dusunList as $dus) {
                                        if (stripos($address, $dus->name) !== false) {
                                            $dusunId = $dus->id;
                                            break;
                                        }
                                    }
                                }
                            }

                            $power1 = $colPlnPower1 !== false && isset($row[$colPlnPower1]) && trim($row[$colPlnPower1]) !== '' ? trim($row[$colPlnPower1]) : null;
                            $power2 = $colPlnPower2 !== false && isset($row[$colPlnPower2]) && trim($row[$colPlnPower2]) !== '' ? trim($row[$colPlnPower2]) : null;
                            $power3 = $colPlnPower3 !== false && isset($row[$colPlnPower3]) && trim($row[$colPlnPower3]) !== '' ? trim($row[$colPlnPower3]) : null;
                            $powers = array_filter([$power1, $power2, $power3]);
                            $electricityPower = count($powers) > 0 ? implode(', ', $powers) : null;

                            $dataToSave = [
                                'head_name' => $colHeadName !== false ? $this->cleanName($row[$colHeadName]) : null,
                                'head_nik' => $colHeadNik !== false ? trim($row[$colHeadNik]) : null,
                                'address' => $address,
                                'dusun_id' => $dusunId,
                                'rt' => $rt,
                                'rw' => $rw,
                                'address_matches_kk' => $colMatchesKk !== false ? $this->parseYesNo($row[$colMatchesKk]) : 0,
                                'assistance_type' => $colBansos !== false ? $this->parseAssistanceType($row[$colBansos]) : null,
                                'family_member_count' => $colMemberCount !== false && isset($row[$colMemberCount]) && trim($row[$colMemberCount]) !== '' ? intval(trim($row[$colMemberCount])) : 1,
                                'building_type' => $colBuildingType !== false ? trim($row[$colBuildingType]) : null,
                                'ownership_status' => $colOwnership !== false ? $this->parseOwnershipStatus($row[$colOwnership]) : null,
                                'ownership_proof' => $colProof !== false ? trim($row[$colProof]) : null,
                                'floor_area' => $colFloorArea !== false ? floatval(trim($row[$colFloorArea])) : null,
                                'floor_material' => $colFloorMat !== false ? trim($row[$colFloorMat]) : null,
                                'wall_material' => $colWallMat !== false ? trim($row[$colWallMat]) : null,
                                'roof_material' => $colRoofMat !== false ? trim($row[$colRoofMat]) : null,
                                'floor_condition' => $colFloorCond !== false ? trim($row[$colFloorCond]) : null,
                                'wall_condition' => $colWallCond !== false ? trim($row[$colWallCond]) : null,
                                'roof_condition' => $colRoofCond !== false ? trim($row[$colRoofCond]) : null,
                                'toilet_facility' => $colToilet !== false ? trim($row[$colToilet]) : null,
                                'closet_type' => $colCloset !== false ? trim($row[$colCloset]) : null,
                                'feces_disposal' => $colFeces !== false ? trim($row[$colFeces]) : null,
                                'water_source' => $colWater !== false ? trim($row[$colWater]) : null,
                                'lighting_source' => $colLight !== false ? trim($row[$colLight]) : null,
                                'electricity_power' => $electricityPower,
                                'electricity_id' => $colPlnId !== false ? trim($row[$colPlnId]) : null,
                                'electricity_cost' => $colPlnCost !== false ? $this->cleanNumeric(trim($row[$colPlnCost])) : 0,
                                'internet_cost' => $colNetCost !== false ? $this->cleanNumeric(trim($row[$colNetCost])) : 0,
                                'photo_front' => $colPhotoFront !== false ? trim($row[$colPhotoFront]) : null,
                                'photo_living_room' => $colPhotoLiving !== false ? trim($row[$colPhotoLiving]) : null,
                                'photo_bathroom' => $colPhotoBath !== false ? trim($row[$colPhotoBath]) : null,
                                'photo_kk' => $colPhotoKk !== false ? trim($row[$colPhotoKk]) : null,
                                'gas_3kg_count' => $colGas3 !== false ? intval(trim($row[$colGas3])) : 0,
                                'gas_5kg_count' => $colGas5 !== false ? intval(trim($row[$colGas5])) : 0,
                                'refrigerator_count' => $colFridge !== false ? intval(trim($row[$colFridge])) : 0,
                                'ac_count' => $colAc !== false ? intval(trim($row[$colAc])) : 0,
                                'jewelry_count' => $colGold !== false ? intval(trim($row[$colGold])) : 0,
                                'computer_count' => $colComp !== false ? intval(trim($row[$colComp])) : 0,
                                'motorcycle_count' => $colMotor !== false ? intval(trim($row[$colMotor])) : 0,
                                'motorcycle_value' => $colMotorVal !== false ? $this->cleanNumeric(trim($row[$colMotorVal])) : 0,
                                'car_count' => $colCar !== false ? intval(trim($row[$colCar])) : 0,
                                'car_value' => $colCarVal !== false ? $this->cleanNumeric(trim($row[$colCarVal])) : 0,
                                'other_land_count' => $colLand !== false ? intval(trim($row[$colLand])) : 0,
                                'other_land_value' => $colLandVal !== false ? $this->cleanNumeric(trim($row[$colLandVal])) : 0,
                                'other_building_count' => $colBld !== false ? intval(trim($row[$colBld])) : 0,
                                'other_building_value' => $colBldVal !== false ? $this->cleanNumeric(trim($row[$colBldVal])) : 0,
                                'cow_count' => $colCow !== false ? intval(trim($row[$colCow])) : 0,
                                'goat_count' => $colGoat !== false ? intval(trim($row[$colGoat])) : 0,
                                'buffalo_count' => $colBuffalo !== false ? intval(trim($row[$colBuffalo])) : 0,
                                'notes' => $colNotes !== false ? trim($row[$colNotes]) : null,
                            ];

                            $family = Family::withTrashed()->where('kk_number', $kkNumber)->first();
                            if ($family) {
                                $updateData = [];
                                foreach ($dataToSave as $key => $value) {
                                    if ($value !== null && $value !== '') {
                                        $updateData[$key] = $value;
                                    }
                                }
                                $family->fill($updateData);
                                if ($family->trashed()) {
                                    $family->restore();
                                } else {
                                    $family->save();
                                }
                            } else {
                                Family::create(array_merge(['kk_number' => $kkNumber], $dataToSave));
                            }
                            $rowCount++;

                            // Also create shell family records for secondary KKs living in the same home (106.b)
                            // so that individual residents belonging to secondary KKs are correctly linked to family_id
                            foreach ($header as $colIdx => $colNameStr) {
                                if (strpos($colNameStr, '106.b.') !== false && isset($row[$colIdx])) {
                                    $secKk = trim($row[$colIdx]);
                                    if (!empty($secKk) && strlen($secKk) >= 10 && is_numeric($secKk)) {
                                        $secFam = Family::withTrashed()->where('kk_number', $secKk)->first();
                                        if (!$secFam) {
                                            Family::create([
                                                'kk_number' => $secKk,
                                                'address' => $address,
                                                'dusun_id' => $dusunId,
                                                'rt' => $rt,
                                                'rw' => $rw,
                                                'notes' => 'Anggota KK tambahan dalam 1 rumah (106.b)',
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        \Illuminate\Support\Facades\DB::commit();

                        foreach (\App\Models\StatisticCategory::where('mapping_table', 'families')->get() as $cat) {
                            $cat->save();
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\DB::rollBack();

                        $rowNumber = isset($index) ? ($index + 2) : 'Tidak diketahui';
                        $errorMessage = $e->getMessage();

                        @unlink($filePath);

                        Notification::make()
                            ->title('Import Gagal (Rollback)')
                            ->body("Terjadi kesalahan pada Baris #{$rowNumber}: {$errorMessage}. Seluruh transaksi dibatalkan.")
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    @unlink($filePath);

                    Notification::make()
                        ->title('Import Sukses!')
                        ->body("Berhasil mengimpor/memperbarui $rowCount profil keluarga.")
                        ->success()
                        ->send();
                }),
        ];
    }

    private function parseYesNo(?string $val): int
    {
        if ($val === null) return 0;
        $clean = strtolower(trim($val));
        if (empty($clean)) return 0;
        
        $isYes = in_array($clean, ['ya', 'yes', 'true', '1', 'sesuai', 'ada']) 
            || strpos($clean, 'ya') === 0 
            || strpos($clean, 'sesuai') === 0;

        return $isYes ? 1 : 0;
    }

    private function parseOwnershipStatus(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'milik sendiri') !== false || strpos($clean, 'sendiri') !== false) {
            return 'Milik sendiri';
        } elseif (strpos($clean, 'sewa') !== false || strpos($clean, 'kontrak') !== false) {
            return 'Sewa/Kontrak';
        }
        return 'Bebas Sewa/Dinas/Lainnya';
    }

    private function parseAssistanceType(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean) || in_array($clean, ['tidak ada', 'tidak', 'none', '-'])) {
            return 'Tidak Ada';
        }
        return trim($val);
    }

    private function findColumnIndex(array $header, array $needles): int|bool
    {
        foreach ($header as $idx => $colName) {
            foreach ($needles as $needle) {
                if (strpos($colName, $needle) !== false) {
                    return $idx;
                }
            }
        }
        return false;
    }

    private function cleanName(?string $name): ?string
    {
        if ($name === null) return null;
        $name = trim($name);
        if (empty($name)) return null;

        // Fix spacing around dots (e.g., "A.ismail" -> "A. Ismail")
        $name = preg_replace('/([a-zA-Z])\.(?=[a-zA-Z])/', '$1. ', $name);

        // Convert to UPPERCASE
        return mb_strtoupper($name);
    }

    private function cleanNumeric(string $val): int
    {
        $val = strtolower(trim($val));
        if (empty($val) || in_array($val, ['tidak ada', 'none', '-', '?'])) return 0;

        // Support shortcuts like "4,8jt" or "4.8 jt"
        if (strpos($val, 'jt') !== false) {
            $numPart = preg_replace('/[^0-9\.,]/', '', str_replace('jt', '', $val));
            $numPart = str_replace(',', '.', $numPart);
            return intval(floatval($numPart) * 1000000);
        }

        // Clean normal formatted currency like 3,000,000.00
        $clean = preg_replace('/[^0-9]/', '', explode('.', $val)[0]);
        return empty($clean) ? 0 : intval($clean);
    }
}
