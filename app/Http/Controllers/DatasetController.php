<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\Dataset;
use App\Models\Family;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $query = Dataset::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('source', 'like', "%{$search}%");
            });
        }

        $datasets = $query->paginate(10)->withQueryString();

        return view('datasets.index', compact('datasets'));
    }

    public function download($type)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        // Cari dataset berdasarkan ID atau Slug
        $dataset = null;
        $format = 'csv';

        if (str_ends_with($type, '-xlsx')) {
            $format = 'xlsx';
            $cleanKey = str_replace('-xlsx', '', $type);
        } else {
            $cleanKey = $type;
        }

        if (is_numeric($cleanKey)) {
            $dataset = Dataset::find($cleanKey);
        } else {
            $dataset = Dataset::where('slug', $cleanKey)
                ->orWhere('slug', 'LIKE', "%{$cleanKey}%")
                ->first();
        }

        $sourceTable = $dataset?->source_table ?? (str_contains($cleanKey, 'keluarga') ? 'families' : 'citizens');
        $selectedColumns = $dataset?->selected_columns;
        $datasetTitle = $dataset?->title ?? ($sourceTable === 'families' ? 'Data Kelompok Keluarga Desa' : 'Data Kependudukan Desa');

        // Ambil Nama Desa dari Pengaturan Website
        $villageName = Setting::where('key', 'village_name')->value('value') ?? 'Kalamang';
        $villageSlug = Str::slug($villageName);
        if (empty($villageSlug)) {
            $villageSlug = 'desa';
        }

        $titleSlug = Str::slug($datasetTitle, '_');
        if (empty($titleSlug)) {
            $titleSlug = 'dataset';
        }

        $dateStr = date('Ymd_His');
        $baseFileName = "{$titleSlug}_desa_{$villageSlug}_{$dateStr}";

        // PETA NAMA KOLOM TERJEMAHAN
        $citizenColumnMap = [
            'gender' => 'Jenis Kelamin',
            'age' => 'Umur',
            'marital_status' => 'Status Perkawinan',
            'family_relation' => 'Hubungan Keluarga',
            'education_level' => 'Tingkat Pendidikan',
            'school_participation' => 'Partisipasi Sekolah',
            'job' => 'Pekerjaan Utama',
            'job_status' => 'Status Pekerjaan',
            'dusun' => 'Nama Dusun',
            'rt_rw' => 'RT / RW',
            'bpjs_status' => 'Status BPJS',
            'pip_status' => 'Status PIP',
            'has_digital_wallet' => 'Dompet Digital',
            'disability_type' => 'Disabilitas',
        ];

        $familyColumnMap = [
            'dusun' => 'Nama Dusun',
            'rt_rw' => 'RT / RW',
            'assistance_type' => 'Jenis Bansos',
            'ownership_status' => 'Status Rumah',
            'house_condition' => 'Kondisi Hunian',
            'building_type' => 'Jenis Bangunan',
            'water_source' => 'Sumber Air Bersih',
            'sanitation_type' => 'Fasilitas Sanitasi',
            'toilet_facility' => 'Fasilitas MCK',
            'closet_type' => 'Jenis Kloset',
            'feces_disposal' => 'Pembuangan Tinja',
            'electricity_power' => 'Daya Listrik',
            'lighting_source' => 'Sumber Penerangan',
            'livestock' => 'Kepemilikan Ternak',
        ];

        // 1. DATASET BERBASIS CITIZEN (KEPENDUDUKAN)
        if ($sourceTable === 'citizens') {
            $activeCols = is_array($selectedColumns) && count($selectedColumns) > 0
                ? $selectedColumns
                : ['gender', 'age', 'marital_status', 'family_relation', 'education_level', 'job', 'dusun'];

            $headers = ['No'];
            foreach ($activeCols as $col) {
                $headers[] = $citizenColumnMap[$col] ?? ucfirst($col);
            }

            if ($format === 'csv') {
                $fileName = "{$baseFileName}.csv";
                $resHeaders = [
                    'Content-type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => "attachment; filename=$fileName",
                    'Pragma' => 'no-cache',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                ];

                $callback = function () use ($activeCols, $headers) {
                    $file = fopen('php://output', 'w');
                    // UTF-8 BOM untuk kompatibilitas otomatis Microsoft Excel & R/Python
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                    // CSV MURNI (Langsung Header & Data sesuai standar Data Science)
                    fputcsv($file, $headers, ';');

                    $query = Citizen::with('dusun')->where('status', 'Aktif');

                    $no = 1;
                    foreach ($query->cursor() as $citizen) {
                        $row = [$no++];
                        foreach ($activeCols as $col) {
                            $row[] = match ($col) {
                                'gender' => $citizen->gender ?? '-',
                                'age' => $citizen->date_of_birth ? $citizen->date_of_birth->age : '-',
                                'marital_status' => $citizen->marital_status ?? '-',
                                'family_relation' => $citizen->family_relation ?? '-',
                                'education_level' => $citizen->education_level ?? '-',
                                'school_participation' => $citizen->school_participation ?? '-',
                                'job' => $citizen->job ?? '-',
                                'job_status' => $citizen->job_status ?? '-',
                                'dusun' => $citizen->dusun?->name ?? '-',
                                'rt_rw' => "RT {$citizen->rt} / RW {$citizen->rw}",
                                'bpjs_status' => $citizen->bpjs_status ?? '-',
                                'pip_status' => $citizen->pip_status ? 'Ya' : 'Tidak',
                                'has_digital_wallet' => $citizen->has_digital_wallet ?? '-',
                                'disability_type' => $citizen->disability_type ?? '-',
                                default => '-',
                            };
                        }
                        fputcsv($file, $row, ';');
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $resHeaders);

            } elseif ($format === 'xlsx') {
                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Dataset Kependudukan');

                // KOP HEADER EXCEL RAPI
                $sheet->setCellValue('A1', 'PEMERINTAH DESA '.strtoupper($villageName));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('047857'));

                $sheet->setCellValue('A2', 'DATASET: '.strtoupper($datasetTitle).' (Tahun 2026)');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('0F766E'));

                $sheet->setCellValue('A3', 'Portal Open Data Desa '.$villageName.' | Diunduh: '.date('d/m/Y H:i').' | Dianonimkan Sesuai UU PDP');
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->setColor(new Color('64748B'));

                // HEADER TABEL (BARIS 5)
                $sheet->fromArray($headers, null, 'A5');
                $lastColumnLetter = Coordinate::stringFromColumnIndex(count($headers));

                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                ];
                $sheet->getStyle("A5:{$lastColumnLetter}5")->applyFromArray($headerStyle);
                $sheet->getRowDimension(5)->setRowHeight(26);

                $citizens = Citizen::with('dusun')->where('status', 'Aktif')->get();

                $no = 1;
                $rowNum = 6;
                foreach ($citizens as $citizen) {
                    $row = [$no++];
                    foreach ($activeCols as $col) {
                        $row[] = match ($col) {
                            'gender' => $citizen->gender ?? '-',
                            'age' => $citizen->date_of_birth ? Carbon::parse($citizen->date_of_birth)->age : '-',
                            'marital_status' => $citizen->marital_status ?? '-',
                            'family_relation' => $citizen->family_relation ?? '-',
                            'education_level' => $citizen->education_level ?? '-',
                            'school_participation' => $citizen->school_participation ?? '-',
                            'job' => $citizen->job ?? '-',
                            'job_status' => $citizen->job_status ?? '-',
                            'dusun' => $citizen->dusun?->name ?? '-',
                            'rt_rw' => "RT {$citizen->rt} / RW {$citizen->rw}",
                            'bpjs_status' => $citizen->bpjs_status ?? '-',
                            'pip_status' => $citizen->pip_status ? 'Ya' : 'Tidak',
                            'has_digital_wallet' => $citizen->has_digital_wallet ?? '-',
                            'disability_type' => $citizen->disability_type ?? '-',
                            default => '-',
                        };
                    }
                    $sheet->fromArray($row, null, 'A'.$rowNum);

                    // Zebra striping untuk baris genap
                    if ($rowNum % 2 === 0) {
                        $sheet->getStyle("A{$rowNum}:{$lastColumnLetter}{$rowNum}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F8FAFC');
                    }

                    // Format alignment kolom No
                    $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $rowNum++;
                }

                // LEBAR KOLOM EXCEL (Kolom A khusus No = 8, Kolom B dst Auto-Size)
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(8);

                for ($colIdx = 2; $colIdx <= count($headers); $colIdx++) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                }

                $writer = new Xlsx($spreadsheet);
                $fileName = "{$baseFileName}.xlsx";

                return response()->streamDownload(function () use ($writer) {
                    $writer->save('php://output');
                }, $fileName, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0',
                ]);
            }
        }

        // 2. DATASET BERBASIS FAMILY (KELUARGA)
        if ($sourceTable === 'families') {
            $activeCols = is_array($selectedColumns) && count($selectedColumns) > 0
                ? $selectedColumns
                : ['dusun', 'rt_rw', 'assistance_type', 'ownership_status', 'water_source'];

            $headers = ['No'];
            foreach ($activeCols as $col) {
                $headers[] = $familyColumnMap[$col] ?? ucfirst($col);
            }

            if ($format === 'csv') {
                $fileName = "{$baseFileName}.csv";
                $resHeaders = [
                    'Content-type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => "attachment; filename=$fileName",
                    'Pragma' => 'no-cache',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                ];

                $callback = function () use ($activeCols, $headers) {
                    $file = fopen('php://output', 'w');
                    // UTF-8 BOM untuk kompatibilitas otomatis Microsoft Excel & R/Python
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                    // CSV MURNI (Langsung Header & Data sesuai standar Data Science)
                    fputcsv($file, $headers, ';');

                    $query = Family::with('dusun');

                    $no = 1;
                    foreach ($query->cursor() as $family) {
                        $row = [$no++];
                        foreach ($activeCols as $col) {
                            $row[] = match ($col) {
                                'dusun' => $family->dusun?->name ?? '-',
                                'rt_rw' => "RT {$family->rt} / RW {$family->rw}",
                                'assistance_type' => $family->assistance_type ?? 'Tidak Ada',
                                'ownership_status' => $family->ownership_status ?? '-',
                                'house_condition' => $family->house_condition ?? '-',
                                'building_type' => $family->building_type ?? '-',
                                'water_source' => $family->water_source ?? '-',
                                'sanitation_type' => $family->sanitation_type ?? '-',
                                'toilet_facility' => $family->toilet_facility ?? '-',
                                'closet_type' => $family->closet_type ?? '-',
                                'feces_disposal' => $family->feces_disposal ?? '-',
                                'electricity_power' => $family->electricity_power ?? '-',
                                'lighting_source' => $family->lighting_source ?? '-',
                                'livestock' => $family->livestock ?? '-',
                                default => '-',
                            };
                        }
                        fputcsv($file, $row, ';');
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $resHeaders);

            } elseif ($format === 'xlsx') {
                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Dataset Profil Keluarga');

                // KOP HEADER EXCEL RAPI
                $sheet->setCellValue('A1', 'PEMERINTAH DESA '.strtoupper($villageName));
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('047857'));

                $sheet->setCellValue('A2', 'DATASET: '.strtoupper($datasetTitle).' (Tahun 2026)');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('0F766E'));

                $sheet->setCellValue('A3', 'Portal Open Data Desa '.$villageName.' | Diunduh: '.date('d/m/Y H:i').' | Dianonimkan Sesuai UU PDP');
                $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->setColor(new Color('64748B'));

                // HEADER TABEL (BARIS 5)
                $sheet->fromArray($headers, null, 'A5');
                $lastColumnLetter = Coordinate::stringFromColumnIndex(count($headers));

                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                ];
                $sheet->getStyle("A5:{$lastColumnLetter}5")->applyFromArray($headerStyle);
                $sheet->getRowDimension(5)->setRowHeight(26);

                $families = Family::with('dusun')->get();

                $no = 1;
                $rowNum = 6;
                foreach ($families as $family) {
                    $row = [$no++];
                    foreach ($activeCols as $col) {
                        $row[] = match ($col) {
                            'dusun' => $family->dusun?->name ?? '-',
                            'rt_rw' => "RT {$family->rt} / RW {$family->rw}",
                            'assistance_type' => $family->assistance_type ?? 'Tidak Ada',
                            'ownership_status' => $family->ownership_status ?? '-',
                            'house_condition' => $family->house_condition ?? '-',
                            'building_type' => $family->building_type ?? '-',
                            'water_source' => $family->water_source ?? '-',
                            'sanitation_type' => $family->sanitation_type ?? '-',
                            'toilet_facility' => $family->toilet_facility ?? '-',
                            'closet_type' => $family->closet_type ?? '-',
                            'feces_disposal' => $family->feces_disposal ?? '-',
                            'electricity_power' => $family->electricity_power ?? '-',
                            'lighting_source' => $family->lighting_source ?? '-',
                            'livestock' => $family->livestock ?? '-',
                            default => '-',
                        };
                    }
                    $sheet->fromArray($row, null, 'A'.$rowNum);

                    // Zebra striping untuk baris genap
                    if ($rowNum % 2 === 0) {
                        $sheet->getStyle("A{$rowNum}:{$lastColumnLetter}{$rowNum}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F8FAFC');
                    }

                    // Format alignment kolom No
                    $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $rowNum++;
                }

                // LEBAR KOLOM EXCEL (Kolom A khusus No = 8, Kolom B dst Auto-Size)
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(8);

                for ($colIdx = 2; $colIdx <= count($headers); $colIdx++) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                }

                $writer = new Xlsx($spreadsheet);
                $fileName = "{$baseFileName}.xlsx";

                return response()->streamDownload(function () use ($writer) {
                    $writer->save('php://output');
                }, $fileName, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0',
                ]);
            }
        }

        abort(404);
    }
}
