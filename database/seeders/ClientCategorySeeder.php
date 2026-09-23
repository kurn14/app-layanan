<?php

namespace Database\Seeders;

use App\Models\ClientCategory;
use Illuminate\Database\Seeder;

class ClientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Lanjut Usia Terlantar',
            'Penyandang Disabilitas Fisik / Motorik',
            'Penyandang Disabilitas Sensorik (Netra / Rungu Wicara)',
            'Penyandang Disabilitas Intelektual / Ganda',
            'Orang Dengan Gangguan Jiwa (ODGJ) Terlantar',
            'Anak Terlantar / Anak Memerlukan Perlindungan Khusus (AMPK)',
            'Korban Tindak Kekerasan / Trafiking / Pekerja Migran Terlantar',
            'Gelandangan dan Pengemis (Gepeng)',
            'Pemerlu Pelayanan Kesejahteraan Sosial (PPKS) Lainnya',
        ];

        foreach ($categories as $name) {
            ClientCategory::updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
