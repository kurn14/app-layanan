<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class DistrictAndVillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            [
                'code' => '35.05.01',
                'name' => 'Wonodadi',
                'villages' => [
                    ['code' => '35.05.01.2001', 'name' => 'Wonodadi'],
                    ['code' => '35.05.01.2002', 'name' => 'Pikatan'],
                    ['code' => '35.05.01.2003', 'name' => 'Rejosari'],
                    ['code' => '35.05.01.2004', 'name' => 'Kolomayan'],
                    ['code' => '35.05.01.2005', 'name' => 'Tawangrejo'],
                ],
            ],
            [
                'code' => '35.05.02',
                'name' => 'Udanawu',
                'villages' => [
                    ['code' => '35.05.02.2001', 'name' => 'Bakis'],
                    ['code' => '35.05.02.2002', 'name' => 'Besuki'],
                    ['code' => '35.05.02.2003', 'name' => 'Bendorejo'],
                    ['code' => '35.05.02.2004', 'name' => 'Ringinanom'],
                    ['code' => '35.05.02.2005', 'name' => 'Karanggondang'],
                ],
            ],
            [
                'code' => '35.05.03',
                'name' => 'Sanankulon',
                'villages' => [
                    ['code' => '35.05.03.2001', 'name' => 'Sanankulon'],
                    ['code' => '35.05.03.2002', 'name' => 'Kalipucang'],
                    ['code' => '35.05.03.2003', 'name' => 'Bendowulung'],
                    ['code' => '35.05.03.2004', 'name' => 'Purworejo'],
                    ['code' => '35.05.03.2005', 'name' => 'Sumber'],
                ],
            ],
            [
                'code' => '35.05.04',
                'name' => 'Ponggok',
                'villages' => [
                    ['code' => '35.05.04.2001', 'name' => 'Ponggok'],
                    ['code' => '35.05.04.2002', 'name' => 'Kawedusan'],
                    ['code' => '35.05.04.2003', 'name' => 'Maliran'],
                    ['code' => '35.05.04.2004', 'name' => 'Candirejo'],
                    ['code' => '35.05.04.2005', 'name' => 'Pojok'],
                ],
            ],
            [
                'code' => '35.05.05',
                'name' => 'Nglegok',
                'villages' => [
                    ['code' => '35.05.05.1001', 'name' => 'Nglegok'],
                    ['code' => '35.05.05.2002', 'name' => 'Modangan'],
                    ['code' => '35.05.05.2003', 'name' => 'Penataran'],
                    ['code' => '35.05.05.2004', 'name' => 'Kedawung'],
                    ['code' => '35.05.05.2005', 'name' => 'Sumberasri'],
                ],
            ],
            [
                'code' => '35.05.06',
                'name' => 'Kanigoro',
                'villages' => [
                    ['code' => '35.05.06.1001', 'name' => 'Kanigoro'],
                    ['code' => '35.05.06.1002', 'name' => 'Satreyan'],
                    ['code' => '35.05.06.2003', 'name' => 'Tlogo'],
                    ['code' => '35.05.06.2004', 'name' => 'Gaprang'],
                    ['code' => '35.05.06.2005', 'name' => 'Sawentar'],
                    ['code' => '35.05.06.2006', 'name' => 'Kuningan'],
                    ['code' => '35.05.06.2007', 'name' => 'Minggirsari'],
                ],
            ],
            [
                'code' => '35.05.07',
                'name' => 'Garum',
                'villages' => [
                    ['code' => '35.05.07.1001', 'name' => 'Garum'],
                    ['code' => '35.05.07.1002', 'name' => 'Tawangsari'],
                    ['code' => '35.05.07.1003', 'name' => 'Bence'],
                    ['code' => '35.05.07.2004', 'name' => 'Tingal'],
                    ['code' => '35.05.07.2005', 'name' => 'Pojok'],
                    ['code' => '35.05.07.2006', 'name' => 'Slorok'],
                ],
            ],
            [
                'code' => '35.05.08',
                'name' => 'Sutojayan',
                'villages' => [
                    ['code' => '35.05.08.1001', 'name' => 'Sutojayan'],
                    ['code' => '35.05.08.1002', 'name' => 'Kalipang'],
                    ['code' => '35.05.08.1003', 'name' => 'Sukorejo'],
                    ['code' => '35.05.08.1004', 'name' => 'Kembangarum'],
                    ['code' => '35.05.08.2005', 'name' => 'Pandanarum'],
                ],
            ],
            [
                'code' => '35.05.09',
                'name' => 'Panggungrejo',
                'villages' => [
                    ['code' => '35.05.09.2001', 'name' => 'Panggungrejo'],
                    ['code' => '35.05.09.2002', 'name' => 'Serang'],
                    ['code' => '35.05.09.2003', 'name' => 'Kalitengah'],
                    ['code' => '35.05.09.2004', 'name' => 'Margomulyo'],
                    ['code' => '35.05.09.2005', 'name' => 'Sumberagung'],
                ],
            ],
            [
                'code' => '35.05.10',
                'name' => 'Talun',
                'villages' => [
                    ['code' => '35.05.10.1001', 'name' => 'Talun'],
                    ['code' => '35.05.10.1002', 'name' => 'Kamulan'],
                    ['code' => '35.05.10.2003', 'name' => 'Kendalrejo'],
                    ['code' => '35.05.10.2004', 'name' => 'Pasirharjo'],
                    ['code' => '35.05.10.2005', 'name' => 'Jeblog'],
                ],
            ],
            [
                'code' => '35.05.11',
                'name' => 'Gandusari',
                'villages' => [
                    ['code' => '35.05.11.2001', 'name' => 'Gandusari'],
                    ['code' => '35.05.11.2002', 'name' => 'Sukosewu'],
                    ['code' => '35.05.11.2003', 'name' => 'Kotes'],
                    ['code' => '35.05.11.2004', 'name' => 'Semen'],
                    ['code' => '35.05.11.2005', 'name' => 'Tulungrejo'],
                ],
            ],
            [
                'code' => '35.05.12',
                'name' => 'Binangun',
                'villages' => [
                    ['code' => '35.05.12.2001', 'name' => 'Binangun'],
                    ['code' => '35.05.12.2002', 'name' => 'Rejoso'],
                    ['code' => '35.05.12.2003', 'name' => 'Kedungwungu'],
                    ['code' => '35.05.12.2004', 'name' => 'Sambigede'],
                    ['code' => '35.05.12.2005', 'name' => 'Ngembul'],
                ],
            ],
            [
                'code' => '35.05.13',
                'name' => 'Wlingi',
                'villages' => [
                    ['code' => '35.05.13.1001', 'name' => 'Wlingi'],
                    ['code' => '35.05.13.1002', 'name' => 'Beru'],
                    ['code' => '35.05.13.1003', 'name' => 'Babadan'],
                    ['code' => '35.05.13.1004', 'name' => 'Tangkil'],
                    ['code' => '35.05.13.2005', 'name' => 'Tembalang'],
                ],
            ],
            [
                'code' => '35.05.14',
                'name' => 'Doko',
                'villages' => [
                    ['code' => '35.05.14.2001', 'name' => 'Doko'],
                    ['code' => '35.05.14.2002', 'name' => 'Resapombo'],
                    ['code' => '35.05.14.2003', 'name' => 'Plumbangan'],
                    ['code' => '35.05.14.2004', 'name' => 'Suru'],
                    ['code' => '35.05.14.2005', 'name' => 'Kalimanis'],
                ],
            ],
            [
                'code' => '35.05.15',
                'name' => 'Kesamben',
                'villages' => [
                    ['code' => '35.05.15.2001', 'name' => 'Kesamben'],
                    ['code' => '35.05.15.2002', 'name' => 'Pagerwojo'],
                    ['code' => '35.05.15.2003', 'name' => 'Siraman'],
                    ['code' => '35.05.15.2004', 'name' => 'Jugo'],
                    ['code' => '35.05.15.2005', 'name' => 'Tapakrejo'],
                ],
            ],
            [
                'code' => '35.05.16',
                'name' => 'Wates',
                'villages' => [
                    ['code' => '35.05.16.2001', 'name' => 'Wates'],
                    ['code' => '35.05.16.2002', 'name' => 'Mojorejo'],
                    ['code' => '35.05.16.2003', 'name' => 'Ringinrejo'],
                    ['code' => '35.05.16.2004', 'name' => 'Tugurejo'],
                    ['code' => '35.05.16.2005', 'name' => 'Purworejo'],
                ],
            ],
            [
                'code' => '35.05.17',
                'name' => 'Kademangan',
                'villages' => [
                    ['code' => '35.05.17.1001', 'name' => 'Kademangan'],
                    ['code' => '35.05.17.2002', 'name' => 'Rejotangan'],
                    ['code' => '35.05.17.2003', 'name' => 'Plosorejo'],
                    ['code' => '35.05.17.2004', 'name' => 'Jimbe'],
                    ['code' => '35.05.17.2005', 'name' => 'Darungan'],
                ],
            ],
            [
                'code' => '35.05.18',
                'name' => 'Bakung',
                'villages' => [
                    ['code' => '35.05.18.2001', 'name' => 'Bakung'],
                    ['code' => '35.05.18.2002', 'name' => 'Plandirejo'],
                    ['code' => '35.05.18.2003', 'name' => 'Lorejo'],
                    ['code' => '35.05.18.2004', 'name' => 'Pulerejo'],
                    ['code' => '35.05.18.2005', 'name' => 'Sidomulyo'],
                ],
            ],
            [
                'code' => '35.05.19',
                'name' => 'Wonotirto',
                'villages' => [
                    ['code' => '35.05.19.2001', 'name' => 'Wonotirto'],
                    ['code' => '35.05.19.2002', 'name' => 'Pasiraman'],
                    ['code' => '35.05.19.2003', 'name' => 'Gununggede'],
                    ['code' => '35.05.19.2004', 'name' => 'Tambakrejo'],
                    ['code' => '35.05.19.2005', 'name' => 'Kaligrenjeng'],
                ],
            ],
            [
                'code' => '35.05.20',
                'name' => 'Selorejo',
                'villages' => [
                    ['code' => '35.05.20.2001', 'name' => 'Selorejo'],
                    ['code' => '35.05.20.2002', 'name' => 'Boro'],
                    ['code' => '35.05.20.2003', 'name' => 'Olak-Alen'],
                    ['code' => '35.05.20.2004', 'name' => 'Pohgajih'],
                    ['code' => '35.05.20.2005', 'name' => 'Sidomulyo'],
                ],
            ],
            [
                'code' => '35.05.21',
                'name' => 'Selopuro',
                'villages' => [
                    ['code' => '35.05.21.2001', 'name' => 'Selopuro'],
                    ['code' => '35.05.21.2002', 'name' => 'Jatitengah'],
                    ['code' => '35.05.21.2003', 'name' => 'Ploso'],
                    ['code' => '35.05.21.2004', 'name' => 'Jambewangi'],
                    ['code' => '35.05.21.2005', 'name' => 'Mandesan'],
                ],
            ],
            [
                'code' => '35.05.22',
                'name' => 'Srengat',
                'villages' => [
                    ['code' => '35.05.22.1001', 'name' => 'Srengat'],
                    ['code' => '35.05.22.1002', 'name' => 'Dandong'],
                    ['code' => '35.05.22.1003', 'name' => 'Kauman'],
                    ['code' => '35.05.22.1004', 'name' => 'Togogan'],
                    ['code' => '35.05.22.2005', 'name' => 'Selokajang'],
                ],
            ],
        ];

        foreach ($districts as $districtData) {
            $district = District::updateOrCreate(
                ['code' => $districtData['code']],
                ['name' => $districtData['name']]
            );

            foreach ($districtData['villages'] as $villageData) {
                Village::updateOrCreate(
                    ['code' => $villageData['code']],
                    [
                        'district_id' => $district->id,
                        'name' => $villageData['name'],
                    ]
                );
            }
        }
    }
}
