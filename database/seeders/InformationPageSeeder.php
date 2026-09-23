<?php

namespace Database\Seeders;

use App\Enums\InformationCategory;
use App\Enums\InformationPublishStatus;
use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\InformationPage;
use App\Models\PageVisit;
use App\Models\SearchLog;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InformationPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'admin@dinsos.blitarkab.go.id')->first()
            ?? User::where('email', 'adi@adi.com')->first();

        $dtsenType = ServiceType::where('code', 'DTSEN')->first();
        $pbiType = ServiceType::where('code', 'PBI')->first();
        $rehsosType = ServiceType::where('code', 'REHSOS')->first();

        $pages = [
            [
                'title' => 'Layanan Penerbitan Surat Keterangan DTSEN',
                'slug' => 'surat-keterangan-dtsen',
                'category' => InformationCategory::PROGRAM,
                'service_type_id' => $dtsenType?->id,
                'description' => 'Layanan penerbitan Surat Keterangan Data Tunggal Sosial Ekonomi Nasional (DTSEN) bagi warga Kabupaten Blitar yang terdaftar dalam data kemiskinan/desil nasional untuk keperluan pendaftaran sekolah afirmasi (SPMB/PPDB), beasiswa PIP/KIP Kuliah, maupun bantuan sosial.',
                'requirements' => "1. Foto/Scan Kartu Tanda Penduduk (KTP) Pemohon asli\n2. Foto/Scan Kartu Keluarga (KK) asli Kabupaten Blitar\n3. Surat pengantar atau bukti pendaftaran sekolah / perguruan tinggi (opsional)",
                'procedure' => "1. Pemohon memilih menu Pengajuan Layanan -> Surat Keterangan DTSEN\n2. Mengisi formulir dan mengunggah dokumen persyaratan\n3. Petugas memverifikasi kelengkapan berkas dan memeriksa status/desil di SIKS-NG\n4. Pembuatan draf surat dan persetujuan bertingkat (Kabid & Kadis)\n5. Surat bertanda tangan elektronik dan QR Code terbit serta dapat diunduh langsung.",
                'service_hours' => 'Senin - Kamis: 08.00 - 15.00 WIB, Jumat: 08.00 - 14.30 WIB',
                'location' => 'Kantor Dinas Sosial Kabupaten Blitar, Jl. Raya Kanigoro, Blitar',
                'contact' => 'WhatsApp Layanan: 0857-3399-0011 / Telp: (0342) 801122',
                'publish_status' => InformationPublishStatus::PUBLISHED,
                'published_at' => Carbon::now()->subDays(30),
                'manager_id' => $manager?->id,
                'forms' => [
                    [
                        'name' => 'Formulir F-1.01 Permohonan Surat Keterangan DTSEN',
                        'file_path' => 'forms/f101_permohonan_dtsen.pdf',
                        'version' => '1.0',
                        'is_current' => true,
                    ],
                    [
                        'name' => 'Surat Pernyataan Keperluan Jalur Afirmasi Sekolah',
                        'file_path' => 'forms/surat_pernyataan_afirmasi.pdf',
                        'version' => '1.1',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Berapa lama proses penerbitan Surat Keterangan DTSEN?',
                        'answer' => 'Proses penerbitan memerlukan waktu maksimal 3 hari kerja sejak dokumen persyaratan dinyatakan lengkap dan diverifikasi pada sistem SIKS-NG.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Apakah pemohon yang berada di Desil 6 ke atas dapat diterbitkan SK DTSEN?',
                        'answer' => 'Surat Keterangan DTSEN untuk tujuan SPMB Afirmasi hanya dapat diterbitkan apabila pemohon berada maksimal pada Desil 5. Jika di luar desil ketentuan, permohonan akan ditolak secara otomatis oleh sistem dengan alasan resmi.',
                        'sort_order' => 2,
                    ],
                    [
                        'question' => 'Bagaimana cara mengecek keaslian Surat Keterangan DTSEN yang telah terbit?',
                        'answer' => 'Setiap surat dilengkapi QR Code unik. Pihak sekolah atau kampus dapat memindai QR Code tersebut atau memasukkan kode verifikasi pada portal SAPA SOSIAL di menu Verifikasi Surat.',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'title' => 'Fasilitasi Reaktivasi KIS / PBI-JK Nonaktif',
                'slug' => 'reaktivasi-kis-pbi-jk',
                'category' => InformationCategory::PROGRAM,
                'service_type_id' => $pbiType?->id,
                'description' => 'Fasilitasi pengusulan pengaktifan kembali kepesertaan Jaminan Kesehatan Nasional (JKN-KIS PBI-JK) yang dibiayai APBN/Kemensos yang telah nonaktif, diprioritaskan bagi pasien kondisi darurat medis dan penyakit katastropik.',
                'requirements' => "1. Foto/Scan KTP dan KK peserta\n2. Foto Kartu BPJS Kesehatan / KIS yang nonaktif\n3. Surat Keterangan Rawat Inap / Rekam Medis / Rujukan dari Rumah Sakit atau Puskesmas yang menerangkan kondisi medis",
                'procedure' => "1. Pemohon mengajukan permohonan secara online atau dibantu operator desa/kecamatan\n2. Petugas memverifikasi kelayakan desil dan urgensi medis\n3. Dinas Sosial menerbitkan Surat Rekomendasi Reaktivasi\n4. Petugas mengusulkan data ke Kementerian Sosial RI melalui SIKS-NG\n5. Pemantauan berkala hingga kepesertaan dinyatakan aktif kembali oleh BPJS Kesehatan.",
                'service_hours' => 'Senin - Jumat: 24 Jam (Khusus Gawat Darurat Medis via Call Center TRC)',
                'location' => 'Bidang Perlindungan dan Jaminan Sosial Dinsos Blitar / Puskesmas / Faskes Terdekat',
                'contact' => 'Hotline Darurat Medis PBI: 0857-3399-0022',
                'publish_status' => InformationPublishStatus::PUBLISHED,
                'published_at' => Carbon::now()->subDays(25),
                'manager_id' => $manager?->id,
                'forms' => [
                    [
                        'name' => 'Formulir Permohonan Rekomendasi Reaktivasi KIS PBI',
                        'file_path' => 'forms/form_reaktivasi_pbi_jk.pdf',
                        'version' => '2.0',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Apakah pasien gawat darurat yang KIS-nya mati bisa langsung dilayani di rumah sakit?',
                        'answer' => 'Ya, untuk kondisi darurat medis (gawat darurat/opname), pemohon diberi penanda Prioritas Tinggi. Surat rekomendasi diterbitkan dalam waktu 1x24 jam untuk penjaminan di faskes.',
                        'sort_order' => 1,
                    ],
                    [
                        'question' => 'Mengapa kartu KIS PBI-JK saya bisa nonaktif secara tiba-tiba?',
                        'answer' => 'Penonaktifan dapat disebabkan oleh pemutakhiran data berkala dari Kementerian Sosial RI (SK DTKS/DTSEN terbaru), migrasi NIK, atau kuota kepesertaan.',
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'title' => 'Pelayanan dan Rehabilitasi Sosial Terpadu',
                'slug' => 'pelayanan-rehabilitasi-sosial',
                'category' => InformationCategory::REHABILITATION,
                'service_type_id' => $rehsosType?->id,
                'description' => 'Penanganan menyeluruh bagi Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) meliputi lansia terlantar, disabilitas, anak telantar, ODGJ, dan gelandangan. Layanan mencakup assessment awal, pelayanan langsung, dan rujukan ke balai/panti rehabilitasi sosial.',
                'requirements' => "1. Identitas Klien / Surat Keterangan Domisili dari Desa\n2. Informasi kronologi kondisi dan lokasi keberadaan klien\n3. Surat pengantar rujukan medis (bila membutuhkan penanganan kejiwaan/kesehatan)",
                'procedure' => "1. Laporan diterima petugas dari pengajuan langsung, pengaduan masyarakat, atau tim TRC\n2. Pekerja Sosial melakukan assessment komprehensif di lapangan atau kantor\n3. Penyusunan rencana pelayanan (pelayanan langsung atau rujukan lembaga)\n4. Pelaksanaan rujukan ke Balai/Panti/RS bila diperlukan\n5. Monitoring dan pendampingan hingga kasus selesai.",
                'service_hours' => 'Setiap Hari Kerja (08.00 - 15.30 WIB), TRC Siaga 24 Jam',
                'location' => 'Bidang Rehabilitasi Sosial Dinsos Blitar / Shelter Penampungan Sementara',
                'contact' => 'TRC Rehabilitasi Sosial: 0857-3399-0033',
                'publish_status' => InformationPublishStatus::PUBLISHED,
                'published_at' => Carbon::now()->subDays(20),
                'manager_id' => $manager?->id,
                'forms' => [
                    [
                        'name' => 'Instrumen Assessment Awal Pemerlu Pelayanan Kesejahteraan Sosial',
                        'file_path' => 'forms/instrumen_assessment_rehsos.pdf',
                        'version' => '1.0',
                        'is_current' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Apakah keluarga dipungut biaya untuk rujukan ke balai/panti sosial provinsi?',
                        'answer' => 'Seluruh proses pelayanan dan rujukan rehabilitasi sosial oleh Dinas Sosial Kabupaten Blitar ke panti/balai sosial pemerintah adalah GRATIS (tidak dipungut biaya).',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'title' => 'Kanal Pengaduan Masalah Sosial (SAPA SOSIAL)',
                'slug' => 'pengaduan-sosial',
                'category' => InformationCategory::COMPLAINT,
                'service_type_id' => null,
                'description' => 'Saluran resmi bagi masyarakat Kabupaten Blitar untuk melaporkan permasalahan sosial di lingkungannya, seperti bantuan sosial yang salah sasaran, keberadaan orang terlantar, ODGJ mengamuk, lansia sakit tanpa keluarga, atau dugaan pungli.',
                'requirements' => "1. Identitas Pelapor (Nama & No. WhatsApp aktif)\n2. Lokasi kejadian minimal mencakup desa/kelurahan dan kecamatan\n3. Uraian kronologi kejadian yang jelas\n4. Foto/video bukti pendukung bila memungkinkan",
                'procedure' => "1. Pelapor membuka menu Pengaduan Sosial dan mengisi formulir\n2. Sistem menerbitkan nomor tiket pengaduan (ADU-YYYYMM-NNNNN)\n3. Petugas Dinsos memverifikasi laporan maksimal 1x24 jam\n4. Laporan didisposisikan ke bidang terkait atau tim TRC\n5. Pelapor dapat memantau penanganan secara real-time melalui nomor tiket.",
                'service_hours' => 'Layanan Online 24 Jam',
                'location' => 'Dinas Sosial Kabupaten Blitar',
                'contact' => 'Call Center Pengaduan: 0811-3322-4455',
                'publish_status' => InformationPublishStatus::PUBLISHED,
                'published_at' => Carbon::now()->subDays(15),
                'manager_id' => $manager?->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Apakah identitas pelapor pengaduan dijamin kerahasiaannya?',
                        'answer' => 'Ya, identitas pelapor dilindungi dan hanya dapat dilihat oleh administrator serta petugas verifikasi yang berwenang untuk keperluan klarifikasi.',
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'title' => 'Program Bantuan dan Alat Bantu Penyandang Disabilitas & Lansia',
                'slug' => 'bantuan-disabilitas-dan-lansia',
                'category' => InformationCategory::DISABILITY,
                'service_type_id' => null,
                'description' => 'Informasi penyaluran bantuan asistensi sosial, permakanan, dan alat bantu adaptif (kursi roda, tongkat ketiak, alat bantu dengar) bagi penyandang disabilitas dan lanjut usia kurang mampu di Kabupaten Blitar.',
                'requirements' => "1. KTP dan KK penerima manfaat\n2. Surat Keterangan Tidak Mampu (SKTM) dari desa\n3. Foto seluruh badan yang memperlihatkan kondisi kedisabilitasan\n4. Surat rekomendasi medis/Puskesmas untuk spesifikasi alat bantu",
                'procedure' => "1. Pengusulan dapat melalui desa atau pengajuan langsung via SAPA SOSIAL\n2. Petugas melakukan peninjauan lapangan dan pengukuran alat bantu\n3. Penyaluran alat bantu sesuai ketersediaan alokasi APBD/Kemensos.",
                'service_hours' => 'Senin - Kamis: 08.00 - 15.00 WIB, Jumat: 08.00 - 14.30 WIB',
                'location' => 'Kantor Dinas Sosial Kabupaten Blitar',
                'contact' => 'Seksi Disabilitas & Lansia: 0812-3456-7890',
                'publish_status' => InformationPublishStatus::PUBLISHED,
                'published_at' => Carbon::now()->subDays(10),
                'manager_id' => $manager?->id,
                'forms' => [],
                'faqs' => [
                    [
                        'question' => 'Kapan jadwal penyaluran bantuan alat bantu disabilitas dilaksanakan?',
                        'answer' => 'Penyaluran alat bantu dilakukan secara berkala setiap triwulan atau melalui program respon kedaruratan sosial langsung ke rumah penerima manfaat.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ];

        foreach ($pages as $p) {
            $forms = $p['forms'];
            $faqs = $p['faqs'];
            unset($p['forms'], $p['faqs']);

            $page = InformationPage::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );

            foreach ($forms as $form) {
                DownloadableForm::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'name' => $form['name'],
                    ],
                    $form
                );
            }

            foreach ($faqs as $faq) {
                Faq::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'question' => $faq['question'],
                    ],
                    array_merge($faq, ['is_active' => true])
                );
            }

            // Seed sample page visits for statistics widget
            for ($i = 0; $i < 7; $i++) {
                PageVisit::updateOrCreate(
                    [
                        'information_page_id' => $page->id,
                        'visit_date' => Carbon::today()->subDays($i)->toDateString(),
                    ],
                    [
                        'visit_count' => rand(15, 85),
                    ]
                );
            }
        }

        // Seed search logs for analytics
        $keywords = [
            'dtsen',
            'surat keterangan dtsen',
            'spmb afirmasi blitar',
            'kartu kis mati',
            'reaktivasi pbi jk',
            'odgj terlantar',
            'bantuan lansia',
            'kursi roda disabilitas',
            'bansos tidak tepat sasaran',
            'pip kip kuliah',
        ];

        foreach ($keywords as $kw) {
            SearchLog::updateOrCreate(
                ['keyword' => $kw],
                [
                    'result_count' => rand(3, 20),
                    'searched_at' => Carbon::now()->subHours(rand(1, 72)),
                ]
            );
        }
    }
}
