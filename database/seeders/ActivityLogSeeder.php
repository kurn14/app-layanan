<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Complaint;
use App\Models\DtsenCertificate;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petugasLayanan = User::where('email', 'petugas.layanan@dinsos.blitarkab.go.id')->first();
        $petugasRehsos = User::where('email', 'petugas.rehsos@dinsos.blitarkab.go.id')->first();
        $kadis = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();
        $kabidLinjamsos = User::where('email', 'kabid.linjamsos@dinsos.blitarkab.go.id')->first();
        $wargaAgus = User::where('email', 'warga.agus@gmail.com')->first();

        $sr1 = ServiceRequest::first();
        $cert1 = DtsenCertificate::first();
        $case1 = RehabilitationCase::first();
        $c1 = Complaint::first();

        $logs = [
            [
                'log_name' => 'auth',
                'description' => 'User berhasil login ke sistem SAPA SOSIAL',
                'subject_type' => User::class,
                'subject_id' => $wargaAgus?->id ?? 1,
                'event' => 'login',
                'causer_type' => User::class,
                'causer_id' => $wargaAgus?->id ?? 1,
                'properties' => ['ip' => '127.0.0.1', 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'log_name' => 'service_request',
                'description' => 'Pengajuan Surat Keterangan DTSEN dibuat oleh pemohon',
                'subject_type' => ServiceRequest::class,
                'subject_id' => $sr1?->id ?? 1,
                'event' => 'created',
                'causer_type' => User::class,
                'causer_id' => $wargaAgus?->id ?? 1,
                'properties' => ['request_number' => $sr1?->request_number ?? 'DTSEN-202609-00001', 'service_type' => 'DTSEN'],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'log_name' => 'service_request',
                'description' => 'Petugas melakukan verifikasi data SIKS-NG pemohon',
                'subject_type' => ServiceRequest::class,
                'subject_id' => $sr1?->id ?? 1,
                'event' => 'verified',
                'causer_type' => User::class,
                'causer_id' => $petugasLayanan?->id,
                'properties' => ['decile' => 2, 'status' => 'data_verification'],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'log_name' => 'dtsen_certificate',
                'description' => 'Pemeriksaan draf dan paraf surat oleh Kepala Bidang Linjamsos',
                'subject_type' => DtsenCertificate::class,
                'subject_id' => $cert1?->id ?? 1,
                'event' => 'approved_step_1',
                'causer_type' => User::class,
                'causer_id' => $kabidLinjamsos?->id,
                'properties' => ['step' => 1, 'decision' => 'approved'],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'log_name' => 'dtsen_certificate',
                'description' => 'Persetujuan dan penandatanganan Surat Keterangan DTSEN oleh Kepala Dinas',
                'subject_type' => DtsenCertificate::class,
                'subject_id' => $cert1?->id ?? 1,
                'event' => 'issued',
                'causer_type' => User::class,
                'causer_id' => $kadis?->id,
                'properties' => ['certificate_number' => $cert1?->certificate_number ?? '400.9/012/409.105/2026'],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'log_name' => 'rehabilitation',
                'description' => 'Pekerja Sosial melakukan assessment kasus lansia terlantar',
                'subject_type' => RehabilitationCase::class,
                'subject_id' => $case1?->id ?? 1,
                'event' => 'assessed',
                'causer_type' => User::class,
                'causer_id' => $petugasRehsos?->id,
                'properties' => ['case_number' => $case1?->case_number ?? 'RHS-202609-00001', 'needs_referral' => true],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'log_name' => 'complaint',
                'description' => 'Disposisi laporan penanganan ODGJ terlantar di pasar Kanigoro ke tim TRC',
                'subject_type' => Complaint::class,
                'subject_id' => $c1?->id ?? 1,
                'event' => 'dispatched',
                'causer_type' => User::class,
                'causer_id' => $kadis?->id ?? $petugasLayanan?->id,
                'properties' => ['target_unit' => 'Bidang Rehabilitasi Sosial'],
                'batch_uuid' => Str::uuid()->toString(),
                'created_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create($log);
        }
    }
}
