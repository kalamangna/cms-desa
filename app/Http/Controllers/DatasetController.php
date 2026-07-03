<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;

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
        if ($type === 'penduduk') {
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
                // UTF-8 BOM
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header columns (Fully Anonymous - No PII)
                fputcsv($file, [
                    'No',
                    'Jenis Kelamin',
                    'Umur',
                    'Status Perkawinan',
                    'Hubungan Keluarga',
                    'Tingkat Pendidikan',
                    'Pekerjaan',
                    'Dusun',
                    'RT',
                    'RW',
                    'Status BPJS',
                    'Status PIP'
                ], ';');

                $citizens = \App\Models\Citizen::with('dusun')->where('status', 'Aktif')->get();
                $no = 1;
                foreach ($citizens as $citizen) {
                    $age = $citizen->date_of_birth ? \Carbon\Carbon::parse($citizen->date_of_birth)->age : '-';
                    fputcsv($file, [
                        $no++,
                        $citizen->gender,
                        $age,
                        $citizen->marital_status,
                        $citizen->family_relation,
                        $citizen->education_level,
                        $citizen->job,
                        $citizen->dusun?->name ?? '-',
                        $citizen->rt ?? '-',
                        $citizen->rw ?? '-',
                        $citizen->bpjs_status ?? '-',
                        $citizen->pip_status ?? '-'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } elseif ($type === 'keluarga') {
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
                // UTF-8 BOM
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header columns (Fully Anonymous - No PII)
                fputcsv($file, [
                    'No',
                    'Dusun',
                    'RT',
                    'RW',
                    'Bantuan Sosial',
                    'Status Kepemilikan Rumah'
                ], ';');

                $families = \App\Models\Family::with('dusun')->get();
                $no = 1;
                foreach ($families as $family) {
                    fputcsv($file, [
                        $no++,
                        $family->dusun?->name ?? '-',
                        $family->rt ?? '-',
                        $family->rw ?? '-',
                        $family->assistance_type ?? 'Tidak Ada',
                        $family->ownership_status ?? '-'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        abort(404);
    }
}
