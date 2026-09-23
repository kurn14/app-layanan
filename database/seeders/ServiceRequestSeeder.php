<?php

namespace Database\Seeders;

use App\Enums\ApprovalDecision;
use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use App\Enums\ServiceDocumentVerificationStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\District;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\NumberSequence;
use App\Models\PbiReactivation;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dtsenType = ServiceType::where('code', 'DTSEN')->firstOrFail();
        $pbiType = ServiceType::where('code', 'PBI')->firstOrFail();
        $rehsosType = ServiceType::where('code', 'REHSOS')->firstOrFail();
        $bansosType = ServiceType::where('code', 'REK_BANSOS')->firstOrFail();

        $dtsenRequirements = $dtsenType->requirements->keyBy('sort_order');
        $pbiRequirements = $pbiType->requirements->keyBy('sort_order');
        $rehsosRequirements = $rehsosType->requirements->keyBy('sort_order');

        $linjamsos = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $rehsosUnit = WorkUnit::where('name', 'like', '%Rehsos%')->first();

        $petugasLayanan = User::where('email', 'petugas.layanan@dinsos.blitarkab.go.id')->first();
        $petugasPbi = User::where('email', 'petugas.pbi@dinsos.blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@dinsos.blitarkab.go.id')->first();

        $kabidLinjamsos = User::where('email', 'kabid.linjamsos@dinsos.blitarkab.go.id')->first();
        $kadis = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();

        $operatorKanigoro = User::where('email', 'operator.kanigoro@blitarkab.go.id')->first();
        $wargaAgus = User::where('email', 'warga.agus@gmail.com')->first();
        $wargaSiti = User::where('email', 'warga.siti@gmail.com')->first();

        $kanigoro = District::where('name', 'Kanigoro')->first();
        $desaKanigoro = Village::where('name', 'Kanigoro')->first();
        $desaSatreyan = Village::where('name', 'Satreyan')->first();

        $garum = District::where('name', 'Garum')->first();
        $desaGarum = Village::where('name', 'Garum')->first();

        $wlingi = District::where('name', 'Wlingi')->first();
        $desaWlingi = Village::where('name', 'Wlingi')->first();

        $talun = District::where('name', 'Talun')->first();
        $desaTalun = Village::where('name', 'Talun')->first();

        $spmbPurpose = DtsenPurpose::where('code', 'spmb')->first();
        $kipPurpose = DtsenPurpose::where('code', 'kip_kuliah')->first();
        $pipPurpose = DtsenPurpose::where('code', 'pip')->first();

        $period = Carbon::now()->format('Ym');

        // =========================================================================
        // 1. SK DTSEN TRANSACTIONS (Layanan 1)
        // =========================================================================

        // --- Kasus 1: DTSEN-202609-00001 (COMPLETED - Surat Terbit & Selesai) ---
        $srDtsen1 = ServiceRequest::updateOrCreate(
            ['request_number' => "DTSEN-{$period}-00001"],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $wargaAgus?->id,
                'applicant_name' => 'Agus Supriyadi',
                'applicant_nik' => '3505061203800001',
                'family_card_number' => '3505060105080012',
                'address' => 'RT 02 RW 01 Dusun Krajan, Desa Kanigoro',
                'village_id' => $desaKanigoro?->id,
                'phone' => '081399887766',
                'submitted_at' => Carbon::now()->subDays(6),
                'officer_id' => $petugasLayanan?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::COMPLETED,
                'is_priority' => false,
                'verification_result' => 'Data pemohon dan orang yang diterangkan valid sesuai KTP dan KK. Terdaftar di SIKS-NG pada Desil 2.',
                'officer_notes' => 'Memenuhi persyaratan jalur afirmasi SPMB SMAN 1 Blitar.',
                'service_result' => 'Surat Keterangan DTSEN telah diterbitkan dan diunduh oleh pemohon.',
                'completed_at' => Carbon::now()->subDays(4),
            ]
        );

        // Documents
        $this->seedDocument($srDtsen1->id, $dtsenRequirements[1]?->id, 'documents/dtsen/ktp_agus.jpg', 'ktp_agus.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srDtsen1->id, $dtsenRequirements[2]?->id, 'documents/dtsen/kk_agus.pdf', 'kk_keluarga_agus.pdf', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srDtsen1->id, $dtsenRequirements[3]?->id, 'documents/dtsen/bukti_daftar_spmb.pdf', 'bukti_pendaftaran_spmb.pdf', ServiceDocumentVerificationStatus::VALID);

        // Certificate
        $cert1 = DtsenCertificate::updateOrCreate(
            ['service_request_id' => $srDtsen1->id],
            [
                'dtsen_purpose_id' => $spmbPurpose?->id,
                'purpose_description' => 'Persyaratan Pendaftaran SPMB Jalur Afirmasi SMAN 1 Blitar Tahun Ajaran 2026/2027',
                'subject_name' => 'Rizky Supriyadi',
                'subject_nik' => '3505061508090001',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => true,
                'decile' => 2,
                'checked_at' => Carbon::now()->subDays(5),
                'checker_id' => $petugasLayanan?->id,
                'certificate_number' => "400.9/012/409.105/{$period}",
                'issued_at' => Carbon::now()->subDays(4),
                'valid_until' => Carbon::now()->addDays(90)->toDateString(),
                'signer_id' => $kadis?->id,
                'file_path' => 'certificates/dtsen_202609_00001.pdf',
                'verification_code' => 'DTSEN-BLT-2026-A7K92',
            ]
        );

        // Approvals (Berjenjang: Kabid -> Kadis)
        $this->seedApproval($cert1, 1, $kabidLinjamsos?->id, ApprovalDecision::APPROVED, 'Data SIKS-NG valid desil 2, draf surat disetujui.', Carbon::now()->subDays(5));
        $this->seedApproval($cert1, 2, $kadis?->id, ApprovalDecision::APPROVED, 'Ditandatangani secara digital.', Carbon::now()->subDays(4));

        // Status History
        $this->seedHistory($srDtsen1, null, ServiceRequestStatus::SUBMITTED->value, 'Pengajuan mandiri oleh pemohon via portal SAPA SOSIAL', $wargaAgus?->id, Carbon::now()->subDays(6));
        $this->seedHistory($srDtsen1, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::DOCUMENT_CHECK->value, 'Berkas lengkap dan sesuai ketentuan', $petugasLayanan?->id, Carbon::now()->subDays(5));
        $this->seedHistory($srDtsen1, ServiceRequestStatus::DOCUMENT_CHECK->value, ServiceRequestStatus::DATA_VERIFICATION->value, 'Pengecekan data SIKS-NG terkonfirmasi Desil 2', $petugasLayanan?->id, Carbon::now()->subDays(5));
        $this->seedHistory($srDtsen1, ServiceRequestStatus::DATA_VERIFICATION->value, ServiceRequestStatus::AWAITING_APPROVAL->value, 'Draf surat diajukan untuk paraf Kabid dan tanda tangan Kadis', $petugasLayanan?->id, Carbon::now()->subDays(5));
        $this->seedHistory($srDtsen1, ServiceRequestStatus::AWAITING_APPROVAL->value, ServiceRequestStatus::ISSUED->value, 'Surat Keterangan DTSEN ditandatangani dan diterbitkan', $kadis?->id, Carbon::now()->subDays(4));
        $this->seedHistory($srDtsen1, ServiceRequestStatus::ISSUED->value, ServiceRequestStatus::COMPLETED->value, 'Surat telah diunduh pemohon', null, Carbon::now()->subDays(4));

        // --- Kasus 2: DTSEN-202609-00002 (AWAITING_APPROVAL - Menunggu Tanda Tangan Kadis) ---
        $srDtsen2 = ServiceRequest::updateOrCreate(
            ['request_number' => "DTSEN-{$period}-00002"],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Siti Nurjanah',
                'applicant_nik' => '3505064506040002',
                'family_card_number' => '3505060105080045',
                'address' => 'Jl. Pandanarum No. 14, Desa Satreyan, Kanigoro',
                'village_id' => $desaSatreyan?->id,
                'phone' => '082233445566',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasLayanan?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::AWAITING_APPROVAL,
                'is_priority' => false,
                'verification_result' => 'Data terverifikasi pada SIKS-NG Desil 3.',
                'officer_notes' => 'Pengajuan KIP Kuliah untuk Universitas Brawijaya Malang.',
            ]
        );

        $this->seedDocument($srDtsen2->id, $dtsenRequirements[1]?->id, 'documents/dtsen/ktp_nurjanah.jpg', 'ktp_nurjanah.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srDtsen2->id, $dtsenRequirements[2]?->id, 'documents/dtsen/kk_nurjanah.pdf', 'kk_nurjanah.pdf', ServiceDocumentVerificationStatus::VALID);

        $cert2 = DtsenCertificate::updateOrCreate(
            ['service_request_id' => $srDtsen2->id],
            [
                'dtsen_purpose_id' => $kipPurpose?->id,
                'purpose_description' => 'Persyaratan Verifikasi Berkas Beasiswa KIP Kuliah 2026',
                'subject_name' => 'Siti Nurjanah',
                'subject_nik' => '3505064506040002',
                'relationship_to_applicant' => 'Diri Sendiri',
                'is_registered' => true,
                'decile' => 3,
                'checked_at' => Carbon::now()->subDay(),
                'checker_id' => $petugasLayanan?->id,
                'verification_code' => 'DTSEN-BLT-2026-C8M44',
            ]
        );

        $this->seedApproval($cert2, 1, $kabidLinjamsos?->id, ApprovalDecision::APPROVED, 'Paraf disetujui, teruskan ke Kepala Dinas.', Carbon::now()->subHours(12));
        $this->seedApproval($cert2, 2, $kadis?->id, ApprovalDecision::PENDING, null, null);

        $this->seedHistory($srDtsen2, null, ServiceRequestStatus::SUBMITTED->value, 'Diajukan melalui Operator Kecamatan Kanigoro', $operatorKanigoro?->id, Carbon::now()->subDays(2));
        $this->seedHistory($srDtsen2, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::DATA_VERIFICATION->value, 'Pemeriksaan berkas dan data SIKS-NG', $petugasLayanan?->id, Carbon::now()->subDay());
        $this->seedHistory($srDtsen2, ServiceRequestStatus::DATA_VERIFICATION->value, ServiceRequestStatus::AWAITING_APPROVAL->value, 'Draf surat menunggu persetujuan Kadis', $petugasLayanan?->id, Carbon::now()->subHours(12));

        // --- Kasus 3: DTSEN-202609-00003 (DATA_VERIFICATION - Dalam Verifikasi) ---
        $srDtsen3 = ServiceRequest::updateOrCreate(
            ['request_number' => "DTSEN-{$period}-00003"],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => null,
                'applicant_name' => 'Bambang Sujatmiko',
                'applicant_nik' => '3505101007780004',
                'family_card_number' => '3505102209120005',
                'address' => 'Desa Kamulan, Kecamatan Talun',
                'village_id' => $desaTalun?->id,
                'phone' => '081399887799',
                'submitted_at' => Carbon::now()->subHours(18),
                'officer_id' => $petugasLayanan?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::DATA_VERIFICATION,
                'is_priority' => false,
                'verification_result' => 'Berkas KTP & KK lengkap, sedang proses verifikasi NIK di SIKS-NG.',
            ]
        );

        $this->seedDocument($srDtsen3->id, $dtsenRequirements[1]?->id, 'documents/dtsen/ktp_bambang.jpg', 'ktp_bambang.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srDtsen3->id, $dtsenRequirements[2]?->id, 'documents/dtsen/kk_bambang.pdf', 'kk_bambang.pdf', ServiceDocumentVerificationStatus::VALID);

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $srDtsen3->id],
            [
                'dtsen_purpose_id' => $pipPurpose?->id,
                'purpose_description' => 'Pencairan Dana PIP SMPN 1 Talun',
                'subject_name' => 'Dimas Arya Sujatmiko',
                'subject_nik' => '3505101402120002',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => false,
            ]
        );

        $this->seedHistory($srDtsen3, null, ServiceRequestStatus::SUBMITTED->value, 'Permohonan baru masuk via portal', null, Carbon::now()->subHours(18));
        $this->seedHistory($srDtsen3, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::DATA_VERIFICATION->value, 'Berkas dinyatakan lengkap, dilanjutkan cek SIKS-NG', $petugasLayanan?->id, Carbon::now()->subHours(10));

        // --- Kasus 4: DTSEN-202609-00004 (REVISION_REQUESTED - Perbaikan Berkas) ---
        $srDtsen4 = ServiceRequest::updateOrCreate(
            ['request_number' => "DTSEN-{$period}-00004"],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => null,
                'applicant_name' => 'Endang Lestari',
                'applicant_nik' => '3505076211830005',
                'family_card_number' => '3505071104050019',
                'address' => 'Kelurahan Tawangsari, Kecamatan Garum',
                'village_id' => $desaGarum?->id,
                'phone' => '087811223344',
                'submitted_at' => Carbon::now()->subDays(3),
                'officer_id' => $petugasLayanan?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::REVISION_REQUESTED,
                'is_priority' => false,
                'officer_notes' => 'Foto Kartu Keluarga buram dan NIK anggota keluarga terpotong. Mohon unggah ulang scan KK yang jelas.',
            ]
        );

        $this->seedDocument($srDtsen4->id, $dtsenRequirements[1]?->id, 'documents/dtsen/ktp_endang.jpg', 'ktp_endang.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srDtsen4->id, $dtsenRequirements[2]?->id, 'documents/dtsen/kk_endang_blur.jpg', 'kk_blur.jpg', ServiceDocumentVerificationStatus::REVISION_NEEDED, 'Dokumen buram/tidak terbaca');

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $srDtsen4->id],
            [
                'dtsen_purpose_id' => $spmbPurpose?->id,
                'purpose_description' => 'SPMB Afirmasi SMKN 1 Blitar',
                'subject_name' => 'Putri Ayu',
                'subject_nik' => '3505074510090003',
                'relationship_to_applicant' => 'Anak Kandung',
                'is_registered' => false,
            ]
        );

        $this->seedHistory($srDtsen4, null, ServiceRequestStatus::SUBMITTED->value, 'Permohonan diajukan', null, Carbon::now()->subDays(3));
        $this->seedHistory($srDtsen4, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::REVISION_REQUESTED->value, 'Permintaan perbaikan scan KK', $petugasLayanan?->id, Carbon::now()->subDays(2));

        // --- Kasus 5: DTSEN-202609-00005 (REJECTED - Desil Melebihi Batas) ---
        $srDtsen5 = ServiceRequest::updateOrCreate(
            ['request_number' => "DTSEN-{$period}-00005"],
            [
                'service_type_id' => $dtsenType->id,
                'submitter_id' => null,
                'applicant_name' => 'Hendra Kurniawan',
                'applicant_nik' => '3505132004810006',
                'family_card_number' => '3505130101010077',
                'address' => 'Kelurahan Beru, Kecamatan Wlingi',
                'village_id' => $desaWlingi?->id,
                'phone' => '081255667788',
                'submitted_at' => Carbon::now()->subDays(4),
                'officer_id' => $petugasLayanan?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::REJECTED,
                'is_priority' => false,
                'verification_result' => 'Hasil verifikasi data SIKS-NG menunjukkan pemohon terdaftar pada Desil 7.',
                'rejection_reason' => 'Berdasarkan ketentuan Keputusan Kadinsos, Surat Keterangan DTSEN untuk tujuan SPMB Afirmasi hanya dapat diberikan maksimal pada Desil 5. Hasil pengecekan NIK Anda berada pada Desil 7 sehingga permohonan tidak dapat diproses.',
            ]
        );

        $this->seedDocument($srDtsen5->id, $dtsenRequirements[1]?->id, 'documents/dtsen/ktp_hendra.jpg', 'ktp_hendra.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srDtsen5->id, $dtsenRequirements[2]?->id, 'documents/dtsen/kk_hendra.pdf', 'kk_hendra.pdf', ServiceDocumentVerificationStatus::VALID);

        DtsenCertificate::updateOrCreate(
            ['service_request_id' => $srDtsen5->id],
            [
                'dtsen_purpose_id' => $spmbPurpose?->id,
                'purpose_description' => 'SPMB Afirmasi',
                'subject_name' => 'Hendra Kurniawan',
                'subject_nik' => '3505132004810006',
                'relationship_to_applicant' => 'Diri Sendiri',
                'is_registered' => true,
                'decile' => 7,
                'checked_at' => Carbon::now()->subDays(3),
                'checker_id' => $petugasLayanan?->id,
            ]
        );

        $this->seedHistory($srDtsen5, null, ServiceRequestStatus::SUBMITTED->value, 'Permohonan diajukan', null, Carbon::now()->subDays(4));
        $this->seedHistory($srDtsen5, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::DATA_VERIFICATION->value, 'Pemeriksaan berkas dan SIKS-NG', $petugasLayanan?->id, Carbon::now()->subDays(3));
        $this->seedHistory($srDtsen5, ServiceRequestStatus::DATA_VERIFICATION->value, ServiceRequestStatus::REJECTED->value, 'Ditolak karena Desil 7 melebihi batas maksimal Desil 5', $petugasLayanan?->id, Carbon::now()->subDays(3));

        // =========================================================================
        // 2. REAKTIVASI KIS / PBI-JK TRANSACTIONS (Layanan 2)
        // =========================================================================

        // --- Kasus 1: PBI-202609-00001 (COMPLETED / REACTIVATED - Emergency Priority) ---
        $srPbi1 = ServiceRequest::updateOrCreate(
            ['request_number' => "PBI-{$period}-00001"],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $wargaSiti?->id,
                'applicant_name' => 'Siti Aminah',
                'applicant_nik' => '3505075408850002',
                'family_card_number' => '3505071203090011',
                'address' => 'Dusun Tingal, Desa Tingal, Garum',
                'village_id' => $desaGarum?->id,
                'phone' => '081399887777',
                'submitted_at' => Carbon::now()->subDays(12),
                'officer_id' => $petugasPbi?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::COMPLETED,
                'is_priority' => true, // DARURAT MEDIS
                'verification_result' => 'Pasien cuci darah rutin (hemodialisis) di RSUD Ngudi Waluyo Wlingi. Kartu KIS nonaktif sejak 1 bulan lalu. Masuk kategori desil 1.',
                'service_result' => 'Kepesertaan PBI-JK berhasil diaktifkan kembali oleh BPJS Kesehatan Cabang Blitar per 22 September 2026.',
                'completed_at' => Carbon::now()->subDays(2),
            ]
        );

        $this->seedDocument($srPbi1->id, $pbiRequirements[1]?->id, 'documents/pbi/ktp_siti.jpg', 'ktp_siti.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi1->id, $pbiRequirements[2]?->id, 'documents/pbi/kk_siti.pdf', 'kk_siti.pdf', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi1->id, $pbiRequirements[3]?->id, 'documents/pbi/bpjs_siti.jpg', 'bpjs_siti.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi1->id, $pbiRequirements[4]?->id, 'documents/pbi/surat_faskes_cuci_darah.pdf', 'surat_keterangan_rsud_wlingi.pdf', ServiceDocumentVerificationStatus::VALID);

        $pbi1 = PbiReactivation::updateOrCreate(
            ['service_request_id' => $srPbi1->id],
            [
                'participant_name' => 'Siti Aminah',
                'participant_nik' => '3505075408850002',
                'bpjs_card_number' => '0001456789123',
                'deactivated_date' => Carbon::now()->subMonths(2)->toDateString(),
                'reason' => PbiReactivationReason::EMERGENCY,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => '445/892/RSUD/2026',
                'decile' => 1,
                'eligibility_notes' => 'Pasien darurat cuci darah memerlukan penjaminan segera.',
                'recommendation_number' => "400.9/088/REK-PBI/409.105/{$period}",
                'recommendation_issued_at' => Carbon::now()->subDays(11),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subDays(10),
                'ministry_decision' => MinistryDecision::APPROVED,
                'ministry_decided_at' => Carbon::now()->subDays(3),
                'reactivated_date' => Carbon::now()->subDays(2)->toDateString(),
            ]
        );

        $this->seedApproval($pbi1, 1, $kabidLinjamsos?->id, ApprovalDecision::APPROVED, 'Rekomendasi reaktivasi darurat disetujui.', Carbon::now()->subDays(11));
        $this->seedApproval($pbi1, 2, $kadis?->id, ApprovalDecision::APPROVED, 'Disetujui untuk diteruskan ke Kemensos.', Carbon::now()->subDays(11));

        $this->seedHistory($srPbi1, null, ServiceRequestStatus::SUBMITTED->value, 'Pengajuan reaktivasi darurat medis masuk', $wargaSiti?->id, Carbon::now()->subDays(12));
        $this->seedHistory($srPbi1, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::ELIGIBILITY_VERIFICATION->value, 'Verifikasi urgensi medis dan desil', $petugasPbi?->id, Carbon::now()->subDays(11));
        $this->seedHistory($srPbi1, ServiceRequestStatus::ELIGIBILITY_VERIFICATION->value, ServiceRequestStatus::RECOMMENDATION_ISSUED->value, 'Surat rekomendasi Kadis terbit', $kadis?->id, Carbon::now()->subDays(11));
        $this->seedHistory($srPbi1, ServiceRequestStatus::RECOMMENDATION_ISSUED->value, ServiceRequestStatus::PROPOSED_TO_MINISTRY->value, 'Usulan diinput ke SIKS-NG Kemensos RI', $petugasPbi?->id, Carbon::now()->subDays(10));
        $this->seedHistory($srPbi1, ServiceRequestStatus::PROPOSED_TO_MINISTRY->value, ServiceRequestStatus::MINISTRY_APPROVED->value, 'Persetujuan dari Kemensos RI diterima', $petugasPbi?->id, Carbon::now()->subDays(3));
        $this->seedHistory($srPbi1, ServiceRequestStatus::MINISTRY_APPROVED->value, ServiceRequestStatus::REACTIVATED->value, 'Konfirmasi kepesertaan aktif di BPJS Kesehatan', $petugasPbi?->id, Carbon::now()->subDays(2));
        $this->seedHistory($srPbi1, ServiceRequestStatus::REACTIVATED->value, ServiceRequestStatus::COMPLETED->value, 'Layanan selesai, pasien dapat berobat', $petugasPbi?->id, Carbon::now()->subDays(2));

        // --- Kasus 2: PBI-202609-00002 (PROPOSED_TO_MINISTRY - Menunggu Kemensos) ---
        $srPbi2 = ServiceRequest::updateOrCreate(
            ['request_number' => "PBI-{$period}-00002"],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Suwandi',
                'applicant_nik' => '3505060406600003',
                'family_card_number' => '3505060105080033',
                'address' => 'Desa Tlogo, Kecamatan Kanigoro',
                'village_id' => $desaKanigoro?->id,
                'phone' => '085233669911',
                'submitted_at' => Carbon::now()->subDays(6),
                'officer_id' => $petugasPbi?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::PROPOSED_TO_MINISTRY,
                'is_priority' => false,
                'verification_result' => 'Pasien penyakit kronis hipertensi & diabetes, rawat jalan di Puskesmas Kanigoro. Desil 2.',
            ]
        );

        $this->seedDocument($srPbi2->id, $pbiRequirements[1]?->id, 'documents/pbi/ktp_suwandi.jpg', 'ktp_suwandi.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi2->id, $pbiRequirements[2]?->id, 'documents/pbi/kk_suwandi.pdf', 'kk_suwandi.pdf', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi2->id, $pbiRequirements[3]?->id, 'documents/pbi/bpjs_suwandi.jpg', 'bpjs_suwandi.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi2->id, $pbiRequirements[4]?->id, 'documents/pbi/surat_puskesmas_suwandi.pdf', 'surat_puskesmas_suwandi.pdf', ServiceDocumentVerificationStatus::VALID);

        $pbi2 = PbiReactivation::updateOrCreate(
            ['service_request_id' => $srPbi2->id],
            [
                'participant_name' => 'Suwandi',
                'participant_nik' => '3505060406600003',
                'bpjs_card_number' => '0001223344556',
                'deactivated_date' => Carbon::now()->subMonths(3)->toDateString(),
                'reason' => PbiReactivationReason::CHRONIC,
                'health_facility_name' => 'Puskesmas Kanigoro',
                'health_letter_number' => '440/120/Pusk/2026',
                'decile' => 2,
                'eligibility_notes' => 'Pasien berobat jalan rutin.',
                'recommendation_number' => "400.9/095/REK-PBI/409.105/{$period}",
                'recommendation_issued_at' => Carbon::now()->subDays(5),
                'signer_id' => $kadis?->id,
                'proposed_to_ministry_at' => Carbon::now()->subDays(4),
                'ministry_decision' => MinistryDecision::PENDING,
            ]
        );

        $this->seedApproval($pbi2, 1, $kabidLinjamsos?->id, ApprovalDecision::APPROVED, 'Rekomendasi disetujui.', Carbon::now()->subDays(5));
        $this->seedApproval($pbi2, 2, $kadis?->id, ApprovalDecision::APPROVED, 'Ditandatangani.', Carbon::now()->subDays(5));

        $this->seedHistory($srPbi2, null, ServiceRequestStatus::SUBMITTED->value, 'Diajukan operator Kanigoro', $operatorKanigoro?->id, Carbon::now()->subDays(6));
        $this->seedHistory($srPbi2, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::ELIGIBILITY_VERIFICATION->value, 'Verifikasi data', $petugasPbi?->id, Carbon::now()->subDays(5));
        $this->seedHistory($srPbi2, ServiceRequestStatus::ELIGIBILITY_VERIFICATION->value, ServiceRequestStatus::RECOMMENDATION_ISSUED->value, 'Rekomendasi terbit', $kadis?->id, Carbon::now()->subDays(5));
        $this->seedHistory($srPbi2, ServiceRequestStatus::RECOMMENDATION_ISSUED->value, ServiceRequestStatus::PROPOSED_TO_MINISTRY->value, 'Diusulkan ke Kemensos RI via SIKS-NG', $petugasPbi?->id, Carbon::now()->subDays(4));

        // --- Kasus 3: PBI-202609-00003 (RECOMMENDATION_ISSUED - Siap Diusulkan ke SIKS-NG) ---
        $srPbi3 = ServiceRequest::updateOrCreate(
            ['request_number' => "PBI-{$period}-00003"],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => null,
                'applicant_name' => 'Mardiyah',
                'applicant_nik' => '3505105201940001',
                'family_card_number' => '3505100101150088',
                'address' => 'Desa Kendalrejo, Kecamatan Talun',
                'village_id' => $desaTalun?->id,
                'phone' => '087788990011',
                'submitted_at' => Carbon::now()->subDays(2),
                'officer_id' => $petugasPbi?->id,
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::RECOMMENDATION_ISSUED,
                'is_priority' => false,
                'verification_result' => 'Bayi baru lahir dari ibu peserta PBI-JK aktif.',
            ]
        );

        $this->seedDocument($srPbi3->id, $pbiRequirements[1]?->id, 'documents/pbi/ktp_mardiyah.jpg', 'ktp_mardiyah.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi3->id, $pbiRequirements[2]?->id, 'documents/pbi/kk_mardiyah.pdf', 'kk_mardiyah.pdf', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi3->id, $pbiRequirements[3]?->id, 'documents/pbi/bpjs_mardiyah.jpg', 'bpjs_ibu_mardiyah.jpg', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srPbi3->id, $pbiRequirements[4]?->id, 'documents/pbi/surat_lahir_rs.pdf', 'surat_keterangan_lahir.pdf', ServiceDocumentVerificationStatus::VALID);

        $pbi3 = PbiReactivation::updateOrCreate(
            ['service_request_id' => $srPbi3->id],
            [
                'participant_name' => 'Bayi Ny. Mardiyah',
                'participant_nik' => '3505100109260001',
                'bpjs_card_number' => '0009988776655',
                'deactivated_date' => Carbon::now()->subDays(14)->toDateString(),
                'reason' => PbiReactivationReason::NEWBORN,
                'health_facility_name' => 'RS An-Nisaa Blitar',
                'health_letter_number' => 'SKL/098/2026',
                'decile' => 1,
                'eligibility_notes' => 'Pendaftaran kepesertaan bayi baru lahir dari keluarga PBI.',
                'recommendation_number' => "400.9/102/REK-PBI/409.105/{$period}",
                'recommendation_issued_at' => Carbon::now()->subHours(8),
                'signer_id' => $kadis?->id,
                'ministry_decision' => MinistryDecision::PENDING,
            ]
        );

        $this->seedApproval($pbi3, 1, $kabidLinjamsos?->id, ApprovalDecision::APPROVED, 'Sesuai ketentuan bayi baru lahir dari ibu PBI.', Carbon::now()->subHours(10));
        $this->seedApproval($pbi3, 2, $kadis?->id, ApprovalDecision::APPROVED, 'Rekomendasi disahkan.', Carbon::now()->subHours(8));

        $this->seedHistory($srPbi3, null, ServiceRequestStatus::SUBMITTED->value, 'Pengajuan masuk', null, Carbon::now()->subDays(2));
        $this->seedHistory($srPbi3, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::RECOMMENDATION_ISSUED->value, 'Surat rekomendasi terbit', $kadis?->id, Carbon::now()->subHours(8));

        // --- Kasus 4: PBI-202609-00004 (SUBMITTED - Baru Masuk & Prioritas Darurat) ---
        $srPbi4 = ServiceRequest::updateOrCreate(
            ['request_number' => "PBI-{$period}-00004"],
            [
                'service_type_id' => $pbiType->id,
                'submitter_id' => null,
                'applicant_name' => 'Kasiran',
                'applicant_nik' => '3505130101550009',
                'family_card_number' => '3505130505050011',
                'address' => 'Kelurahan Babadan, Kecamatan Wlingi',
                'village_id' => $desaWlingi?->id,
                'phone' => '085744556677',
                'submitted_at' => Carbon::now()->subHours(3),
                'work_unit_id' => $linjamsos?->id,
                'status' => ServiceRequestStatus::SUBMITTED,
                'is_priority' => true, // Darurat Medis
                'officer_notes' => 'Pasien mengalami stroke hemoragik saat ini di IGD RSUD Wlingi.',
            ]
        );

        $this->seedDocument($srPbi4->id, $pbiRequirements[1]?->id, 'documents/pbi/ktp_kasiran.jpg', 'ktp_kasiran.jpg', ServiceDocumentVerificationStatus::PENDING);
        $this->seedDocument($srPbi4->id, $pbiRequirements[2]?->id, 'documents/pbi/kk_kasiran.pdf', 'kk_kasiran.pdf', ServiceDocumentVerificationStatus::PENDING);
        $this->seedDocument($srPbi4->id, $pbiRequirements[3]?->id, 'documents/pbi/bpjs_kasiran.jpg', 'bpjs_kasiran.jpg', ServiceDocumentVerificationStatus::PENDING);
        $this->seedDocument($srPbi4->id, $pbiRequirements[4]?->id, 'documents/pbi/surat_igd_stroke.pdf', 'surat_igd_stroke.pdf', ServiceDocumentVerificationStatus::PENDING);

        PbiReactivation::updateOrCreate(
            ['service_request_id' => $srPbi4->id],
            [
                'participant_name' => 'Kasiran',
                'participant_nik' => '3505130101550009',
                'bpjs_card_number' => '0001778899001',
                'deactivated_date' => Carbon::now()->subMonths(1)->toDateString(),
                'reason' => PbiReactivationReason::EMERGENCY,
                'health_facility_name' => 'RSUD Ngudi Waluyo Wlingi',
                'health_letter_number' => 'IGD/450/2026',
                'ministry_decision' => MinistryDecision::PENDING,
            ]
        );

        $this->seedHistory($srPbi4, null, ServiceRequestStatus::SUBMITTED->value, 'Pengajuan darurat medis masuk melalui IGD', null, Carbon::now()->subHours(3));

        // =========================================================================
        // 3. LAYANAN LAINNYA (Layanan 4)
        // =========================================================================
        $srRehsos1 = ServiceRequest::updateOrCreate(
            ['request_number' => "REHSOS-{$period}-00001"],
            [
                'service_type_id' => $rehsosType->id,
                'submitter_id' => $operatorKanigoro?->id,
                'applicant_name' => 'Pemerintah Desa Satreyan (Kades)',
                'applicant_nik' => '3505061005750001',
                'family_card_number' => '3505060101010001',
                'address' => 'Kantor Desa Satreyan, Kecamatan Kanigoro',
                'village_id' => $desaSatreyan?->id,
                'phone' => '081233440099',
                'submitted_at' => Carbon::now()->subDays(8),
                'officer_id' => $petugasRehsos?->id,
                'work_unit_id' => $rehsosUnit?->id,
                'status' => ServiceRequestStatus::COMPLETED,
                'is_priority' => false,
                'verification_result' => 'Laporan permohonan pelayanan rehabilitasi sosial untuk lansia terlantar atas nama Mbah Marto diverifikasi lapangan.',
                'assessment_notes' => 'Lansia berusia 82 tahun sebatang kara dan mengalami keterbatasan gerak fisik.',
                'service_result' => 'Kasus telah diteruskan dan dibuka dalam Modul Kasus Rehabilitasi Sosial (RHS-202609-00001).',
                'completed_at' => Carbon::now()->subDays(7),
            ]
        );

        $this->seedDocument($srRehsos1->id, $rehsosRequirements[2]?->id, 'documents/rehsos/surat_pengantar_kades.pdf', 'pengantar_kades_satreyan.pdf', ServiceDocumentVerificationStatus::VALID);
        $this->seedDocument($srRehsos1->id, $rehsosRequirements[3]?->id, 'documents/rehsos/foto_mbah_marto.jpg', 'foto_mbah_marto.jpg', ServiceDocumentVerificationStatus::VALID);

        $this->seedHistory($srRehsos1, null, ServiceRequestStatus::SUBMITTED->value, 'Permohonan diajukan oleh Kepala Desa', $operatorKanigoro?->id, Carbon::now()->subDays(8));
        $this->seedHistory($srRehsos1, ServiceRequestStatus::SUBMITTED->value, ServiceRequestStatus::IN_PROCESS->value, 'Assessment awal lapangan oleh Pekerja Sosial', $petugasRehsos?->id, Carbon::now()->subDays(7));
        $this->seedHistory($srRehsos1, ServiceRequestStatus::IN_PROCESS->value, ServiceRequestStatus::COMPLETED->value, 'Diteruskan menjadi Kasus Rehabilitasi Sosial Terpadu', $petugasRehsos?->id, Carbon::now()->subDays(7));

        // Update Number Sequences
        NumberSequence::updateOrCreate(
            ['prefix' => 'DTSEN', 'period' => $period],
            ['last_number' => 5]
        );
        NumberSequence::updateOrCreate(
            ['prefix' => 'PBI', 'period' => $period],
            ['last_number' => 4]
        );
        NumberSequence::updateOrCreate(
            ['prefix' => 'REHSOS', 'period' => $period],
            ['last_number' => 1]
        );
    }

    private function seedDocument(int $serviceRequestId, ?int $requirementId, string $path, string $name, ServiceDocumentVerificationStatus $status, ?string $notes = null): void
    {
        if (! $requirementId) {
            return;
        }

        ServiceRequestDocument::updateOrCreate(
            [
                'service_request_id' => $serviceRequestId,
                'service_requirement_id' => $requirementId,
            ],
            [
                'file_path' => $path,
                'original_name' => $name,
                'verification_status' => $status->value,
                'notes' => $notes,
            ]
        );
    }

    private function seedApproval($approvable, int $step, ?int $approverId, ApprovalDecision $decision, ?string $notes, ?Carbon $decidedAt): void
    {
        if (! $approverId) {
            return;
        }

        Approval::updateOrCreate(
            [
                'approvable_type' => get_class($approvable),
                'approvable_id' => $approvable->id,
                'step' => $step,
            ],
            [
                'approver_id' => $approverId,
                'decision' => $decision,
                'notes' => $notes,
                'decided_at' => $decidedAt,
            ]
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
