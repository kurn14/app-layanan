<?php

namespace Database\Seeders;

use App\Enums\ComplaintAttachmentType;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\NumberSequence;
use App\Models\RehabilitationCase;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $period = Carbon::now()->format('Ym');

        $petugasLayanan = User::where('email', 'petugas.layanan@dinsos.blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@dinsos.blitarkab.go.id')->first();
        $kadis = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();
        $wargaJoko = User::where('email', 'warga.joko@gmail.com')->first();

        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehsos%')->first();
        $linjamsosUnit = WorkUnit::where('name', 'like', '%Linjamsos%')->first();

        $catOdgj = ComplaintCategory::where('name', 'like', '%Orang Terlantar%')->first();
        $catBansos = ComplaintCategory::where('name', 'like', '%Bantuan Sosial%')->first();
        $catLansia = ComplaintCategory::where('name', 'like', '%Lansia%')->first();

        $desaKanigoro = Village::where('name', 'Kanigoro')->first();
        $desaSawentar = Village::where('name', 'Sawentar')->first();
        $desaSerang = Village::where('name', 'Serang')->first();

        // =========================================================================
        // PENGADUAN 1: ADU-202609-00001 (IN_HANDLING - ODGJ Resah di Pasar Kanigoro)
        // Diteruskan menjadi kasus rehabilitasi RHS-202609-00002!
        // =========================================================================
        $c1 = Complaint::updateOrCreate(
            ['complaint_number' => "ADU-{$period}-00001"],
            [
                'complaint_category_id' => $catOdgj?->id,
                'reporter_id' => $wargaJoko?->id,
                'reporter_name' => 'Joko Susilo',
                'reporter_phone' => '081399887788',
                'location_detail' => 'Emperan Toko Barokah, Depan Pasar Kanigoro',
                'village_id' => $desaKanigoro?->id,
                'description' => 'Ada seorang pria paruh baya tanpa identitas dan tanpa pakaian yang layak, sering berteriak dan tidur di depan pasar Kanigoro. Sangat mengkhawatirkan keselamatan pengunjung dan dirinya sendiri.',
                'reported_at' => Carbon::now()->subDays(3),
                'officer_id' => $petugasRehsos?->id,
                'status' => ComplaintStatus::IN_HANDLING,
                'verification_result' => 'Laporan telah diverifikasi benar oleh Pamong Desa Kanigoro dan Polsek Kanigoro.',
                'action_taken' => 'Tim TRC Rehabilitasi Sosial telah mengevakuasi klien ke RSUD Wlingi dan membuka kasus rehabilitasi terpadu.',
            ]
        );

        // Attachment
        ComplaintAttachment::updateOrCreate(
            ['complaint_id' => $c1->id, 'file_path' => 'complaints/202609/odgj_pasar_kanigoro.jpg'],
            ['type' => ComplaintAttachmentType::PHOTO]
        );

        // Disposition dari Kadis ke Bidang Rehsos
        Disposition::updateOrCreate(
            [
                'dispositionable_type' => Complaint::class,
                'dispositionable_id' => $c1->id,
                'from_user_id' => $kadis?->id ?? $petugasLayanan?->id,
                'to_work_unit_id' => $rehsosUnit?->id,
            ],
            [
                'to_user_id' => $petugasRehsos?->id,
                'instructions' => 'Segera terjunkan tim TRC untuk evakuasi dan assessment medis kejiwaan.',
                'disposed_at' => Carbon::now()->subDays(3),
            ]
        );

        // Link with Rehabilitation Case RHS-202609-00002
        $case2 = RehabilitationCase::where('case_number', "RHS-{$period}-00002")->first();
        if ($case2) {
            $case2->update(['complaint_id' => $c1->id]);
        }

        $this->seedHistory($c1, null, ComplaintStatus::RECEIVED->value, 'Pengaduan disampaikan via aplikasi SAPA SOSIAL', $wargaJoko?->id, Carbon::now()->subDays(3));
        $this->seedHistory($c1, ComplaintStatus::RECEIVED->value, ComplaintStatus::VERIFICATION->value, 'Verifikasi awal lokasi kejadian', $petugasLayanan?->id, Carbon::now()->subDays(3));
        $this->seedHistory($c1, ComplaintStatus::VERIFICATION->value, ComplaintStatus::DISPATCHED->value, 'Didisposisikan ke Bidang Rehabilitasi Sosial', $kadis?->id, Carbon::now()->subDays(3));
        $this->seedHistory($c1, ComplaintStatus::DISPATCHED->value, ComplaintStatus::IN_HANDLING->value, 'Tim TRC meluncur ke lokasi pasar Kanigoro untuk evakuasi', $petugasRehsos?->id, Carbon::now()->subDays(2));

        // =========================================================================
        // PENGADUAN 2: ADU-202609-00002 (RESOLVED - Selesai Ditangani)
        // Bansos Salah Sasaran di Sawentar
        // =========================================================================
        $c2 = Complaint::updateOrCreate(
            ['complaint_number' => "ADU-{$period}-00002"],
            [
                'complaint_category_id' => $catBansos?->id,
                'reporter_id' => null,
                'reporter_name' => 'Nur Cahyono',
                'reporter_phone' => '082188776655',
                'location_detail' => 'Dusun Krajan RT 02 RW 03, Desa Sawentar, Kanigoro',
                'village_id' => $desaSawentar?->id,
                'description' => 'Terdapat penerima bantuan sembako daerah yang keluarganya sudah tergolong mampu dan memiliki kendaraan roda empat, sedangkan janda tua miskin di sebelahnya tidak terdata.',
                'reported_at' => Carbon::now()->subDays(8),
                'officer_id' => $petugasLayanan?->id,
                'status' => ComplaintStatus::RESOLVED,
                'verification_result' => 'Data penerima telah diverifikasi di lapangan bersama Kepala Dusun Krajan.',
                'action_taken' => 'Telah dilakukan musyawarah desa khusus. Data penerima yang bersangkutan telah dialihkan kepada warga desil 1 yang lebih berhak dengan Berita Acara Musdes No. 400/12/Desa/2026.',
                'resolved_at' => Carbon::now()->subDays(1),
            ]
        );

        Disposition::updateOrCreate(
            [
                'dispositionable_type' => Complaint::class,
                'dispositionable_id' => $c2->id,
                'from_user_id' => $petugasLayanan?->id,
                'to_work_unit_id' => $linjamsosUnit?->id,
            ],
            [
                'to_user_id' => $petugasLayanan?->id,
                'instructions' => 'Koordinasi dengan operator SIKS-NG desa untuk verifikasi data penerima.',
                'disposed_at' => Carbon::now()->subDays(7),
            ]
        );

        $this->seedHistory($c2, null, ComplaintStatus::RECEIVED->value, 'Pengaduan masuk', null, Carbon::now()->subDays(8));
        $this->seedHistory($c2, ComplaintStatus::RECEIVED->value, ComplaintStatus::VERIFICATION->value, 'Klarifikasi dan cek ke Pemdes Sawentar', $petugasLayanan?->id, Carbon::now()->subDays(7));
        $this->seedHistory($c2, ComplaintStatus::VERIFICATION->value, ComplaintStatus::IN_HANDLING->value, 'Pelaksanaan Musdes Pemutakhiran Data', $petugasLayanan?->id, Carbon::now()->subDays(4));
        $this->seedHistory($c2, ComplaintStatus::IN_HANDLING->value, ComplaintStatus::RESOLVED->value, 'Masalah selesai dengan Berita Acara Musdes', $petugasLayanan?->id, Carbon::now()->subDays(1));

        // =========================================================================
        // PENGADUAN 3: ADU-202609-00003 (VERIFICATION - Sedang diverifikasi)
        // Lansia Terlantar di Desa Serang, Panggungrejo
        // =========================================================================
        $c3 = Complaint::updateOrCreate(
            ['complaint_number' => "ADU-{$period}-00003"],
            [
                'complaint_category_id' => $catLansia?->id,
                'reporter_id' => null,
                'reporter_name' => 'M. Ridwan',
                'reporter_phone' => '085322110099',
                'location_detail' => 'Dusun Serang RT 01 RW 01 (Dekat TPI Serang)',
                'village_id' => $desaSerang?->id,
                'description' => 'Ada seorang nenek hidup sendirian di gubuk tepi pantai, kondisi sakit dan kesulitan mendapatkan makanan harian.',
                'reported_at' => Carbon::now()->subHours(8),
                'officer_id' => $petugasLayanan?->id,
                'status' => ComplaintStatus::VERIFICATION,
                'verification_result' => 'Petugas sedang menghubungi Puskesmas Pembantu Serang dan Kasun Serang.',
            ]
        );

        $this->seedHistory($c3, null, ComplaintStatus::RECEIVED->value, 'Laporan diterima', null, Carbon::now()->subHours(8));
        $this->seedHistory($c3, ComplaintStatus::RECEIVED->value, ComplaintStatus::VERIFICATION->value, 'Koordinasi awal aparat desa setempat', $petugasLayanan?->id, Carbon::now()->subHours(4));

        // =========================================================================
        // PENGADUAN 4: ADU-202609-00004 (DUPLICATE - Laporan Kembar ODGJ Pasar Kanigoro)
        // Induk: ADU-202609-00001
        // =========================================================================
        $c4 = Complaint::updateOrCreate(
            ['complaint_number' => "ADU-{$period}-00004"],
            [
                'complaint_category_id' => $catOdgj?->id,
                'reporter_id' => null,
                'reporter_name' => 'Hj. Romlah (Pedagang Pasar Kanigoro)',
                'reporter_phone' => '081233998877',
                'location_detail' => 'Pasar Kanigoro depan deretan kios beras',
                'village_id' => $desaKanigoro?->id,
                'description' => 'Tolong orang gila yang di depan pasar segera diangkut dinas sosial, bikin takut pembeli yang mau belanja.',
                'reported_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasLayanan?->id,
                'status' => ComplaintStatus::DUPLICATE,
                'verification_result' => 'Laporan mengenai objek dan peristiwa yang sama dengan laporan ADU-202609-00001.',
                'action_taken' => 'Digabungkan ke penanganan tiket ADU-202609-00001.',
                'duplicate_of_id' => $c1->id,
                'resolved_at' => Carbon::now()->subDays(2),
            ]
        );

        $this->seedHistory($c4, null, ComplaintStatus::RECEIVED->value, 'Laporan masuk', null, Carbon::now()->subDays(2));
        $this->seedHistory($c4, ComplaintStatus::RECEIVED->value, ComplaintStatus::DUPLICATE->value, "Ditandai duplikat dari laporan {$c1->complaint_number}", $petugasLayanan?->id, Carbon::now()->subDays(2));

        // Update Number Sequence for ADU
        NumberSequence::updateOrCreate(
            ['prefix' => 'ADU', 'period' => $period],
            ['last_number' => 4]
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
