<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Keluarga Anonim</title>
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
    <h1>Data Keluarga (Rumah Tangga)</h1>
    <p>Diunduh dari Portal Open Data - {{ date('d M Y H:i') }} (Status: Anonim/Tanpa Data Pribadi)</p>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Dusun</th>
                <th style="width: 10%">RT</th>
                <th style="width: 10%">RW</th>
                <th style="width: 25%">Bantuan Sosial</th>
                <th style="width: 25%">Status Kepemilikan Rumah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($families as $family)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $family->dusun?->name ?? '-' }}</td>
                <td>{{ $family->rt ?? '-' }}</td>
                <td>{{ $family->rw ?? '-' }}</td>
                <td>{{ $family->assistance_type ?? 'Tidak Ada' }}</td>
                <td>{{ $family->ownership_status ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="meta">Semua data telah dianonimkan sesuai dengan ketentuan UU PDP.</div>
</body>
</html>
