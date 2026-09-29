# Rencana Aksi Dashboard Widget — SAPA SOSIAL

**Sumber:** PRD SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar (Bagian 3: Laporan & Dashboard)
**Teknologi:** Laravel · Filament v5 · Livewire v4 · PostgreSQL
**Tanggal:** 29 September 2026

---

## 1. Daftar Widget (diringkas menjadi 5)

| # | Widget | Tipe Filament | Isi | Sumber data | Peran utama |
|---|--------|---------------|-----|-------------|-------------|
| 1 | **Ringkasan Angka** | `StatsOverviewWidget` | SK DTSEN terbit; pengajuan & pengaduan masuk; kasus rehabilitasi aktif; tiket dalam proses vs selesai | `dtsen_certificates.issued_at`, `service_requests.submitted_at`, `complaints.reported_at`, `rehabilitation_cases.status` | Semua (kecuali Masyarakat) |
| 2 | **Perlu Tindakan** | `TableWidget` (3 tab) | (a) Menunggu paraf/TTD; (b) Reaktivasi darurat medis belum selesai; (c) PBI-JK tertahan melebihi batas hari | `approvals.decision = pending` + `status = awaiting_approval`; `service_requests.is_priority`; `pbi_reactivations.proposed_to_ministry_at` | Pejabat Penandatangan, Petugas |
| 3 | **Layanan per Status** | `ChartWidget` (bar), dengan pilihan layanan | Reaktivasi PBI-JK per tahap; status DTSEN, rehabilitasi, dan pengaduan; rujukan per lembaga tujuan (pilihan Rehabilitasi) | kolom `status` tiap modul, `referrals.referral_institution_id` | Petugas, Pimpinan |
| 4 | **SK DTSEN per Tujuan & Desil** | `ChartWidget` (bar), toggle tujuan/desil | Jumlah surat terbit per tujuan penggunaan dan per desil | `dtsen_certificates` join `dtsen_purposes`, kolom `decile` | Pimpinan, Petugas |
| 5 | **Sebaran per Wilayah** | `ChartWidget` (bar horizontal) | Layanan & pengaduan per kecamatan, lalu turun ke desa | join `villages` → `districts` | Semua (Operator: wilayahnya saja) |

**Filter tingkat halaman** (berlaku untuk seluruh widget): periode, jenis layanan, status, kecamatan, desa/kelurahan.

**Aturan akses:**
- Operator Kecamatan/Desa hanya melihat data wilayahnya; filter wilayah dikunci.
- Pimpinan hanya baca.
- Pilihan Rehabilitasi di Widget 3 disembunyikan dari Operator (data klien rehabilitasi bersifat sensitif).

---

## 2. Fase Pengerjaan

### Fase 0 — Klarifikasi definisi (sebelum coding)
- [ ] **Dasar tanggal "periode"**: `submitted_at` (masuk), `issued_at` (terbit), atau `completed_at` (selesai). Usulan: tiap widget memakai tanggal yang sesuai maknanya, dan dicantumkan di label/tooltip.
- [ ] **Status "selesai" vs "dalam proses"** per layanan: petakan enum tiap layanan (mis. `completed`, `rejected`, `ministry_rejected`, `resolved`, `duplicate`, `invalid`, `closed` = status akhir).
- [ ] **Batas hari "tertahan"** untuk PBI-JK: PRD menyebut "diatur admin", tetapi Bagian 4 tidak menyediakan kolom/tabel pengaturannya. Konfirmasi: tambah tabel pengaturan kecil, atau pakai `service_types.sla_days`. (Di luar ruang lingkup PRD, wajib dikonfirmasi.)
- [ ] **Filter yang berlaku per widget**: tetapkan mana yang relevan (mis. filter jenis layanan tidak berlaku pada rujukan per lembaga).
- [ ] **Visibilitas rehabilitasi** untuk Operator: konfirmasi usulan disembunyikan.

### Fase 1 — Fondasi dashboard
1. Halaman dashboard kustom di panel `/admin` dengan **filter form** (Filament v5 Schemas). Desa bergantung pada kecamatan. Widget membaca filter lewat `InteractsWithPageFilters`.
2. **Lapisan query bersama** (mis. DTO `DashboardFilters` + query builder per modul). Semua widget memakai lapisan ini; lapisan yang sama dipakai laporan berkala (Excel/PDF) agar angka dashboard dan laporan selalu sama.
3. **Pembatasan wilayah dan peran** di lapisan query yang sama; visibilitas widget lewat `canView()` berdasarkan role.
4. **Index PostgreSQL**: `status`, `village_id`, `service_type_id`, `submitted_at`; tambahan `issued_at`, `proposed_to_ministry_at`, `reported_at`, dan `approvals (approvable_type, approvable_id, decision)`.
5. **Seeder/Factory data demo** dengan volume realistis (mis. 20–50 ribu tiket). Pengujian memakai PostgreSQL, bukan SQLite.

### Fase 2 — Widget 2 dan Widget 1
- **Widget 2 (Perlu Tindakan)** dikerjakan lebih dulu karena langsung dipakai pejabat/petugas. Tabel berisi tautan ke halaman Resource terkait, diurutkan dari tanggal masuk terlama (tab darurat medis: prioritas paling atas).
- **Widget 1 (Ringkasan Angka)**: hitung SK hanya yang benar-benar `issued` (SK kedaluwarsa tetap terhitung terbit). Pengajuan dan pengaduan ditampilkan sebagai kartu terpisah, tidak dijumlahkan campur.

### Fase 3 — Widget 3
- Tampilkan urutan status sesuai alur (PBI-JK: `eligibility_verification` → `proposed_to_ministry` → `ministry_approved` → `reactivated`).
- Pilihan layanan: PBI-JK, DTSEN, Rehabilitasi, Pengajuan lain, Pengaduan.
- Pilihan Rehabilitasi menambahkan grafik rujukan per lembaga tujuan (dibatasi role).

### Fase 4 — Widget 4 dan Widget 5
- Widget 4: toggle per tujuan penggunaan / per desil.
- Widget 5: mulai dari level kecamatan; drill-down ke desa lewat filter.

### Fase 5 — Optimasi, pengujian, serah terima
- `$isLazy = true` pada widget berat; cache singkat (30–60 detik) dengan kunci per filter + scope pengguna; polling 60 detik untuk kesan "real-time" (websocket tidak diperlukan).
- Empty state, tampilan mobile, susunan widget per role.
- **Pest**: uji angka tiap widget (termasuk batas periode dan batas hari tertahan), uji scoping wilayah/role, uji kombinasi filter.
- Uji performa dengan data seeder.
- **UAT** bersama Petugas, Pejabat, dan Pimpinan memakai data nyata yang sudah dianonimkan.

---

## 3. Kriteria Selesai per Widget

- Angka cocok dengan hitungan manual pada data uji.
- Patuh filter yang berlaku dan scope role.
- Tampil dalam ±1–2 detik pada data demo.
- Label dan satuan berbahasa Indonesia.

---

## 4. Risiko dan Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Definisi "periode" dan "selesai" berbeda antar orang | Dikunci di Fase 0; ditulis sebagai tooltip pada widget |
| Angka dashboard ≠ angka laporan | Satu lapisan query bersama |
| Kebocoran data lintas wilayah atau data klien rehabilitasi | Scope di lapisan query (bukan hanya di widget); ada tes khusus |
| Query lambat karena banyak join ke `villages`/`districts` | Index, cache, agregasi di SQL (`GROUP BY`) |
| Sintaks API Filament v5 berbeda dari v3/v4 | Cek dokumentasi v5 saat membuat filter form dan widget; jangan pakai `HasForms`/`Form $form` |

---

## 5. Catatan

- Widget opsional "Informasi paling sering diakses" (`page_visits`, `search_logs`) **tidak dimasukkan**; dapat ditambahkan kemudian bila portal informasi sudah berjalan dan disetujui masuk lingkup.
- Laporan berkala (Excel/PDF) berada di luar rencana ini, tetapi memakai lapisan query yang sama dengan dashboard.
