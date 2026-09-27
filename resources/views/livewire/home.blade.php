<div>
    <!-- HERO SECTION -->
    <section class="relative overflow-hidden pt-8 pb-16 md:pt-14 md:pb-24 bg-gradient-to-b from-surface-container-low/70 to-canvas-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Headline Block -->
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 text-primary mb-5 border border-primary/20">
                    <span class="material-symbols-outlined text-base text-status-warning">verified</span>
                    <span class="text-xs font-semibold">Portal Resmi Pelayanan Publik Dinas Sosial Kab. Blitar</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold text-inverse-surface tracking-tight mb-4 leading-tight">
                    Satu Pintu Layanan Sosial Kabupaten Blitar
                </h1>
                <p class="text-base md:text-lg text-on-surface-variant max-w-2xl mx-auto mb-8 leading-relaxed">
                    Ajukan layanan, sampaikan pengaduan, dan pantau prosesnya secara online &mdash; setiap tahapan tercatat dan dapat ditelusuri secara transparan.
                </p>

                <!-- Primary CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-10">
                    <a href="{{ route('layanan.index') }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-7 py-3 rounded-xl bg-primary-container text-white font-semibold text-base hover:bg-primary active:scale-95 transition-all shadow-md">
                        <span class="material-symbols-outlined text-xl mr-2">edit_document</span>
                        Ajukan Layanan
                    </a>
                    <a href="{{ route('pengaduan.create') }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-7 py-3 rounded-xl bg-white border-2 border-status-warning/80 text-on-surface font-semibold text-base hover:bg-slate-50 active:scale-95 transition-all shadow-xs">
                        <span class="material-symbols-outlined text-xl text-status-warning mr-2">campaign</span>
                        Sampaikan Pengaduan
                    </a>
                </div>
            </div>

            <!-- PROMINENT TICKET TRACKING BOX -->
            <div class="max-w-4xl mx-auto" id="lacak-tiket">
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-md border border-slate-200 relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary to-status-warning"></div>
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 pb-4 border-b border-slate-100 gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-surface-container-high text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">manage_search</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-inverse-surface">Lacak Status Pengajuan &amp; Tiket</h2>
                                <p class="text-xs text-on-surface-variant">Ketahui progres berkas Anda kapan saja secara transparan</p>
                            </div>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-status-info/10 text-status-info text-xs font-medium">
                            <span class="material-symbols-outlined text-sm">lock</span>
                            Privasi Terlindungi (Masking Data)
                        </div>
                    </div>

                    <!-- Tracking Form -->
                    <form wire:submit="trackTicket" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-6">
                            <label class="block text-sm font-semibold text-inverse-surface mb-2" for="ticket-number">
                                Nomor Tiket Permohonan <span class="text-status-error">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-xl">confirmation_number</span>
                                </span>
                                <input
                                    wire:model="trackingNumber"
                                    class="w-full min-h-[48px] pl-11 pr-4 rounded-xl border border-slate-300 bg-white text-base text-inverse-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all font-mono uppercase"
                                    id="ticket-number"
                                    placeholder="Contoh: DTSEN-202609-00001 atau ADU-202609-00001"
                                    type="text"
                                    required
                                />
                            </div>
                            @error('trackingNumber') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-semibold text-inverse-surface mb-2" for="security-pin">
                                4 Digit Akhir NIK / HP <span class="text-status-error">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-xl">pin</span>
                                </span>
                                <input
                                    wire:model="securityPin"
                                    class="w-full min-h-[48px] pl-11 pr-4 rounded-xl border border-slate-300 bg-white text-base text-inverse-surface focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all text-center tracking-widest font-mono"
                                    id="security-pin"
                                    maxlength="4"
                                    placeholder="XXXX"
                                    type="text"
                                    required
                                />
                            </div>
                            @error('securityPin') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-3 flex items-end">
                            <button type="submit" class="w-full min-h-[48px] px-5 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">search</span>
                                <span>Lacak Status</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: 3 LAYANAN UTAMA (PRIORITAS) -->
    <section class="py-16 bg-white" id="layanan">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold mb-3">
                        <span class="material-symbols-outlined text-sm">stars</span>
                        Layanan Unggulan
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-inverse-surface">Layanan Prioritas Masyarakat</h2>
                    <p class="text-sm text-on-surface-variant mt-1">Tiga layanan sosial yang paling sering diakses warga Kabupaten Blitar</p>
                </div>
                <a href="{{ route('layanan.index') }}" wire:navigate class="mt-4 md:mt-0 text-sm font-semibold text-primary hover:text-primary-container flex items-center gap-1 group">
                    <span>Lihat Semua Layanan</span>
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1: Surat Keterangan DTSEN -->
                <div class="rounded-2xl border border-slate-200 bg-white p-7 custom-shadow-card hover:shadow-lg transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-bl-full -mr-6 -mt-6 transition-all group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-primary-container text-white flex items-center justify-center mb-5 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">description</span>
                        </div>
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-semibold mb-2">
                            Prioritas 1 &bull; 1-2 Hari Kerja
                        </div>
                        <h3 class="text-xl font-bold text-inverse-surface mb-2 group-hover:text-primary transition-colors">
                            Surat Keterangan DTSEN
                        </h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                            Penerbitan surat keterangan status dan peringkat desil keluarga dalam DTSEN untuk syarat SPMB jalur afirmasi, PIP, KIP Kuliah, bansos, dan kesehatan.
                        </p>
                        <div class="space-y-2 mb-6 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-emerald-600">check_circle</span>
                                <span>Syarat mudah: KTP &amp; Kartu Keluarga</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-emerald-600">check_circle</span>
                                <span>Dilengkapi QR Code verifikasi resmi</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('layanan.detail', 'dtsen') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors">
                            Lihat Persyaratan
                        </a>
                        <a href="{{ route('pengajuan.dtsen') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl bg-primary text-white text-center font-semibold text-xs hover:bg-primary-container transition-colors shadow-xs">
                            Ajukan Surat
                        </a>
                    </div>
                </div>

                <!-- Card 2: Reaktivasi KIS / PBI-JK -->
                <div class="rounded-2xl border border-slate-200 bg-white p-7 custom-shadow-card hover:shadow-lg transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-6 -mt-6 transition-all group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-secondary text-white flex items-center justify-center mb-5 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">health_and_safety</span>
                        </div>
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[11px] font-semibold mb-2">
                            Prioritas Medis &bull; Cepat
                        </div>
                        <h3 class="text-xl font-bold text-inverse-surface mb-2 group-hover:text-secondary transition-colors">
                            Reaktivasi KIS / PBI-JK
                        </h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                            Fasilitasi pengaktifan kembali kepesertaan BPJS Kesehatan PBI-JK yang dinonaktifkan bagi warga kurang mampu atau kondisi darurat medis.
                        </p>
                        <div class="space-y-2 mb-6 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-blue-600">check_circle</span>
                                <span>Prioritas penanganan darurat rawat inap</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-blue-600">check_circle</span>
                                <span>Terintegrasi usulan SIKS-NG Kemensos</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('layanan.detail', 'pbi') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors">
                            Lihat Persyaratan
                        </a>
                        <a href="{{ route('pengajuan.pbi') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl bg-secondary text-white text-center font-semibold text-xs hover:bg-slate-700 transition-colors shadow-xs">
                            Ajukan Reaktivasi
                        </a>
                    </div>
                </div>

                <!-- Card 3: Pelayanan Rehabilitasi Sosial -->
                <div class="rounded-2xl border border-slate-200 bg-white p-7 custom-shadow-card hover:shadow-lg transition-all flex flex-col justify-between group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-6 -mt-6 transition-all group-hover:scale-110"></div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-tertiary-container text-white flex items-center justify-center mb-5 shadow-xs">
                            <span class="material-symbols-outlined text-2xl">support</span>
                        </div>
                        <div class="inline-block px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[11px] font-semibold mb-2">
                            Pendampingan Khusus
                        </div>
                        <h3 class="text-xl font-bold text-inverse-surface mb-2 group-hover:text-tertiary-container transition-colors">
                            Rehabilitasi Sosial
                        </h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                            Pelayanan dan rujukan bagi lansia terlantar, penyandang disabilitas, ODGJ terlantar, anak terlantar, serta korban tindak kekerasan.
                        </p>
                        <div class="space-y-2 mb-6 text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-amber-600">check_circle</span>
                                <span>Assessment mendalam oleh petugas Dinsos</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm text-amber-600">check_circle</span>
                                <span>Rujukan ke balai/panti rehabilitasi resmi</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('layanan.detail', 'rehsos') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors">
                            Lihat Persyaratan
                        </a>
                        <a href="{{ route('pengaduan.create') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl bg-tertiary-container text-white text-center font-semibold text-xs hover:bg-tertiary transition-colors shadow-xs">
                            Lapor Kasus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: LAYANAN LAINNYA -->
    <section class="py-14 bg-surface-container-low/50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-inverse-surface mb-6">Layanan Pendukung &amp; Kanal Partisipasi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Item 1 -->
                <a href="{{ route('pengaduan.create') }}" wire:navigate class="bg-white p-5 rounded-xl border border-slate-200 hover:border-primary/50 transition-all custom-shadow-card group">
                    <div class="w-10 h-10 rounded-lg bg-red-100 text-status-error flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">campaign</span>
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface group-hover:text-primary transition-colors mb-1">Pengaduan Sosial</h4>
                    <p class="text-xs text-on-surface-variant">Lapor masalah sosial di lingkungan sekitar Anda secara online.</p>
                </a>

                <!-- Item 2 -->
                <a href="{{ route('cek-status') }}" wire:navigate class="bg-white p-5 rounded-xl border border-slate-200 hover:border-primary/50 transition-all custom-shadow-card group">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 text-primary flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">manage_search</span>
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface group-hover:text-primary transition-colors mb-1">Cek Status Tiket</h4>
                    <p class="text-xs text-on-surface-variant">Pantau progres pengajuan dan respon pengaduan secara transparan.</p>
                </a>

                <!-- Item 3 -->
                <a href="{{ route('verifikasi') }}" wire:navigate class="bg-white p-5 rounded-xl border border-slate-200 hover:border-primary/50 transition-all custom-shadow-card group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">qr_code_scanner</span>
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface group-hover:text-primary transition-colors mb-1">Verifikasi Surat DTSEN</h4>
                    <p class="text-xs text-on-surface-variant">Validasi keaslian draf/surat SK DTSEN lewat kode QR resmi.</p>
                </a>

                <!-- Item 4 -->
                <a href="{{ route('informasi-faq') }}" wire:navigate class="bg-white p-5 rounded-xl border border-slate-200 hover:border-primary/50 transition-all custom-shadow-card group">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">help</span>
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface group-hover:text-primary transition-colors mb-1">Informasi &amp; FAQ</h4>
                    <p class="text-xs text-on-surface-variant">Panduan alur, formulir unduhan, dan pertanyaan yang sering diajukan.</p>
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION: 4-STEP ALUR KERJA -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">Transparan &amp; Terstruktur</span>
                <h2 class="text-2xl md:text-3xl font-bold text-inverse-surface mt-1">Bagaimana Cara Kerjanya?</h2>
                <p class="text-sm text-on-surface-variant mt-2">Empat langkah mudah pengajuan layanan sosial tanpa antre di kantor</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-200 text-primary flex items-center justify-center font-extrabold text-xl shadow-xs mb-4">
                        1
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface mb-1">Pilih Layanan</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Tentukan layanan sosial yang Anda butuhkan (SK DTSEN, Reaktivasi KIS, atau lainnya).
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-200 text-primary flex items-center justify-center font-extrabold text-xl shadow-xs mb-4">
                        2
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface mb-1">Isi Formulir &amp; Berkas</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Lengkapi identitas diri dan unggah foto/scan dokumen pendukung seperti KTP dan Kartu Keluarga.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-200 text-primary flex items-center justify-center font-extrabold text-xl shadow-xs mb-4">
                        3
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface mb-1">Dapatkan Nomor Tiket</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Sistem otomatis menerbitkan kode unik permohonan untuk melacak tahapan verifikasi berkas.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-200 text-primary flex items-center justify-center font-extrabold text-xl shadow-xs mb-4">
                        4
                    </div>
                    <h4 class="text-base font-bold text-inverse-surface mb-1">Pantau &amp; Terima Hasil</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed">
                        Petugas memproses berkas, surat disetujui, dan surat hasil dapat diunduh langsung via website.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: BANNER CEK KEASLIAN SURAT DTSEN -->
    <section class="py-14 bg-gradient-to-r from-primary to-primary-container text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="max-w-xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-semibold mb-3">
                        <span class="material-symbols-outlined text-sm">security</span>
                        Anti-Pemalsuan Dokumen
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Verifikasi Keaslian Surat DTSEN</h2>
                    <p class="text-sm text-teal-100 mt-2 leading-relaxed">
                        Pihak sekolah, kampus (SPMB/KIP-K), atau instansi dapat memastikan keabsahan Surat Keterangan DTSEN terbitan Dinas Sosial Kabupaten Blitar dengan memasukkan kode unik surat.
                    </p>
                </div>
                <div class="w-full lg:w-auto flex-shrink-0">
                    <form wire:submit="verifyCode" class="flex flex-col sm:flex-row gap-3">
                        <input
                            wire:model="verificationCode"
                            type="text"
                            placeholder="Masukkan Kode Verifikasi Surat"
                            class="min-h-[48px] px-4 py-2.5 rounded-xl bg-white text-slate-900 placeholder:text-slate-400 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-status-warning w-full sm:w-80 shadow-md uppercase"
                            required
                        />
                        <button type="submit" class="min-h-[48px] px-6 py-2.5 rounded-xl bg-status-warning text-slate-900 font-bold text-sm hover:bg-amber-400 active:scale-95 transition-all shadow-md flex items-center justify-center gap-2 whitespace-nowrap">
                            <span class="material-symbols-outlined text-lg">verified_user</span>
                            <span>Cek Keaslian</span>
                        </button>
                    </form>
                    @error('verificationCode') <p class="text-xs text-amber-200 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: FAQ ACCORDION -->
    <section class="py-16 bg-white" id="faq">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">Pusat Bantuan</span>
                <h2 class="text-2xl md:text-3xl font-bold text-inverse-surface mt-1">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-sm text-on-surface-variant mt-2">Jawaban singkat atas kendala yang kerap dihadapi pemohon</p>
            </div>

            <div class="space-y-4" x-data="{ activeAccordion: null }">
                @forelse ($faqs as $index => $faq)
                    <div class="border border-slate-200 rounded-xl overflow-hidden custom-shadow-card">
                        <button
                            type="button"
                            @click="activeAccordion = activeAccordion === {{ $index }} ? null : {{ $index }}"
                            class="w-full flex items-center justify-between p-5 text-left bg-white hover:bg-slate-50 transition-colors"
                        >
                            <span class="text-sm md:text-base font-semibold text-inverse-surface">{{ $faq->question }}</span>
                            <span class="material-symbols-outlined text-xl text-primary transition-transform duration-200" :class="{ 'rotate-180': activeAccordion === {{ $index }} }">
                                keyboard_arrow_down
                            </span>
                        </button>
                        <div x-show="activeAccordion === {{ $index }}" x-collapse style="display: none;" class="px-5 pb-5 pt-1 text-sm text-slate-600 bg-slate-50/50 leading-relaxed border-t border-slate-100">
                            {{ $faq->answer }}
                        </div>
                    </div>
                @empty
                    <div class="border border-slate-200 rounded-xl overflow-hidden custom-shadow-card" x-data="{ open: true }">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-5 text-left bg-white hover:bg-slate-50">
                            <span class="text-sm md:text-base font-semibold text-inverse-surface">Berapa lama proses penerbitan Surat Keterangan DTSEN?</span>
                            <span class="material-symbols-outlined text-xl text-primary" :class="{ 'rotate-180': open }">keyboard_arrow_down</span>
                        </button>
                        <div x-show="open" class="px-5 pb-5 pt-1 text-sm text-slate-600 bg-slate-50 leading-relaxed border-t border-slate-100">
                            Proses penerbitan berkas rata-rata memakan waktu 1 hingga 2 hari kerja setelah dokumen persyaratan KTP dan KK dinyatakan lengkap dan data terverifikasi di SIKS-NG oleh petugas.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('informasi-faq') }}" wire:navigate class="text-sm font-semibold text-primary hover:underline inline-flex items-center gap-1">
                    <span>Lihat Semua FAQ dan Formulir Unduhan</span>
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION: KONTAK & LOKASI KANTOR STRIP -->
    <section class="py-14 bg-surface-container-low border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-6 md:p-10 border border-slate-200 shadow-sm flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-2xl">support_agent</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-inverse-surface">Butuh Bantuan Langsung dari Petugas?</h3>
                        <p class="text-sm text-on-surface-variant mt-1 max-w-xl">
                            Petugas Dinsos dan Operator Puskesos di kecamatan/desa siap memandu Anda jika mengalami kendala dalam pengisian formulir atau persyaratan layanan.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <a href="https://wa.me/6281234567890?text=Halo%20Dinas%20Sosial%20Kabupaten%20Blitar,%20saya%20butuh%20informasi%20layanan%20SAPA%20SOSIAL" target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors shadow-xs">
                        <span class="material-symbols-outlined text-lg">chat</span>
                        WhatsApp Call Center
                    </a>
                    <a href="tel:0342801123" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-lg">call</span>
                        (0342) 801123
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
