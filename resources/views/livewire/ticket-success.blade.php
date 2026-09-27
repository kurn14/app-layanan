<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <div class="text-center mb-8">
        <!-- Success Icon Pulse -->
        <div class="w-20 h-20 rounded-full bg-emerald-100 text-status-success flex items-center justify-center mx-auto mb-4 ring-8 ring-emerald-50">
            <span class="material-symbols-outlined text-4xl">check_circle</span>
        </div>
        <span class="text-xs font-bold uppercase tracking-wider text-status-success">Pengajuan Diterima Sistem</span>
        <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface mt-1">
            Permohonan Anda Berhasil Terkirim!
        </h1>
        <p class="text-xs md:text-sm text-on-surface-variant max-w-lg mx-auto mt-2 leading-relaxed">
            Berkas dan data Anda telah masuk ke dalam antrean sistem verifikasi Dinas Sosial Kabupaten Blitar.
        </p>
    </div>

    <!-- TICKET CARD -->
    <div class="bg-white rounded-2xl border-2 border-teal-200 custom-shadow-card p-6 md:p-8 mb-8 relative overflow-hidden" x-data="{ copied: false }">
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-primary to-status-warning"></div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase tracking-wider">Nomor Tiket Anda</span>
                <span class="text-2xl md:text-3xl font-bold font-mono text-primary tracking-wider mt-1 block">
                    {{ $ticketNumber }}
                </span>
                <span class="text-xs text-slate-500 mt-1 block">Simpan nomor tiket ini untuk memantau perkembangan permohonan.</span>
            </div>

            <button
                type="button"
                @click="navigator.clipboard.writeText('{{ $ticketNumber }}'); copied = true; setTimeout(() => copied = false, 2500)"
                class="px-5 py-2.5 rounded-xl border border-teal-300 text-primary font-semibold text-xs hover:bg-teal-50 transition-colors flex items-center gap-2 shadow-xs"
            >
                <span class="material-symbols-outlined text-base" x-text="copied ? 'check' : 'content_copy'"></span>
                <span x-text="copied ? 'Tersalin!' : 'Salin Nomor Tiket'"></span>
            </button>
        </div>

        <!-- Detail Breakdown -->
        <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Jenis Layanan:</span>
                <span class="font-bold text-slate-800">
                    @if($isComplaint)
                        Pengaduan &amp; Laporan Sosial
                    @else
                        {{ $record?->serviceType?->name ?? 'Layanan Sosial' }}
                    @endif
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Nama Pemohon:</span>
                <span class="font-semibold text-slate-800">
                    @if($record)
                        @php
                            $name = $isComplaint ? $record->reporter_name : $record->applicant_name;
                            $parts = explode(' ', $name);
                            $masked = $parts[0] . ' ' . (isset($parts[1]) ? substr($parts[1], 0, 1) . '****' : '***');
                        @endphp
                        {{ $masked }}
                    @else
                        -
                    @endif
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Waktu Masuk:</span>
                <span class="font-semibold text-slate-800">
                    {{ $record ? $record->created_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Estimasi Proses:</span>
                <span class="font-bold text-primary">
                    {{ $isComplaint ? '1-3 Hari Kerja' : '1-2 Hari Kerja' }}
                </span>
            </div>
        </div>
    </div>

    <!-- NEXT STEPS BOX -->
    <div class="bg-surface-container-low rounded-2xl p-6 border border-secondary-container/60 mb-8">
        <h3 class="text-sm font-bold text-inverse-surface mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-base text-primary">info</span>
            Tahapan Selanjutnya:
        </h3>
        <ul class="space-y-2.5 text-xs text-slate-600">
            <li class="flex items-start gap-2">
                <span class="material-symbols-outlined text-sm text-status-success mt-0.5">check_circle</span>
                <span><strong>1. Pemeriksaan Berkas:</strong> Petugas verifikator Dinas Sosial akan memeriksa keaslian dan kelengkapan dokumen yang diunggah.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="material-symbols-outlined text-sm text-status-success mt-0.5">check_circle</span>
                <span><strong>2. Pemberitahuan:</strong> Jika ada berkas yang kurang jelas atau perlu perbaikan, status tiket akan berubah menjadi <em>Perbaikan Diminta</em>.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="material-symbols-outlined text-sm text-status-success mt-0.5">check_circle</span>
                <span><strong>3. Surat Terbit / Hasil:</strong> Setelah disetujui, surat atau rekomendasi resmi dapat diunduh langsung lewat halaman Cek Status Tiket.</span>
            </li>
        </ul>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('cek-status', ['ticket' => $ticketNumber]) }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container transition-all shadow-xs">
            <span class="material-symbols-outlined text-lg">manage_search</span>
            <span>Lacak Status Tiket Ini</span>
        </a>
        <a href="{{ route('home') }}" wire:navigate class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-white transition-colors">
            <span class="material-symbols-outlined text-lg">home</span>
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</div>
