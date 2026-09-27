<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Verifikasi Dokumen Resmi</span>
    </nav>

    <!-- Header Box & Form -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8">
        <div class="mb-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold mb-2">
                <span class="material-symbols-outlined text-sm">verified</span>
                Pemeriksaan Anti-Pemalsuan
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface mt-1">Verifikasi Keaslian Surat DTSEN</h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-1 leading-relaxed">
                Pihak sekolah, perguruan tinggi (SPMB / KIP Kuliah), perbankan, atau instansi penyalur bantuan dapat memvalidasi keabsahan dokumen Surat Keterangan DTSEN resmi terbitan Dinas Sosial Kabupaten Blitar.
            </p>
        </div>

        <form wire:submit="verify" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-lg">qr_code_scanner</span>
                </span>
                <input
                    wire:model="code"
                    type="text"
                    placeholder="Contoh: DTSEN-XXXXXXXXXX atau Nomor Surat"
                    class="w-full min-h-[48px] pl-10 pr-4 rounded-xl border border-slate-300 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-primary uppercase shadow-xs"
                    required
                />
            </div>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="min-h-[48px] px-7 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center justify-center gap-2 whitespace-nowrap"
            >
                <span wire:loading.remove wire:target="verify" class="material-symbols-outlined text-lg">verified_user</span>
                <span wire:loading wire:target="verify" class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="verify">Periksa Dokumen</span>
                <span wire:loading wire:target="verify">Memeriksa...</span>
            </button>
        </form>
        @error('code') <p class="text-xs text-status-error mt-2">{{ $message }}</p> @enderror
    </div>

    <!-- RESULT CERTIFICATE STATE -->
    @if ($hasChecked)
        @if ($certificate && ! $isExpired)
            <!-- VALID & GENUINE DOCUMENT -->
            <div class="bg-white rounded-2xl border-2 border-emerald-300 custom-shadow-card p-6 md:p-10 mb-8 relative overflow-hidden">
                <!-- Top Emerald Banner -->
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-5 mb-8 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-emerald-100 text-status-success flex items-center justify-center flex-shrink-0 ring-4 ring-emerald-50">
                        <span class="material-symbols-outlined text-3xl">verified</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Status Validasi</span>
                        <h2 class="text-lg md:text-xl font-bold text-emerald-950">DOKUMEN ASLI, RESMI &amp; SAH</h2>
                        <p class="text-xs text-emerald-800 mt-0.5">Surat Keterangan ini terdaftar secara sah dalam pangkalan data Dinas Sosial Kabupaten Blitar.</p>
                    </div>
                </div>

                <!-- Certificate Metadata Details -->
                <div class="space-y-6 text-sm">
                    <div class="pb-6 border-b border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-xs text-slate-400 block mb-1">Nomor Surat Resmi:</span>
                            <span class="font-bold text-slate-900 font-mono text-base">{{ $certificate->certificate_number ?? '400.9/DTSEN/' . $certificate->id . '/2026' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block mb-1">Kode Verifikasi QR:</span>
                            <span class="font-bold text-primary font-mono text-base">{{ $certificate->verification_code }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pb-6 border-b border-slate-100">
                        <div>
                            <span class="text-xs text-slate-400 block mb-1">Tanggal Diterbitkan:</span>
                            <span class="font-semibold text-slate-800">
                                {{ $certificate->issued_at ? $certificate->issued_at->format('d F Y') : now()->format('d F Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block mb-1">Masa Berlaku Sampai:</span>
                            <span class="font-semibold text-emerald-700">
                                {{ $certificate->valid_until ? $certificate->valid_until->format('d F Y') : '1 Semester (Aktif)' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block mb-1">Tujuan Penggunaan:</span>
                            <span class="font-semibold text-slate-800">{{ $certificate->dtsenPurpose?->name ?? 'SPMB / KIP Kuliah' }}</span>
                        </div>
                    </div>

                    <!-- Subject Data & Decile -->
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700 block mb-3">Keterangan Subjek Terdaftar</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 block mb-0.5">Nama Subjek:</span>
                                @php
                                    $parts = explode(' ', $certificate->subject_name);
                                    $maskedSubject = $parts[0] . ' ' . (isset($parts[1]) ? substr($parts[1], 0, 1) . '****' : '***');
                                @endphp
                                <span class="font-bold text-slate-900 text-sm">{{ $maskedSubject }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">NIK (Masked):</span>
                                <span class="font-mono text-slate-800">
                                    {{ substr($certificate->subject_nik, 0, 4) . '********' . substr($certificate->subject_nik, -4) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Status Peringkat DTSEN:</span>
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-800 font-bold">
                                    Terdaftar &bull; Desil {{ $certificate->decile ?? '1-3' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Signer info -->
                    <div class="flex items-center justify-between pt-4">
                        <div class="text-xs">
                            <span class="text-slate-400 block">Pejabat Penandatangan:</span>
                            <span class="font-bold text-slate-800">Kepala Dinas Sosial Kabupaten Blitar</span>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-100/70 text-emerald-800 text-xs font-semibold">
                            <span class="material-symbols-outlined text-sm">qr_code</span>
                            <span>Tanda Tangan Elektronik Valid</span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($certificate && $isExpired)
            <!-- EXPIRED DOCUMENT -->
            <div class="bg-white rounded-2xl border-2 border-amber-300 custom-shadow-card p-6 md:p-8 mb-8 text-center">
                <div class="w-14 h-14 rounded-full bg-amber-100 text-status-warning flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">history_toggle_off</span>
                </div>
                <h3 class="text-lg font-bold text-amber-950">DOKUMEN TELAH MELEBIHI MASA BERLAKU (KEDALUWARSA)</h3>
                <p class="text-xs text-amber-800 max-w-md mx-auto mt-2 leading-relaxed">
                    Surat Keterangan DTSEN dengan nomor <strong>{{ $certificate->certificate_number }}</strong> memang pernah diterbitkan secara sah, namun masa berlakunya telah berakhir pada tanggal {{ $certificate->valid_until?->format('d F Y') }}.
                </p>
                <div class="mt-6">
                    <a href="{{ route('pengajuan.dtsen') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary-container">
                        Ajukan Surat Baru
                    </a>
                </div>
            </div>
        @else
            <!-- NOT FOUND / INVALID -->
            <div class="bg-white rounded-2xl border-2 border-red-300 custom-shadow-card p-6 md:p-8 mb-8 text-center">
                <div class="w-14 h-14 rounded-full bg-red-100 text-status-error flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">gpp_bad</span>
                </div>
                <h3 class="text-lg font-bold text-red-950">KODE TIDAK DITEMUKAN / DOKUMEN TIDAK TERDAFTAR</h3>
                <p class="text-xs text-red-700 max-w-md mx-auto mt-2 leading-relaxed">
                    Kode verifikasi atau nomor surat <strong>"{{ $code }}"</strong> tidak ada di pangkalan data Dinas Sosial Kabupaten Blitar. Waspadai indikasi pemalsuan dokumen.
                </p>
            </div>
        @endif
    @endif
</div>
