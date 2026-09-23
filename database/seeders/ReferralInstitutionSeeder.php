<?php

namespace Database\Seeders;

use App\Models\ReferralInstitution;
use Illuminate\Database\Seeder;

class ReferralInstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [
            [
                'name' => 'UPT Balai Pelayanan dan Rehabilitasi Sosial PMKS Sidoarjo',
                'type' => 'balai',
                'address' => 'Jl. Pahlawan No. 5, Sidoarjo, Jawa Timur',
                'contact' => '(031) 8921122 / 081233445511',
                'is_active' => true,
            ],
            [
                'name' => 'RSUD Ngudi Waluyo Wlingi (Instalasi Kedokteran Jiwa)',
                'type' => 'RS',
                'address' => 'Jl. Dokter Soetomo No. 53, Wlingi, Kabupaten Blitar',
                'contact' => '(0342) 691006',
                'is_active' => true,
            ],
            [
                'name' => 'Balai Rehabilitasi Sosial Lanjut Usia (BRSLU) Magetan',
                'type' => 'balai',
                'address' => 'Jl. Mayjen Sukowati No. 45, Magetan, Jawa Timur',
                'contact' => '(0351) 895123',
                'is_active' => true,
            ],
            [
                'name' => 'Panti Sosial Bina Remaja (PSBR) Blitar',
                'type' => 'panti',
                'address' => 'Jl. Supriyadi No. 22, Kota Blitar, Jawa Timur',
                'contact' => '(0342) 801234',
                'is_active' => true,
            ],
            [
                'name' => 'LKS Disabilitas "Harapan Mulia" Kabupaten Blitar',
                'type' => 'LKS',
                'address' => 'Jl. Raya Garum No. 18, Garum, Kabupaten Blitar',
                'contact' => '081234567890',
                'is_active' => true,
            ],
            [
                'name' => 'Rumah Sakit Jiwa Menur Surabaya',
                'type' => 'RS',
                'address' => 'Jl. Raya Menur No. 120, Surabaya, Jawa Timur',
                'contact' => '(031) 5021635',
                'is_active' => true,
            ],
            [
                'name' => 'Sentra Terpadu Prof. Dr. Soeharso Surakarta (Kemensos RI)',
                'type' => 'balai',
                'address' => 'Jl. Tentara Pelajar, Jebres, Kota Surakarta',
                'contact' => '(0271) 714441',
                'is_active' => true,
            ],
        ];

        foreach ($institutions as $item) {
            ReferralInstitution::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
