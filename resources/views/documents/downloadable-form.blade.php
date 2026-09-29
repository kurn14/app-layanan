<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - Formulir Resmi</title>
    <style>
        @page {
            margin: 15mm 15mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #111827;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2.5px double #111827;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-kop .instansi-1 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-kop .instansi-2 {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e3a8a;
            margin: 2px 0;
        }
        .header-kop .alamat {
            font-size: 8pt;
            color: #4b5563;
        }
        .form-title-box {
            text-align: center;
            margin-bottom: 14px;
        }
        .form-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .form-version {
            font-size: 8pt;
            color: #4b5563;
        }
        .section-title {
            font-weight: bold;
            font-size: 9pt;
            margin-top: 10px;
            margin-bottom: 6px;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 0.5px solid #cbd5e1;
            padding-bottom: 2px;
        }
        ol.instructions {
            margin: 0;
            padding-left: 18px;
            font-size: 8.5pt;
            color: #334155;
        }
        ol.instructions li {
            margin-bottom: 3px;
        }
        table.form-fields {
            width: 100%;
            margin-top: 6px;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.form-fields td {
            padding: 5px 4px;
            vertical-align: middle;
        }
        table.form-fields .num {
            width: 4%;
            vertical-align: top;
        }
        table.form-fields .label {
            width: 32%;
            vertical-align: top;
        }
        table.form-fields .colon {
            width: 2%;
            vertical-align: top;
        }
        table.form-fields .blank-line {
            width: 62%;
            border-bottom: 0.75px dotted #64748b;
            min-height: 18px;
        }
        .declaration-box {
            margin-top: 15px;
            padding: 8px 10px;
            border: 0.75px solid #cbd5e1;
            background-color: #f8fafc;
            font-size: 8pt;
            line-height: 1.4;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .sig-space {
            height: 55px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .footer-note {
            margin-top: 25px;
            font-size: 7pt;
            color: #64748b;
            text-align: center;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header-kop">
        <div class="instansi-1">Pemerintah Kabupaten Blitar</div>
        <div class="instansi-2">Dinas Sosial — Portal SAPA SOSIAL</div>
        <div class="alamat">Jl. Raya Kanigoro No. 10, Blitar, Jawa Timur | Telp. (0342) 801122 | Call Center 112</div>
    </div>

    <div class="form-title-box">
        <div class="form-title">{{ $title }}</div>
        <div class="form-version">Dokumen Resmi Dinas Sosial Kab. Blitar | Versi: {{ $version }} | Status: Berlaku</div>
    </div>

    <div class="section-title">A. Petunjuk Pengisian Formulir:</div>
    <ol class="instructions">
        <li>Isilah seluruh kolom data identitas di bawah ini dengan jelas dan menggunakan huruf balok/cetak.</li>
        <li>Lampirkan fotokopi KTP Pemohon dan Kartu Keluarga (KK) asli Kabupaten Blitar.</li>
        <li>Berkas fisik dapat diserahkan ke loket Puskesos Kantor Desa/Kelurahan atau diunggah secara online.</li>
        <li>Pengajuan online dapat dipantau setiap saat melalui menu Cek Status pada portal resmi SAPA SOSIAL.</li>
    </ol>

    <div class="section-title">B. Data Identitas Pemohon / Warga:</div>
    <table class="form-fields">
        <tr>
            <td class="num">1.</td>
            <td class="label">Nama Lengkap Sesuai KTP</td>
            <td class="colon">:</td>
            <td class="blank-line"></td>
        </tr>
        <tr>
            <td class="num">2.</td>
            <td class="label">Nomor Induk Kependudukan (NIK)</td>
            <td class="colon">:</td>
            <td class="blank-line"></td>
        </tr>
        <tr>
            <td class="num">3.</td>
            <td class="label">Nomor Kartu Keluarga (KK)</td>
            <td class="colon">:</td>
            <td class="blank-line"></td>
        </tr>
        <tr>
            <td class="num">4.</td>
            <td class="label">Alamat Lengkap Domisili</td>
            <td class="colon">:</td>
            <td class="blank-line" style="font-size: 8pt; color: #64748b;">RT _______ / RW _______ Dusun ________________________________</td>
        </tr>
        <tr>
            <td class="num">5.</td>
            <td class="label">Desa / Kelurahan & Kecamatan</td>
            <td class="colon">:</td>
            <td class="blank-line" style="font-size: 8pt; color: #64748b;">Desa _______________________, Kec. ___________________________</td>
        </tr>
        <tr>
            <td class="num">6.</td>
            <td class="label">Nomor WhatsApp / Kontak Aktif</td>
            <td class="colon">:</td>
            <td class="blank-line"></td>
        </tr>
        <tr>
            <td class="num">7.</td>
            <td class="label">Keperluan / Tujuan Permohonan</td>
            <td class="colon">:</td>
            <td class="blank-line"></td>
        </tr>
    </table>

    <div class="declaration-box">
        <strong>Pernyataan Keabsahan Data:</strong><br>
        Dengan ini saya menyatakan bahwa seluruh data yang saya isikan pada formulir ini adalah benar, lengkap, dan sah. Apabila di kemudian hari ditemukan ketidakbenaran data, saya bersedia bertanggung jawab sesuai dengan peraturan perundang-undangan yang berlaku.
    </div>

    <table class="signatures">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Desa / Lurah Setempat
                <div class="sig-space"></div>
                <div class="sig-name">( ________________________________ )</div>
                <div>NIP. ____________________________</div>
            </td>
            <td>
                Blitar, _______________________ {{ date('Y') }}<br>
                Pemohon / Warga,
                <div class="sig-space"></div>
                <div class="sig-name">( ________________________________ )</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Formulir ini dapat diunduh melalui portal SAPA SOSIAL Dinas Sosial Kab. Blitar — Dicetak pada: {{ now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
    </div>
</body>
</html>
