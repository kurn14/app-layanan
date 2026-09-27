# Prompt Google Stitch — Portal Publik SAPA SOSIAL

Kumpulan prompt untuk membuat UI/frontend **Portal Publik SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar** di [Google Stitch](https://stitch.withgoogle.com/), berdasarkan `PRD_SAPA_SOSIAL.md`.

> Prompt ditulis dalam **bahasa Inggris** (Stitch lebih akurat dengan bahasa Inggris), sedangkan seluruh **teks antarmuka tetap bahasa Indonesia**.

---

## Cara Pakai

1. Mulai dari **Prompt 1 (Beranda)** untuk membentuk gaya visual (design system).
2. Jalankan prompt halaman lain **satu per satu dalam project yang sama** agar tampilan konsisten.
3. Setiap prompt sudah memuat ringkasan gaya visual sehingga tetap bisa ditempel mandiri.
4. Perbaiki hasil dengan prompt pendek (lihat bagian *Prompt Perbaikan Cepat* di akhir).
5. Hasil Stitch berupa HTML/Tailwind sehingga mudah dipindahkan ke komponen **Livewire v4 + Tailwind CSS** sesuai PRD.

## Daftar Prompt

| No | Halaman | Rujukan PRD |
|----|---------|-------------|
| 1 | Beranda (Master Prompt) | Layanan 6 |
| 2 | Daftar Layanan | Layanan 1–6 |
| 3 | Detail Layanan (contoh: Surat Keterangan DTSEN) | Layanan 1, 2, 3 |
| 4 | Form Pengajuan Surat Keterangan DTSEN | Layanan 1 |
| 5 | Form Pengajuan Reaktivasi KIS / PBI-JK | Layanan 2 |
| 6 | Halaman Sukses & Nomor Tiket | Layanan 1, 2, 4 |
| 7 | Cek Status Tiket | Layanan 1, 2, 4, 5 |
| 8 | Form Pengaduan & Laporan Sosial | Layanan 5 |
| 9 | Verifikasi Keaslian Surat DTSEN | Layanan 1 |
| 10 | Informasi Layanan & FAQ | Layanan 6 |
| 11 | Login / Daftar Akun Masyarakat | Asumsi PRD Bagian 5 |
| 12 | Akun Saya (Riwayat Pengajuan & Pengaduan) | Layanan 1, 2, 4, 5 |

---

## Prompt 1 — Beranda (Master Prompt)

```
Design a responsive, mobile-first public service portal website called "SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar", the official digital one-stop social services portal of Dinas Sosial Kabupaten Blitar, Indonesia. Users are ordinary citizens (many elderly, low digital literacy, mostly on smartphones), so the design must be clear, warm, trustworthy, and very easy to use. All UI text in Indonesian.

DESIGN SYSTEM (reuse on all screens):
- Modern government service portal: clean, friendly, card-based, generous white space, rounded corners (12–16px), soft shadows.
- Colors: primary deep teal #0F766E, dark navy #0B2545 for headings/footer, warm amber #F59E0B for accents and key highlights, background #F8FAFC, plus success green, warning amber, error red. WCAG AA contrast.
- Typography: Plus Jakarta Sans (or Inter), body text min 16px, tap targets min 48px.
- Outline icons (Lucide/Heroicons style). Minimal flat illustrations, no stock photos.
- Consistent components: navbar, buttons, cards, form fields, status badges, timeline, breadcrumbs, footer.

GLOBAL LAYOUT:
- Sticky top navbar: small emblem placeholder + "SAPA SOSIAL" and "Dinas Sosial Kab. Blitar"; menu: Beranda, Layanan, Pengaduan, Cek Status, Verifikasi Surat, Informasi & FAQ; "Masuk" button. Hamburger menu on mobile.
- Footer: address, phone, email, office hours, quick links, "© Dinas Sosial Kabupaten Blitar".

HOME PAGE SECTIONS:
1. Hero: headline "Satu Pintu Layanan Sosial Kabupaten Blitar"; subheadline "Ajukan layanan, sampaikan pengaduan, dan pantau prosesnya secara online — setiap tahapan tercatat dan dapat ditelusuri."; two buttons "Ajukan Layanan" and "Sampaikan Pengaduan"; directly below, a prominent "Cek Status Tiket" box (ticket number input + button).
2. "Layanan Utama": 3 large highlighted cards: (a) "Surat Keterangan DTSEN" — "Surat keterangan status & desil DTSEN untuk SPMB, PIP, KIP Kuliah, bansos, dan layanan kesehatan"; (b) "Reaktivasi KIS / PBI-JK" — "Aktifkan kembali kepesertaan JKN-KIS PBI yang dinonaktifkan"; (c) "Rehabilitasi Sosial" — "Layanan untuk lansia terlantar, penyandang disabilitas, ODGJ, anak, dan korban kekerasan". Each with icon, description, buttons "Lihat Persyaratan" and "Ajukan".
3. "Layanan Lainnya": smaller cards: Pengajuan Layanan Sosial Lainnya, Pengaduan & Laporan Sosial, Informasi Layanan, Verifikasi Keaslian Surat.
4. "Bagaimana Cara Kerjanya": 4-step horizontal stepper: Pilih layanan → Isi formulir & unggah dokumen → Dapatkan nomor tiket → Pantau status sampai selesai.
5. Banner "Cek Keaslian Surat DTSEN" with code input + button.
6. "Pertanyaan yang Sering Diajukan": accordion with 5 items.
7. "Informasi Paling Sering Diakses": list of 5 popular info links.
8. Contact & office hours strip with map placeholder.
```

---

## Prompt 2 — Daftar Layanan

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the "Layanan" listing page.

- Breadcrumb: Beranda > Layanan.
- Page header: title "Layanan Sosial", subtitle "Pilih layanan yang Anda butuhkan".
- Search bar "Cari layanan atau informasi..." and category filter chips: Semua, Administrasi, Kesehatan, Rehabilitasi Sosial, Pengaduan.
- Section "Layanan Prioritas": 3 large cards (Surat Keterangan DTSEN, Reaktivasi KIS/PBI-JK, Rehabilitasi Sosial), each with icon, short description, estimated service time, and buttons "Detail" and "Ajukan".
- Section "Layanan Lainnya": grid of smaller cards including "Pengajuan Layanan Sosial Lainnya" and "Pengaduan & Laporan Sosial".
- Empty-state design for "Layanan tidak ditemukan".
- Bottom CTA: "Bingung memilih layanan? Hubungi petugas kami" with phone/WhatsApp button.
```

---

## Prompt 3 — Detail Layanan (contoh: Surat Keterangan DTSEN)

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design a service detail page for "Surat Keterangan DTSEN".

- Breadcrumb: Beranda > Layanan > Surat Keterangan DTSEN.
- Header: title, short description ("Surat keterangan yang menerangkan status keluarga dalam Data Tunggal Sosial Ekonomi Nasional (DTSEN), termasuk peringkat desil."), badge "Layanan Prioritas", last-updated date, and sticky primary button "Ajukan Sekarang".
- Two-column layout on desktop (content left, sticky info sidebar right); single column on mobile.
- Content sections as tabs or anchored sections: "Tentang Layanan", "Tujuan Penggunaan" (chips: SPMB, PIP, KIP Kuliah, Bantuan Sosial, Kesehatan, Lainnya), "Persyaratan" (checklist: KTP pemohon, Kartu Keluarga), "Alur Pelayanan" (vertical stepper: Ajukan online → Pemeriksaan berkas → Pengecekan data → Persetujuan pejabat → Surat terbit & diunduh), "Ketentuan Penting" (info box: surat hanya terbit jika desil sesuai ketentuan tujuan penggunaan; surat memiliki masa berlaku dan kode verifikasi QR), "Formulir Unduhan" (list with file name, version, download button), "FAQ" accordion.
- Sidebar card: "Waktu Layanan", "Lokasi Kantor", "Kontak", and buttons "Ajukan Sekarang" and "Cek Status Tiket".
- Footer as usual.
(Reusable for "Reaktivasi KIS/PBI-JK" with requirements KTP, KK, Kartu BPJS/KIS, and surat keterangan faskes for medical reasons; and for "Rehabilitasi Sosial".)
```

---

## Prompt 4 — Form Pengajuan: Surat Keterangan DTSEN (Wizard)

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design a multi-step application form wizard for "Pengajuan Surat Keterangan DTSEN".

- Top progress stepper with 4 steps: 1 Tujuan, 2 Data Pemohon, 3 Unggah Dokumen, 4 Tinjau & Kirim. Show the current step (Step 2) as the active screen.
- Step 2 "Data Pemohon" fields: Nama Lengkap, NIK (16 digits, numeric, helper text), Nomor KK (16 digits), Alamat, Kecamatan (dropdown), Desa/Kelurahan (dependent dropdown), Nomor HP/WhatsApp. Then section "Data Orang yang Diterangkan" with Nama, NIK, Hubungan dengan pemohon (dropdown: Diri sendiri, Anak, Orang tua, Lainnya). Include a checkbox "Data orang yang diterangkan sama dengan pemohon".
- Step 1 preview (as a small side illustration or collapsed summary): "Tujuan Penggunaan" radio cards: SPMB, PIP, KIP Kuliah, Bantuan Sosial, Kesehatan, Lainnya.
- Step 3 hint: file upload zones for KTP dan KK with drag-drop, format/size helper (JPG, PNG, PDF, maks. 2 MB), preview thumbnail and remove button.
- Show inline validation states (one field with error message in Indonesian, one valid).
- Sticky bottom bar on mobile with "Kembali" and "Lanjut" buttons; "Simpan Draf" text link.
- Right sidebar (desktop) "Ringkasan & Bantuan": checklist of requirements, and a note "Data Anda dilindungi dan hanya digunakan untuk keperluan layanan".
```

---

## Prompt 5 — Form Pengajuan: Reaktivasi KIS / PBI-JK

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design a multi-step application form for "Pengajuan Reaktivasi KIS / PBI-JK".

- Stepper: 1 Data Peserta, 2 Alasan Reaktivasi, 3 Unggah Dokumen, 4 Tinjau & Kirim. Show Step 2 as the active screen.
- Step 2 "Alasan Reaktivasi": radio cards — Penyakit kronis/katastropik, Kondisi darurat medis, Bayi baru lahir dari ibu peserta PBI, Lainnya. When "Kondisi darurat medis" is selected, show an amber priority banner: "Pengajuan darurat medis akan diprioritaskan oleh petugas".
- Additional fields: Nomor Kartu BPJS/KIS (13 digits), Perkiraan Tanggal Nonaktif (date picker), Nama Fasilitas Kesehatan, Nomor Surat Keterangan Faskes, Keterangan tambahan (textarea).
- Step 3 hint: upload zones — KTP, Kartu Keluarga, Kartu BPJS/KIS, Surat Keterangan Faskes (marked "Wajib untuk alasan medis"), each with status (belum diunggah / berhasil).
- Sidebar: "Alur setelah Anda mengajukan" mini vertical timeline: Pemeriksaan berkas → Verifikasi kelayakan → Surat rekomendasi → Diusulkan ke Kemensos → Keputusan Kemensos → Aktif kembali di BPJS.
- Sticky bottom action bar on mobile with "Kembali" and "Lanjut".
```

---

## Prompt 6 — Halaman Sukses & Nomor Tiket

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design a submission success confirmation page.

- Centered card with a green success checkmark illustration, heading "Pengajuan Berhasil Dikirim", subtext "Simpan nomor tiket Anda untuk memantau perkembangan layanan."
- Large ticket number display "DTSEN-202610-00012" in a highlighted box with a "Salin Nomor" button and a "Unduh Bukti Pengajuan (PDF)" button.
- Summary list: Jenis Layanan, Tanggal Pengajuan, Nama Pemohon, Status "Diajukan" (badge).
- Info box: "Untuk cek status, gunakan nomor tiket dan 4 digit terakhir NIK atau No. HP Anda."
- "Langkah Selanjutnya" 3-item list: Petugas memeriksa berkas, Anda akan diminta memperbaiki jika ada kekurangan, Surat diterbitkan setelah disetujui pejabat.
- Buttons: "Cek Status Tiket" (primary), "Kembali ke Beranda" (secondary).
```

---

## Prompt 7 — Cek Status Tiket (Hasil Pelacakan)

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the "Cek Status Tiket" page showing a tracking result.

- Top section: page title "Cek Status Tiket"; form with two fields: "Nomor Tiket" and "4 Digit Terakhir NIK / No. HP", plus button "Lacak". Helper text explaining the verification.
- Result card below (state after successful search): ticket number "PBI-202610-00007", service name "Reaktivasi KIS / PBI-JK", submission date, current status badge "Diusulkan ke Kemensos", and a line "Terakhir diperbarui: 2 hari lalu".
- Vertical timeline of stages with completed (green check), current (teal, pulsing), and upcoming (gray) states, each with date and short note: Diajukan → Pemeriksaan Berkas → Verifikasi Kelayakan → Menunggu Persetujuan → Rekomendasi Terbit → Diusulkan ke Kemensos (current) → Disetujui Kemensos → Aktif Kembali di BPJS → Selesai.
- Also design alternate state components (as small variants or separate cards): "Perbaikan Diminta" — amber alert card listing what to fix with a button "Perbaiki Data/Dokumen"; "Ditolak" — red card with alasan penolakan and tindak lanjut information; "Selesai" — green card with "Unduh Surat" button.
- Error state: "Nomor tiket tidak ditemukan atau data tidak cocok."
- Do not display sensitive personal data (NIK, address) in the result.
```

---

## Prompt 8 — Form Pengaduan & Laporan Sosial

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the "Pengaduan & Laporan Sosial" submission page.

- Header: title "Sampaikan Pengaduan Sosial", subtitle "Laporkan permasalahan sosial di lingkungan Anda. Laporan Anda akan diverifikasi dan ditindaklanjuti petugas."
- Form in a single clean card, grouped sections:
  1. Kategori Permasalahan — selectable chips/cards (e.g. Lansia terlantar, Penyandang disabilitas, ODGJ terlantar, Anak terlantar, Kekerasan, Bantuan sosial tidak tepat sasaran, Lainnya).
  2. Lokasi Kejadian — Kecamatan (dropdown), Desa/Kelurahan (dependent dropdown), Detail lokasi (textarea), optional map pin placeholder.
  3. Uraian Permasalahan — textarea with character counter and guidance "Jelaskan siapa, apa, kapan, dan di mana."
  4. Lampiran (opsional) — drag-drop photo/document upload with thumbnails (JPG, PNG, PDF, maks. 5 MB).
  5. Data Pelapor — Nama Lengkap, Nomor HP (both required), with note "Data pelapor dijaga kerahasiaannya".
- Consent checkbox and button "Kirim Pengaduan"; secondary "Batal".
- Right sidebar (desktop): "Alur Penanganan" 5-step mini timeline: Diterima → Verifikasi → Didisposisikan → Dalam Penanganan → Selesai; plus emergency contact card for urgent cases.
```

---

## Prompt 9 — Verifikasi Keaslian Surat DTSEN

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the public page "Verifikasi Keaslian Surat" reachable by scanning the QR code on a DTSEN letter (URL /verifikasi/{kode}).

- Header with shield-check icon, title "Verifikasi Keaslian Surat Keterangan DTSEN", short explanation.
- Input form: "Kode Verifikasi" field + button "Periksa" (also mention "atau pindai QR Code pada surat").
- Design three result states:
  1. VALID (green): "Surat Terverifikasi Asli". Show: Nomor Surat (400.9/123/409.XX/2026), Nama yang diterangkan (partially masked, e.g. "Ahmad M****"), Tujuan Penggunaan, Tanggal Terbit, Masa Berlaku sampai, Pejabat Penandatangan, Diterbitkan oleh Dinas Sosial Kabupaten Blitar. Do not show NIK or desil.
  2. KEDALUWARSA (amber): "Surat Sudah Melewati Masa Berlaku" with expiry date.
  3. TIDAK DITEMUKAN (red): "Kode verifikasi tidak ditemukan. Surat mungkin tidak asli." with a contact button.
- Footer note about how verification works and a "Laporkan surat mencurigakan" link.
```

---

## Prompt 10 — Informasi Layanan & FAQ

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the "Informasi Layanan" page.

- Large search bar at top "Cari informasi (mis. syarat DTSEN, reaktivasi BPJS)..." with recent/popular keyword chips below.
- Category tabs/chips: Semua, Program Sosial, Rehabilitasi Sosial, Disabilitas, Lansia, Pengaduan.
- Grid of information cards: title, category badge, 2-line excerpt, updated date, "Baca Selengkapnya".
- Right/side column: "Paling Sering Diakses" list, "Formulir Unduhan" list (file name, version "v2.1", download icon).
- Bottom section: FAQ accordion (5 items) and CTA banner "Sudah paham? Langsung ajukan layanan" with two buttons "Ajukan Layanan" and "Sampaikan Pengaduan".
- Also design an information article detail view: breadcrumb, title, last-updated, content with headings, downloadable forms box, related information, and buttons "Ajukan Layanan" / "Sampaikan Pengaduan".
- Include search-no-results empty state.
```

---

## Prompt 11 — Login / Daftar Akun Masyarakat

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the citizen authentication page.

- Split layout on desktop: left panel with teal gradient, logo, tagline "Satu Pintu Layanan Sosial Kabupaten Blitar" and 3 benefit bullets (Ajukan layanan tanpa antre, Pantau status real-time, Simpan riwayat pengajuan); right panel with the form. On mobile only the form.
- Tabs "Masuk" and "Daftar".
- Masuk: No. HP/Email, Kata Sandi (show/hide), "Lupa kata sandi?", button "Masuk".
- Daftar: Nama Lengkap, NIK, No. HP, Email (opsional), Kata Sandi, Konfirmasi Kata Sandi, consent checkbox for personal data, button "Buat Akun".
- Note below: "Tanpa akun pun Anda tetap bisa cek status dengan nomor tiket."
- Small link "Petugas Dinsos? Masuk ke Panel Admin".
```

---

## Prompt 12 — Akun Saya (Riwayat Pengajuan & Pengaduan)

```
Same design system as the SAPA SOSIAL portal (deep teal #0F766E, navy #0B2545, amber accent #F59E0B, Plus Jakarta Sans, rounded cards, mobile-first, Indonesian UI text). Design the logged-in citizen dashboard "Akun Saya".

- Greeting header "Halo, Siti Aminah" with quick action buttons: "Ajukan Layanan Baru", "Sampaikan Pengaduan".
- Summary stat cards: Sedang Diproses (3), Perlu Perbaikan (1, amber highlight), Selesai (5).
- Tabs: "Pengajuan Saya", "Pengaduan Saya", "Profil".
- List/table of tickets (cards on mobile): ticket number, service name, submission date, status badge (Diajukan, Pemeriksaan Berkas, Perbaikan Diminta, Diusulkan ke Kemensos, Selesai, Ditolak), and actions "Lihat Detail" and "Unduh Surat" (only when completed). Include filter by status and search by ticket number.
- Highlight the "Perbaikan Diminta" ticket with an amber alert strip "Petugas meminta perbaikan dokumen" and button "Perbaiki Sekarang".
- Profil tab: editable personal data form and change password section.
- Empty state: "Belum ada pengajuan" with illustration and CTA.
```

---

## Prompt Perbaikan Cepat

Gunakan setelah halaman ter-generate jika hasilnya perlu disesuaikan:

```
Make all buttons and inputs larger and text 18px for elderly users.
```

```
Use a more official, formal look with less amber.
```

```
Add a dark navy top bar with running text for announcements.
```

```
Show the mobile version (375px) of this screen.
```

```
Make status badges color-coded: blue = proses, amber = perbaikan, green = selesai, red = ditolak.
```

```
Keep the same navbar, footer, colors, and typography as the Beranda screen.
```

---

## Catatan

- **Panel admin (`/admin`)** tidak dibuat dengan Stitch karena dibangun memakai Filament v5.
- Halaman tambahan yang dapat dibuat bila perlu: Rehabilitasi Sosial (versi informasi), halaman 404/maintenance, dan Kebijakan Privasi.
- Cek status tiket sesuai asumsi PRD: **nomor tiket + 4 digit terakhir NIK/No. HP** (tanpa login wajib).
- Halaman verifikasi surat tidak menampilkan NIK maupun desil demi perlindungan data pribadi.
