<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Rekomendasi PBI-JK - {{ $pbi->recommendation_number ?? 'Draf' }}</title>
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
            width: 32%;
        }
        table.identitas .colon {
            width: 3%;
        }
        table.identitas .value {
            width: 65%;
            font-weight: bold;
        }
        .status-box {
            border: 1px solid #000;
            padding: 8px 12px;
            margin: 15px 0;
            background-color: #f9f9f9;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
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
        <h3>Surat Rekomendasi Reaktivasi PBI-JK</h3>
        <p>Nomor: {{ $pbi->recommendation_number ?? '.../.../REK-PBI/'.date('Y') }}</p>
    </div>

    <div class="body-text">
        Berdasarkan hasil verifikasi dan validasi kelayakan data sosial ekonomi serta kondisi darurat medis pada Sistem SAPA SOSIAL, Dinas Sosial Kabupaten Blitar memberikan rekomendasi reaktivasi kepesertaan Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) kepada:
    </div>

    <table class="identitas">
        <tr>
            <td class="label">Nama Peserta</td>
            <td class="colon">:</td>
            <td class="value">{{ $pbi->participant_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK Peserta</td>
            <td class="colon">:</td>
            <td class="value">{{ $pbi->participant_nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Kartu BPJS</td>
            <td class="colon">:</td>
            <td class="value">{{ $pbi->bpjs_card_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Desa / Kelurahan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pbi->serviceRequest?->village?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kecamatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pbi->serviceRequest?->village?->district?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alasan Pengusulan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pbi->reason instanceof \App\Enums\PbiReactivationReason ? $pbi->reason->label() : ($pbi->reason ?? '-') }}</td>
        </tr>
        @if(!empty($pbi->health_facility_name))
            <tr>
                <td class="label">Fasilitas Pelayanan Kesehatan</td>
                <td class="colon">:</td>
                <td class="value">{{ $pbi->health_facility_name }}</td>
            </tr>
        @endif
    </table>

    <div class="status-box">
        DIREKOMENDASIKAN UNTUK PENGUSULAN REAKTIVASI PBI-JK KE KEMENTERIAN SOSIAL RI MELALUI SIKS-NG
    </div>

    <div class="body-text">
        Surat rekomendasi ini diterbitkan untuk dipergunakan sebagai dasar pengusulan pengaktifan kembali status kepesertaan PBI-JK pada sistem aplikasi Kementerian Sosial Republik Indonesia.
    </div>

    <div class="body-text">
        Demikian surat rekomendasi ini dibuat dengan sebenarnya agar pihak terkait dapat mempergunakannya sebagaimana mestinya.
    </div>

    <div class="signatures">
        <div class="sig-box">
            <div>Blitar, {{ $pbi->recommendation_issued_at ? $pbi->recommendation_issued_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') : now()->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
            <div style="font-weight: bold; margin-top: 2px;">KEPALA DINAS SOSIAL<br>KABUPATEN BLITAR</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $pbi->signer?->name ?? 'Drs. H. BAMBANG SETIAWAN, M.Si' }}</div>
            <div>Pembina Utama Muda</div>
            <div>NIP. {{ $pbi->signer?->nip ?? '19680512 199403 1 004' }}</div>
        </div>
        <div class="clearfix"></div>
    </div>
</body>
</html>
