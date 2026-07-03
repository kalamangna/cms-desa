<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        $query = Dataset::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
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
        // Increase memory and time limits for large dataset exports
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        // 1. DATA PENDUDUK
        if ($type === 'penduduk') {
            // CSV
            $fileName = 'data_penduduk_' . date('Ymd_His') . '.csv';
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, [
                    'No', 'Jenis Kelamin', 'Umur', 'Status Perkawinan', 'Hubungan Keluarga',
                    'Tingkat Pendidikan', 'Pekerjaan', 'Dusun', 'RT', 'RW', 'Status BPJS', 'Status PIP'
                ], ';');

                $citizens = \App\Models\Citizen::with('dusun')->where('status', 'Aktif')->get();
                $no = 1;
                foreach ($citizens as $citizen) {
                    $age = $citizen->date_of_birth ? \Carbon\Carbon::parse($citizen->date_of_birth)->age : '-';
                    fputcsv($file, [
                        $no++, $citizen->gender, $age, $citizen->marital_status, $citizen->family_relation,
                        $citizen->education_level, $citizen->job, $citizen->dusun?->name ?? '-',
                        $citizen->rt ?? '-', $citizen->rw ?? '-', $citizen->bpjs_status ?? '-', $citizen->pip_status ?? '-'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
            
        } elseif ($type === 'penduduk-xlsx') {
            // Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = [
                'No', 'Jenis Kelamin', 'Umur', 'Status Perkawinan', 'Hubungan Keluarga',
                'Tingkat Pendidikan', 'Pekerjaan', 'Dusun', 'RT', 'RW', 'Status BPJS', 'Status PIP'
            ];
            $sheet->fromArray($headers, null, 'A1');
            
            $citizens = \App\Models\Citizen::with('dusun')->where('status', 'Aktif')->get();
            $no = 1;
            $rowNum = 2;
            foreach ($citizens as $citizen) {
                $age = $citizen->date_of_birth ? \Carbon\Carbon::parse($citizen->date_of_birth)->age : '-';
                $row = [
                    $no++, $citizen->gender, $age, $citizen->marital_status, $citizen->family_relation,
                    $citizen->education_level, $citizen->job, $citizen->dusun?->name ?? '-',
                    $citizen->rt ?? '-', $citizen->rw ?? '-', $citizen->bpjs_status ?? '-', $citizen->pip_status ?? '-'
                ];
                $sheet->fromArray($row, null, 'A' . $rowNum);
                $rowNum++;
            }
            
            $writer = new Xlsx($spreadsheet);
            $fileName = 'data_penduduk_' . date('Ymd_His') . '.xlsx';
            
            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);

        } elseif ($type === 'penduduk-pdf') {
            // PDF
            $citizens = \App\Models\Citizen::with('dusun')->where('status', 'Aktif')->get();
            $pdf = Pdf::loadView('pdf.penduduk', compact('citizens'))->setPaper('a4', 'landscape');
            return $pdf->download('data_penduduk_' . date('Ymd_His') . '.pdf');

        // 2. DATA KELUARGA
        } elseif ($type === 'keluarga') {
            // CSV
            $fileName = 'data_keluarga_' . date('Ymd_His') . '.csv';
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, [
                    'No', 'Dusun', 'RT', 'RW', 'Bantuan Sosial', 'Status Kepemilikan Rumah'
                ], ';');

                $families = \App\Models\Family::with('dusun')->get();
                $no = 1;
                foreach ($families as $family) {
                    fputcsv($file, [
                        $no++, $family->dusun?->name ?? '-', $family->rt ?? '-', $family->rw ?? '-',
                        $family->assistance_type ?? 'Tidak Ada', $family->ownership_status ?? '-'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } elseif ($type === 'keluarga-xlsx') {
            // Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = [
                'No', 'Dusun', 'RT', 'RW', 'Bantuan Sosial', 'Status Kepemilikan Rumah'
            ];
            $sheet->fromArray($headers, null, 'A1');
            
            $families = \App\Models\Family::with('dusun')->get();
            $no = 1;
            $rowNum = 2;
            foreach ($families as $family) {
                $row = [
                    $no++, $family->dusun?->name ?? '-', $family->rt ?? '-', $family->rw ?? '-',
                    $family->assistance_type ?? 'Tidak Ada', $family->ownership_status ?? '-'
                ];
                $sheet->fromArray($row, null, 'A' . $rowNum);
                $rowNum++;
            }
            
            $writer = new Xlsx($spreadsheet);
            $fileName = 'data_keluarga_' . date('Ymd_His') . '.xlsx';
            
            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);

        } elseif ($type === 'keluarga-pdf') {
            // PDF
            $families = \App\Models\Family::with('dusun')->get();
            $pdf = Pdf::loadView('pdf.keluarga', compact('families'))->setPaper('a4', 'portrait');
            return $pdf->download('data_keluarga_' . date('Ymd_His') . '.pdf');
        }

        abort(404);
    }
}
