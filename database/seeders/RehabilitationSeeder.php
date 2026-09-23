<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\MonitoringRecord;
use App\Models\NumberSequence;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RehabilitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $period = Carbon::now()->format('Ym');

        $petugasRehsos = User::where('email', 'petugas.rehsos@dinsos.blitarkab.go.id')->first();

        $catLansia = ClientCategory::where('name', 'like', '%Lanjut Usia%')->first();
        $catOdgj = ClientCategory::where('name', 'like', '%ODGJ%')->first();
        $catAnak = ClientCategory::where('name', 'like', '%Anak%')->first();
        $catDisabilitas = ClientCategory::where('name', 'like', '%Disabilitas%')->first();

        $desaSatreyan = Village::where('name', 'Satreyan')->first();
        $desaKanigoro = Village::where('name', 'Kanigoro')->first();
        $desaWlingi = Village::where('name', 'Wlingi')->first();
        $desaSutojayan = Village::where('name', 'Sutojayan')->first();

        $brsluMagetan = ReferralInstitution::where('name', 'like', '%Magetan%')->first();
        $rsudWlingi = ReferralInstitution::where('name', 'like', '%RSUD Ngudi Waluyo%')->first();
        $lksDisabilitas = ReferralInstitution::where('name', 'like', '%Harapan Mulia%')->first();

        $srRehsos1 = ServiceRequest::where('request_number', "REHSOS-{$period}-00001")->first();

        // =========================================================================
        // KLIEN 1: Mbah Marto (Lansia Terlantar -> Rujukan ke BRSLU Magetan)
        // =========================================================================
        $client1 = Client::updateOrCreate(
            ['name' => 'Mbah Marto', 'village_id' => $desaSatreyan?->id],
            [
                'client_category_id' => $catLansia?->id,
                'nik' => '3505060101440005',
                'birth_date' => Carbon::createFromDate(1944, 5, 12),
                'gender' => Gender::MALE,
                'address' => 'RT 04 RW 02 Dusun Krajan, Desa Satreyan, Kec. Kanigoro',
                'phone' => null,
            ]
        );

        $case1 = RehabilitationCase::updateOrCreate(
            ['case_number' => "RHS-{$period}-00001"],
            [
                'client_id' => $client1->id,
                'service_request_id' => $srRehsos1?->id,
                'complaint_id' => null,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::REFERRAL,
                'status' => RehabilitationCaseStatus::IN_SERVICE,
                'handling_result' => null,
                'received_at' => Carbon::now()->subDays(7),
            ]
        );

        $assessment1 = Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case1->id],
            [
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(6)->toDateString(),
                'result' => 'Klien lansia berusia 82 tahun sebatang kara, rumah semi permanen nyaris roboh, tidak ada keluarga yang mengurus. Mengalami penurunan daya ingat dan mobilitas lambat.',
                'service_needs' => 'Kebutuhan tempat tinggal layak, jaminan pemenuhan permakanan, perawatan kesehatan harian, dan lingkungan sosial ramah lansia.',
                'recommendation' => 'Direkomendasikan rujukan residensial panti ke Balai Rehabilitasi Sosial Lanjut Usia (BRSLU) Magetan.',
                'needs_referral' => true,
            ]
        );

        $referral1 = Referral::updateOrCreate(
            ['referral_number' => "RJK-{$period}-00001"],
            [
                'rehabilitation_case_id' => $case1->id,
                'assessment_id' => $assessment1->id,
                'referral_institution_id' => $brsluMagetan?->id,
                'officer_id' => $petugasRehsos?->id,
                'referral_date' => Carbon::now()->subDays(5)->toDateString(),
                'status' => ReferralStatus::IN_SERVICE,
                'service_result' => 'Klien telah diterima dan menempati Wisma Anyelir BRSLU Magetan.',
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case1->id,
                'referral_id' => $referral1->id,
                'monitoring_date' => Carbon::now()->subDays(2)->toDateString(),
            ],
            [
                'officer_id' => $petugasRehsos?->id,
                'progress' => 'Koordinasi via daring dan konfirmasi pekerja sosial panti: Mbah Marto sudah mulai berbaur dengan lansia lain dan rutin mengikuti senam lansia pagi.',
                'result_notes' => 'Pemeriksaan tensi darah normal, nafsu makan baik.',
            ]
        );

        $this->seedHistory($case1, null, RehabilitationCaseStatus::RECEIVED->value, 'Kasus diterima dari pengajuan Layanan Sosial Pemdes Satreyan', $petugasRehsos?->id, Carbon::now()->subDays(7));
        $this->seedHistory($case1, RehabilitationCaseStatus::RECEIVED->value, RehabilitationCaseStatus::ASSESSMENT->value, 'Assessment komprehensif oleh Pekerja Sosial Dinsos', $petugasRehsos?->id, Carbon::now()->subDays(6));
        $this->seedHistory($case1, RehabilitationCaseStatus::ASSESSMENT->value, RehabilitationCaseStatus::SERVICE_PLANNING->value, 'Penyusunan rencana pelayanan rujukan BRSLU', $petugasRehsos?->id, Carbon::now()->subDays(5));
        $this->seedHistory($case1, RehabilitationCaseStatus::SERVICE_PLANNING->value, RehabilitationCaseStatus::IN_SERVICE->value, 'Klien resmi dirujuk dan dalam pelayanan BRSLU Magetan', $petugasRehsos?->id, Carbon::now()->subDays(4));

        // =========================================================================
        // KLIEN 2: Joko Santoso (ODGJ Terlantar Pasar Kanigoro -> RSUD Wlingi)
        // (Nantinya di ComplaintSeeder akan dihubungkan ke complaint_id)
        // =========================================================================
        $client2 = Client::updateOrCreate(
            ['name' => 'Joko Santoso (Mr. X Pasar Kanigoro)', 'village_id' => $desaKanigoro?->id],
            [
                'client_category_id' => $catOdgj?->id,
                'nik' => null, // Klien tanpa identitas awal
                'birth_date' => Carbon::createFromDate(1989, 2, 10),
                'gender' => Gender::MALE,
                'address' => 'Ditemukan di Kompleks Pasar Kanigoro, Kec. Kanigoro',
                'phone' => null,
            ]
        );

        $case2 = RehabilitationCase::updateOrCreate(
            ['case_number' => "RHS-{$period}-00002"],
            [
                'client_id' => $client2->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::BOTH,
                'status' => RehabilitationCaseStatus::IN_SERVICE,
                'received_at' => Carbon::now()->subDays(3),
            ]
        );

        $assessment2 = Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case2->id],
            [
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(3)->toDateString(),
                'result' => 'Klien mengalami disorientasi waktu dan tempat, gelisah, berbicara melantur, dan sempat berteriak di pasar. Berpotensi mencelakai diri atau orang lain bila tidak ditangani medis.',
                'service_needs' => 'Stabilisasi kejiwaan medis psikiatris segera di rumah sakit, dilanjutkan penelusuran keluarga (biometrik) dan rehabilitasi sosial lanjutan.',
                'recommendation' => 'Evakuasi darurat dan rujukan ke Instalasi Kedokteran Jiwa RSUD Ngudi Waluyo Wlingi.',
                'needs_referral' => true,
            ]
        );

        $referral2 = Referral::updateOrCreate(
            ['referral_number' => "RJK-{$period}-00002"],
            [
                'rehabilitation_case_id' => $case2->id,
                'assessment_id' => $assessment2->id,
                'referral_institution_id' => $rsudWlingi?->id,
                'officer_id' => $petugasRehsos?->id,
                'referral_date' => Carbon::now()->subDays(2)->toDateString(),
                'status' => ReferralStatus::ACCEPTED,
                'service_result' => 'Klien dirawat di Ruang Wijaya Kusuma RSUD Wlingi.',
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case2->id,
                'referral_id' => $referral2->id,
                'monitoring_date' => Carbon::now()->subDay()->toDateString(),
            ],
            [
                'officer_id' => $petugasRehsos?->id,
                'progress' => 'Kondisi kegelisahan mulai mereda pasca pemberian injeksi antipsikotik. Tim Dinsos berkoordinasi dengan Disdukcapil untuk perekaman iris mata biometrik pelacakan keluarga.',
                'result_notes' => 'Keluarga sementara belum ditemukan, koordinasi desa sekitar berlanjut.',
            ]
        );

        $this->seedHistory($case2, null, RehabilitationCaseStatus::RECEIVED->value, 'Laporan evakuasi ODGJ masuk via TRC', $petugasRehsos?->id, Carbon::now()->subDays(3));
        $this->seedHistory($case2, RehabilitationCaseStatus::RECEIVED->value, RehabilitationCaseStatus::ASSESSMENT->value, 'Assessment cepat di lokasi oleh tim TRC Dinsos', $petugasRehsos?->id, Carbon::now()->subDays(3));
        $this->seedHistory($case2, RehabilitationCaseStatus::ASSESSMENT->value, RehabilitationCaseStatus::IN_SERVICE->value, 'Rujukan darurat ke RSUD Ngudi Waluyo Wlingi', $petugasRehsos?->id, Carbon::now()->subDays(2));

        // =========================================================================
        // KLIEN 3: Ananda Dimas (Anak Memerlukan Perlindungan Khusus -> Pelayanan Langsung)
        // =========================================================================
        $client3 = Client::updateOrCreate(
            ['name' => 'Dimas Arya Pratama', 'village_id' => $desaWlingi?->id],
            [
                'client_category_id' => $catAnak?->id,
                'nik' => '3505131206150002',
                'birth_date' => Carbon::createFromDate(2015, 6, 12),
                'gender' => Gender::MALE,
                'address' => 'Kelurahan Beru, Kecamatan Wlingi',
                'phone' => '085711223344',
            ]
        );

        $case3 = RehabilitationCase::updateOrCreate(
            ['case_number' => "RHS-{$period}-00003"],
            [
                'client_id' => $client3->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::DIRECT,
                'status' => RehabilitationCaseStatus::MONITORING,
                'received_at' => Carbon::now()->subDays(15),
            ]
        );

        Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case3->id],
            [
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(14)->toDateString(),
                'result' => 'Anak yatim piatu, diasuh oleh bibi lansia dengan kondisi ekonomi rentan. Anak sempat membolos sekolah 2 minggu karena terkendala biaya seragam dan perlengkapan.',
                'service_needs' => 'Pendampingan psikososial, bantuan perlengkapan sekolah, dan advokasi ke pihak sekolah agar bebas iuran.',
                'recommendation' => 'Penanganan pelayanan langsung oleh Pekerja Sosial Dinsos dan pemberian paket asistensi pendidikan anak.',
                'needs_referral' => false,
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case3->id,
                'referral_id' => null,
                'monitoring_date' => Carbon::now()->subDays(4)->toDateString(),
            ],
            [
                'officer_id' => $petugasRehsos?->id,
                'progress' => 'Pekerja sosial melakukan home visit dan mediasi ke pihak SDN Beru 01. Anak telah kembali aktif bersekolah dengan perlengkapan lengkap bantuan Dinsos.',
                'result_notes' => 'Anak tampak ceria dan bersemangat belajar.',
            ]
        );

        $this->seedHistory($case3, null, RehabilitationCaseStatus::RECEIVED->value, 'Laporan dari satgas PPA desa', $petugasRehsos?->id, Carbon::now()->subDays(15));
        $this->seedHistory($case3, RehabilitationCaseStatus::RECEIVED->value, RehabilitationCaseStatus::ASSESSMENT->value, 'Assessment keluarga & psikososial', $petugasRehsos?->id, Carbon::now()->subDays(14));
        $this->seedHistory($case3, RehabilitationCaseStatus::ASSESSMENT->value, RehabilitationCaseStatus::IN_SERVICE->value, 'Penyerahan bantuan alat sekolah & advokasi pendidikan', $petugasRehsos?->id, Carbon::now()->subDays(10));
        $this->seedHistory($case3, RehabilitationCaseStatus::IN_SERVICE->value, RehabilitationCaseStatus::MONITORING->value, 'Monitoring keaktifan belajar anak', $petugasRehsos?->id, Carbon::now()->subDays(4));

        // =========================================================================
        // KLIEN 4: Ibu Sumiati (Disabilitas Fisik -> Bantuan Kursi Roda Selesai & CLOSED)
        // =========================================================================
        $client4 = Client::updateOrCreate(
            ['name' => 'Sumiati', 'village_id' => $desaSutojayan?->id],
            [
                'client_category_id' => $catDisabilitas?->id,
                'nik' => '3505085409820003',
                'birth_date' => Carbon::createFromDate(1982, 9, 24),
                'gender' => Gender::FEMALE,
                'address' => 'Kelurahan Sutojayan, Kecamatan Sutojayan',
                'phone' => '081299881122',
            ]
        );

        $case4 = RehabilitationCase::updateOrCreate(
            ['case_number' => "RHS-{$period}-00004"],
            [
                'client_id' => $client4->id,
                'officer_id' => $petugasRehsos?->id,
                'handling_type' => RehabilitationHandlingType::DIRECT,
                'status' => RehabilitationCaseStatus::CLOSED,
                'handling_result' => 'Bantuan kursi roda adaptif dan modal usaha warung kelontong telah disalurkan. Klien telah mampu beraktivitas mandiri secara produktif.',
                'received_at' => Carbon::now()->subDays(30),
                'closed_at' => Carbon::now()->subDays(5),
            ]
        );

        Assessment::updateOrCreate(
            ['rehabilitation_case_id' => $case4->id],
            [
                'officer_id' => $petugasRehsos?->id,
                'assessment_date' => Carbon::now()->subDays(28)->toDateString(),
                'result' => 'Klien mengalami disabilitas daksa paraplegia akibat kecelakaan kerja masa lalu. Memiliki keterampilan menjahit dan ingin membuka usaha mandiri.',
                'service_needs' => 'Alat bantu kursi roda dan bantuan stimulus ekonomi produktif.',
                'recommendation' => 'Penyaluran alat bantu kursi roda standar Dinsos dan fasilitasi bantuan usaha ekonomi produktif.',
                'needs_referral' => false,
            ]
        );

        MonitoringRecord::updateOrCreate(
            [
                'rehabilitation_case_id' => $case4->id,
                'referral_id' => null,
                'monitoring_date' => Carbon::now()->subDays(6)->toDateString(),
            ],
            [
                'officer_id' => $petugasRehsos?->id,
                'progress' => 'Monitoring akhir penggunaan kursi roda dan usaha warung kelontong di rumah. Klien sangat terbantu mobilitasnya dan omset usaha berjalan lancar.',
                'result_notes' => 'Kasus dinyatakan tuntas dan siap ditutup.',
            ]
        );

        $this->seedHistory($case4, null, RehabilitationCaseStatus::RECEIVED->value, 'Permohonan bantuan masuk', $petugasRehsos?->id, Carbon::now()->subDays(30));
        $this->seedHistory($case4, RehabilitationCaseStatus::RECEIVED->value, RehabilitationCaseStatus::IN_SERVICE->value, 'Penyerahan alat bantu kursi roda', $petugasRehsos?->id, Carbon::now()->subDays(20));
        $this->seedHistory($case4, RehabilitationCaseStatus::IN_SERVICE->value, RehabilitationCaseStatus::MONITORING->value, 'Monitoring perkembangan usaha mandiri', $petugasRehsos?->id, Carbon::now()->subDays(6));
        $this->seedHistory($case4, RehabilitationCaseStatus::MONITORING->value, RehabilitationCaseStatus::CLOSED->value, 'Kasus ditutup dengan hasil pelayanan sukses', $petugasRehsos?->id, Carbon::now()->subDays(5));

        // Update Number Sequences
        NumberSequence::updateOrCreate(
            ['prefix' => 'RHS', 'period' => $period],
            ['last_number' => 4]
        );
        NumberSequence::updateOrCreate(
            ['prefix' => 'RJK', 'period' => $period],
            ['last_number' => 2]
        );
    }

    private function seedHistory($model, ?string $from, string $to, string $notes, ?int $userId, Carbon $createdAt): void
    {
        StatusHistory::create([
            'statusable_type' => get_class($model),
            'statusable_id' => $model->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'user_id' => $userId,
            'created_at' => $createdAt,
        ]);
    }
}
