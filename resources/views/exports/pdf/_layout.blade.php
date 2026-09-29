<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan Data' }} - SAPA SOSIAL</title>
    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px double #111827;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header-kop .instansi-1 {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
            color: #111827;
        }
        .header-kop .instansi-2 {
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 2px 0;
            color: #0f172a;
        }
        .header-kop .alamat {
            font-size: 8pt;
            color: #4b5563;
            margin: 0;
        }
        .report-title-box {
            text-align: center;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 0 0 3px 0;
        }
        .report-subtitle {
            font-size: 8.5pt;
            color: #4b5563;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 8.5pt;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .meta-table .label {
            width: 18%;
            color: #4b5563;
            font-weight: bold;
        }
        .meta-table .separator {
            width: 2%;
        }
        .meta-table .value {
            width: 80%;
            color: #111827;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        table.data-table th, table.data-table td {
            border: 0.75px solid #94a3b8;
            padding: 5px 6px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        table.data-table td.text-center {
            text-align: center;
        }
        table.data-table td.text-right {
            text-align: right;
        }
        table.data-table td.font-bold {
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
            font-size: 7pt;
            font-weight: 600;
            border-radius: 3px;
            text-align: center;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
        .badge-gray { background-color: #f3f4f6; color: #4b5563; }
        .badge-purple { background-color: #f3e8ff; color: #7e22ce; }
        .badge-primary { background-color: #dbeafe; color: #1d4ed8; }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .summary-card-title {
            font-weight: bold;
            font-size: 8.5pt;
            margin-bottom: 6px;
            color: #1e293b;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 0.5px solid #cbd5e1;
            font-size: 7.5pt;
            color: #64748b;
            display: flex;
            justify-content: space-between;
        }
        .footer-left {
            float: left;
        }
        .footer-right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        .signature-section {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-box {
            float: right;
            width: 240px;
            text-align: center;
            font-size: 8.5pt;
        }
        .signature-space {
            height: 50px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="header-kop">
        <div class="instansi-1">Pemerintah Kabupaten Blitar</div>
        <div class="instansi-2">Dinas Sosial</div>
        <div class="alamat">Jl. Raya Kanigoro No. 10, Blitar, Jawa Timur | Telp. (0342) 801122 | Laman: dinsos.blitarkab.go.id</div>
    </div>

    <div class="report-title-box">
        <div class="report-title">{{ $title ?? 'LAPORAN' }}</div>
        @if(!empty($subtitle))
            <div class="report-subtitle">{{ $subtitle }}</div>
        @endif
    </div>

    @yield('content')

    <div class="footer clearfix">
        <span class="footer-left">SAPA SOSIAL — Sistem Administrasi & Pelayanan Terpadu Sosial Kab. Blitar</span>
        <span class="footer-right">Dicetak pada: {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</span>
    </div>
</body>
</html>
