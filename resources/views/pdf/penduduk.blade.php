<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dataset Kependudukan Publik</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 9px; color: #1e293b; margin: 0; padding: 0; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #059669; padding-bottom: 8px; }
        h1 { font-size: 16px; color: #047857; margin: 0 0 4px 0; font-weight: bold; }
        h2 { font-size: 12px; color: #0f766e; margin: 0 0 4px 0; text-transform: uppercase; }
        p { margin: 0; color: #64748b; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th { background-color: #059669; color: white; text-align: left; padding: 5px 6px; font-size: 9px; font-weight: bold; word-wrap: break-word; }
        td { border-bottom: 1px solid #e2e8f0; padding: 5px 6px; font-size: 8.5px; word-wrap: break-word; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .footer { font-size: 8px; color: #94a3b8; text-align: right; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PEMERINTAH DESA {{ strtoupper($villageName ?? 'Kalamang') }}</h1>
        <h2>{{ strtoupper($datasetTitle ?? 'DATASET KEPENDUDUKAN DESA') }}</h2>
        <p>Portal Open Data Desa {{ $villageName ?? 'Kalamang' }} - Tanggal Unduh: {{ date('d/m/Y H:i') }} (Dianonimkan sesuai UU PDP)</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                @foreach($activeCols as $col)
                    <th>{{ $citizenColumnMap[$col] ?? ucfirst($col) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($citizens as $citizen)
            <tr>
                <td>{{ $no++ }}</td>
                @foreach($activeCols as $col)
                    <td>
                        @switch($col)
                            @case('gender') {{ $citizen->gender ?? '-' }} @break
                            @case('age') {{ $citizen->date_of_birth ? \Carbon\Carbon::parse($citizen->date_of_birth)->age : '-' }} @break
                            @case('marital_status') {{ $citizen->marital_status ?? '-' }} @break
                            @case('family_relation') {{ $citizen->family_relation ?? '-' }} @break
                            @case('education_level') {{ $citizen->education_level ?? '-' }} @break
                            @case('school_participation') {{ $citizen->school_participation ?? '-' }} @break
                            @case('job') {{ $citizen->job ?? '-' }} @break
                            @case('job_status') {{ $citizen->job_status ?? '-' }} @break
                            @case('dusun') {{ $citizen->dusun?->name ?? '-' }} @break
                            @case('rt_rw') RT {{ $citizen->rt ?? '-' }} / RW {{ $citizen->rw ?? '-' }} @break
                            @case('bpjs_status') {{ $citizen->bpjs_status ?? '-' }} @break
                            @case('pip_status') {{ $citizen->pip_status ?? '-' }} @break
                            @case('has_digital_wallet') {{ $citizen->has_digital_wallet ?? '-' }} @break
                            @case('disability_type') {{ $citizen->disability_type ?? '-' }} @break
                            @default -
                        @endswitch
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">Semua data telah dianonimkan sesuai dengan ketentuan UU No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP).</div>
</body>
</html>
