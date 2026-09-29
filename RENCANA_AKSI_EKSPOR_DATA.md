# Rencana Aksi: Fitur Ekspor Data ke Excel & PDF

**Sistem:** SAPA SOSIAL — Dinas Sosial Kabupaten Blitar

| | |
|--|--|
| **Stack** | Laravel 13 · PHP 8.4 · Filament v5 · Livewire v4 · PostgreSQL |
| **Paket Ekspor Excel** | `phpoffice/phpspreadsheet` — https://github.com/phpoffice/phpspreadsheet |
| **Paket Ekspor PDF** | `barryvdh/laravel-dompdf` — https://github.com/barryvdh/laravel-dompdf |
| **Referensi PRD** | Bagian 3 — Laporan & Dashboard (rekap Excel & PDF per modul) |
| **Permission terkait** | `ExportReport` (`ekspor_laporan`) — sudah ada di `PermissionType.php` |

---

## 1. Latar Belakang

PRD Bagian 3 mensyaratkan 5 jenis laporan berkala yang **dapat diekspor ke Excel dan PDF**:

| # | Laporan | Sumber Data |
|---|---------|-------------|
| 1 | Rekap SK DTSEN | `service_requests` + `dtsen_certificates` + `dtsen_purposes` |
| 2 | Rekap Reaktivasi PBI-JK | `service_requests` + `pbi_reactivations` |
| 3 | Laporan Rehabilitasi Sosial | `rehabilitation_cases` + `referrals` + `clients` |
| 4 | Laporan Pelayanan (semua jenis) | `service_requests` + `service_types` |
| 5 | Laporan Pengaduan | `complaints` + `complaint_categories` |

Selain laporan berkala, dibutuhkan pula:
- **Ekspor tabel** (bulk export dari daftar data di panel Filament)
- **Cetak PDF dokumen** (SK DTSEN, surat rekomendasi PBI-JK — sudah ada `DocumentTemplateService`, perlu migrasi ke DomPDF)

Saat ini belum ada paket ekspor yang terinstal. `DocumentTemplateService` menghasilkan PDF raw stream — akan dimigrasi ke DomPDF agar mendukung template Blade.

---

## 2. Keputusan Desain

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Paket Excel | `phpoffice/phpspreadsheet` langsung | Kontrol penuh terhadap format, styling, multi-sheet. Tanpa wrapper agar tidak menambah dependensi |
| Paket PDF | `barryvdh/laravel-dompdf` | Dukungan template Blade, integrasi Laravel facade, sesuai rekomendasi PRD |
| Lokasi logic ekspor | `App\Services\Export\*` | Terpisah dari controller/resource, reusable dari Filament Action maupun route API |
| Template PDF | Blade views di `resources/views/exports/pdf/*` | Konsisten dengan ekosistem Laravel, mudah diubah tanpa mengubah code |
| Template Excel | Dibangun programatik via PhpSpreadsheet API | Lebih fleksibel untuk header dinamis, multi-sheet, conditional formatting |
| Eksekusi | Sinkron langsung (`->download()`) untuk semua ukuran data, tanpa queue job | Diputuskan agar tombol ekspor langsung menghasilkan file tanpa perlu queue worker berjalan di server; menghindari kompleksitas notifikasi ekspor selesai |
| Hak akses | Dicek via `PermissionType::ExportReport` (`ekspor_laporan`) | Sudah didefinisikan di enum, belum diimplementasikan |
| Pembatasan wilayah | Query scope yang sama dengan `getEloquentQuery()` di Resource | Operator hanya ekspor data wilayahnya |
| Bahasa file | Header kolom dan judul dalam **Bahasa Indonesia** | Sesuai konvensi PRD: label UI dalam Bahasa Indonesia |

---

## 3. Arsitektur

```
app/
├── Services/
│   └── Export/
│       ├── BaseExcelExporter.php          # Abstract: setup workbook, styling, response
│       ├── BasePdfExporter.php            # Abstract: load Blade view, return PDF response
│       ├── ServiceRequestExcelExport.php  # Ekspor tabel pengajuan layanan
│       ├── ServiceRequestPdfExport.php    # Ekspor PDF pengajuan layanan
│       ├── DtsenReportExcelExport.php     # Rekap SK DTSEN
│       ├── DtsenReportPdfExport.php       # Rekap SK DTSEN (PDF)
│       ├── PbiReportExcelExport.php       # Rekap Reaktivasi PBI-JK
│       ├── PbiReportPdfExport.php         # Rekap Reaktivasi PBI-JK (PDF)
│       ├── RehabReportExcelExport.php     # Laporan Rehabilitasi Sosial
│       ├── RehabReportPdfExport.php       # Laporan Rehabilitasi Sosial (PDF)
│       ├── ComplaintExcelExport.php       # Ekspor tabel & laporan pengaduan
│       └── ComplaintPdfExport.php         # Ekspor PDF pengaduan
│
├── Filament/
│   ├── Resources/
│   │   ├── ServiceRequests/Tables/        # + headerActions: ExportExcel, ExportPdf
│   │   ├── Complaints/Tables/             # + headerActions: ExportExcel, ExportPdf
│   │   └── RehabilitationCases/Tables/    # + headerActions: ExportExcel, ExportPdf
│   └── Pages/
│       └── Reports/
│           ├── DtsenReport.php            # Halaman rekap SK DTSEN + filter + ekspor
│           ├── PbiReport.php              # Halaman rekap Reaktivasi PBI-JK + filter + ekspor
│           ├── RehabReport.php            # Halaman laporan Rehabilitasi Sosial + filter + ekspor
│           ├── ServiceReport.php          # Halaman laporan Pelayanan Umum + filter + ekspor
│           └── ComplaintReport.php        # Halaman laporan Pengaduan + filter + ekspor
│
resources/views/exports/pdf/
├── _layout.blade.php                      # Layout dasar PDF (kop surat Dinsos, footer)
├── service-requests.blade.php             # Template tabel pengajuan
├── dtsen-report.blade.php                 # Template rekap SK DTSEN
├── pbi-report.blade.php                   # Template rekap PBI-JK
├── rehab-report.blade.php                 # Template laporan rehabilitasi
├── complaint-report.blade.php             # Template laporan pengaduan
└── service-report.blade.php               # Template laporan pelayanan umum
```

---

## 4. Tahapan Implementasi

### Tahap 1 — Instalasi & Fondasi

**Tujuan:** Instal paket, buat base class, pastikan konfigurasi berjalan.

#### 1.1 Instal paket

```bash
composer require phpoffice/phpspreadsheet barryvdh/laravel-dompdf --no-interaction
```

#### 1.2 Publish konfigurasi DomPDF

```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --no-interaction
```

Konfigurasi yang perlu disesuaikan di `config/dompdf.php`:
- `'default_paper_size' => 'a4'`
- `'default_font' => 'sans-serif'`
- `'isRemoteEnabled' => false` (keamanan: tidak memuat resource eksternal)
- `'tempDir' => storage_path('app/dompdf-temp')`

#### 1.3 Buat `BaseExcelExporter`

```php
// app/Services/Export/BaseExcelExporter.php
namespace App\Services\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class BaseExcelExporter
{
    protected Spreadsheet $spreadsheet;
    protected string $title;
    protected array $filters;

    abstract protected function getHeaders(): array;
    abstract protected function getRows(): iterable;
    abstract protected function getFilename(): string;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->spreadsheet = new Spreadsheet();
    }

    public function download(): StreamedResponse
    {
        $this->build();
        $writer = new Xlsx($this->spreadsheet);

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $this->getFilename() . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    protected function build(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($this->title, 0, 31));

        // Header kop
        $this->writeKopSurat($sheet);

        // Header kolom
        $headers = $this->getHeaders();
        $headerRow = 5; // setelah kop surat
        foreach ($headers as $col => $header) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, $headerRow);
            $cell->setValue($header);
        }
        $this->styleHeaderRow($sheet, $headerRow, count($headers));

        // Data rows
        $row = $headerRow + 1;
        foreach ($this->getRows() as $data) {
            foreach ($data as $col => $value) {
                $sheet->getCellByColumnAndRow($col + 1, $row)->setValue($value);
            }
            $row++;
        }

        // Auto-size kolom
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
    }

    // ... helper methods: writeKopSurat(), styleHeaderRow()
}
```

#### 1.4 Buat `BasePdfExporter`

```php
// app/Services/Export/BasePdfExporter.php
namespace App\Services\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

abstract class BasePdfExporter
{
    protected array $filters;
    protected string $orientation = 'landscape'; // default landscape untuk tabel

    abstract protected function getViewName(): string;
    abstract protected function getViewData(): array;
    abstract protected function getFilename(): string;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(): Response
    {
        $pdf = Pdf::loadView($this->getViewName(), array_merge(
            $this->getViewData(),
            ['filters' => $this->filters]
        ));

        $pdf->setPaper('a4', $this->orientation);

        return $pdf->download($this->getFilename() . '.pdf');
    }

    public function stream(): Response
    {
        $pdf = Pdf::loadView($this->getViewName(), array_merge(
            $this->getViewData(),
            ['filters' => $this->filters]
        ));

        $pdf->setPaper('a4', $this->orientation);

        return $pdf->stream($this->getFilename() . '.pdf');
    }
}
```

#### 1.5 Buat layout PDF dasar

File `resources/views/exports/pdf/_layout.blade.php` berisi:
- Kop surat Pemerintah Kabupaten Blitar / Dinas Sosial
- Styling CSS inline (DomPDF tidak mendukung external CSS penuh)
- Footer: tanggal cetak, halaman
- Slot `@yield('content')` untuk isi laporan

---

### Tahap 2 — Ekspor Tabel dari Filament Resource

**Tujuan:** Tambahkan tombol "Ekspor Excel" dan "Ekspor PDF" di header tabel setiap resource utama.

#### 2.1 Pengajuan Layanan (`ServiceRequestsTable`)

Tambahkan `headerActions` pada method `configure()`:

```php
use Filament\Tables\Actions\Action;

->headerActions([
    Action::make('exportExcel')
        ->label('Ekspor Excel')
        ->icon(Heroicon::OutlinedArrowDownTray)
        ->color('success')
        ->visible(fn () => auth()->user()?->can('ekspor_laporan'))
        ->action(fn () => (new ServiceRequestExcelExport(
            $this->getTableFilters() // pass current filters
        ))->download()),

    Action::make('exportPdf')
        ->label('Ekspor PDF')
        ->icon(Heroicon::OutlinedDocumentArrowDown)
        ->color('danger')
        ->visible(fn () => auth()->user()?->can('ekspor_laporan'))
        ->action(fn () => (new ServiceRequestPdfExport(
            $this->getTableFilters()
        ))->download()),
])
```

**Data yang diekspor (kolom):**
| Kolom | Sumber |
|-------|--------|
| No. Tiket | `request_number` |
| Nama Pemohon | `applicant_name` |
| NIK | `applicant_nik` |
| Jenis Layanan | `serviceType.name` |
| Desa / Kelurahan | `village.name` |
| Kecamatan | `village.district.name` |
| Status | `status` (label Indonesia) |
| Tanggal Pengajuan | `submitted_at` |
| Petugas | `officer.name` |
| Hasil Layanan | `service_result` |
| Tanggal Selesai | `completed_at` |

#### 2.2 Pengaduan Sosial (`ComplaintsTable`)

Pola yang sama, dengan kolom:
| Kolom | Sumber |
|-------|--------|
| No. Laporan | `complaint_number` |
| Nama Pelapor | `reporter_name` |
| Kategori | `complaintCategory.name` |
| Lokasi | `location_detail` |
| Desa | `village.name` |
| Kecamatan | `village.district.name` |
| Status | `status` (label Indonesia) |
| Tanggal Laporan | `reported_at` |
| Petugas | `officer.name` |
| Tindakan | `action_taken` |
| Tanggal Selesai | `resolved_at` |

#### 2.3 Kasus Rehabilitasi (`RehabilitationCasesTable`)

| Kolom | Sumber |
|-------|--------|
| No. Kasus | `case_number` |
| Nama Klien | `client.name` |
| Kategori Klien | `client.clientCategory.name` |
| Desa | `client.village.name` |
| Kecamatan | `client.village.district.name` |
| Jenis Penanganan | `handling_type` (label Indonesia) |
| Status | `status` (label Indonesia) |
| Petugas | `officer.name` |
| Tanggal Diterima | `received_at` |
| Hasil Penanganan | `handling_result` |
| Tanggal Ditutup | `closed_at` |

#### 2.4 Catatan implementasi headerActions

Karena kelas `ServiceRequestsTable` dll. menggunakan pola static `configure(Table $table)`, tombol ekspor perlu menerima filter aktif tabel. Pendekatan:

1. **Filament v5 Table Header Actions** mendukung akses ke `$livewire` lewat closure.
2. Filter aktif diambil dari `$livewire->getTableFilterState()`.
3. Pembatasan wilayah operator diterapkan di dalam exporter (reuse scope `getEloquentQuery()`).

---

### Tahap 3 — Halaman Laporan Berkala

**Tujuan:** Buat 5 halaman laporan di panel Filament dengan filter, tampilan tabel/ringkasan, dan tombol ekspor.

#### 3.1 Navigasi

Tambahkan navigation group **"Laporan"** di panel Filament, berisi:
- Rekap SK DTSEN
- Rekap Reaktivasi PBI-JK
- Laporan Rehabilitasi Sosial
- Laporan Pelayanan
- Laporan Pengaduan

Visibilitas: hanya user dengan permission `ViewReport` (`lihat_laporan`).

#### 3.2 Halaman Rekap SK DTSEN (`DtsenReport`)

**File:** `app/Filament/Pages/Reports/DtsenReport.php`

**Filter:**
- Periode (tanggal mulai & akhir)
- Tujuan penggunaan (dari `dtsen_purposes`)
- Desil
- Kecamatan & Desa

**Data ditampilkan:**
- Tabel ringkasan: jumlah surat per tujuan penggunaan, per desil
- Tabel detail: daftar surat terbit & ditolak
- Statistik: total terbit, total ditolak, rata-rata waktu proses

**Ekspor:**
- Tombol "Ekspor Excel" → `DtsenReportExcelExport` (multi-sheet: Ringkasan + Detail)
- Tombol "Ekspor PDF" → `DtsenReportPdfExport` (satu file, ringkasan + tabel)

**Exporter Excel (multi-sheet):**
| Sheet | Isi |
|-------|-----|
| Ringkasan | Jumlah per tujuan × desil, total terbit, total ditolak |
| Detail | Daftar per surat: no. surat, nama, NIK, tujuan, desil, tanggal terbit, penandatangan |
| Per Kecamatan | Breakdown per kecamatan/desa |

#### 3.3 Halaman Rekap Reaktivasi PBI-JK (`PbiReport`)

**File:** `app/Filament/Pages/Reports/PbiReport.php`

**Filter:**
- Periode
- Alasan reaktivasi
- Status akhir
- Kecamatan & Desa

**Data ditampilkan:**
- Jumlah per alasan, per status keputusan Kemensos
- Daftar detail pengajuan
- Lama proses rata-rata
- Pengajuan tertahan (melebihi SLA)

**Exporter Excel (multi-sheet):**
| Sheet | Isi |
|-------|-----|
| Ringkasan | Jumlah per alasan × status, rata-rata lama proses |
| Detail | No. tiket, nama peserta, NIK, no. BPJS, alasan, desil, status, tanggal-tanggal milestone |
| Per Kecamatan | Breakdown per kecamatan/desa |

#### 3.4 Halaman Laporan Rehabilitasi Sosial (`RehabReport`)

**File:** `app/Filament/Pages/Reports/RehabReport.php`

**Filter:**
- Periode
- Kategori klien
- Status kasus
- Lembaga tujuan rujukan
- Kecamatan & Desa

**Data ditampilkan:**
- Jumlah kasus per kategori, per status
- Jumlah rujukan per lembaga tujuan
- Daftar detail kasus aktif dan selesai

**Exporter Excel (multi-sheet):**
| Sheet | Isi |
|-------|-----|
| Ringkasan | Jumlah kasus per kategori × status, jumlah rujukan per lembaga |
| Detail Kasus | No. kasus, nama klien, kategori, jenis penanganan, status, petugas, tanggal |
| Detail Rujukan | No. rujukan, lembaga tujuan, status rujukan, tanggal, hasil |

#### 3.5 Halaman Laporan Pelayanan Umum (`ServiceReport`)

**File:** `app/Filament/Pages/Reports/ServiceReport.php`

**Filter:**
- Periode
- Jenis layanan
- Status
- Kecamatan & Desa

**Data ditampilkan:**
- Jumlah pengajuan per jenis layanan, per status
- Distribusi per wilayah
- Daftar detail pengajuan

**Exporter Excel:**
| Sheet | Isi |
|-------|-----|
| Ringkasan | Jumlah per jenis layanan × status |
| Detail | Seluruh pengajuan dengan kolom lengkap |
| Per Kecamatan | Breakdown per kecamatan/desa |

#### 3.6 Halaman Laporan Pengaduan (`ComplaintReport`)

**File:** `app/Filament/Pages/Reports/ComplaintReport.php`

**Filter:**
- Periode
- Kategori pengaduan
- Status
- Kecamatan & Desa

**Data ditampilkan:**
- Jumlah per kategori, per status
- Distribusi per wilayah
- Daftar detail pengaduan

**Exporter Excel:**
| Sheet | Isi |
|-------|-----|
| Ringkasan | Jumlah per kategori × status |
| Detail | Seluruh pengaduan dengan kolom lengkap |
| Per Kecamatan | Breakdown per kecamatan/desa |

---

### Tahap 4 — Template PDF Laporan

**Tujuan:** Buat template Blade untuk setiap jenis laporan PDF.

#### 4.1 Layout dasar (`_layout.blade.php`)

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop h2 { margin: 0; font-size: 14px; }
        .kop h3 { margin: 0; font-size: 12px; }
        .kop p { margin: 2px 0; font-size: 9px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #333; padding: 4px 6px; }
        table.data th { background-color: #e2e8f0; font-weight: bold; text-align: center; }
        .footer { margin-top: 20px; font-size: 9px; color: #666; text-align: right; }
        .meta { margin-bottom: 10px; }
        .meta td { padding: 2px 8px; font-size: 10px; }
        .summary-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 10px; margin: 10px 0; }
        @page { margin: 15mm 12mm; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>PEMERINTAH KABUPATEN BLITAR</h2>
        <h3>DINAS SOSIAL</h3>
        <p>Jl. Raya Kanigoro No. 10, Blitar, Jawa Timur | Telp. (0342) 801122</p>
    </div>

    @yield('content')

    <div class="footer">
        Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
        — SAPA SOSIAL
    </div>
</body>
</html>
```

#### 4.2 Konvensi template per laporan

Setiap template Blade menggunakan `@extends('exports.pdf._layout')` dan mengisi `@section('content')` dengan:
- Judul laporan
- Metadata filter (periode, wilayah yang dipilih)
- Tabel ringkasan (jika ada)
- Tabel detail data
- Total/subtotal di baris terakhir

---

### Tahap 5 — (Dibatalkan) Queue untuk Ekspor Besar

**Keputusan revisi:** Tahap ini **tidak jadi diimplementasikan**. Semua ekspor — berapa pun jumlah barisnya — berjalan sinkron: tombol ekspor ditekan, request diproses langsung di request yang sama, dan file (Excel/PDF) langsung diunduh browser via `->download()`.

Alasan pembatalan:
- Kompleksitas queue job + notifikasi "Ekspor Selesai" tidak sepadan dengan manfaatnya di skala data Dinas Sosial Kab. Blitar saat ini.
- Queue job (`App\Jobs\ProcessLargeExport`) mensyaratkan queue worker (`php artisan queue:work`) berjalan terus-menerus di server; jika worker tidak berjalan, job menumpuk di tabel `jobs` tanpa notifikasi apa pun ke user — sumber kebingungan yang ditemukan saat pengujian manual.
- Setiap `Action::make('exportExcel'|'exportPdf')` di 3 tabel Filament Resource (`ServiceRequestsTable`, `ComplaintsTable`, `RehabilitationCasesTable`) dan 5 halaman Laporan kini langsung memanggil `(new XExporter($filters, $query))->download()` tanpa pengecekan jumlah baris.
- Kelas `App\Jobs\ProcessLargeExport`, route `exports.download`, serta `->databaseNotifications()` di `AdminPanelProvider` sudah dihapus karena tidak lagi dipakai.

---

### Tahap 6 — Migrasi `DocumentTemplateService` ke DomPDF

**Tujuan:** Refaktor generate PDF SK DTSEN dan surat rekomendasi PBI-JK agar menggunakan DomPDF + template Blade.

#### 6.1 Buat template Blade untuk SK DTSEN

File: `resources/views/documents/dtsen-certificate.blade.php`

Berisi:
- Kop surat resmi
- Nomor surat, tanggal
- Isi surat keterangan (nama, NIK, desil, tujuan)
- Tanda tangan pejabat
- QR code verifikasi (render gambar QR dari kode verifikasi)

#### 6.2 Buat template Blade untuk surat rekomendasi PBI-JK

File: `resources/views/documents/pbi-recommendation.blade.php`

#### 6.3 Refaktor `DocumentTemplateService`

- Method `createFormPdf()` → gunakan `Pdf::loadView()`
- Hapus raw PDF stream generation
- Pertahankan signature method yang sama agar tidak break existing code

---

### Tahap 7 — Testing

#### 7.1 Feature test ekspor Excel

```php
// tests/Feature/Export/ServiceRequestExcelExportTest.php
public function test_service_request_excel_export_returns_xlsx(): void
{
    $user = User::factory()->create();
    $user->assignRole('administrator');

    $this->actingAs($user)
        ->get(route('export.service-requests.excel'))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
}

public function test_operator_only_exports_own_district_data(): void
{
    // ...
}

public function test_user_without_export_permission_is_forbidden(): void
{
    // ...
}
```

#### 7.2 Feature test ekspor PDF

```php
public function test_complaint_pdf_export_returns_pdf(): void
{
    $user = User::factory()->create();
    $user->assignRole('administrator');

    $this->actingAs($user)
        ->get(route('export.complaints.pdf'))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
}
```

#### 7.3 Unit test exporter class

```php
public function test_dtsen_report_excel_has_correct_sheets(): void
{
    $exporter = new DtsenReportExcelExport(['startDate' => '2026-01-01', 'endDate' => '2026-12-31']);
    // Verify sheet names, headers, data rows
}
```

---

### Tahap 8 — Pint & Finalisasi

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact
```

---

## 5. Daftar File yang Dibuat / Diubah

### File baru

| File | Keterangan |
|------|------------|
| `app/Services/Export/BaseExcelExporter.php` | Abstract class ekspor Excel |
| `app/Services/Export/BasePdfExporter.php` | Abstract class ekspor PDF |
| `app/Services/Export/ServiceRequestExcelExport.php` | Ekspor tabel pengajuan ke Excel |
| `app/Services/Export/ServiceRequestPdfExport.php` | Ekspor tabel pengajuan ke PDF |
| `app/Services/Export/DtsenReportExcelExport.php` | Rekap SK DTSEN ke Excel (multi-sheet) |
| `app/Services/Export/DtsenReportPdfExport.php` | Rekap SK DTSEN ke PDF |
| `app/Services/Export/PbiReportExcelExport.php` | Rekap PBI-JK ke Excel (multi-sheet) |
| `app/Services/Export/PbiReportPdfExport.php` | Rekap PBI-JK ke PDF |
| `app/Services/Export/RehabReportExcelExport.php` | Laporan rehabilitasi ke Excel (multi-sheet) |
| `app/Services/Export/RehabReportPdfExport.php` | Laporan rehabilitasi ke PDF |
| `app/Services/Export/ComplaintExcelExport.php` | Ekspor pengaduan ke Excel |
| `app/Services/Export/ComplaintPdfExport.php` | Ekspor pengaduan ke PDF |
| `app/Services/Export/ServiceReportExcelExport.php` | Laporan pelayanan umum ke Excel |
| `app/Services/Export/ServiceReportPdfExport.php` | Laporan pelayanan umum ke PDF |
| `app/Filament/Pages/Reports/DtsenReport.php` | Halaman rekap SK DTSEN |
| `app/Filament/Pages/Reports/PbiReport.php` | Halaman rekap PBI-JK |
| `app/Filament/Pages/Reports/RehabReport.php` | Halaman laporan rehabilitasi |
| `app/Filament/Pages/Reports/ServiceReport.php` | Halaman laporan pelayanan |
| `app/Filament/Pages/Reports/ComplaintReport.php` | Halaman laporan pengaduan |
| `resources/views/exports/pdf/_layout.blade.php` | Layout dasar PDF |
| `resources/views/exports/pdf/service-requests.blade.php` | Template PDF pengajuan |
| `resources/views/exports/pdf/dtsen-report.blade.php` | Template PDF rekap DTSEN |
| `resources/views/exports/pdf/pbi-report.blade.php` | Template PDF rekap PBI-JK |
| `resources/views/exports/pdf/rehab-report.blade.php` | Template PDF rehabilitasi |
| `resources/views/exports/pdf/complaint-report.blade.php` | Template PDF pengaduan |
| `resources/views/exports/pdf/service-report.blade.php` | Template PDF pelayanan umum |
| `resources/views/documents/dtsen-certificate.blade.php` | Template surat SK DTSEN (DomPDF) |
| `resources/views/documents/pbi-recommendation.blade.php` | Template surat rekomendasi PBI |
| `tests/Feature/Export/ServiceRequestExcelExportTest.php` | Test ekspor Excel pengajuan |
| `tests/Feature/Export/ComplaintPdfExportTest.php` | Test ekspor PDF pengaduan |
| `tests/Feature/Export/DtsenReportExportTest.php` | Test rekap SK DTSEN |

### File diubah

| File | Perubahan |
|------|-----------|
| `composer.json` | Tambah `phpoffice/phpspreadsheet` & `barryvdh/laravel-dompdf` |
| `app/Filament/Resources/ServiceRequests/Tables/ServiceRequestsTable.php` | Tambah headerActions ekspor |
| `app/Filament/Resources/Complaints/Tables/ComplaintsTable.php` | Tambah headerActions ekspor |
| `app/Filament/Resources/RehabilitationCases/Tables/RehabilitationCasesTable.php` | Tambah headerActions ekspor |
| `app/Filament/Providers/Filament/AdminPanelProvider.php` (atau sejenisnya) | Daftarkan halaman Reports |
| `app/Services/DocumentTemplateService.php` | Refaktor ke DomPDF |

---

## 6. Urutan Pengerjaan (Prioritas)

| Urutan | Tahap | Estimasi |
|--------|-------|----------|
| 1 | Tahap 1 — Instalasi & fondasi (base class) | ⬛⬛ |
| 2 | Tahap 2.1 — Ekspor tabel Pengajuan Layanan (Excel + PDF) | ⬛⬛ |
| 3 | Tahap 2.2 — Ekspor tabel Pengaduan (Excel + PDF) | ⬛ |
| 4 | Tahap 2.3 — Ekspor tabel Rehabilitasi (Excel + PDF) | ⬛ |
| 5 | Tahap 3.1–3.2 — Halaman & ekspor Rekap SK DTSEN | ⬛⬛⬛ |
| 6 | Tahap 3.3 — Halaman & ekspor Rekap PBI-JK | ⬛⬛ |
| 7 | Tahap 3.4 — Halaman & ekspor Rehabilitasi | ⬛⬛ |
| 8 | Tahap 3.5 — Halaman & ekspor Pelayanan Umum | ⬛⬛ |
| 9 | Tahap 3.6 — Halaman & ekspor Pengaduan | ⬛⬛ |
| 10 | Tahap 4 — Template PDF laporan | ⬛⬛ |
| 11 | Tahap 5 — (Dibatalkan) Queue untuk ekspor besar | — |
| 12 | Tahap 6 — Migrasi DocumentTemplateService | ⬛⬛ |
| 13 | Tahap 7 — Testing | ⬛⬛ |
| 14 | Tahap 8 — Pint & finalisasi | ⬛ |

---

## 7. Hal yang Perlu Dikonfirmasi

| # | Pertanyaan | Dampak |
|---|-----------|--------|
| 1 | Apakah format kop surat PDF sudah ada desain resmi? | Template `_layout.blade.php` |
| 2 | Apakah ada logo Dinsos / Pemkab Blitar yang perlu ditampilkan di PDF? | Jika ya, simpan di `storage/app/public/images/` |
| 3 | Apakah file ekspor perlu disimpan di server (arsip) atau hanya download langsung? | Menentukan apakah perlu tabel `export_logs` |
| 4 | Apakah Operator Kecamatan/Desa boleh mengekspor data? | Jika tidak, tambahkan pengecekan role di `visible()` |
| 5 | Apakah ekspor dari halaman laporan perlu format tambahan selain Excel & PDF (misal CSV)? | Penambahan exporter |
| 6 | Threshold ekspor besar (default 1000 baris) — apakah sudah sesuai? | Konfigurasi queue |
