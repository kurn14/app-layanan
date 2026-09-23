<?php

namespace Database\Seeders;

use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'code' => 'DTSEN',
                'name' => 'Surat Keterangan DTSEN',
                'category' => 'Perlindungan dan Jaminan Sosial',
                'description' => 'Penerbitan surat keterangan resmi yang menerangkan status pemohon dalam Data Tunggal Sosial Ekonomi Nasional (DTSEN) serta desil untuk keperluan pendidikan (SPMB, PIP, KIP-K) dan bantuan sosial.',
                'handler' => 'dtsen',
                'needs_assessment' => false,
                'sla_days' => 3,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Kartu Tanda Penduduk (KTP) Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Dokumen Pendukung (Surat Sekolah/Bukti Pendaftaran/KIP)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'code' => 'PBI',
                'name' => 'Reaktivasi KIS / PBI-JK',
                'category' => 'Jaminan Kesehatan Sosial',
                'description' => 'Fasilitasi permohonan pengaktifan kembali kepesertaan Program Jaminan Kesehatan Nasional Kartu Indonesia Sehat segmen Penerima Bantuan Iuran Jaminan Kesehatan (PBI-JK) yang dinonaktifkan.',
                'handler' => 'pbi',
                'needs_assessment' => false,
                'sla_days' => 7,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Kartu Tanda Penduduk (KTP) Peserta',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Kartu BPJS Kesehatan / KIS Nonaktif',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Keterangan Rawat Inap / Rekam Medis Faskes',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'REHSOS',
                'name' => 'Permohonan Pelayanan Rehabilitasi Sosial',
                'category' => 'Rehabilitasi Sosial',
                'description' => 'Permohonan penanganan dan pelayanan rehabilitasi sosial bagi penyandang disabilitas terlantar, lansia terlantar, ODGJ terlantar, anak memerlukan perlindungan khusus, atau korban tindak kekerasan.',
                'handler' => 'generic',
                'needs_assessment' => true,
                'sla_days' => 14,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'Identitas Klien / KTP / KK (bila ada)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Surat Pengantar dari Pemerintah Desa / Kelurahan',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Foto Kondisi Calon Klien / Tempat Tinggal',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Surat Keterangan Dokter / Puskesmas (bila sakit/ODGJ)',
                        'is_mandatory' => false,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'code' => 'REK_BANSOS',
                'name' => 'Rekomendasi Bantuan Sosial Terencana / Insidental',
                'category' => 'Bantuan Sosial',
                'description' => 'Penerbitan surat rekomendasi untuk mendapatkan bantuan sosial APBD Kabupaten Blitar, penanganan kebencanaan sosial, atau bantuan asistensi sosial lainnya.',
                'handler' => 'generic',
                'needs_assessment' => true,
                'sla_days' => 5,
                'is_active' => true,
                'requirements' => [
                    [
                        'name' => 'KTP Pemohon',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kartu Keluarga (KK)',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Surat Keterangan Tidak Mampu (SKTM) dari Desa',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'pdf,jpg,png',
                        'sort_order' => 3,
                    ],
                    [
                        'name' => 'Foto Rumah Tinggal Tampak Depan dan Dalam',
                        'is_mandatory' => true,
                        'allowed_mimes' => 'jpg,png',
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $requirements = $serviceData['requirements'];
            unset($serviceData['requirements']);

            $service = ServiceType::updateOrCreate(
                ['code' => $serviceData['code']],
                $serviceData
            );

            foreach ($requirements as $req) {
                ServiceRequirement::updateOrCreate(
                    [
                        'service_type_id' => $service->id,
                        'name' => $req['name'],
                    ],
                    [
                        'is_mandatory' => $req['is_mandatory'],
                        'allowed_mimes' => $req['allowed_mimes'],
                        'sort_order' => $req['sort_order'],
                    ]
                );
            }
        }
    }
}
