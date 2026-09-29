<?php

namespace App\Enums;

/**
 * Daftar permission yang tersedia di sistem SAPA SOSIAL.
 *
 * Konvensi penamaan:
 * - CRUD pada suatu resource → "kelola_{resource}" (mencakup create, read, update, delete)
 * - Aksi khusus non-CRUD → nama aksi spesifik (approve, assign, lihat)
 *
 * Diturunkan dari Filament Resources yang ada di app/Filament/Resources:
 * - ServiceRequestResource       → ManageServiceRequest
 * - ComplaintResource            → ManageComplaint
 * - ClientResource               → ManageRehabilitationClient
 * - RehabilitationCaseResource   → ManageRehabilitationCase
 * - InformationPageResource      → ManageInformation (termasuk FAQ & form unduhan)
 * - FaqResource                  → (digabung ke ManageInformation)
 * - ServiceTypeResource          → ManageServiceType
 * - DtsenPurposeResource         → ManageDtsenPurpose
 * - ClientCategoryResource       → ManageClientCategory
 * - ComplaintCategoryResource    → ManageComplaintCategory
 * - ReferralInstitutionResource  → ManageReferralInstitution
 * - UserResource                 → ManageUser
 * - WorkUnitResource             → ManageWorkUnit
 * - DistrictResource             → ManageRegion (kecamatan & desa digabung)
 * - VillageResource              → (digabung ke ManageRegion)
 */
enum PermissionType: string
{
    // ===== Layanan Sosial =====
    case ManageServiceRequest = 'kelola_pengajuan_layanan';
    case ManageComplaint = 'kelola_aduan';
    case ManageRehabilitationClient = 'kelola_klien_rehabilitasi';
    case ManageRehabilitationCase = 'kelola_kasus_rehabilitasi';

    // ===== Persetujuan Berjenjang =====
    case ApproveDtsenLetter = 'approve_surat_dtsen';
    case ApprovePbiRecommendation = 'approve_rekomendasi_pbi';

    // ===== Pusat Informasi =====
    case ManageInformation = 'kelola_informasi';

    // ===== Data Master =====
    case ManageServiceType = 'kelola_jenis_layanan';
    case ManageDtsenPurpose = 'kelola_tujuan_dtsen';
    case ManageClientCategory = 'kelola_kategori_klien';
    case ManageComplaintCategory = 'kelola_kategori_aduan';
    case ManageReferralInstitution = 'kelola_lembaga_rujukan';

    // ===== Pengguna & Sistem =====
    case ManageUser = 'kelola_pengguna';
    case ManageRole = 'atur_role';
    case ManageWorkUnit = 'kelola_unit_kerja';
    case ManageRegion = 'kelola_wilayah';

    // ===== Dashboard & Laporan =====
    case ViewDashboard = 'lihat_dashboard';
    case ViewReport = 'lihat_laporan';
    case ExportReport = 'ekspor_laporan';

    /**
     * Label tampilan dalam bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::ManageServiceRequest => 'Kelola Pengajuan Layanan',
            self::ManageComplaint => 'Kelola Pengaduan Sosial',
            self::ManageRehabilitationClient => 'Kelola Klien Rehabilitasi',
            self::ManageRehabilitationCase => 'Kelola Kasus Rehabilitasi',
            self::ApproveDtsenLetter => 'Setujui/Paraf SK DTSEN',
            self::ApprovePbiRecommendation => 'Setujui/Paraf Rekomendasi PBI',
            self::ManageInformation => 'Kelola Informasi Publik',
            self::ManageServiceType => 'Kelola Jenis Layanan',
            self::ManageDtsenPurpose => 'Kelola Tujuan SK DTSEN',
            self::ManageClientCategory => 'Kelola Kategori Klien',
            self::ManageComplaintCategory => 'Kelola Kategori Pengaduan',
            self::ManageReferralInstitution => 'Kelola Lembaga Rujukan',
            self::ManageUser => 'Kelola Pengguna',
            self::ManageRole => 'Atur Role & Permission',
            self::ManageWorkUnit => 'Kelola Unit Kerja',
            self::ManageRegion => 'Kelola Wilayah (Kecamatan & Desa)',
            self::ViewDashboard => 'Lihat Dashboard',
            self::ViewReport => 'Lihat Laporan',
            self::ExportReport => 'Ekspor Laporan',
        };
    }

    /**
     * Kelompok navigasi permission (untuk tampilan di halaman kelola role).
     */
    public function group(): string
    {
        return match ($this) {
            self::ManageServiceRequest,
            self::ManageComplaint,
            self::ManageRehabilitationClient,
            self::ManageRehabilitationCase => 'Layanan Sosial',

            self::ApproveDtsenLetter,
            self::ApprovePbiRecommendation => 'Persetujuan Berjenjang',

            self::ManageInformation => 'Pusat Informasi',

            self::ManageServiceType,
            self::ManageDtsenPurpose,
            self::ManageClientCategory,
            self::ManageComplaintCategory,
            self::ManageReferralInstitution => 'Data Master',

            self::ManageUser,
            self::ManageRole,
            self::ManageWorkUnit,
            self::ManageRegion => 'Pengguna & Sistem',

            self::ViewDashboard,
            self::ViewReport,
            self::ExportReport => 'Dashboard & Laporan',
        };
    }
}
