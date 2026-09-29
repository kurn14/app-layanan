# Rencana Aksi: Pembatasan Akses SAPA SOSIAL dengan Spatie Permission v8

**Revisi:** 3 role panel (Administrator, Operator, Pimpinan) + pengunjung hanya di frontend

| | |
|--|--|
| **Sistem** | SAPA SOSIAL — Dinas Sosial Kabupaten Blitar |
| **Stack** | Laravel 12+ · PHP 8.3+ · Filament v5 · Livewire v4 · PostgreSQL |
| **Paket** | `spatie/laravel-permission ^8.0` |
| **Referensi** | https://spatie.be/docs/laravel-permission/v8/installation-laravel |

---

## 1. Latar Belakang dan Asumsi

Di PRD, tabel `users` dipakai oleh staf dan masyarakat (`submitter_id` / `reporter_id` mengarah ke `users`). Spatie hanya menjawab "apakah user ini punya izin X". Ia tidak otomatis menutup panel `/admin`, sehingga perlu dua lapisan:

1. **Gerbang masuk panel** (Filament `canAccessPanel`).
2. **Otorisasi di dalam** (role + permission Spatie, Policy, dan pembatasan baris data).

**Asumsi revisi ini:**

- **Administrator, Operator, Pimpinan** adalah satu-satunya role di Spatie. Semuanya masuk panel Filament `/admin`.
- **Petugas Dinsos digabung ke Operator.**
- **Pengunjung/masyarakat tidak punya role sama sekali.** Mereka hanya masuk portal Livewire (`/`) dan tidak bisa masuk panel.
- **Pejabat Penandatangan** tidak menjadi role sendiri (satu akun bisa merangkap, mis. Kadis = Pimpinan + Penandatangan). Hak tanda tangan diberikan lewat permission `approve_*`.
- **Operator Kecamatan/Desa** dipertahankan tanpa role baru: jika `district_id` / `village_id` user terisi, datanya dibatasi ke wilayah itu; jika kosong, ia staf Dinsos dengan akses sesuai izinnya.

## 2. Perubahan dari Rencana Sebelumnya

| Sebelumnya | Sekarang |
|---|---|
| 6 role (administrator, officer, signer, leadership, area_operator, citizen) | **3 role**: `administrator`, `operator`, `pimpinan` |
| Role `citizen` | Dihapus. Tidak punya role = hanya frontend |
| Role `signer` | Diganti permission `approve_*` |
| Role `area_operator` | Diganti pembatasan wilayah berdasarkan `district_id` / `village_id` |
| Gerbang panel: cek 5 role | Cek 3 role |

## 3. Temuan Dokumentasi Spatie v8

- **Versi.** Spatie v7 dan v8 untuk Laravel 12 dan 13; v8 butuh PHP 8.3+. Laravel 11 hanya didukung v6. PRD menulis Laravel minimal 11.28, maka gunakan **Laravel 12 atau lebih baru** dan pastikan cocok dengan Filament v5.
- **Nama yang dilarang.** Model `User` tidak boleh punya kolom, relasi, atau method bernama `role`, `roles`, `permission`, `permissions`. Skema `users` di PRD tidak melanggar ini.
- **Primary key.** Harus integer auto-increment (sesuai PRD, `bigint id`).
- **Cache.** Jika `CACHE_STORE=database`, jalankan migrasi cache Laravel.
- `config/permission.php` akan dipublish; jika sudah ada file bernama sama, ganti nama atau hapus dulu.

## 4. Keputusan Desain

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Guard | Satu guard `web` | Portal dan panel memakai tabel `users` yang sama |
| Teams | Tidak dipakai (`teams => false`) | Wilayah dibatasi lewat `users.district_id` / `village_id` dan Eloquent scope |
| Multi-peran | Didukung | Sesuai PRD |
| Nama role | Inggris/Indonesia snake_case konsisten | Konvensi PRD 4.1; label tampilan lewat PHP Enum |
| Nama permission | Bahasa Indonesia, `kelola_{resource}` untuk CRUD | Lebih alami dan tidak terlalu granular; dideklarasikan di `App\Enums\PermissionType` |
| Pembatasan baris | Policy + query scope | Spatie menjawab "boleh melakukan aksi apa", Policy menjawab "pada baris mana" |

## 5. Tahapan

### Tahap 1 — Instalasi dan fondasi ✅ SELESAI

1. ~~Pastikan `config/permission.php` belum ada.~~ ✅
2. ~~`composer require spatie/laravel-permission`~~ ✅ v8.3.0
3. ~~`php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`~~ ✅
4. ~~Set `'teams' => false` di `config/permission.php`.~~ ✅
5. ~~`php artisan optimize:clear`, lalu `php artisan migrate` (PostgreSQL).~~ ✅
6. ~~Tambahkan trait `HasRoles` ke `App\Models\User`.~~ ✅
7. ~~Buat PHP Enum untuk role dan permission (cek halaman Enums di dokumentasi Spatie v8).~~ ✅ `App\Enums\PermissionType`

**Gerbang panel — SUDAH DIPERBAIKI:**

```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active
            && $this->hasAnyRole(['administrator', 'operator', 'pimpinan']);
    }
}
```

> **Catatan:** Sebelumnya `canAccessPanel` hanya cek `is_active`, sehingga masyarakat yang mendaftar bisa masuk panel admin. Sekarang sudah diperbaiki — masyarakat (tanpa role) otomatis tertolak.

### Tahap 2 — Permission dan matriks role ✅ SELESAI

#### 2.1 Daftar Permission (`App\Enums\PermissionType`)

| Kelompok | Enum Case | Nilai (`value`) | Label |
|---|---|---|---|
| **Layanan Sosial** | `ManageServiceRequest` | `kelola_pengajuan_layanan` | Kelola Pengajuan Layanan |
| | `ManageComplaint` | `kelola_aduan` | Kelola Pengaduan Sosial |
| | `ManageRehabilitationClient` | `kelola_klien_rehabilitasi` | Kelola Klien Rehabilitasi |
| | `ManageRehabilitationCase` | `kelola_kasus_rehabilitasi` | Kelola Kasus Rehabilitasi |
| **Persetujuan** | `ApproveDtsenLetter` | `approve_surat_dtsen` | Setujui/Paraf SK DTSEN |
| | `ApprovePbiRecommendation` | `approve_rekomendasi_pbi` | Setujui/Paraf Rekomendasi PBI |
| **Pusat Informasi** | `ManageInformation` | `kelola_informasi` | Kelola Informasi Publik |
| **Data Master** | `ManageServiceType` | `kelola_jenis_layanan` | Kelola Jenis Layanan |
| | `ManageDtsenPurpose` | `kelola_tujuan_dtsen` | Kelola Tujuan SK DTSEN |
| | `ManageClientCategory` | `kelola_kategori_klien` | Kelola Kategori Klien |
| | `ManageComplaintCategory` | `kelola_kategori_aduan` | Kelola Kategori Pengaduan |
| | `ManageReferralInstitution` | `kelola_lembaga_rujukan` | Kelola Lembaga Rujukan |
| **Pengguna & Sistem** | `ManageUser` | `kelola_pengguna` | Kelola Pengguna |
| | `ManageRole` | `atur_role` | Atur Role & Permission |
| | `ManageWorkUnit` | `kelola_unit_kerja` | Kelola Unit Kerja |
| | `ManageRegion` | `kelola_wilayah` | Kelola Wilayah (Kecamatan & Desa) |
| **Dashboard & Laporan** | `ViewDashboard` | `lihat_dashboard` | Lihat Dashboard |
| | `ViewReport` | `lihat_laporan` | Lihat Laporan |
| | `ExportReport` | `ekspor_laporan` | Ekspor Laporan |

#### 2.2 Pemetaan Permission ke Filament Resource

| Filament Resource | Permission | Catatan |
|---|---|---|
| `ServiceRequestResource` | `kelola_pengajuan_layanan` | Termasuk dokumen, disposisi, riwayat status |
| `ComplaintResource` | `kelola_aduan` | Termasuk lampiran, disposisi, riwayat status |
| `ClientResource` | `kelola_klien_rehabilitasi` | Data sensitif, dibatasi per petugas |
| `RehabilitationCaseResource` | `kelola_kasus_rehabilitasi` | Termasuk assessment, rujukan, monitoring |
| `InformationPageResource` | `kelola_informasi` | Termasuk form unduhan dan FAQ |
| `FaqResource` | `kelola_informasi` | Digabung dengan InformationPage |
| `ServiceTypeResource` | `kelola_jenis_layanan` | Data master |
| `DtsenPurposeResource` | `kelola_tujuan_dtsen` | Data master |
| `ClientCategoryResource` | `kelola_kategori_klien` | Data master |
| `ComplaintCategoryResource` | `kelola_kategori_aduan` | Data master |
| `ReferralInstitutionResource` | `kelola_lembaga_rujukan` | Data master |
| `UserResource` | `kelola_pengguna` | Manajemen akun dan penetapan role |
| `RoleResource` | `atur_role` | Manajemen role & konfigurasi hak akses |
| `WorkUnitResource` | `kelola_unit_kerja` | Data master |
| `DistrictResource` | `kelola_wilayah` | Digabung kecamatan + desa |
| `VillageResource` | `kelola_wilayah` | Digabung kecamatan + desa |

#### 2.3 Matriks Role × Permission

| Permission | administrator | operator | pimpinan |
|---|---|---|---|
| `kelola_pengajuan_layanan` | ✅ | ✅ | ❌ |
| `kelola_aduan` | ✅ | ✅ | ❌ |
| `kelola_klien_rehabilitasi` | ✅ | ✅ (kasus ditugaskan) | ❌ |
| `kelola_kasus_rehabilitasi` | ✅ | ✅ (kasus ditugaskan) | ❌ |
| `approve_surat_dtsen` | ❌ | ❌ | ✅ |
| `approve_rekomendasi_pbi` | ❌ | ❌ | ✅ |
| `kelola_informasi` | ✅ | ❌ | ❌ |
| `kelola_jenis_layanan` | ✅ | ❌ | ❌ |
| `kelola_tujuan_dtsen` | ✅ | ❌ | ❌ |
| `kelola_kategori_klien` | ✅ | ❌ | ❌ |
| `kelola_kategori_aduan` | ✅ | ❌ | ❌ |
| `kelola_lembaga_rujukan` | ✅ | ❌ | ❌ |
| `kelola_pengguna` | ✅ | ❌ | ❌ |
| `atur_role` | ✅ | ❌ | ❌ |
| `kelola_unit_kerja` | ✅ | ❌ | ❌ |
| `kelola_wilayah` | ✅ | ❌ | ❌ |
| `lihat_dashboard` | ✅ | ✅ | ✅ |
| `lihat_laporan` | ✅ | ✅ (sesuai wilayah) | ✅ |
| `ekspor_laporan` | ✅ | ✅ | ❌ |

Catatan:

- **Persetujuan.** Permission `approve_surat_dtsen` dan `approve_rekomendasi_pbi` diberikan ke role `pimpinan`. Jika Kabid bukan `pimpinan` tetapi harus memaraf, berikan permission itu langsung ke user tersebut (`givePermissionTo`) tanpa membuat role baru. Langkah paraf tetap ditentukan `approvals.approver_id` dan `step`, sehingga hanya pejabat yang ditunjuk pada langkah itu yang bisa memaraf.
- **Pimpinan "hanya baca".** Pimpinan tidak dapat mengubah data apa pun; ia hanya menyetujui dokumen yang menunggu tanda tangannya, dan melihat dashboard/laporan.
- **Administrator.** `Gate::before` boleh dipakai agar admin lolos semua izin, kecuali `approve_*`. Administrator tidak boleh otomatis menandatangani surat.

### Tahap 3 — Seeder & Antarmuka Panel Filament ✅ SELESAI

1. **`RolePermissionSeeder`** (idempotent):
   - Reset cache Spatie (`forgetCachedPermissions()`).
   - `firstOrCreate` semua 19 permission dari `PermissionType::cases()`.
   - `syncPermissions` untuk 3 role (`administrator`, `operator`, `pimpinan`) sesuai matriks.
   - Tetapkan role awal untuk akun bawaan (`adi@adi.com`, `hilmi@hilmi.com`, `admin@...`, `kadis@...`, `petugas.*`, `operator.*`).
   - Didaftarkan di `DatabaseSeeder.php` dan telah dijalankan (`php artisan db:seed --class=RolePermissionSeeder`).

2. **Filament `RoleResource` (`App\Filament\Resources\Roles`)**:
   - Menu: **Pengguna & Sistem** > **Role & Hak Akses** (Sort: 2).
   - Form: Input nama role, deskripsi, dan `CheckboxList` permission dengan fitur search, multi-kolom, deskripsi kategori, dan `bulkToggleable()`.
   - Table: Kolom nama role (badge), guard, jumlah pengguna (`users_count`), jumlah permission (`permissions_count`), dan cuplikan badges permission.
   - Keamanan: Proteksi tombol Delete untuk 3 role inti sistem (`administrator`, `operator`, `pimpinan`).

3. **Penyempurnaan Filament `UserResource` (`App\Filament\Resources\Users`)**:
   - Form: Ditambahkan section **Peran & Hak Akses (Role)** dengan `Select::make('roles')` multiple, preload, searchable.
   - Table: Ditambahkan kolom badge `roles.name` (warna danger untuk administrator, primary untuk operator, success untuk pimpinan, gray untuk Warga/Tanpa Role) serta `SelectFilter` berdasarkan role.

### Tahap 4 — Pemisahan masyarakat dan staf ✅ SUDAH DITERAPKAN

1. **Registrasi portal publik tidak memberi role apa pun.** ✅ Tidak ada `assignRole` pada alur `CitizenAuth::register()`, dan `roles` tidak masuk `$fillable`.
2. **`canAccessPanel` mengharuskan salah satu dari 3 role.** ✅ Masyarakat tanpa role otomatis tertolak dari `/admin`.
3. Hanya `administrator` yang dapat membuat akun staf dan menetapkan role (permission `kelola_pengguna` + `atur_role` di `UserResource`).
4. Jika `is_active = false`, blokir login di portal maupun panel.
5. Pengaman ganda: pengujian bahwa user tanpa role tidak dapat menjangkau `/admin/*`, dan route portal yang butuh login tidak menampilkan data user lain.

Risiko utama adalah akun staf yang kehilangan role. Efeknya aman: ia tidak bisa masuk panel, bukan naik hak akses.

### Tahap 5 — Laravel Policy dan pembatasan akses data

> **Referensi:** [Laravel Authorization (Policies)](https://laravel.com/docs/12.x/authorization) · [Filament v5 Authorization](https://filamentphp.com/docs/5.x/panel-configuration#strict-authorization-mode)

#### 5.1 Konsep dasar

Laravel Policy adalah class PHP yang mengelompokkan logika otorisasi untuk satu model. Setiap method di Policy menjawab: *"apakah user ini boleh melakukan aksi X pada model Y?"*

Filament v5 **otomatis** memeriksa Policy yang terdaftar untuk model di setiap Resource. Method yang diperiksa:

| Method Policy | Kapan diperiksa Filament | Efek |
|---|---|---|
| `viewAny(User $user)` | Saat menampilkan halaman list / navigasi menu | Menu disembunyikan jika `false` |
| `view(User $user, Model $record)` | Saat membuka halaman view | Akses ditolak jika `false` |
| `create(User $user)` | Saat membuka halaman create / tombol "Baru" | Tombol/halaman disembunyikan |
| `update(User $user, Model $record)` | Saat membuka halaman edit | Akses ditolak jika `false` |
| `delete(User $user, Model $record)` | Saat menekan tombol hapus | Tombol disembunyikan / aksi ditolak |
| `restore(User $user, Model $record)` | Saat restore soft-deleted | Tombol disembunyikan |
| `forceDelete(User $user, Model $record)` | Saat force delete | Tombol disembunyikan |

> Jika Policy atau method-nya **tidak ada**, Filament **mengizinkan** akses secara default. Untuk mencegah ini, aktifkan `strictAuthorization()` di panel (lihat Tahap 6).

#### 5.2 `Gate::before` — Super-access Administrator

Di `AppServiceProvider::boot()`, daftarkan `Gate::before` agar role `administrator` lolos semua izin **kecuali** permission `approve_*`:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::before(function ($user, string $ability) {
        if ($user->hasRole('administrator') && ! str_starts_with($ability, 'approve')) {
            return true;
        }
    });
}
```

> **Penting:** `Gate::before` yang mengembalikan `true` langsung mem-bypass semua Policy. Kita sengaja mengecualikan `approve_*` agar admin tidak bisa otomatis menandatangani surat.

#### 5.3 Daftar Policy yang harus dibuat

Buat Policy dengan Artisan:

```bash
php artisan make:policy ServiceRequestPolicy --model=ServiceRequest
php artisan make:policy ComplaintPolicy --model=Complaint
php artisan make:policy ClientPolicy --model=Client
php artisan make:policy RehabilitationCasePolicy --model=RehabilitationCase
php artisan make:policy InformationPagePolicy --model=InformationPage
php artisan make:policy FaqPolicy --model=Faq
php artisan make:policy ServiceTypePolicy --model=ServiceType
php artisan make:policy DtsenPurposePolicy --model=DtsenPurpose
php artisan make:policy ClientCategoryPolicy --model=ClientCategory
php artisan make:policy ComplaintCategoryPolicy --model=ComplaintCategory
php artisan make:policy ReferralInstitutionPolicy --model=ReferralInstitution
php artisan make:policy UserPolicy --model=User
php artisan make:policy WorkUnitPolicy --model=WorkUnit
php artisan make:policy DistrictPolicy --model=District
php artisan make:policy VillagePolicy --model=Village
php artisan make:policy DtsenCertificatePolicy --model=DtsenCertificate
php artisan make:policy PbiReactivationPolicy --model=PbiReactivation
```

#### 5.4 Pola implementasi Policy

Setiap Policy method mengecek permission Spatie via `$user->hasPermissionTo()`. Contoh:

**ServiceRequestPolicy** (Layanan Sosial — pengajuan):

```php
<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    /**
     * Apakah user boleh melihat daftar pengajuan?
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest);
    }

    /**
     * Apakah user boleh melihat detail pengajuan ini?
     * Operator wilayah hanya bisa lihat data di wilayahnya.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ManageServiceRequest)) {
            return false;
        }

        return $this->isWithinUserScope($user, $serviceRequest);
    }

    /**
     * Apakah user boleh membuat pengajuan baru (atas nama warga)?
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest);
    }

    /**
     * Apakah user boleh mengedit pengajuan ini?
     * Pimpinan tidak boleh mengubah data transaksi.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if (! $user->hasPermissionTo(PermissionType::ManageServiceRequest)) {
            return false;
        }

        return $this->isWithinUserScope($user, $serviceRequest);
    }

    /**
     * Soft delete — sama dengan update.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->update($user, $serviceRequest);
    }

    /**
     * Cek apakah data berada dalam lingkup wilayah operator.
     */
    private function isWithinUserScope(User $user, ServiceRequest $serviceRequest): bool
    {
        // Administrator: semua data
        if ($user->hasRole('administrator')) {
            return true;
        }

        // Pimpinan: baca semua
        if ($user->hasRole('pimpinan')) {
            return true;
        }

        // Operator dengan wilayah: hanya data di wilayahnya
        if ($user->village_id) {
            return $serviceRequest->village_id === $user->village_id;
        }

        if ($user->district_id) {
            return $serviceRequest->village?->district_id === $user->district_id;
        }

        // Operator tanpa wilayah (staf Dinsos): sesuai officer_id / work_unit_id
        return $serviceRequest->officer_id === $user->id
            || $serviceRequest->work_unit_id === $user->work_unit_id;
    }
}
```

**RehabilitationCasePolicy** (Data sensitif klien):

```php
<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\RehabilitationCase;
use App\Models\User;

class RehabilitationCasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    /**
     * Operator hanya bisa lihat kasus yang ditugaskan kepadanya.
     */
    public function view(User $user, RehabilitationCase $case): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)) {
            return false;
        }

        if ($user->hasRole('administrator')) {
            return true;
        }

        // Operator: hanya kasus miliknya
        return $case->officer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, RehabilitationCase $case): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if (! $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)) {
            return false;
        }

        if ($user->hasRole('administrator')) {
            return true;
        }

        return $case->officer_id === $user->id;
    }

    public function delete(User $user, RehabilitationCase $case): bool
    {
        return $this->update($user, $case);
    }
}
```

**Pola untuk Data Master** (ServiceType, DtsenPurpose, ClientCategory, dsb.):

```php
// Pola sederhana — hanya cek permission, tanpa pembatasan baris
public function viewAny(User $user): bool
{
    return $user->hasPermissionTo(PermissionType::ManageServiceType);
}

public function create(User $user): bool
{
    return $user->hasPermissionTo(PermissionType::ManageServiceType);
}

public function update(User $user, ServiceType $serviceType): bool
{
    return $user->hasPermissionTo(PermissionType::ManageServiceType);
}

public function delete(User $user, ServiceType $serviceType): bool
{
    return $user->hasPermissionTo(PermissionType::ManageServiceType);
}
```

**Pola untuk Approval** (aksi khusus):

```php
// DtsenCertificatePolicy — method custom untuk approve
public function approve(User $user, DtsenCertificate $certificate): bool
{
    // Wajib punya permission approve
    if (! $user->hasPermissionTo(PermissionType::ApproveDtsenLetter)) {
        return false;
    }

    // Wajib tercatat sebagai approver pada langkah aktif
    return $certificate->approvals()
        ->where('approver_id', $user->id)
        ->where('decision', 'pending')
        ->exists();
}
```

#### 5.5 Matriks Policy Method per Model

| Model | `viewAny` | `view` | `create` | `update` | `delete` | Custom |
|---|---|---|---|---|---|---|
| `ServiceRequest` | permission `kelola_pengajuan_layanan` | + cek wilayah | permission | + bukan pimpinan + cek wilayah | = update | — |
| `Complaint` | permission `kelola_aduan` | + cek wilayah | permission | + bukan pimpinan + cek wilayah | = update | — |
| `Client` | permission `kelola_klien_rehabilitasi` | + cek officer_id | permission | + bukan pimpinan + cek officer_id | = update | — |
| `RehabilitationCase` | permission `kelola_kasus_rehabilitasi` | + cek officer_id | permission + bukan pimpinan | + bukan pimpinan + cek officer_id | = update | — |
| `DtsenCertificate` | via ServiceRequest | via ServiceRequest | via ServiceRequest | via ServiceRequest | via ServiceRequest | `approve` |
| `PbiReactivation` | via ServiceRequest | via ServiceRequest | via ServiceRequest | via ServiceRequest | via ServiceRequest | `approve` |
| `InformationPage` | permission `kelola_informasi` | permission | permission | permission | permission | — |
| `Faq` | permission `kelola_informasi` | permission | permission | permission | permission | — |
| `ServiceType` | permission `kelola_jenis_layanan` | permission | permission | permission | permission | — |
| `DtsenPurpose` | permission `kelola_tujuan_dtsen` | permission | permission | permission | permission | — |
| `ClientCategory` | permission `kelola_kategori_klien` | permission | permission | permission | permission | — |
| `ComplaintCategory` | permission `kelola_kategori_aduan` | permission | permission | permission | permission | — |
| `ReferralInstitution` | permission `kelola_lembaga_rujukan` | permission | permission | permission | permission | — |
| `User` | permission `kelola_pengguna` | permission | permission | permission | permission | — |
| `WorkUnit` | permission `kelola_unit_kerja` | permission | permission | permission | permission | — |
| `District` | permission `kelola_wilayah` | permission | permission | permission | permission | — |
| `Village` | permission `kelola_wilayah` | permission | permission | permission | permission | — |

Keterangan:
- **"permission"** = hanya cek `$user->hasPermissionTo(...)`, tanpa pembatasan baris
- **"+ cek wilayah"** = setelah cek permission, batasi berdasarkan `district_id` / `village_id` user
- **"+ cek officer_id"** = setelah cek permission, batasi ke kasus yang ditugaskan pada operator tersebut
- **"via ServiceRequest"** = karena DtsenCertificate/PbiReactivation merupakan detail 1:1 dari ServiceRequest, otorisasi mengikuti parent-nya

#### 5.6 Query Scope — pembatasan data di level query

Selain Policy per-record, **wajib** juga membatasi data di level query agar daftar, ekspor, dan widget dashboard konsisten. Terapkan di `getEloquentQuery()` pada setiap Resource:

```php
// ServiceRequestResource.php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    // Administrator: semua
    if ($user->hasRole('administrator')) {
        return $query;
    }

    // Pimpinan: semua (read-only, diatur Policy)
    if ($user->hasRole('pimpinan')) {
        return $query;
    }

    // Operator wilayah desa
    if ($user->village_id) {
        return $query->where('village_id', $user->village_id);
    }

    // Operator wilayah kecamatan
    if ($user->district_id) {
        return $query->whereHas('village', fn ($q) => $q->where('district_id', $user->district_id));
    }

    // Operator staf Dinsos (tanpa wilayah): data yang ditangani atau unit kerjanya
    return $query->where(fn ($q) => $q
        ->where('officer_id', $user->id)
        ->orWhere('work_unit_id', $user->work_unit_id)
    );
}
```

```php
// RehabilitationCaseResource.php
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    if ($user->hasRole('administrator')) {
        return $query;
    }

    // Operator: hanya kasus yang ditugaskan
    return $query->where('officer_id', $user->id);
}
```

#### 5.7 Otorisasi di portal Livewire (frontend)

Portal publik **tidak** memakai Filament, jadi Policy tidak otomatis diperiksa. Wajib memanggil otorisasi sendiri:

```php
// Contoh: di Livewire component CitizenAccount
use Illuminate\Support\Facades\Gate;

public function viewServiceRequest(int $id): void
{
    $serviceRequest = ServiceRequest::findOrFail($id);

    // Cek apakah ini milik user yang login
    if ($serviceRequest->submitter_id !== auth()->id()
        && $serviceRequest->applicant_nik !== auth()->user()->nik) {
        abort(403);
    }

    // ... tampilkan detail
}
```

Prinsip di portal:
- Masyarakat hanya bisa lihat **data miliknya sendiri** (`submitter_id` / `reporter_id` / `applicant_nik`)
- Tidak perlu cek Spatie permission (masyarakat tidak punya role)
- Tetap pakai `abort(403)` atau `$this->authorize()` untuk keamanan

Dokumen pribadi tetap lewat temporary signed URL yang dibuat setelah cek kepemilikan lolos.

### Tahap 6 — Integrasi Filament dengan Policy

#### 6.1 Aktifkan Strict Authorization

Tambahkan `strictAuthorization()` di `AdminPanelProvider` agar Filament **menolak** akses jika Policy belum dibuat (mencegah kebocoran akses saat development):

```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->strictAuthorization();
}
```

> **Catatan:** Dengan `strictAuthorization()`, setiap Resource **wajib** memiliki Policy yang lengkap. Jika method Policy belum ada, Filament akan throw exception alih-alih mengizinkan akses.

#### 6.2 Navigasi dan visibilitas

1. **Menu otomatis tersembunyi** — Filament memeriksa `viewAny` dari Policy. Jika `false`, menu tidak tampil. Tidak perlu konfigurasi tambahan.
2. **Pimpinan** hanya melihat: Dashboard, Laporan, dan antrean persetujuan (karena `viewAny` return `false` di Resource operasional).
3. **Operator** melihat: Layanan Sosial (pengajuan, aduan, klien, kasus) + Dashboard/Laporan. Data master tersembunyi.

#### 6.3 Sembunyikan Action sensitif

Selain Policy, sembunyikan tombol/aksi di tabel dan form:

```php
// Contoh: tombol hapus hanya untuk administrator
Tables\Actions\DeleteAction::make()
    ->visible(fn () => auth()->user()->hasRole('administrator')),

// Contoh: tombol approve hanya jika punya permission
Tables\Actions\Action::make('approve')
    ->label('Setujui')
    ->visible(fn (DtsenCertificate $record) => auth()->user()->can('approve', $record)),
```

> **Prinsip:** `->visible()` hanya menyembunyikan UI. Validasi sebenarnya tetap dilakukan di Policy (server-side). Filament v5 menjalankan ulang otorisasi di setiap Livewire request, bukan hanya saat mount.

#### 6.4 Filter wilayah pada dashboard

Widget dashboard dikunci untuk operator yang memiliki `district_id`/`village_id`:

```php
// Di widget dashboard, filter query berdasarkan wilayah operator
protected function getTableQuery(): Builder
{
    $query = ServiceRequest::query();
    $user = auth()->user();

    if ($user->village_id) {
        $query->where('village_id', $user->village_id);
    } elseif ($user->district_id) {
        $query->whereHas('village', fn ($q) => $q->where('district_id', $user->district_id));
    }

    return $query;
}
```

#### 6.5 Halaman kelola role/permission

Hanya untuk administrator — buat halaman Filament Pages kustom atau gunakan `UserResource` dengan tab role/permission. Permission `atur_role` mengatur akses ke fitur ini.


### Tahap 7 — Audit dan pengujian (Pest, PostgreSQL)

Skenario minimum:

- Pengunjung tanpa role tidak bisa membuka `/admin` dan tidak bisa melihat tiket milik orang lain.
- Registrasi publik menghasilkan user tanpa role.
- Operator desa A tidak bisa melihat data desa B, termasuk lewat ekspor.
- Operator A tidak bisa membuka kasus rehabilitasi milik operator B.
- Pimpinan tidak bisa mengubah data transaksi, tetapi bisa menyetujui dokumen pada langkahnya.
- Administrator tidak bisa menyetujui SK DTSEN tanpa permission `approve_*`.
- User `is_active = false` tidak bisa login di portal maupun panel.
- Perubahan role/permission tercatat di `activity_log` (spatie/laravel-activitylog).

### Tahap 8 — Deploy dan operasional

1. Jalankan `php artisan permission:cache-reset` setelah setiap deploy yang mengubah permission.
2. Perubahan permission hanya lewat seeder atau UI admin agar tercatat.
3. Dokumentasikan matriks final di README.

## 6. Hal yang Perlu Dikonfirmasi

1. **Kabid:** termasuk role `pimpinan`, atau diberi permission `approve_*` langsung di atas role `operator`? Ini menentukan siapa yang bisa memaraf.
2. **Operator Kecamatan/Desa:** tetap dipakai dengan pembatasan wilayah (usulan), atau dihapus dari lingkup?
3. **Pimpinan dan rehabilitasi:** apakah ringkasan tanpa identitas klien sudah cukup?
