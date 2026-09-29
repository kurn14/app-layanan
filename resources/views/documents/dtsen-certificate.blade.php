<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK DTSEN - {{ $certificate->certificate_number ?? 'Draf' }}</title>
    <style>
        @page {
            margin: 15mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .header-kop .instansi-1 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-kop .instansi-2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 2px 0;
        }
        .header-kop .alamat {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
        }
        .letter-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .letter-title h3 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .letter-title p {
            margin: 2px 0 0 0;
            font-size: 10.5pt;
        }
        .body-text {
            text-align: justify;
            margin-bottom: 12px;
            text-indent: 30px;
        }
        table.identitas {
            width: 100%;
            margin: 10px 0 15px 30px;
            border-collapse: collapse;
        }
        table.identitas td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        table.identitas .label {
            width: 28%;
        }
        table.identitas .colon {
            width: 3%;
        }
        table.identitas .value {
            width: 69%;
            font-weight: bold;
        }
        .decile-box {
            border: 1px solid #000;
            padding: 8px 12px;
            margin: 15px 0;
            background-color: #f9f9f9;
            text-align: center;
            font-weight: bold;
            font-size: 11.5pt;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
        }
        .signatures td {
            vertical-align: top;
        }
        .sig-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .qr-section {
            float: left;
            width: 200px;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 8pt;
            margin-top: 15px;
            border: 1px dashed #999;
            padding: 6px;
        }
        .clearfix {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header-kop">
        <div class="instansi-1">Pemerintah Kabupaten Blitar</div>
        <div class="instansi-2">Dinas Sosial</div>
        <div class="alamat">Jl. Raya Kanigoro No. 10, Blitar, Jawa Timur | Telp. (0342) 801122 | Laman: dinsos.blitarkab.go.id</div>
    </div>

    <div class="letter-title">
        <h3>Surat Keterangan Terdaftar DTSEN</h3>
        <p>Nomor: {{ $certificate->certificate_number ?? '.../.../DINSOS/'.date('Y') }}</p>
    </div>

    <div class="body-text">
        Yang bertanda tangan di bawah ini, Kepala Dinas Sosial Kabupaten Blitar dengan ini menerangkan bahwa:
    </div>

    <table class="identitas">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td class="value">{{ $certificate->subject_name ?? $certificate->serviceRequest?->applicant_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td class="value">{{ $certificate->subject_nik ?? $certificate->serviceRequest?->applicant_nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Kartu Keluarga</td>
            <td class="colon">:</td>
            <td class="value">{{ $certificate->serviceRequest?->family_card_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat / Domisili</td>
            <td class="colon">:</td>
            <td class="value">{{ $certificate->serviceRequest?->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Desa / Kelurahan</td>
            <td class="colon">:</td>
            <td class="value">{{ $certificate->serviceRequest?->village?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kecamatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $certificate->serviceRequest?->village?->district?->name ?? '-' }}</td>
        </tr>
    </table>

    <div class="body-text">
        Berdasarkan basis data terpadu Sistem Administrasi & Pelayanan Terpadu Sosial (SAPA SOSIAL) Kabupaten Blitar dan sinkronisasi Data Terpadu Sosial Ekonomi Nasional (DTSEN), yang bersangkutan dinyatakan:
    </div>

    <div class="decile-box">
        @if($certificate->is_registered)
            TERDAFTAR DALAM BASIS DATA DTSEN — DESIL {{ $certificate->decile ?? '-' }}
        @else
            BELUM TERDAFTAR DALAM BASIS DATA DTSEN
        @endif
    </div>

    <div class="body-text">
        Surat keterangan ini diterbitkan secara resmi untuk keperluan: <strong>{{ $certificate->dtsenPurpose?->name ?? $certificate->purpose_description ?? 'Persyaratan Pelayanan Sosial' }}</strong>, dan berlaku sampai dengan tanggal <strong>{{ $certificate->valid_until ? $certificate->valid_until->translatedFormat('d F Y') : now()->addMonths(3)->translatedFormat('d F Y') }}</strong>.
    </div>

    <div class="body-text">
        Demikian surat keterangan ini diberikan agar dapat dipergunakan sebagaimana mestinya.
    </div>

    <div class="signatures">
        <div class="qr-section">
            <div style="font-weight: bold; margin-bottom: 4px;">VERIFIKASI RESMI</div>
            <div>Kode Verifikasi:</div>
            <div style="font-family: monospace; font-size: 9pt; font-weight: bold;">{{ $certificate->verification_code ?? '-' }}</div>
            <div style="font-size: 7pt; color: #555; margin-top: 4px;">Pindai atau akses tautan verifikasi pada portal SAPA SOSIAL</div>
        </div>

        <div class="sig-box">
            <div>Blitar, {{ $certificate->issued_at ? $certificate->issued_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') : now()->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
            <div style="font-weight: bold; margin-top: 2px;">KEPALA DINAS SOSIAL<br>KABUPATEN BLITAR</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $certificate->signer?->name ?? 'Drs. H. BAMBANG SETIAWAN, M.Si' }}</div>
            <div>Pembina Utama Muda</div>
            <div>NIP. {{ $certificate->signer?->nip ?? '19680512 199403 1 004' }}</div>
        </div>
        <div class="clearfix"></div>
    </div>
</body>
</html>
