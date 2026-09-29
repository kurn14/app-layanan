<?php

namespace Database\Seeders;

use App\Enums\ApprovalDecision;
use App\Enums\ComplaintAttachmentType;
use App\Enums\ComplaintStatus;
use App\Enums\Gender;
use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use App\Enums\ReferralStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Enums\ServiceDocumentVerificationStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Assessment;
use App\Models\Client;
use App\Models\ClientCategory;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Disposition;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\NumberSequence;
use App\Models\PbiReactivation;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\ServiceRequirement;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Village;
use App\Models\WorkUnit;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    protected Generator $faker;

    protected array $villagesByDistrict = [];

    protected array $allVillages = [];

    protected array $dtsenPurposes = [];

    protected array $serviceRequirementsByType = [];

    protected array $complaintCategories = [];

    protected array $clientCategories = [];

    protected array $referralInstitutions = [];

    protected ?User $petugasLayanan = null;

    protected ?User $petugasPbi = null;

    protected ?User $petugasRehsos = null;

    protected ?User $kabidLinjamsos = null;

    protected ?User $kadis = null;

    protected ?WorkUnit $linjamsosUnit = null;

    protected ?WorkUnit $rehsosUnit = null;

    protected ?WorkUnit $dayasosUnit = null;

    protected array $citizenUsers = [];

    // Counter sequence per prefix dan period
    protected array $sequences = [];

    protected int $certNumberSeq = 100;

    protected int $pbiRecNumberSeq = 100;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->faker = Faker::create('id_ID');

        $this->command?->info('Menyiapkan data referensi master untuk DummyDataSeeder...');
        $this->loadMasterData();

        $this->command?->info('Mulai generate data dummy pengajuan layanan (>1.200 tiket)...');
        $this->seedServiceRequests();

        $this->command?->info('Mulai generate data dummy pengaduan sosial (>350 laporan)...');
        $this->seedComplaints();

        $this->command?->info('Mulai generate data dummy klien & rehabilitasi sosial (>200 kasus & rujukan)...');
        $this->seedRehabilitation();

        $this->command?->info('Sinkronisasi nomor sequence tiket...');
        $this->syncNumberSequences();

        $this->command?->info('DummyDataSeeder selesai dieksekusi dengan sukses!');
    }

    /**
     * Muat semua data master dan pengguna ke memory untuk performa optimal.
     */
    protected function loadMasterData(): void
    {
        $villages = Village::with('district')->get();
        $this->allVillages = $villages->all();

        foreach ($villages as $village) {
            $this->villagesByDistrict[$village->district_id][] = $village;
        }

        $this->dtsenPurposes = DtsenPurpose::where('is_active', true)->get()->all();
        $this->complaintCategories = ComplaintCategory::where('is_active', true)->get()->all();
        $this->clientCategories = ClientCategory::all()->all();
        $this->referralInstitutions = ReferralInstitution::where('is_active', true)->get()->all();

        $requirements = ServiceRequirement::all();
        foreach ($requirements as $req) {
            $this->serviceRequirementsByType[$req->service_type_id][] = $req;
        }

        $this->petugasLayanan = User::where('email', 'petugas.layanan@dinsos.blitarkab.go.id')->first()
            ?? User::first();
        $this->petugasPbi = User::where('email', 'petugas.pbi@dinsos.blitarkab.go.id')->first()
            ?? $this->petugasLayanan;
        $this->petugasRehsos = User::where('email', 'petugas.rehsos@dinsos.blitarkab.go.id')->first()
            ?? $this->petugasLayanan;
        $this->kabidLinjamsos = User::where('email', 'kabid.linjamsos@dinsos.blitarkab.go.id')->first()
            ?? $this->petugasLayanan;
        $this->kadis = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first()
            ?? $this->petugasLayanan;

        $this->linjamsosUnit = WorkUnit::where('name', 'like', '%Linjamsos%')->first();
        $this->rehsosUnit = WorkUnit::where('name', 'like', '%Rehsos%')->first();
        $this->dayasosUnit = WorkUnit::where('name', 'like', '%Pemberdayaan%')->first();

        $this->citizenUsers = User::whereNull('district_id')
            ->whereNull('village_id')
            ->where('id', '>', 10)
            ->pluck('id')
            ->all();
    }

    /**
     * Dapatkan desa acak dari seluruh wilayah Kabupaten Blitar dengan variasi realistis.
     */
    protected function getRandomVillage(): Village
    {
        return $this->allVillages[array_rand($this->allVillages)];
    }

    /**
     * Generate tanggal acak dalam rentang 6 bulan terakhir (April 2026 - September 2026).
     */
    protected function getRandomSubmissionDate(): Carbon
    {
        // Distribusi bobot: lebih banyak di bulan-bulan baru (Juli, Agustus, September)
        $monthWeight = $this->faker->randomElement([
            4, 4,
            5, 5, 5,
            6, 6, 6, 6,
            7, 7, 7, 7, 7,
            8, 8, 8, 8, 8, 8,
            9, 9, 9, 9, 9, 9, 9, 9,
        ]);

        $year = 2026;
        $maxDay = ($monthWeight === 9) ? 29 : ($monthWeight === 4 || $monthWeight === 6 ? 30 : 31);
        $day = $this->faker->numberBetween(1, $maxDay);
        $hour = $this->faker->numberBetween(8, 16);
        $minute = $this->faker->numberBetween(0, 59);

        return Carbon::create($year, $monthWeight, $day, $hour, $minute, 0);
    }

    /**
     * Generate nomor tiket unik berformat KODE-YYYYMM-NNNNN.
     */
    protected function generateTicketNumber(string $prefix, Carbon $date): string
    {
        $period = $date->format('Ym');
        $key = "{$prefix}-{$period}";

        if (! isset($this->sequences[$key])) {
            $this->sequences[$key] = 100;
        }

        $this->sequences[$key]++;
        $numberPadded = str_pad((string) $this->sequences[$key], 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$period}-{$numberPadded}";
    }

    /**
     * Generate NIK 16 digit khas Kabupaten Blitar (3505XXXXXXXXXXXX).
     */
    protected function generateBlitarNik(): string
    {
        $subDistrictCode = str_pad((string) $this->faker->numberBetween(1, 22), 2, '0', STR_PAD_LEFT);
        $dobPart = str_pad((string) $this->faker->numberBetween(1, 71), 2, '0', STR_PAD_LEFT)
            .str_pad((string) $this->faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT)
            .str_pad((string) $this->faker->numberBetween(50, 99), 2, '0', STR_PAD_LEFT);
        $seq = str_pad((string) $this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

        return "3505{$subDistrictCode}{$dobPart}{$seq}";
    }

    /**
     * =========================================================================
     * SEED SERVICE REQUESTS (DTSEN, PBI-JK, REHSOS, BANSOS) -> > 1.200 baris
     * =========================================================================
     */
    protected function seedServiceRequests(): void
    {
        $dtsenType = ServiceType::where('code', 'DTSEN')->first();
        $pbiType = ServiceType::where('code', 'PBI')->first();
        $rehsosType = ServiceType::where('code', 'REHSOS')->first();
        $bansosType = ServiceType::where('code', 'REK_BANSOS')->first();

        // 1. Data Dummy SK DTSEN (650 Tiket)
        $this->seedDtsenBatch($dtsenType?->id ?? 1, 650);

        // 2. Data Dummy Reaktivasi PBI-JK (415 Tiket)
        $this->seedPbiBatch($pbiType?->id ?? 2, 415);

        // 3. Data Dummy Layanan Lainnya (REHSOS & REK_BANSOS) (160 Tiket)
        $this->seedGeneralServiceBatch($rehsosType?->id ?? 3, 80);
        $this->seedGeneralServiceBatch($bansosType?->id ?? 4, 80);
    }

    /**
     * Batch SK DTSEN (Layanan 1).
     */
    protected function seedDtsenBatch(int $serviceTypeId, int $totalCount): void
    {
        $statusDistribution = [
            ServiceRequestStatus::COMPLETED->value => (int) ($totalCount * 0.55),        // 357
            ServiceRequestStatus::ISSUED->value => (int) ($totalCount * 0.14),           // 91
            ServiceRequestStatus::AWAITING_APPROVAL->value => (int) ($totalCount * 0.09), // 58 (Widget 2 Tab a)
            ServiceRequestStatus::DATA_VERIFICATION->value => (int) ($totalCount * 0.08), // 52
            ServiceRequestStatus::DOCUMENT_CHECK->value => (int) ($totalCount * 0.05),   // 32
            ServiceRequestStatus::REVISION_REQUESTED->value => (int) ($totalCount * 0.03), // 19
            ServiceRequestStatus::SUBMITTED->value => (int) ($totalCount * 0.03),        // 19
            ServiceRequestStatus::REJECTED->value => (int) ($totalCount * 0.03),         // 19
        ];

        $certificatesToInsert = [];
        $approvalsToInsert = [];
        $documentsToInsert = [];
        $historiesToInsert = [];

        foreach ($statusDistribution as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $submittedAt = $this->getRandomSubmissionDate();
                $village = $this->getRandomVillage();
                $applicantName = $this->faker->name();
                $applicantNik = $this->generateBlitarNik();
                $kkNumber = $this->generateBlitarNik();
                $requestNumber = $this->generateTicketNumber('DTSEN', $submittedAt);
                $isCompleted = ($statusValue === ServiceRequestStatus::COMPLETED->value);
                $isIssued = ($statusValue === ServiceRequestStatus::ISSUED->value);
                $isRejected = ($statusValue === ServiceRequestStatus::REJECTED->value);
                $isAwaiting = ($statusValue === ServiceRequestStatus::AWAITING_APPROVAL->value);

                $completedAt = null;
                $serviceResult = null;
                $rejectionReason = null;

                if ($isCompleted) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(2, 5));
                    $serviceResult = 'Surat Keterangan DTSEN telah diterbitkan dan diunduh/diterima oleh pemohon.';
                } elseif ($isRejected) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(1, 3));
                    $rejectionReason = 'Pemohon atau subjek tidak terdaftar dalam data SIKS-NG Kabupaten Blitar atau desil melebihi batas ketentuan yang dipersyaratkan.';
                }

                $serviceRequestId = DB::table('service_requests')->insertGetId([
                    'request_number' => $requestNumber,
                    'service_type_id' => $serviceTypeId,
                    'submitter_id' => ! empty($this->citizenUsers) && $this->faker->boolean(40) ? $this->faker->randomElement($this->citizenUsers) : null,
                    'applicant_name' => $applicantName,
                    'applicant_nik' => $applicantNik,
                    'family_card_number' => $kkNumber,
                    'address' => 'RT '.str_pad((string) $this->faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT).' RW '.str_pad((string) $this->faker->numberBetween(1, 8), 2, '0', STR_PAD_LEFT).' Dusun '.$this->faker->streetName().', Desa '.$village->name,
                    'village_id' => $village->id,
                    'phone' => '08'.$this->faker->numerify('##########'),
                    'submitted_at' => $submittedAt,
                    'officer_id' => $this->petugasLayanan?->id,
                    'work_unit_id' => $this->linjamsosUnit?->id,
                    'status' => $statusValue,
                    'is_priority' => false,
                    'verification_result' => ($statusValue !== ServiceRequestStatus::SUBMITTED->value) ? 'Hasil verifikasi data kependudukan dan status DTKS/DTSEN telah dicocokkan.' : null,
                    'officer_notes' => 'Permohonan diproses sesuai SOP pelayanan terpadu SAPA SOSIAL.',
                    'assessment_notes' => null,
                    'service_result' => $serviceResult,
                    'rejection_reason' => $rejectionReason,
                    'completed_at' => $completedAt,
                    'created_at' => $submittedAt,
                    'updated_at' => $completedAt ?? $submittedAt,
                ]);

                // Detail DTSEN Certificate
                /** @var DtsenPurpose $purpose */
                $purpose = $this->faker->randomElement($this->dtsenPurposes);
                $isRegistered = ! $isRejected || $this->faker->boolean(50);
                $decile = $isRegistered ? ($isRejected ? $this->faker->numberBetween(6, 9) : $this->faker->numberBetween(1, $purpose->max_decile)) : null;

                $certNumber = null;
                $issuedAt = null;
                $validUntil = null;
                $signerId = null;
                $verificationCode = null;

                if ($isCompleted || $isIssued) {
                    $this->certNumberSeq++;
                    $certNumber = '400.9/'.str_pad((string) $this->certNumberSeq, 4, '0', STR_PAD_LEFT).'/409.105/2026';
                    $issuedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(1, 3));
                    $validUntil = (clone $issuedAt)->addDays($purpose->validity_days ?? 90)->toDateString();
                    $signerId = $this->kadis?->id;
                    $verificationCode = 'DTSEN-'.strtoupper(Str::random(12));
                }

                $subjectName = $this->faker->boolean(70) ? $this->faker->name() : $applicantName;
                $subjectNik = ($subjectName === $applicantName) ? $applicantNik : $this->generateBlitarNik();

                $certId = DB::table('dtsen_certificates')->insertGetId([
                    'service_request_id' => $serviceRequestId,
                    'dtsen_purpose_id' => $purpose->id,
                    'purpose_description' => $purpose->name.' - '.$applicantName,
                    'subject_name' => $subjectName,
                    'subject_nik' => $subjectNik,
                    'relationship_to_applicant' => ($subjectName === $applicantName) ? 'Diri Sendiri' : $this->faker->randomElement(['Anak Kandung', 'Istri', 'Suami', 'Orang Tua']),
                    'is_registered' => $isRegistered,
                    'decile' => $decile,
                    'checked_at' => ($statusValue !== ServiceRequestStatus::SUBMITTED->value) ? (clone $submittedAt)->addHours(4) : null,
                    'checker_id' => $this->petugasLayanan?->id,
                    'certificate_number' => $certNumber,
                    'issued_at' => $issuedAt,
                    'valid_until' => $validUntil,
                    'signer_id' => $signerId,
                    'file_path' => $certNumber ? 'certificates/2026/dtsen_'.md5($certNumber).'.pdf' : null,
                    'verification_code' => $verificationCode,
                    'created_at' => $submittedAt,
                    'updated_at' => $issuedAt ?? $submittedAt,
                ]);

                // Approvals untuk Widget 2 Tab (a)
                if ($isAwaiting) {
                    // Sebagian step 1 pending, sebagian step 1 disetujui tapi step 2 pending
                    $isStep1Pending = $this->faker->boolean(50);

                    $approvalsToInsert[] = [
                        'approvable_type' => DtsenCertificate::class,
                        'approvable_id' => $certId,
                        'step' => 1,
                        'approver_id' => $this->kabidLinjamsos?->id,
                        'decision' => $isStep1Pending ? ApprovalDecision::PENDING->value : ApprovalDecision::APPROVED->value,
                        'notes' => $isStep1Pending ? null : 'Memenuhi syarat administratif dan data SIKS-NG valid.',
                        'decided_at' => $isStep1Pending ? null : (clone $submittedAt)->addDays(1),
                        'created_at' => $submittedAt,
                        'updated_at' => (clone $submittedAt)->addDays(1),
                    ];

                    if (! $isStep1Pending) {
                        $approvalsToInsert[] = [
                            'approvable_type' => DtsenCertificate::class,
                            'approvable_id' => $certId,
                            'step' => 2,
                            'approver_id' => $this->kadis?->id,
                            'decision' => ApprovalDecision::PENDING->value,
                            'notes' => null,
                            'decided_at' => null,
                            'created_at' => (clone $submittedAt)->addDays(1),
                            'updated_at' => (clone $submittedAt)->addDays(1),
                        ];
                    }
                } elseif ($isCompleted || $isIssued) {
                    // Keduanya Approved
                    $approvalsToInsert[] = [
                        'approvable_type' => DtsenCertificate::class,
                        'approvable_id' => $certId,
                        'step' => 1,
                        'approver_id' => $this->kabidLinjamsos?->id,
                        'decision' => ApprovalDecision::APPROVED->value,
                        'notes' => 'Paraf disetujui.',
                        'decided_at' => (clone $submittedAt)->addDays(1),
                        'created_at' => $submittedAt,
                        'updated_at' => (clone $submittedAt)->addDays(1),
                    ];
                    $approvalsToInsert[] = [
                        'approvable_type' => DtsenCertificate::class,
                        'approvable_id' => $certId,
                        'step' => 2,
                        'approver_id' => $this->kadis?->id,
                        'decision' => ApprovalDecision::APPROVED->value,
                        'notes' => 'Surat ditandatangani secara digital.',
                        'decided_at' => $issuedAt,
                        'created_at' => (clone $submittedAt)->addDays(1),
                        'updated_at' => $issuedAt,
                    ];
                }

                // Documents
                $dtsenReqs = $this->serviceRequirementsByType[$serviceTypeId] ?? [];
                foreach ($dtsenReqs as $req) {
                    $docStatus = $isRejected ? ServiceDocumentVerificationStatus::REVISION_NEEDED->value : ServiceDocumentVerificationStatus::VALID->value;
                    $documentsToInsert[] = [
                        'service_request_id' => $serviceRequestId,
                        'service_requirement_id' => $req->id,
                        'file_path' => 'documents/dtsen/'.Str::slug($req->name).'_'.$serviceRequestId.'.pdf',
                        'original_name' => Str::slug($req->name).'.pdf',
                        'verification_status' => $docStatus,
                        'notes' => 'Terverifikasi otomatis oleh sistem.',
                        'created_at' => $submittedAt,
                        'updated_at' => $submittedAt,
                    ];
                }

                // Status Histories
                $this->buildStatusHistoriesForRequest(
                    $historiesToInsert,
                    $serviceRequestId,
                    $statusValue,
                    $submittedAt,
                    $completedAt
                );

                // Batch insert child jika array besar
                if (count($approvalsToInsert) >= 200) {
                    DB::table('approvals')->insert($approvalsToInsert);
                    $approvalsToInsert = [];
                }
                if (count($documentsToInsert) >= 300) {
                    DB::table('service_request_documents')->insert($documentsToInsert);
                    $documentsToInsert = [];
                }
                if (count($historiesToInsert) >= 500) {
                    DB::table('status_histories')->insert($historiesToInsert);
                    $historiesToInsert = [];
                }
            }
        }

        if (! empty($approvalsToInsert)) {
            DB::table('approvals')->insert($approvalsToInsert);
        }
        if (! empty($documentsToInsert)) {
            DB::table('service_request_documents')->insert($documentsToInsert);
        }
        if (! empty($historiesToInsert)) {
            DB::table('status_histories')->insert($historiesToInsert);
        }
    }

    /**
     * Batch Reaktivasi PBI-JK (Layanan 2).
     */
    protected function seedPbiBatch(int $serviceTypeId, int $totalCount): void
    {
        $statusDistribution = [
            ServiceRequestStatus::COMPLETED->value => 140,
            ServiceRequestStatus::REACTIVATED->value => 40,
            ServiceRequestStatus::MINISTRY_APPROVED->value => 30,
            ServiceRequestStatus::PROPOSED_TO_MINISTRY->value => 70, // 35 stuck (>14 hari), 35 normal
            ServiceRequestStatus::RECOMMENDATION_ISSUED->value => 25,
            ServiceRequestStatus::AWAITING_APPROVAL->value => 30,    // Widget 2 Tab a
            ServiceRequestStatus::ELIGIBILITY_VERIFICATION->value => 25,
            ServiceRequestStatus::DOCUMENT_CHECK->value => 15,
            ServiceRequestStatus::SUBMITTED->value => 15,
            ServiceRequestStatus::MINISTRY_REJECTED->value => 15,
            ServiceRequestStatus::REJECTED->value => 10,
        ];

        $reactivationsToInsert = [];
        $approvalsToInsert = [];
        $documentsToInsert = [];
        $historiesToInsert = [];

        $now = Carbon::create(2026, 9, 29, 10, 0, 0);

        foreach ($statusDistribution as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $submittedAt = $this->getRandomSubmissionDate();
                $village = $this->getRandomVillage();
                $applicantName = $this->faker->name();
                $applicantNik = $this->generateBlitarNik();
                $kkNumber = $this->generateBlitarNik();
                $requestNumber = $this->generateTicketNumber('PBI', $submittedAt);

                $reason = $this->faker->randomElement([
                    PbiReactivationReason::EMERGENCY->value,
                    PbiReactivationReason::CHRONIC->value,
                    PbiReactivationReason::CATASTROPHIC->value,
                    PbiReactivationReason::NEWBORN->value,
                    PbiReactivationReason::OTHER->value,
                ]);

                // Prioritas jika darurat medis!
                $isEmergency = ($reason === PbiReactivationReason::EMERGENCY->value);
                $isPriority = $isEmergency;

                $isCompleted = ($statusValue === ServiceRequestStatus::COMPLETED->value);
                $isReactivated = ($statusValue === ServiceRequestStatus::REACTIVATED->value);
                $isMinistryApproved = ($statusValue === ServiceRequestStatus::MINISTRY_APPROVED->value);
                $isProposed = ($statusValue === ServiceRequestStatus::PROPOSED_TO_MINISTRY->value);
                $isRecommendationIssued = ($statusValue === ServiceRequestStatus::RECOMMENDATION_ISSUED->value);
                $isAwaiting = ($statusValue === ServiceRequestStatus::AWAITING_APPROVAL->value);
                $isMinistryRejected = ($statusValue === ServiceRequestStatus::MINISTRY_REJECTED->value);
                $isRejected = ($statusValue === ServiceRequestStatus::REJECTED->value);

                $completedAt = null;
                $serviceResult = null;
                $rejectionReason = null;

                if ($isCompleted) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(10, 25));
                    $serviceResult = 'Kepesertaan PBI-JK berhasil diaktifkan kembali oleh BPJS Kesehatan.';
                } elseif ($isRejected) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(2, 4));
                    $rejectionReason = 'Data tidak memenuhi kriteria reaktivasi PBI-JK sesuai ketentuan Dinsos.';
                } elseif ($isMinistryRejected) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(15, 30));
                    $rejectionReason = 'Usulan reaktivasi ditolak oleh Kementerian Sosial RI.';
                }

                $serviceRequestId = DB::table('service_requests')->insertGetId([
                    'request_number' => $requestNumber,
                    'service_type_id' => $serviceTypeId,
                    'submitter_id' => ! empty($this->citizenUsers) && $this->faker->boolean(40) ? $this->faker->randomElement($this->citizenUsers) : null,
                    'applicant_name' => $applicantName,
                    'applicant_nik' => $applicantNik,
                    'family_card_number' => $kkNumber,
                    'address' => 'RT '.str_pad((string) $this->faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT).' RW '.str_pad((string) $this->faker->numberBetween(1, 8), 2, '0', STR_PAD_LEFT).' Dusun '.$this->faker->streetName().', Desa '.$village->name,
                    'village_id' => $village->id,
                    'phone' => '08'.$this->faker->numerify('##########'),
                    'submitted_at' => $submittedAt,
                    'officer_id' => $this->petugasPbi?->id,
                    'work_unit_id' => $this->linjamsosUnit?->id,
                    'status' => $statusValue,
                    'is_priority' => $isPriority,
                    'verification_result' => ($statusValue !== ServiceRequestStatus::SUBMITTED->value) ? 'Verifikasi kelayakan kepesertaan PBI-JK dan riwayat penonaktifan.' : null,
                    'officer_notes' => $isEmergency ? 'URGENT: Pasien membutuhkan tindakan medis segera di fasilitas kesehatan.' : 'Proses verifikasi berkas dan rekomendasi.',
                    'assessment_notes' => null,
                    'service_result' => $serviceResult,
                    'rejection_reason' => $rejectionReason,
                    'completed_at' => $completedAt,
                    'created_at' => $submittedAt,
                    'updated_at' => $completedAt ?? $submittedAt,
                ]);

                // Tanggal Usulan ke Kemensos & Kasus Tertahan (Widget 2 Tab c)
                $proposedAt = null;
                $ministryDecision = MinistryDecision::PENDING->value;
                $ministryDecidedAt = null;
                $reactivatedDate = null;
                $recommendationNumber = null;
                $recommendationIssuedAt = null;

                if ($isCompleted || $isReactivated || $isMinistryApproved || $isMinistryRejected || $isProposed) {
                    $this->pbiRecNumberSeq++;
                    $recommendationNumber = '440/'.str_pad((string) $this->pbiRecNumberSeq, 4, '0', STR_PAD_LEFT).'/409.105/2026';
                    $recommendationIssuedAt = (clone $submittedAt)->addDays(2);

                    if ($isProposed) {
                        // Separuh kasus tertahan > 14 hari (misal 15-40 hari yang lalu)
                        if ($i < 35) {
                            $proposedAt = (clone $now)->subDays($this->faker->numberBetween(15, 45));
                        } else {
                            $proposedAt = (clone $now)->subDays($this->faker->numberBetween(1, 10));
                        }
                    } else {
                        $proposedAt = (clone $submittedAt)->addDays(4);
                    }

                    if ($isCompleted || $isReactivated || $isMinistryApproved) {
                        $ministryDecision = MinistryDecision::APPROVED->value;
                        $ministryDecidedAt = (clone $proposedAt)->addDays($this->faker->numberBetween(7, 14));
                    } elseif ($isMinistryRejected) {
                        $ministryDecision = MinistryDecision::REJECTED->value;
                        $ministryDecidedAt = (clone $proposedAt)->addDays($this->faker->numberBetween(7, 14));
                    }

                    if ($isCompleted || $isReactivated) {
                        $reactivatedDate = (clone $ministryDecidedAt)->addDays($this->faker->numberBetween(2, 5))->toDateString();
                    }
                } elseif ($isRecommendationIssued) {
                    $this->pbiRecNumberSeq++;
                    $recommendationNumber = '440/'.str_pad((string) $this->pbiRecNumberSeq, 4, '0', STR_PAD_LEFT).'/409.105/2026';
                    $recommendationIssuedAt = (clone $submittedAt)->addDays(2);
                }

                $participantName = $this->faker->boolean(60) ? $applicantName : $this->faker->name();
                $participantNik = ($participantName === $applicantName) ? $applicantNik : $this->generateBlitarNik();

                $reactivationId = DB::table('pbi_reactivations')->insertGetId([
                    'service_request_id' => $serviceRequestId,
                    'participant_name' => $participantName,
                    'participant_nik' => $participantNik,
                    'bpjs_card_number' => '000'.$this->faker->numerify('##########'),
                    'deactivated_date' => (clone $submittedAt)->subMonths($this->faker->numberBetween(1, 4))->toDateString(),
                    'reason' => $reason,
                    'health_facility_name' => $isEmergency ? $this->faker->randomElement(['RSUD Ngudi Waluyo Wlingi', 'RSUD Srengat', 'RS Bhayangkara']) : 'Puskesmas '.$village->district?->name,
                    'health_letter_number' => 'SK/MED/'.$submittedAt->format('Ym').'/'.$this->faker->numerify('###'),
                    'decile' => $this->faker->numberBetween(1, 4),
                    'eligibility_notes' => 'Pasien berhak menerima bantuan iuran JKN berdasarkan verifikasi data sosial.',
                    'recommendation_number' => $recommendationNumber,
                    'recommendation_issued_at' => $recommendationIssuedAt,
                    'signer_id' => $recommendationNumber ? $this->kadis?->id : null,
                    'proposed_to_ministry_at' => $proposedAt,
                    'ministry_decision' => $ministryDecision,
                    'ministry_decided_at' => $ministryDecidedAt,
                    'reactivated_date' => $reactivatedDate,
                    'created_at' => $submittedAt,
                    'updated_at' => $completedAt ?? $proposedAt ?? $submittedAt,
                ]);

                // Approvals untuk PBI-JK (Widget 2 Tab a)
                if ($isAwaiting) {
                    $isStep1Pending = $this->faker->boolean(50);
                    $approvalsToInsert[] = [
                        'approvable_type' => PbiReactivation::class,
                        'approvable_id' => $reactivationId,
                        'step' => 1,
                        'approver_id' => $this->kabidLinjamsos?->id,
                        'decision' => $isStep1Pending ? ApprovalDecision::PENDING->value : ApprovalDecision::APPROVED->value,
                        'notes' => $isStep1Pending ? null : 'Rekomendasi reaktivasi disetujui oleh Kabid.',
                        'decided_at' => $isStep1Pending ? null : (clone $submittedAt)->addDays(1),
                        'created_at' => $submittedAt,
                        'updated_at' => (clone $submittedAt)->addDays(1),
                    ];

                    if (! $isStep1Pending) {
                        $approvalsToInsert[] = [
                            'approvable_type' => PbiReactivation::class,
                            'approvable_id' => $reactivationId,
                            'step' => 2,
                            'approver_id' => $this->kadis?->id,
                            'decision' => ApprovalDecision::PENDING->value,
                            'notes' => null,
                            'decided_at' => null,
                            'created_at' => (clone $submittedAt)->addDays(1),
                            'updated_at' => (clone $submittedAt)->addDays(1),
                        ];
                    }
                } elseif ($recommendationNumber) {
                    $approvalsToInsert[] = [
                        'approvable_type' => PbiReactivation::class,
                        'approvable_id' => $reactivationId,
                        'step' => 1,
                        'approver_id' => $this->kabidLinjamsos?->id,
                        'decision' => ApprovalDecision::APPROVED->value,
                        'notes' => 'Paraf rekomendasi reaktivasi.',
                        'decided_at' => (clone $submittedAt)->addDays(1),
                        'created_at' => $submittedAt,
                        'updated_at' => (clone $submittedAt)->addDays(1),
                    ];
                    $approvalsToInsert[] = [
                        'approvable_type' => PbiReactivation::class,
                        'approvable_id' => $reactivationId,
                        'step' => 2,
                        'approver_id' => $this->kadis?->id,
                        'decision' => ApprovalDecision::APPROVED->value,
                        'notes' => 'Surat rekomendasi ditandatangani Kadis.',
                        'decided_at' => $recommendationIssuedAt,
                        'created_at' => (clone $submittedAt)->addDays(1),
                        'updated_at' => $recommendationIssuedAt,
                    ];
                }

                // Documents
                $pbiReqs = $this->serviceRequirementsByType[$serviceTypeId] ?? [];
                foreach ($pbiReqs as $req) {
                    $documentsToInsert[] = [
                        'service_request_id' => $serviceRequestId,
                        'service_requirement_id' => $req->id,
                        'file_path' => 'documents/pbi/'.Str::slug($req->name).'_'.$serviceRequestId.'.pdf',
                        'original_name' => Str::slug($req->name).'.pdf',
                        'verification_status' => ServiceDocumentVerificationStatus::VALID->value,
                        'notes' => 'Sesuai persyaratan.',
                        'created_at' => $submittedAt,
                        'updated_at' => $submittedAt,
                    ];
                }

                // Status Histories
                $this->buildStatusHistoriesForRequest(
                    $historiesToInsert,
                    $serviceRequestId,
                    $statusValue,
                    $submittedAt,
                    $completedAt
                );

                if (count($approvalsToInsert) >= 200) {
                    DB::table('approvals')->insert($approvalsToInsert);
                    $approvalsToInsert = [];
                }
                if (count($documentsToInsert) >= 300) {
                    DB::table('service_request_documents')->insert($documentsToInsert);
                    $documentsToInsert = [];
                }
                if (count($historiesToInsert) >= 500) {
                    DB::table('status_histories')->insert($historiesToInsert);
                    $historiesToInsert = [];
                }
            }
        }

        if (! empty($approvalsToInsert)) {
            DB::table('approvals')->insert($approvalsToInsert);
        }
        if (! empty($documentsToInsert)) {
            DB::table('service_request_documents')->insert($documentsToInsert);
        }
        if (! empty($historiesToInsert)) {
            DB::table('status_histories')->insert($historiesToInsert);
        }
    }

    /**
     * Batch Layanan Umum (REHSOS / BANSOS).
     */
    protected function seedGeneralServiceBatch(int $serviceTypeId, int $totalCount): void
    {
        $prefix = ($serviceTypeId === 3) ? 'REHSOS' : 'BANSOS';
        $unitId = ($serviceTypeId === 3) ? $this->rehsosUnit?->id : $this->dayasosUnit?->id;
        $officerId = ($serviceTypeId === 3) ? $this->petugasRehsos?->id : $this->petugasLayanan?->id;

        $statuses = [
            ServiceRequestStatus::COMPLETED->value => (int) ($totalCount * 0.55),
            ServiceRequestStatus::IN_PROCESS->value => (int) ($totalCount * 0.20),
            ServiceRequestStatus::VERIFICATION->value => (int) ($totalCount * 0.12),
            ServiceRequestStatus::DOCUMENT_CHECK->value => (int) ($totalCount * 0.08),
            ServiceRequestStatus::REJECTED->value => (int) ($totalCount * 0.05),
        ];

        $documentsToInsert = [];
        $historiesToInsert = [];

        foreach ($statuses as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $submittedAt = $this->getRandomSubmissionDate();
                $village = $this->getRandomVillage();
                $applicantName = $this->faker->name();
                $requestNumber = $this->generateTicketNumber($prefix, $submittedAt);
                $isCompleted = ($statusValue === ServiceRequestStatus::COMPLETED->value);
                $isRejected = ($statusValue === ServiceRequestStatus::REJECTED->value);

                $completedAt = null;
                $serviceResult = null;
                $rejectionReason = null;

                if ($isCompleted) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(3, 7));
                    $serviceResult = 'Layanan bantuan sosial / rehabilitasi telah selesai disalurkan dan diproses.';
                } elseif ($isRejected) {
                    $completedAt = (clone $submittedAt)->addDays($this->faker->numberBetween(1, 3));
                    $rejectionReason = 'Kriteria pemohon tidak memenuhi persyaratan program bantuan sosial daerah.';
                }

                $serviceRequestId = DB::table('service_requests')->insertGetId([
                    'request_number' => $requestNumber,
                    'service_type_id' => $serviceTypeId,
                    'submitter_id' => ! empty($this->citizenUsers) && $this->faker->boolean(40) ? $this->faker->randomElement($this->citizenUsers) : null,
                    'applicant_name' => $applicantName,
                    'applicant_nik' => $this->generateBlitarNik(),
                    'family_card_number' => $this->generateBlitarNik(),
                    'address' => 'RT '.str_pad((string) $this->faker->numberBetween(1, 10), 2, '0', STR_PAD_LEFT).' Dusun '.$this->faker->streetName().', Desa '.$village->name,
                    'village_id' => $village->id,
                    'phone' => '08'.$this->faker->numerify('##########'),
                    'submitted_at' => $submittedAt,
                    'officer_id' => $officerId,
                    'work_unit_id' => $unitId,
                    'status' => $statusValue,
                    'is_priority' => false,
                    'verification_result' => ($statusValue !== ServiceRequestStatus::SUBMITTED->value) ? 'Verifikasi administrasi berkas pendukung.' : null,
                    'officer_notes' => 'Diproses sesuai antrean pelayanan.',
                    'assessment_notes' => ($serviceTypeId === 3) ? 'Assessment kebutuhan rehabilitasi sosial dasar telah dilakukan.' : null,
                    'service_result' => $serviceResult,
                    'rejection_reason' => $rejectionReason,
                    'completed_at' => $completedAt,
                    'created_at' => $submittedAt,
                    'updated_at' => $completedAt ?? $submittedAt,
                ]);

                // Documents
                $reqs = $this->serviceRequirementsByType[$serviceTypeId] ?? [];
                foreach ($reqs as $req) {
                    $documentsToInsert[] = [
                        'service_request_id' => $serviceRequestId,
                        'service_requirement_id' => $req->id,
                        'file_path' => 'documents/general/'.Str::slug($req->name).'_'.$serviceRequestId.'.pdf',
                        'original_name' => Str::slug($req->name).'.pdf',
                        'verification_status' => ServiceDocumentVerificationStatus::VALID->value,
                        'notes' => 'Lengkap.',
                        'created_at' => $submittedAt,
                        'updated_at' => $submittedAt,
                    ];
                }

                $this->buildStatusHistoriesForRequest(
                    $historiesToInsert,
                    $serviceRequestId,
                    $statusValue,
                    $submittedAt,
                    $completedAt
                );
            }
        }

        if (! empty($documentsToInsert)) {
            DB::table('service_request_documents')->insert($documentsToInsert);
        }
        if (! empty($historiesToInsert)) {
            DB::table('status_histories')->insert($historiesToInsert);
        }
    }

    /**
     * Susun riwayat status (status_histories) untuk permohonan.
     */
    protected function buildStatusHistoriesForRequest(
        array &$histories,
        int $serviceRequestId,
        string $finalStatus,
        Carbon $submittedAt,
        ?Carbon $completedAt
    ): void {
        $histories[] = [
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $serviceRequestId,
            'from_status' => null,
            'to_status' => ServiceRequestStatus::SUBMITTED->value,
            'notes' => 'Pengajuan berhasil dikirimkan oleh pemohon/operator.',
            'user_id' => $this->petugasLayanan?->id,
            'created_at' => $submittedAt,
        ];

        if ($finalStatus === ServiceRequestStatus::SUBMITTED->value) {
            return;
        }

        $checkAt = (clone $submittedAt)->addHours(4);
        $histories[] = [
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $serviceRequestId,
            'from_status' => ServiceRequestStatus::SUBMITTED->value,
            'to_status' => ServiceRequestStatus::DOCUMENT_CHECK->value,
            'notes' => 'Pemeriksaan kelengkapan dokumen persyaratan oleh petugas.',
            'user_id' => $this->petugasLayanan?->id,
            'created_at' => $checkAt,
        ];

        if ($finalStatus === ServiceRequestStatus::DOCUMENT_CHECK->value) {
            return;
        }

        if ($finalStatus === ServiceRequestStatus::REVISION_REQUESTED->value) {
            $histories[] = [
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $serviceRequestId,
                'from_status' => ServiceRequestStatus::DOCUMENT_CHECK->value,
                'to_status' => ServiceRequestStatus::REVISION_REQUESTED->value,
                'notes' => 'Dokumen belum memenuhi kriteria, diminta perbaikan berkas.',
                'user_id' => $this->petugasLayanan?->id,
                'created_at' => (clone $checkAt)->addHours(2),
            ];

            return;
        }

        $verifyAt = (clone $checkAt)->addDay();
        $histories[] = [
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $serviceRequestId,
            'from_status' => ServiceRequestStatus::DOCUMENT_CHECK->value,
            'to_status' => ServiceRequestStatus::DATA_VERIFICATION->value,
            'notes' => 'Pengecekan dan validasi data terpadu di sistem.',
            'user_id' => $this->petugasLayanan?->id,
            'created_at' => $verifyAt,
        ];

        if ($finalStatus === ServiceRequestStatus::DATA_VERIFICATION->value) {
            return;
        }

        if ($finalStatus === ServiceRequestStatus::REJECTED->value) {
            $histories[] = [
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $serviceRequestId,
                'from_status' => ServiceRequestStatus::DATA_VERIFICATION->value,
                'to_status' => ServiceRequestStatus::REJECTED->value,
                'notes' => 'Pengajuan ditolak karena kriteria desil / syarat tidak terpenuhi.',
                'user_id' => $this->petugasLayanan?->id,
                'created_at' => $completedAt ?? (clone $verifyAt)->addHours(4),
            ];

            return;
        }

        $awaitingAt = (clone $verifyAt)->addDay();
        $histories[] = [
            'statusable_type' => ServiceRequest::class,
            'statusable_id' => $serviceRequestId,
            'from_status' => ServiceRequestStatus::DATA_VERIFICATION->value,
            'to_status' => ServiceRequestStatus::AWAITING_APPROVAL->value,
            'notes' => 'Draf surat telah dibuat dan diajukan untuk persetujuan pejabat penandatangan.',
            'user_id' => $this->petugasLayanan?->id,
            'created_at' => $awaitingAt,
        ];

        if ($finalStatus === ServiceRequestStatus::AWAITING_APPROVAL->value) {
            return;
        }

        if ($finalStatus === ServiceRequestStatus::ISSUED->value || $finalStatus === ServiceRequestStatus::COMPLETED->value) {
            $issuedAt = (clone $awaitingAt)->addDay();
            $histories[] = [
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $serviceRequestId,
                'from_status' => ServiceRequestStatus::AWAITING_APPROVAL->value,
                'to_status' => ServiceRequestStatus::ISSUED->value,
                'notes' => 'Surat telah ditandatangani dan diterbitkan secara resmi.',
                'user_id' => $this->kadis?->id,
                'created_at' => $issuedAt,
            ];

            if ($finalStatus === ServiceRequestStatus::COMPLETED->value) {
                $histories[] = [
                    'statusable_type' => ServiceRequest::class,
                    'statusable_id' => $serviceRequestId,
                    'from_status' => ServiceRequestStatus::ISSUED->value,
                    'to_status' => ServiceRequestStatus::COMPLETED->value,
                    'notes' => 'Proses pelayanan telah selesai dan arsip ditutup.',
                    'user_id' => $this->petugasLayanan?->id,
                    'created_at' => $completedAt ?? (clone $issuedAt)->addHours(6),
                ];
            }
        }
    }

    /**
     * =========================================================================
     * SEED COMPLAINTS (Pengaduan Sosial) -> > 350 baris
     * =========================================================================
     */
    protected function seedComplaints(): void
    {
        $totalCount = 360;

        $statuses = [
            ComplaintStatus::RESOLVED->value => 190,
            ComplaintStatus::IN_HANDLING->value => 65,
            ComplaintStatus::DISPATCHED->value => 40,
            ComplaintStatus::VERIFICATION->value => 30,
            ComplaintStatus::RECEIVED->value => 20,
            ComplaintStatus::CLARIFICATION_REQUESTED->value => 5,
            ComplaintStatus::DUPLICATE->value => 5,
            ComplaintStatus::INVALID->value => 5,
        ];

        $attachmentsToInsert = [];
        $dispositionsToInsert = [];
        $historiesToInsert = [];

        foreach ($statuses as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $reportedAt = $this->getRandomSubmissionDate();
                $village = $this->getRandomVillage();
                $reporterName = $this->faker->name();
                $category = $this->faker->randomElement($this->complaintCategories);
                $complaintNumber = $this->generateTicketNumber('ADU', $reportedAt);

                $isResolved = ($statusValue === ComplaintStatus::RESOLVED->value);
                $resolvedAt = $isResolved ? (clone $reportedAt)->addDays($this->faker->numberBetween(1, 5)) : null;

                $actionTaken = $isResolved ? 'Tim penanganan dinas sosial bersama pamong setempat telah menindaklanjuti dan menyelesaikan permasalahan di lokasi.' : null;
                $verificationResult = ($statusValue !== ComplaintStatus::RECEIVED->value) ? 'Laporan telah diverifikasi kebenarannya oleh tim pelayanan dinas sosial.' : null;

                $complaintId = DB::table('complaints')->insertGetId([
                    'complaint_number' => $complaintNumber,
                    'complaint_category_id' => $category->id,
                    'reporter_id' => ! empty($this->citizenUsers) && $this->faker->boolean(40) ? $this->faker->randomElement($this->citizenUsers) : null,
                    'reporter_name' => $reporterName,
                    'reporter_phone' => '08'.$this->faker->numerify('##########'),
                    'location_detail' => 'Dusun '.$this->faker->streetName().' RT 0'.$this->faker->numberBetween(1, 9).', Desa '.$village->name.', Kec. '.$village->district?->name,
                    'village_id' => $village->id,
                    'description' => 'Laporan aduan warga: '.$category->name.' di lingkungan RT/RW sekitar. Mohon bantuan petugas dinas sosial terkait penanganan segera.',
                    'reported_at' => $reportedAt,
                    'officer_id' => $this->petugasRehsos?->id,
                    'status' => $statusValue,
                    'verification_result' => $verificationResult,
                    'action_taken' => $actionTaken,
                    'duplicate_of_id' => null,
                    'resolved_at' => $resolvedAt,
                    'created_at' => $reportedAt,
                    'updated_at' => $resolvedAt ?? $reportedAt,
                ]);

                // Attachment (Foto kejadian)
                $attachmentsToInsert[] = [
                    'complaint_id' => $complaintId,
                    'file_path' => 'complaints/'.$reportedAt->format('Ym').'/foto_aduan_'.$complaintId.'.jpg',
                    'type' => ComplaintAttachmentType::PHOTO->value,
                    'created_at' => $reportedAt,
                    'updated_at' => $reportedAt,
                ];

                // Disposition jika status >= dispatched
                if (in_array($statusValue, [ComplaintStatus::DISPATCHED->value, ComplaintStatus::IN_HANDLING->value, ComplaintStatus::RESOLVED->value])) {
                    $dispositionsToInsert[] = [
                        'dispositionable_type' => Complaint::class,
                        'dispositionable_id' => $complaintId,
                        'from_user_id' => $this->kadis?->id,
                        'to_work_unit_id' => $this->rehsosUnit?->id,
                        'to_user_id' => $this->petugasRehsos?->id,
                        'instructions' => 'Segera koordinasikan dengan aparat desa dan tindak lanjuti laporan ini.',
                        'disposed_at' => (clone $reportedAt)->addHours(6),
                        'created_at' => (clone $reportedAt)->addHours(6),
                        'updated_at' => (clone $reportedAt)->addHours(6),
                    ];
                }

                // History
                $historiesToInsert[] = [
                    'statusable_type' => Complaint::class,
                    'statusable_id' => $complaintId,
                    'from_status' => null,
                    'to_status' => ComplaintStatus::RECEIVED->value,
                    'notes' => 'Pengaduan disampaikan via portal SAPA SOSIAL.',
                    'user_id' => null,
                    'created_at' => $reportedAt,
                ];

                if ($isResolved) {
                    $historiesToInsert[] = [
                        'statusable_type' => Complaint::class,
                        'statusable_id' => $complaintId,
                        'from_status' => ComplaintStatus::IN_HANDLING->value,
                        'to_status' => ComplaintStatus::RESOLVED->value,
                        'notes' => 'Penanganan selesai dan laporan ditutup.',
                        'user_id' => $this->petugasRehsos?->id,
                        'created_at' => $resolvedAt,
                    ];
                }

                if (count($attachmentsToInsert) >= 200) {
                    DB::table('complaint_attachments')->insert($attachmentsToInsert);
                    $attachmentsToInsert = [];
                }
                if (count($dispositionsToInsert) >= 200) {
                    DB::table('dispositions')->insert($dispositionsToInsert);
                    $dispositionsToInsert = [];
                }
                if (count($historiesToInsert) >= 500) {
                    DB::table('status_histories')->insert($historiesToInsert);
                    $historiesToInsert = [];
                }
            }
        }

        if (! empty($attachmentsToInsert)) {
            DB::table('complaint_attachments')->insert($attachmentsToInsert);
        }
        if (! empty($dispositionsToInsert)) {
            DB::table('dispositions')->insert($dispositionsToInsert);
        }
        if (! empty($historiesToInsert)) {
            DB::table('status_histories')->insert($historiesToInsert);
        }
    }

    /**
     * =========================================================================
     * SEED REHABILITATION (Klien, Kasus, Assessment, Rujukan, Monitoring)
     * =========================================================================
     */
    protected function seedRehabilitation(): void
    {
        $totalCases = 205;

        $statuses = [
            RehabilitationCaseStatus::CLOSED->value => 80,
            RehabilitationCaseStatus::IN_SERVICE->value => 50,    // Aktif
            RehabilitationCaseStatus::MONITORING->value => 35,    // Aktif
            RehabilitationCaseStatus::SERVICE_PLANNING->value => 20, // Aktif
            RehabilitationCaseStatus::ASSESSMENT->value => 12,    // Aktif
            RehabilitationCaseStatus::RECEIVED->value => 8,       // Aktif
        ];

        $clientsToInsert = [];
        $assessmentsToInsert = [];
        $referralsToInsert = [];
        $monitoringToInsert = [];
        $historiesToInsert = [];

        $referralSeq = 100;

        foreach ($statuses as $statusValue => $count) {
            for ($i = 0; $i < $count; $i++) {
                $receivedAt = $this->getRandomSubmissionDate();
                $village = $this->getRandomVillage();
                $clientCategory = $this->faker->randomElement($this->clientCategories);
                $clientName = $this->faker->name();
                $gender = $this->faker->randomElement([Gender::MALE->value, Gender::FEMALE->value]);

                $isClosed = ($statusValue === RehabilitationCaseStatus::CLOSED->value);
                $closedAt = $isClosed ? (clone $receivedAt)->addDays($this->faker->numberBetween(14, 45)) : null;

                // 1. Client
                $clientId = DB::table('clients')->insertGetId([
                    'name' => $clientName,
                    'client_category_id' => $clientCategory->id,
                    'nik' => $this->generateBlitarNik(),
                    'birth_date' => $this->faker->dateTimeBetween('-80 years', '-10 years')->format('Y-m-d'),
                    'gender' => $gender,
                    'address' => 'Dusun '.$this->faker->streetName().', Desa '.$village->name.', Kec. '.$village->district?->name,
                    'village_id' => $village->id,
                    'phone' => $this->faker->boolean(60) ? '08'.$this->faker->numerify('##########') : null,
                    'created_at' => $receivedAt,
                    'updated_at' => $receivedAt,
                ]);

                // 2. Rehabilitation Case
                $handlingType = $this->faker->randomElement([
                    RehabilitationHandlingType::REFERRAL->value,
                    RehabilitationHandlingType::BOTH->value,
                    RehabilitationHandlingType::DIRECT->value,
                ]);

                $caseNumber = $this->generateTicketNumber('RHS', $receivedAt);

                $caseId = DB::table('rehabilitation_cases')->insertGetId([
                    'case_number' => $caseNumber,
                    'client_id' => $clientId,
                    'service_request_id' => null,
                    'complaint_id' => null,
                    'officer_id' => $this->petugasRehsos?->id,
                    'handling_type' => $handlingType,
                    'status' => $statusValue,
                    'handling_result' => $isClosed ? 'Pelayanan rehabilitasi telah tuntas dan klien telah mandiri/kembali ke keluarga/menetap di panti.' : null,
                    'received_at' => $receivedAt,
                    'closed_at' => $closedAt,
                    'created_at' => $receivedAt,
                    'updated_at' => $closedAt ?? $receivedAt,
                ]);

                // 3. Assessment jika status >= assessment
                if ($statusValue !== RehabilitationCaseStatus::RECEIVED->value) {
                    $needsReferral = ($handlingType !== RehabilitationHandlingType::DIRECT->value);
                    $assessmentDate = (clone $receivedAt)->addDays(1)->toDateString();

                    $assessmentId = DB::table('assessments')->insertGetId([
                        'rehabilitation_case_id' => $caseId,
                        'officer_id' => $this->petugasRehsos?->id,
                        'assessment_date' => $assessmentDate,
                        'result' => 'Kondisi sosial klien memerlukan pendampingan berkelanjutan dalam pemenuhan hak dasar dan pemulihan sosial ekonomi.',
                        'service_needs' => 'Kebutuhan tempat tinggal, permakanan bergizi, perawatan medis, dan bimbingan psikososial.',
                        'recommendation' => $needsReferral ? 'Direkomendasikan rujukan ke lembaga rujukan sosial mitra Dinas Sosial.' : 'Pelayanan langsung dan bantuan permakanan oleh Dinsos Blitar.',
                        'needs_referral' => $needsReferral,
                        'created_at' => (clone $receivedAt)->addDays(1),
                        'updated_at' => (clone $receivedAt)->addDays(1),
                    ]);

                    // 4. Referral jika needs_referral dan status >= service_planning
                    if ($needsReferral && in_array($statusValue, [RehabilitationCaseStatus::SERVICE_PLANNING->value, RehabilitationCaseStatus::IN_SERVICE->value, RehabilitationCaseStatus::MONITORING->value, RehabilitationCaseStatus::CLOSED->value])) {
                        $referralSeq++;
                        $institution = $this->faker->randomElement($this->referralInstitutions);
                        $referralNumber = 'RJK-'.$receivedAt->format('Ym').'-'.str_pad((string) $referralSeq, 5, '0', STR_PAD_LEFT);
                        $refStatus = $isClosed ? ReferralStatus::COMPLETED->value : ReferralStatus::IN_SERVICE->value;

                        $referralId = DB::table('referrals')->insertGetId([
                            'referral_number' => $referralNumber,
                            'rehabilitation_case_id' => $caseId,
                            'assessment_id' => $assessmentId,
                            'referral_institution_id' => $institution->id,
                            'officer_id' => $this->petugasRehsos?->id,
                            'referral_date' => (clone $receivedAt)->addDays(3)->toDateString(),
                            'status' => $refStatus,
                            'service_result' => $isClosed ? 'Klien telah selesai menerima program rehabilitasi sosial di lembaga rujukan.' : null,
                            'completed_at' => $isClosed ? $closedAt : null,
                            'created_at' => (clone $receivedAt)->addDays(3),
                            'updated_at' => $closedAt ?? (clone $receivedAt)->addDays(3),
                        ]);

                        // 5. Monitoring Records jika in_service / monitoring / closed
                        if (in_array($statusValue, [RehabilitationCaseStatus::MONITORING->value, RehabilitationCaseStatus::CLOSED->value])) {
                            $monitoringToInsert[] = [
                                'rehabilitation_case_id' => $caseId,
                                'referral_id' => $referralId,
                                'officer_id' => $this->petugasRehsos?->id,
                                'monitoring_date' => (clone $receivedAt)->addDays(10)->toDateString(),
                                'progress' => 'Klien beradaptasi dengan baik di lingkungan baru, kondisi kesehatan stabil.',
                                'result_notes' => 'Direkomendasikan pemantauan berkala bulanan.',
                                'created_at' => (clone $receivedAt)->addDays(10),
                                'updated_at' => (clone $receivedAt)->addDays(10),
                            ];
                        }
                    }
                }

                // History
                $historiesToInsert[] = [
                    'statusable_type' => RehabilitationCase::class,
                    'statusable_id' => $caseId,
                    'from_status' => null,
                    'to_status' => RehabilitationCaseStatus::RECEIVED->value,
                    'notes' => 'Kasus rehabilitasi sosial berhasil dicatat ke sistem.',
                    'user_id' => $this->petugasRehsos?->id,
                    'created_at' => $receivedAt,
                ];

                if ($isClosed) {
                    $historiesToInsert[] = [
                        'statusable_type' => RehabilitationCase::class,
                        'statusable_id' => $caseId,
                        'from_status' => RehabilitationCaseStatus::MONITORING->value,
                        'to_status' => RehabilitationCaseStatus::CLOSED->value,
                        'notes' => 'Penanganan tuntas, kasus ditutup secara resmi.',
                        'user_id' => $this->petugasRehsos?->id,
                        'created_at' => $closedAt,
                    ];
                }
            }
        }

        if (! empty($monitoringToInsert)) {
            DB::table('monitoring_records')->insert($monitoringToInsert);
        }
        if (! empty($historiesToInsert)) {
            DB::table('status_histories')->insert($historiesToInsert);
        }
    }

    /**
     * Sinkronisasi nilai last_number di tabel number_sequences.
     */
    protected function syncNumberSequences(): void
    {
        foreach ($this->sequences as $prefixPeriod => $lastNumber) {
            [$prefix, $period] = explode('-', $prefixPeriod);

            NumberSequence::updateOrCreate(
                ['prefix' => $prefix, 'period' => $period],
                ['last_number' => $lastNumber]
            );
        }
    }
}
