<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-secondary mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <a href="{{ route('layanan.index') }}" wire:navigate class="hover:text-primary transition-colors">
            Layanan
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-on-surface font-semibold text-primary">
            {{ $service?->name ?? 'Detail Layanan' }}
        </span>
    </nav>

    <!-- Header Section -->
    <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 custom-shadow-card mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2.5 mb-3">
                    <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold">
                        {{ $service?->category ?? 'Layanan Sosial' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">
                        Layanan Prioritas Daerah
                    </span>
                    <span class="text-xs text-slate-400 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">update</span>
                        Diperbarui September 2026
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface tracking-tight">
                    {{ $service?->name ?? 'Surat Keterangan DTSEN' }}
                </h1>
                <p class="text-sm md:text-base text-on-surface-variant mt-2 leading-relaxed">
                    {{ $service?->description ?? 'Penerbitan surat keterangan yang menerangkan status keluarga dalam Data Tunggal Sosial Ekonomi Nasional (DTSEN), termasuk peringkat desil.' }}
                </p>
            </div>

            <div class="flex-shrink-0 flex flex-col sm:flex-row lg:flex-col gap-3">
                @php
                    $applyRoute = match(strtolower($slug)) {
                        'dtsen' => route('pengajuan.dtsen'),
                        'pbi' => route('pengajuan.pbi'),
                        'rehsos' => route('pengaduan.create'),
                        default => route('pengajuan.umum', $slug),
                    };
                @endphp
                <a href="{{ $applyRoute }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-md">
                    <span class="material-symbols-outlined text-lg">edit_document</span>
                    <span>Ajukan Sekarang</span>
                </a>
                <a href="{{ route('cek-status') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-base">manage_search</span>
                    <span>Lacak Status Tiket</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main 8+4 Asymmetric Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- LEFT 8 COLUMNS: Tabbed Details & Information -->
        <div class="lg:col-span-8 flex flex-col gap-8">
            <!-- Navigation Tabs -->
            <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-2 custom-scroll">
                <button
                    wire:click="setTab('tentang')"
                    class="px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors border-b-2 {{ $activeTab === 'tentang' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
                >
                    Tentang Layanan
                </button>
                @if(strtolower($slug) === 'dtsen')
                    <button
                        wire:click="setTab('tujuan')"
                        class="px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors border-b-2 {{ $activeTab === 'tujuan' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
                    >
                        Tujuan &amp; Batas Desil
                    </button>
                @endif
                <button
                    wire:click="setTab('syarat')"
                    class="px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors border-b-2 {{ $activeTab === 'syarat' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
                >
                    Persyaratan Berkas
                </button>
                <button
                    wire:click="setTab('alur')"
                    class="px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors border-b-2 {{ $activeTab === 'alur' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
                >
                    Alur Pelayanan
                </button>
                <button
                    wire:click="setTab('faq')"
                    class="px-4 py-2 text-sm font-semibold whitespace-nowrap transition-colors border-b-2 {{ $activeTab === 'faq' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
                >
                    Tanya Jawab (FAQ)
                </button>
            </div>

            <!-- Tab Content: Tentang Layanan -->
            @if($activeTab === 'tentang')
                <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 custom-shadow-card space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-inverse-surface mb-3">Deskripsi Layanan</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            @if(strtolower($slug) === 'dtsen')
                                Surat Keterangan DTSEN diterbitkan oleh Dinas Sosial Kabupaten Blitar untuk menerangkan keberadaan dan peringkat kesejahteraan sosial ekonomi (desil 1 hingga desil 10) dari suatu keluarga atau individu yang tercatat dalam pangkalan data nasional Data Tunggal Sosial Ekonomi Nasional (DTSEN / SIKS-NG Kementerian Sosial RI).
                            @elseif(strtolower($slug) === 'pbi')
                                Layanan Fasilitasi Reaktivasi JKN-KIS PBI-JK bertujuan membantu masyarakat tidak mampu atau yang sedang dalam kondisi darurat medis untuk mengaktifkan kembali kartu BPJS Kesehatan Penerima Bantuan Iuran yang nonaktif.
                            @else
                                Pelayanan rehabilitasi sosial memberikan penanganan terpadu mulai dari asesmen, pendampingan, hingga rujukan ke balai/panti sosial yang kompeten bagi lansia terlantar, disabilitas, anak, dan penyandang masalah sosial lainnya.
                            @endif
                        </p>
                    </div>

                    <!-- Ketentuan Penting Notice Box -->
                    <div class="rounded-xl bg-amber-50/80 border-l-4 border-status-warning p-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-status-warning mt-0.5">info</span>
                            <div class="text-xs text-amber-900 leading-relaxed">
                                <span class="font-bold">Ketentuan Penting:</span>
                                @if(strtolower($slug) === 'dtsen')
                                    Surat hanya dapat diterbitkan apabila desil hasil pengecekan di sistem SIKS-NG memenuhi batas desil maksimal yang telah ditetapkan untuk tujuan penggunaan yang dipilih. Seluruh surat terbit dilengkapi kode verifikasi unik dan QR Code anti-pemalsuan.
                                @elseif(strtolower($slug) === 'pbi')
                                    Pengajuan dengan alasan medis darurat (misal rawat inap) wajib melampirkan Surat Keterangan Rawat/Medis dari fasilitas kesehatan resmi agar dapat diprioritaskan pemeriksaannya oleh petugas.
                                @else
                                    Seluruh laporan kasus yang masuk akan ditindaklanjuti dengan asesmen mendalam oleh pekerja sosial atau staf penelaah teknis sebelum penanganan rujukan diputuskan.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab Content: Tujuan & Desil (DTSEN specific) -->
            @if($activeTab === 'tujuan' && strtolower($slug) === 'dtsen')
                <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 custom-shadow-card">
                    <h3 class="text-lg font-bold text-inverse-surface mb-2">Tujuan Penggunaan &amp; Ketentuan Desil</h3>
                    <p class="text-xs text-on-surface-variant mb-6">
                        Setiap instansi/kegiatan memiliki batas desil maksimal penerimaan surat sesuai regulasi:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($purposes as $purpose)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-bold text-inverse-surface">{{ $purpose->name }}</h4>
                                        <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 text-[11px] font-bold">
                                            Maks. Desil {{ $purpose->max_decile }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mb-3">
                                        Surat keterangan dapat diproses jika pemohon terdaftar di DTSEN dengan peringkat Desil 1 s.d. Desil {{ $purpose->max_decile }}.
                                    </p>
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1 pt-2 border-t border-slate-200">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    <span>Masa berlaku: {{ $purpose->validity_days ? $purpose->validity_days . ' hari' : '1 semester' }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">Tidak ada data tujuan penggunaan.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Tab Content: Persyaratan Berkas -->
            @if($activeTab === 'syarat')
                <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 custom-shadow-card">
                    <h3 class="text-lg font-bold text-inverse-surface mb-2">Persyaratan Dokumen yang Wajib Disiapkan</h3>
                    <p class="text-xs text-on-surface-variant mb-6">
                        Pastikan dokumen pendukung berformat foto (JPG/PNG) atau PDF yang terbaca jelas, ukuran maksimal 2 MB per berkas:
                    </p>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50">
                            <span class="material-symbols-outlined text-status-success mt-0.5">verified</span>
                            <div>
                                <h4 class="text-sm font-bold text-inverse-surface">Kartu Tanda Penduduk (KTP) Pemohon</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Foto asli KTP-el pemohon/orang tua yang jelas dan tidak buram/terpotong.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50">
                            <span class="material-symbols-outlined text-status-success mt-0.5">verified</span>
                            <div>
                                <h4 class="text-sm font-bold text-inverse-surface">Kartu Keluarga (KK) Kabupaten Blitar</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Scan/foto KK asli untuk mencocokkan Nomor KK dan hubungan keluarga.</p>
                            </div>
                        </div>

                        @if(strtolower($slug) === 'pbi')
                            <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50">
                                <span class="material-symbols-outlined text-status-success mt-0.5">verified</span>
                                <div>
                                    <h4 class="text-sm font-bold text-inverse-surface">Kartu BPJS Kesehatan / KIS</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Foto fisik kartu BPJS Kesehatan atau tangkapan layar kartu digital JKN.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50">
                                <span class="material-symbols-outlined text-status-warning mt-0.5">medical_information</span>
                                <div>
                                    <h4 class="text-sm font-bold text-inverse-surface">Surat Keterangan Rawat dari Faskes (Khusus Medis Darurat)</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Wajib melampirkan surat rawat inap/keterangan dokter bagi pasien kritis/kronis.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Tab Content: Alur Pelayanan -->
            @if($activeTab === 'alur')
                <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 custom-shadow-card">
                    <h3 class="text-lg font-bold text-inverse-surface mb-6">Tahapan Proses Pelayanan</h3>

                    <div class="relative pl-6 border-l-2 border-teal-200 space-y-8">
                        <!-- Step 1 -->
                        <div class="relative">
                            <div class="absolute -left-[31px] top-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                1
                            </div>
                            <h4 class="text-sm font-bold text-inverse-surface">Pengajuan Online</h4>
                            <p class="text-xs text-slate-500 mt-1">Pemohon mengisi formulir digital dan mengunggah dokumen persyaratan melalui portal SAPA SOSIAL.</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative">
                            <div class="absolute -left-[31px] top-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                2
                            </div>
                            <h4 class="text-sm font-bold text-inverse-surface">Pemeriksaan Kelengkapan Berkas</h4>
                            <p class="text-xs text-slate-500 mt-1">Petugas Dinsos meneliti kejelasan dan kesesuaian dokumen yang diunggah. Jika kurang lengkap, pemohon diminta perbaikan.</p>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative">
                            <div class="absolute -left-[31px] top-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                3
                            </div>
                            <h4 class="text-sm font-bold text-inverse-surface">Pengecekan Data SIKS-NG / Verifikasi</h4>
                            <p class="text-xs text-slate-500 mt-1">Petugas memeriksa status pendaftaran dan desil pemohon di pangkalan data nasional SIKS-NG.</p>
                        </div>

                        <!-- Step 4 -->
                        <div class="relative">
                            <div class="absolute -left-[31px] top-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                4
                            </div>
                            <h4 class="text-sm font-bold text-inverse-surface">Persetujuan Pejabat Penandatangan</h4>
                            <p class="text-xs text-slate-500 mt-1">Pemeriksaan berjenjang (paraf Kepala Bidang dan tanda tangan elektronik Kepala Dinas).</p>
                        </div>

                        <!-- Step 5 -->
                        <div class="relative">
                            <div class="absolute -left-[31px] top-0 w-6 h-6 rounded-full bg-status-success text-white flex items-center justify-center text-xs font-bold ring-4 ring-white">
                                5
                            </div>
                            <h4 class="text-sm font-bold text-inverse-surface">Penerbitan Surat &amp; Unduh Hasil</h4>
                            <p class="text-xs text-slate-500 mt-1">Surat terbit resmi dengan kode verifikasi/QR dan dapat diunduh pemohon secara langsung.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tab Content: FAQ -->
            @if($activeTab === 'faq')
                <div class="bg-white rounded-2xl p-6 md:p-8 border border-slate-200 custom-shadow-card">
                    <h3 class="text-lg font-bold text-inverse-surface mb-6">Pertanyaan Terkait Layanan Ini</h3>

                    <div class="space-y-4">
                        @foreach ($faqs as $faq)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                                <h4 class="text-sm font-bold text-inverse-surface mb-1">{{ $faq->question }}</h4>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $faq->answer }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT 4 COLUMNS: Sticky Summary & Sidebar Box -->
        <div class="lg:col-span-4 flex flex-col gap-6 sticky top-24">
            <!-- Summary Info Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 custom-shadow-card">
                <h3 class="text-base font-bold text-inverse-surface mb-4 pb-3 border-b border-slate-100">
                    Informasi Pelayanan
                </h3>

                <div class="space-y-4 text-xs">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-lg text-primary mt-0.5">schedule</span>
                        <div>
                            <span class="text-slate-400 block">Waktu / Durasi Proses</span>
                            <span class="font-semibold text-slate-800">{{ $service?->sla_days ?? '1-2' }} Hari Kerja</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-lg text-primary mt-0.5">payments</span>
                        <div>
                            <span class="text-slate-400 block">Biaya / Tarif Layanan</span>
                            <span class="font-bold text-emerald-600 uppercase">Gratis (Rp 0,-)</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-lg text-primary mt-0.5">location_on</span>
                        <div>
                            <span class="text-slate-400 block">Lokasi Pelayanan</span>
                            <span class="font-semibold text-slate-800">Dinas Sosial Kabupaten Blitar</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jl. Mojopahit No. 5, Kota Blitar</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-lg text-primary mt-0.5">timer</span>
                        <div>
                            <span class="text-slate-400 block">Jam Operasional</span>
                            <span class="font-semibold text-slate-800">Senin &ndash; Jumat, 07.30 &ndash; 15.30 WIB</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col gap-3">
                    <a href="{{ $applyRoute }}" wire:navigate class="w-full py-3 rounded-xl bg-primary text-white text-center font-semibold text-xs hover:bg-primary-container transition-colors shadow-xs flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-base">edit_document</span>
                        <span>Mulai Buat Pengajuan</span>
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="w-full py-2.5 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-emerald-600">chat</span>
                        <span>Konsultasi Petugas</span>
                    </a>
                </div>
            </div>

            <!-- Privacy Guarantee Badge -->
            <div class="p-4 rounded-xl bg-surface-container-low border border-secondary-container/50 flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary text-2xl">verified_user</span>
                <p class="text-[11px] text-slate-600 leading-tight">
                    Data pribadi (NIK &amp; KK) dijaga kerahasiaannya dan hanya dipergunakan untuk keperluan verifikasi pelayanan dinas.
                </p>
            </div>
        </div>
    </div>
</div>
