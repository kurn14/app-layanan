<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use Illuminate\Database\Seeder;

class ComplaintCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Bantuan Sosial Tidak Tepat Sasaran / Pungutan Liar',
            'Orang Terlantar / ODGJ Resah di Fasilitas Umum',
            'Lansia / Penyandang Disabilitas Terlantar Butuh Evakuasi',
            'Kekerasan, Penelantaran Anak & Perempuan',
            'Pelayanan KIS / PBI-JK Ditolak Fasilitas Kesehatan',
            'Kualitas Pelayanan dan Petugas Dinas Sosial',
            'Bencana Alam & Permasalahan Sosial Lainnya',
        ];

        foreach ($categories as $name) {
            ComplaintCategory::updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
