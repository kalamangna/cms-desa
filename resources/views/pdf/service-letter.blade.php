<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $letterTitle }}</title>
    <style>
        @page {
            margin: 1.6cm 2.2cm 2.4cm 2.2cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11.5pt;
            line-height: 1.45;
            color: #111827;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo {
            width: 72px;
            vertical-align: middle;
        }
        .header-logo img {
            width: 66px;
            height: auto;
            max-height: 76px;
            display: block;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text .gov-line {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.25;
        }
        .header-text .village-line {
            font-size: 14.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 2px 0;
            line-height: 1.2;
        }
        .header-text .address-line {
            font-size: 9pt;
            font-style: italic;
            margin: 3px 0 0 0;
            color: #374151;
        }
        .header-divider {
            border-top: 2.5px solid #111827;
            border-bottom: 0.8px solid #111827;
            height: 2px;
            margin: 8px 0 16px 0;
        }
        .title-block {
            text-align: center;
            margin: 10px 0 18px 0;
        }
        .title-block h1 {
            display: inline-block;
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1.5px solid #111827;
            padding-bottom: 2px;
            margin: 0;
        }
        .title-block .number {
            font-size: 11pt;
            margin: 5px 0 0 0;
        }
        .content {
            text-align: justify;
        }
        .content p {
            margin: 0 0 10px 0;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 12px 0;
        }
        .content td {
            padding: 2.5px 4px;
            vertical-align: top;
            line-height: 1.4;
        }
        .content td p {
            margin: 0;
        }
        .content table td:first-child {
            width: 175px;
            padding-left: 16px;
        }
        .content table td:nth-child(2) {
            width: 14px;
            text-align: center;
        }
        .signature-wrapper {
            width: 100%;
            margin-top: 22px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-spacer {
            width: 46%;
        }
        .signature-box {
            width: 54%;
            text-align: left;
            vertical-align: top;
        }
        .signature-date {
            font-size: 11pt;
            margin-bottom: 6px;
        }
        .specimen-card {
            width: 100%;
            border: 1px solid #94a3b8;
            border-radius: 6px;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #f8fafc;
        }
        .specimen-qr-cell {
            width: 76px;
            padding: 8px;
            vertical-align: middle;
            text-align: center;
            border-right: 1px solid #cbd5e1;
        }
        .specimen-qr-cell img {
            width: 68px;
            height: 68px;
            display: block;
            margin: 0 auto;
        }
        .specimen-info-cell {
            padding: 8px 10px;
            vertical-align: middle;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.35;
        }
        .specimen-label {
            font-size: 8pt;
            color: #475569;
            margin-bottom: 3px;
        }
        .specimen-position {
            font-size: 9pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .specimen-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .draft-signature-space {
            height: 60px;
            padding-top: 20px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #64748b;
            font-style: italic;
        }
        .footer-note {
            position: fixed;
            bottom: -1.4cm;
            left: 0;
            right: 0;
            border-top: 0.75px solid #cbd5e1;
            padding-top: 5px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.35;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            @if($logoDataUri)
                <td class="header-logo">
                    <img src="{{ $logoDataUri }}" alt="Logo Kabupaten Sinjai">
                </td>
            @endif
            <td class="header-text">
                @php
                    $resolvedRegency = $regencyName ?? ($settings['regency_name'] ?? '');
                    $resolvedDistrict = $districtName ?? ($settings['district_name'] ?? '');
                    $resolvedVillage = $villageName ?? ($settings['village_name'] ?? 'Desa');
                    $headerContacts = array_values(array_filter([
                        trim((string) ($settings['village_address'] ?? '')),
                        trim((string) ($settings['village_email'] ?? '')),
                        trim((string) ($websiteUrl ?? '')),
                    ], fn (string $item): bool => $item !== ''));
                @endphp
                @if(!empty($resolvedRegency))
                    <p class="gov-line">PEMERINTAH KABUPATEN {{ strtoupper($resolvedRegency) }}</p>
                @endif
                @if(!empty($resolvedDistrict))
                    <p class="gov-line">KECAMATAN {{ strtoupper($resolvedDistrict) }}</p>
                @endif
                <p class="village-line">PEMERINTAH DESA {{ strtoupper($resolvedVillage ?: 'DESA') }}</p>
                @if($headerContacts !== [])
                    <p class="address-line">{{ implode(' • ', $headerContacts) }}</p>
                @endif
            </td>
            @if($logoDataUri)
                <td class="header-logo"></td>
            @endif
        </tr>
    </table>
    <div class="header-divider"></div>

    <div class="title-block">
        <h1>{{ $letterTitle }}</h1>
        <p class="number">Nomor: {{ $letterNumber }}</p>
    </div>

    <div class="content">
        {!! $bodyHtml !!}
    </div>

    <div class="signature-wrapper">
        <table class="signature-table">
            <tr>
                <td class="signature-spacer"></td>
                <td class="signature-box">
                    <div class="signature-date">{{ $resolvedVillage ?: 'Desa' }}, {{ $letterDateFormatted }}</div>
                    @if($showTteVisual ?? false)
                        <table class="specimen-card">
                            <tr>
                                @if(!empty($qrCodeDataUri))
                                    <td class="specimen-qr-cell">
                                        @if(!empty($verificationUrl))
                                            <a href="{{ $verificationUrl }}" style="display: inline-block; text-decoration: none;">
                                                <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi TTE">
                                            </a>
                                        @else
                                            <img src="{{ $qrCodeDataUri }}" alt="QR Verifikasi TTE">
                                        @endif
                                    </td>
                                @endif
                                <td class="specimen-info-cell">
                                    <div class="specimen-label">Ditandatangani secara elektronik oleh:</div>
                                    <div class="specimen-position">{{ $signerPosition }}</div>
                                    <div class="specimen-name">{{ $signerName }}</div>
                                </td>
                            </tr>
                        </table>
                    @else
                        <div><strong>{{ $signerPosition }}</strong></div>
                        <div class="draft-signature-space">Draf belum ditandatangani secara elektronik</div>
                        <div><strong><u>{{ $signerName }}</u></strong></div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($showTteVisual ?? false)
        <div class="footer-note">
            <div>{{ $footerText }}</div>
        </div>
    @endif
</body>
</html>
