<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Penduduk Anonim</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #334155; }
        h1 { font-size: 18px; color: #047857; margin-bottom: 2px; }
        p { margin-top: 0; color: #64748b; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #059669; color: white; text-align: left; padding: 6px; font-weight: bold; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .meta { font-size: 9px; color: #94a3b8; text-align: right; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Data Penduduk (Kependudukan)</h1>
    <p>Diunduh dari Portal Open Data - {{ date('d M Y H:i') }} (Status: Anonim/Tanpa Data Pribadi)</p>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 12%">Gender</th>
                <th style="width: 8%">Umur</th>
                <th style="width: 15%">Status Perkawinan</th>
                <th style="width: 15%">Hubungan</th>
                <th style="width: 15%">Pendidikan</th>
                <th style="width: 15%">Pekerjaan</th>
                <th>Dusun</th>
                <th style="width: 6%">RT</th>
                <th style="width: 6%">RW</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($citizens as $citizen)
            @php $age = $citizen->date_of_birth ? \Carbon\Carbon::parse($citizen->date_of_birth)->age : '-'; @endphp
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $citizen->gender }}</td>
                <td>{{ $age }}</td>
                <td>{{ $citizen->marital_status }}</td>
                <td>{{ $citizen->family_relation }}</td>
                <td>{{ $citizen->education_level }}</td>
                <td>{{ $citizen->job }}</td>
                <td>{{ $citizen->dusun?->name ?? '-' }}</td>
                <td>{{ $citizen->rt ?? '-' }}</td>
                <td>{{ $citizen->rw ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="meta">Semua data telah dianonimkan sesuai dengan ketentuan UU PDP.</div>
</body>
</html>
