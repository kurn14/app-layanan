<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $sekretariat = WorkUnit::where('name', 'like', '%Sekretariat%')->first();
        $linjamsos = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsos = WorkUnit::where('name', 'like', '%Rehsos%')->first();
        $puskesos = WorkUnit::where('name', 'like', '%Puskesos%')->first();

        $kanigoro = District::where('name', 'Kanigoro')->first();
        $desaKanigoro = Village::where('name', 'Kanigoro')->first();
        $desaSatreyan = Village::where('name', 'Satreyan')->first();

        $garum = District::where('name', 'Garum')->first();
        $desaGarum = Village::where('name', 'Garum')->first();

        $wlingi = District::where('name', 'Wlingi')->first();
        $desaWlingi = Village::where('name', 'Wlingi')->first();

        $talun = District::where('name', 'Talun')->first();
        $desaTalun = Village::where('name', 'Talun')->first();

        $users = [
            // 1. Existing / Superadmin
            [
                'email' => 'adi@adi.com',
                'name' => 'Kurnia Wahyu Adi',
                'phone' => '081234567890',
                'nik' => '3505060101900001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            [
                'email' => 'hilmi@hilmi.com',
                'name' => 'Hilmi Administrator',
                'phone' => '081234567891',
                'nik' => '3505060101900002',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            // 2. Administrator Sistem Dinsos
            [
                'email' => 'admin@dinsos.blitarkab.go.id',
                'name' => 'Administrator SAPA SOSIAL',
                'phone' => '081133224455',
                'nik' => '3505061010880001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            // 3. Pimpinan & Pejabat Penandatangan
            [
                'email' => 'kadis@dinsos.blitarkab.go.id',
                'name' => 'Drs. H. Bambang Hermanto, M.Si',
                'phone' => '081233001122',
                'nik' => '3505061504680001',
                'work_unit_id' => $sekretariat?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            [
                'email' => 'kabid.linjamsos@dinsos.blitarkab.go.id',
                'name' => 'Rina Setyowati, S.Sos, M.AP',
                'phone' => '081233001123',
                'nik' => '3505062208750002',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            [
                'email' => 'kabid.rehsos@dinsos.blitarkab.go.id',
                'name' => 'Budi Santoso, S.ST',
                'phone' => '081233001124',
                'nik' => '3505061803730003',
                'work_unit_id' => $rehsos?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            // 4. Petugas Teknis Pelayanan & Rehsos
            [
                'email' => 'petugas.layanan@dinsos.blitarkab.go.id',
                'name' => "Ahmad Mu'amar Muzakki",
                'phone' => '085733990011',
                'nik' => '3505061907950001',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaSatreyan?->id,
                'is_active' => true,
            ],
            [
                'email' => 'petugas.pbi@dinsos.blitarkab.go.id',
                'name' => 'Dewi Lestari, S.Tr.Sos',
                'phone' => '085733990022',
                'nik' => '3505076005930001',
                'work_unit_id' => $linjamsos?->id,
                'district_id' => $garum?->id,
                'village_id' => $desaGarum?->id,
                'is_active' => true,
            ],
            [
                'email' => 'petugas.rehsos@dinsos.blitarkab.go.id',
                'name' => 'dr. Hendro Prasetyo',
                'phone' => '085733990033',
                'nik' => '3505131411870001',
                'work_unit_id' => $rehsos?->id,
                'district_id' => $wlingi?->id,
                'village_id' => $desaWlingi?->id,
                'is_active' => true,
            ],
            // 5. Operator Kecamatan / Desa / Puskesos
            [
                'email' => 'operator.kanigoro@blitarkab.go.id',
                'name' => 'Siti Nurhaliza (Operator Kec. Kanigoro)',
                'phone' => '082144556677',
                'nik' => '3505064509960001',
                'work_unit_id' => $puskesos?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            [
                'email' => 'operator.satreyan@blitarkab.go.id',
                'name' => 'Eko Prasetyo (Operator Desa Satreyan)',
                'phone' => '082144556688',
                'nik' => '3505061211940002',
                'work_unit_id' => $puskesos?->id,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaSatreyan?->id,
                'is_active' => true,
            ],
            [
                'email' => 'operator.wlingi@blitarkab.go.id',
                'name' => 'Tri Wahyuni (Operator Kec. Wlingi)',
                'phone' => '082144556699',
                'nik' => '3505135102950001',
                'work_unit_id' => $puskesos?->id,
                'district_id' => $wlingi?->id,
                'village_id' => $desaWlingi?->id,
                'is_active' => true,
            ],
            // 6. Akun Masyarakat / Warga
            [
                'email' => 'warga.agus@gmail.com',
                'name' => 'Agus Supriyadi',
                'phone' => '081399887766',
                'nik' => '3505061203800001',
                'work_unit_id' => null,
                'district_id' => $kanigoro?->id,
                'village_id' => $desaKanigoro?->id,
                'is_active' => true,
            ],
            [
                'email' => 'warga.siti@gmail.com',
                'name' => 'Siti Aminah',
                'phone' => '081399887777',
                'nik' => '3505075408850002',
                'work_unit_id' => null,
                'district_id' => $garum?->id,
                'village_id' => $desaGarum?->id,
                'is_active' => true,
            ],
            [
                'email' => 'warga.joko@gmail.com',
                'name' => 'Joko Susilo',
                'phone' => '081399887788',
                'nik' => '3505131505920003',
                'work_unit_id' => null,
                'district_id' => $wlingi?->id,
                'village_id' => $desaWlingi?->id,
                'is_active' => true,
            ],
            [
                'email' => 'warga.bambang@gmail.com',
                'name' => 'Bambang Sujatmiko',
                'phone' => '081399887799',
                'nik' => '3505101007780004',
                'work_unit_id' => null,
                'district_id' => $talun?->id,
                'village_id' => $desaTalun?->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => $password,
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
