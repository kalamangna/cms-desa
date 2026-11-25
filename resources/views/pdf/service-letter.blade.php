<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $letterTitle }}</title>
    <style>
        @page {
            margin: 2cm 2.2cm 2.5cm 2.2cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #111827;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #111827;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .header-logo {
            width: 72px;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 68px;
            max-height: 68px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text .gov-line {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.25;
        }
        .header-text .village-line {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
            line-height: 1.2;
        }
        .header-text .address-line {
            font-size: 9.5pt;
            margin: 2px 0 0 0;
            color: #374151;
        }
        .title-block {
            text-align: center;
            margin: 14px 0 18px 0;
        }
        .title-block h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .title-block .number {
            font-size: 11pt;
            margin: 4px 0 0 0;
        }
        .content {
            text-align: justify;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
        }
        .content td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .signature-wrapper {
            width: 100%;
            margin-top: 28px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-spacer {
            width: 52%;
        }
        .signature-box {
            width: 48%;
            text-align: left;
            vertical-align: top;
        }
        .signature-space {
            height: 76px;
            padding-top: 28px;
        }
        .bsre-tag {
            color: #ffffff;
            font-size: 10px;
        }
        .footer-note {
            position: fixed;
            bottom: -1.4cm;
            left: 0;
            right: 0;
            border-top: 1px solid #d1d5db;
            padding-top: 6px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #4b5563;
            line-height: 1.35;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            @if($logoDataUri)
                <td class="header-logo">
                    <img src="{{ $logoDataUri }}" alt="Logo Desa">
                </td>
            @endif
            <td class="header-text">
                @if(!empty($settings['regency_name']))
                    <p class="gov-line">PEMERINTAH KABUPATEN {{ strtoupper($settings['regency_name']) }}</p>
                @endif
                @if(!empty($settings['district_name']))
                    <p class="gov-line">KECAMATAN {{ strtoupper($settings['district_name']) }}</p>
                @endif
                <p class="village-line">PEMERINTAH DESA {{ strtoupper($settings['village_name'] ?? 'DESA') }}</p>
                @if(!empty($settings['village_address']) || !empty($settings['village_email']))
                    <p class="address-line">
                        {{ $settings['village_address'] ?? '' }}
                        @if(!empty($settings['village_address']) && !empty($settings['village_email']))
                            &bull;
                        @endif
                        {{ $settings['village_email'] ?? '' }}
                    </p>
                @endif
            </td>
        </tr>
    </table>

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
                    <div>{{ $settings['village_name'] ?? 'Desa' }}, {{ $letterDateFormatted }}</div>
                    <div><strong>{{ $signerPosition }}</strong></div>
                    <div class="signature-space">
                        <span class="bsre-tag">{{ $tagCoordinate }}</span>
                    </div>
                    <div><strong><u>{{ $signerName }}</u></strong></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        <div>{{ $footerText }}</div>
        @if(!empty($websiteUrl))
            <div>Website Resmi: {{ $websiteUrl }}</div>
        @endif
    </div>
</body>
</html>
