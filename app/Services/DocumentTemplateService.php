<?php

namespace App\Services;

class DocumentTemplateService
{
    /**
     * Generate a valid standalone PDF for the official downloadable form.
     */
    public static function createFormPdf(string $title, string $version = '1.0'): string
    {
        $escapedTitle = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $title);

        $stream = "BT\n"
            ."/F1 16 Tf\n"
            ."50 780 Td\n"
            ."(PEMERINTAH KABUPATEN BLITAR) Tj\n"
            ."/F1 13 Tf\n"
            ."0 -22 Td\n"
            ."(DINAS SOSIAL - PORTAL SAPA SOSIAL) Tj\n"
            ."/F2 9 Tf\n"
            ."0 -15 Td\n"
            ."(Jl. Raya Kanigoro No. 10, Blitar, Jawa Timur | Telp. 0342-801122 | Call Center 112) Tj\n"
            ."0 -15 Td\n"
            ."(_____________________________________________________________________________________) Tj\n"
            ."/F1 12 Tf\n"
            ."0 -35 Td\n"
            .'('.$escapedTitle.") Tj\n"
            ."/F2 9 Tf\n"
            ."0 -16 Td\n"
            .'(Dokumen Resmi Dinas Sosial Kab. Blitar | Versi: '.$version." | Status: Berlaku) Tj\n"
            ."0 -28 Td\n"
            ."/F1 10 Tf\n"
            ."(PETUNJUK PENGISIAN FORMULIR:) Tj\n"
            ."/F2 9 Tf\n"
            ."0 -16 Td\n"
            ."(1. Isilah seluruh kolom data identitas di bawah ini dengan jelas dan menggunakan huruf balok/cetak.) Tj\n"
            ."0 -14 Td\n"
            ."(2. Lampirkan fotokopi KTP Pemohon dan Kartu Keluarga (KK) asli Kabupaten Blitar.) Tj\n"
            ."0 -14 Td\n"
            ."(3. Berkas fisik dapat diserahkan ke loket Puskesos Kantor Desa/Kelurahan atau diunggah online.) Tj\n"
            ."0 -14 Td\n"
            ."(4. Pengajuan online dapat dipantau setiap saat melalui menu Cek Status pada portal SAPA SOSIAL.) Tj\n"
            ."0 -30 Td\n"
            ."/F1 10 Tf\n"
            ."(DATA IDENTITAS PEMOHON / WARGA:) Tj\n"
            ."/F2 9 Tf\n"
            ."0 -20 Td\n"
            ."(1. Nama Lengkap Sesuai KTP  : _________________________________________________________________) Tj\n"
            ."0 -20 Td\n"
            ."(2. Nomor Induk Kependudukan (NIK): ____________________________________________________________) Tj\n"
            ."0 -20 Td\n"
            ."(3. Nomor Kartu Keluarga (KK)     : ____________________________________________________________) Tj\n"
            ."0 -20 Td\n"
            ."(4. Alamat Lengkap Domisili       : RT _____ RW _____ Dusun ____________________________________) Tj\n"
            ."0 -20 Td\n"
            ."(5. Desa / Kelurahan & Kecamatan  : Desa _____________________, Kec. ___________________________) Tj\n"
            ."0 -20 Td\n"
            ."(6. Nomor WhatsApp / Kontak Aktif : ____________________________________________________________) Tj\n"
            ."0 -20 Td\n"
            ."(7. Keperluan / Tujuan Permohonan : ____________________________________________________________) Tj\n"
            ."0 -40 Td\n"
            ."(PERNYATAAN KEABSAHAN DATA:) Tj\n"
            ."0 -14 Td\n"
            ."(Dengan ini saya menyatakan bahwa data yang saya isikan pada formulir ini adalah benar dan sah.) Tj\n"
            ."0 -45 Td\n"
            ."(Mengetahui,                                                     Blitar, _____________________ 2026) Tj\n"
            ."0 -14 Td\n"
            ."(Kepala Desa / Lurah Setempat,                                   Pemohon,) Tj\n"
            ."0 -55 Td\n"
            ."(( ___________________________ )                                 ( ___________________________ )) Tj\n"
            ."ET\n";

        $length = strlen($stream);

        // Build valid PDF structure
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[3] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>';
        $objects[4] = '<< /Length '.$length." >>\nstream\n".$stream.'endstream';
        $objects[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
        $objects[6] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $num => $obj) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $num." 0 obj\n".$obj."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= '0 '.(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n";
        $pdf .= '<< /Root 1 0 R /Size '.(count($objects) + 1)." >>\n";
        $pdf .= "startxref\n";
        $pdf .= $xrefOffset."\n";
        $pdf .= "%%EOF\n";

        return $pdf;
    }
}
