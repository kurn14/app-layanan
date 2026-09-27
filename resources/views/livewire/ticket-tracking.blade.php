<div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Lacak Status Pengajuan</span>
    </nav>

    <!-- Header Box & Search Form -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8">
        <div class="mb-6">
            <span class="text-xs font-bold uppercase tracking-wider text-primary">Transparansi Pelayanan</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface mt-1">Lacak Status Pengajuan &amp; Tiket</h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-1">
                Ketahui tahapan berkas permohonan atau laporan sosial Anda kapan saja secara transparan.
            </p>
        </div>

        <form wire:submit="track" class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
                <label class="block text-xs font-semibold text-inverse-surface mb-1.5" for="ticket">
                    Nomor Tiket Permohonan / Laporan <span class="text-status-error">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-lg">confirmation_number</span>
                    </span>
                    <input
                        wire:model="ticket"
                        type="text"
                        placeholder="Contoh: DTSEN-202609-00001"
                        class="w-full min-h-[46px] pl-10 pr-4 rounded-xl border border-slate-300 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-primary uppercase"
                        required
                    />
                </div>
                @error('ticket') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-inverse-surface mb-1.5" for="pin">
                    4 Digit Akhir NIK / HP <span class="text-status-error">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <span class="material-symbols-outlined text-lg">pin</span>
                    </span>
                    <input
                        wire:model="pin"
                        type="text"
                        maxlength="4"
                        placeholder="XXXX"
                        class="w-full min-h-[46px] pl-10 pr-4 rounded-xl border border-slate-300 font-mono text-center text-sm focus:outline-none focus:ring-2 focus:ring-primary tracking-widest"
                        required
                    />
                </div>
                @error('pin') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-3 flex items-end">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full min-h-[46px] px-5 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove wire:target="track" class="material-symbols-outlined text-lg">search</span>
                    <span wire:loading wire:target="track" class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="track">Lacak Tiket</span>
                    <span wire:loading wire:target="track">Mencari...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- SEARCH ERROR ALERT -->
    @if ($searchError)
        <div class="rounded-2xl bg-red-50 border border-red-200 p-6 mb-8 text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 text-status-error flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl">error</span>
            </div>
            <h3 class="text-sm font-bold text-red-900 mb-1">Tiket Tidak Dapat Ditampilkan</h3>
            <p class="text-xs text-red-700 max-w-lg mx-auto">{{ $searchError }}</p>
        </div>
    @endif

    <!-- RESULT CANVAS -->
    @if ($serviceRequest)
        @php
            $statusEnum = $serviceRequest->status;
            $statusValue = is_object($statusEnum) ? $statusEnum->value : $statusEnum;

            // Step index calculator
            $stepIndex = match($statusValue) {
                'submitted' => 1,
                'document_check' => 2,
                'revision_requested' => 2,
                'data_verification', 'verification', 'eligibility_verification' => 3,
                'awaiting_approval' => 4,
                'issued', 'completed', 'reactivated' => 5,
                'rejected' => 3,
                default => 1,
            };

            $isCompleted = in_array($statusValue, ['issued', 'completed', 'reactivated']);
            $isRevision = ($statusValue === 'revision_requested');
            $isRejected = ($statusValue === 'rejected');
        @endphp

        <div class="space-y-8">
            <!-- TICKET SUMMARY CARD -->
            <div class="bg-white rounded-2xl border-2 {{ $isCompleted ? 'border-emerald-300' : ($isRevision ? 'border-amber-300' : ($isRejected ? 'border-red-300' : 'border-teal-200')) }} custom-shadow-card p-6 md:p-8 relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                {{ $serviceRequest->serviceType?->name }}
                            </span>
                            <span class="text-xs text-slate-400">&bull; Diajukan {{ $serviceRequest->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <h2 class="text-2xl font-bold font-mono text-primary tracking-wide">
                            {{ $serviceRequest->request_number }}
                        </h2>
                    </div>

                    <!-- Status Pill -->
                    <div>
                        @if($isCompleted)
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs shadow-xs">
                                <span class="material-symbols-outlined text-base">verified</span>
                                <span>Permohonan Selesai / Terbit</span>
                            </div>
                        @elseif($isRevision)
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-100 text-amber-900 font-bold text-xs shadow-xs">
                                <span class="material-symbols-outlined text-base">warning</span>
                                <span>Perlu Perbaikan Dokumen</span>
                            </div>
                        @elseif($isRejected)
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-100 text-red-900 font-bold text-xs shadow-xs">
                                <span class="material-symbols-outlined text-base">cancel</span>
                                <span>Pengajuan Ditolak</span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-100 text-primary font-bold text-xs shadow-xs">
                                <span class="material-symbols-outlined text-base animate-spin">sync</span>
                                <span>Sedang Diproses Petugas</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Applicant Data & Subject with privacy masking -->
                <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Pemohon (Dimask):</span>
                        @php
                            $parts = explode(' ', $serviceRequest->applicant_name);
                            $maskedName = $parts[0] . ' ' . (isset($parts[1]) ? substr($parts[1], 0, 1) . '****' : '***');
                        @endphp
                        <span class="font-bold text-slate-800">{{ $maskedName }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-1">NIK Pemohon:</span>
                        <span class="font-mono text-slate-700">
                            {{ substr($serviceRequest->applicant_nik, 0, 4) . '********' . substr($serviceRequest->applicant_nik, -4) }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-1">Kecamatan:</span>
                        <span class="font-semibold text-slate-800">{{ $serviceRequest->village?->district?->name ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-1">Desa / Kelurahan:</span>
                        <span class="font-semibold text-slate-800">{{ $serviceRequest->village?->name ?? '-' }}</span>
                    </div>
                </div>

                @if($serviceRequest->officer_notes)
                    <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                        <span class="font-bold text-slate-700 block mb-1">Catatan Petugas Verifikator:</span>
                        <p class="text-slate-600 leading-relaxed">{{ $serviceRequest->officer_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- PROGRESS TIMELINE STEPPER -->
            <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-6">
                    Perjalanan Tahapan Dokumen
                </h3>

                <div class="relative flex flex-col md:flex-row items-start justify-between gap-6 md:gap-2">
                    <!-- Step 1 -->
                    <div class="flex items-center md:flex-col md:items-center gap-3 md:gap-2 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex >= 1 ? 'bg-status-success text-white ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-400' }}">
                            <span class="material-symbols-outlined text-base">check</span>
                        </div>
                        <div class="md:text-center">
                            <span class="text-xs font-bold block text-slate-800">1. Pengajuan Masuk</span>
                            <span class="text-[11px] text-slate-400">Berkas terdaftar di sistem</span>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-center md:flex-col md:items-center gap-3 md:gap-2 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex > 2 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($stepIndex === 2 ? ($isRevision ? 'bg-status-warning text-white ring-4 ring-amber-50' : 'bg-primary text-white ring-4 ring-teal-50') : 'bg-slate-100 text-slate-400') }}">
                            @if($stepIndex > 2) <span class="material-symbols-outlined text-base">check</span> @else 2 @endif
                        </div>
                        <div class="md:text-center">
                            <span class="text-xs font-bold block {{ $stepIndex === 2 ? 'text-primary' : 'text-slate-800' }}">2. Periksa Berkas</span>
                            <span class="text-[11px] text-slate-400">{{ $isRevision ? 'Menunggu perbaikan' : 'Kelengkapan KTP &amp; KK' }}</span>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-center md:flex-col md:items-center gap-3 md:gap-2 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex > 3 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($stepIndex === 3 ? 'bg-primary text-white ring-4 ring-teal-50' : 'bg-slate-100 text-slate-400') }}">
                            @if($stepIndex > 3) <span class="material-symbols-outlined text-base">check</span> @else 3 @endif
                        </div>
                        <div class="md:text-center">
                            <span class="text-xs font-bold block {{ $stepIndex === 3 ? 'text-primary' : 'text-slate-800' }}">3. Verifikasi SIKS-NG</span>
                            <span class="text-[11px] text-slate-400">Cek desil / kelayakan</span>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex items-center md:flex-col md:items-center gap-3 md:gap-2 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $stepIndex > 4 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($stepIndex === 4 ? 'bg-primary text-white ring-4 ring-teal-50' : 'bg-slate-100 text-slate-400') }}">
                            @if($stepIndex > 4) <span class="material-symbols-outlined text-base">check</span> @else 4 @endif
                        </div>
                        <div class="md:text-center">
                            <span class="text-xs font-bold block {{ $stepIndex === 4 ? 'text-primary' : 'text-slate-800' }}">4. Paraf &amp; Tanda Tangan</span>
                            <span class="text-[11px] text-slate-400">Persetujuan Kepala Dinas</span>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="flex items-center md:flex-col md:items-center gap-3 md:gap-2 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-xs {{ $isCompleted ? 'bg-status-success text-white ring-4 ring-emerald-50' : 'bg-slate-100 text-slate-400' }}">
                            @if($isCompleted) <span class="material-symbols-outlined text-base">check</span> @else 5 @endif
                        </div>
                        <div class="md:text-center">
                            <span class="text-xs font-bold block {{ $isCompleted ? 'text-status-success' : 'text-slate-800' }}">5. Surat Selesai</span>
                            <span class="text-[11px] text-slate-400">Dokumen siap diunduh</span>
                        </div>
                    </div>
                </div>

                <!-- If completed: download button -->
                @if($isCompleted && $serviceRequest->dtsenCertificate?->certificate_number)
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-emerald-50/70 p-4 rounded-xl border border-emerald-200">
                        <div>
                            <span class="text-xs text-emerald-800 font-bold block">Surat Keterangan Resmi Telah Diterbitkan</span>
                            <span class="text-xs text-emerald-700">Nomor Surat: <strong>{{ $serviceRequest->dtsenCertificate->certificate_number }}</strong></span>
                        </div>
                        <a href="{{ route('verifikasi', ['code' => $serviceRequest->dtsenCertificate->verification_code]) }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-emerald-700 text-white font-semibold text-xs hover:bg-emerald-800 transition-colors shadow-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">verified</span>
                            <span>Lihat Surat &amp; Verifikasi QR</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- AUDIT HISTORY -->
            @if($serviceRequest->statusHistories->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-4">
                        Riwayat Aktivitas &amp; Penanganan
                    </h3>
                    <div class="space-y-3">
                        @foreach ($serviceRequest->statusHistories as $history)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <span class="material-symbols-outlined text-primary mt-0.5">history</span>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-800 capitalize">{{ str_replace('_', ' ', $history->to_status) }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $history->created_at->format('d M Y, H:i') }} WIB</span>
                                    </div>
                                    <p class="text-slate-600 mt-0.5">{{ $history->notes }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @elseif ($complaint)
        <!-- COMPLAINT TRACKING RESULT -->
        <div class="space-y-8">
            <div class="bg-white rounded-2xl border-2 border-red-200 custom-shadow-card p-6 md:p-8">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-bold">
                            {{ $complaint->complaintCategory?->name }}
                        </span>
                        <h2 class="text-2xl font-bold font-mono text-status-error tracking-wide mt-1">
                            {{ $complaint->complaint_number }}
                        </h2>
                    </div>

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-status-error font-bold text-xs border border-red-200">
                        <span class="material-symbols-outlined text-base">campaign</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $complaint->status->value)) }}</span>
                    </div>
                </div>

                <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Pelapor (Dimask):</span>
                        @php
                            $parts = explode(' ', $complaint->reporter_name);
                            $maskedName = $parts[0] . ' ' . (isset($parts[1]) ? substr($parts[1], 0, 1) . '****' : '***');
                        @endphp
                        <span class="font-bold text-slate-800">{{ $maskedName }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Lokasi Kejadian:</span>
                        <span class="font-semibold text-slate-800">{{ $complaint->location_detail }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Wilayah:</span>
                        <span class="font-semibold text-slate-800">{{ $complaint->village?->name }}, Kec. {{ $complaint->village?->district?->name }}</span>
                    </div>
                </div>

                @if($complaint->attachments->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <span class="text-slate-600 font-bold text-xs mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-primary">photo_library</span>
                            Foto &amp; Lampiran Pendukung:
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($complaint->attachments as $att)
                                @php
                                    $ext = strtolower(pathinfo($att->file_path, PATHINFO_EXTENSION));
                                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    $url = asset('storage/' . $att->file_path);
                                    $fileName = basename($att->file_path);
                                @endphp
                                <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50 p-2 flex flex-col gap-2 shadow-2xs hover:shadow-xs transition">
                                    @if($isImg)
                                        <div class="aspect-video w-full rounded-lg overflow-hidden bg-slate-200">
                                            <img src="{{ $url }}" alt="Bukti Lampiran" class="w-full h-full object-cover" loading="lazy" />
                                        </div>
                                    @else
                                        <div class="aspect-video w-full rounded-lg bg-slate-200 flex flex-col items-center justify-center text-slate-500 gap-1">
                                            <span class="material-symbols-outlined text-3xl">description</span>
                                            <span class="text-[10px] uppercase font-bold text-slate-400">{{ $ext ?: 'DOKUMEN' }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between text-xs px-1">
                                        <span class="font-medium text-slate-700 truncate max-w-[140px] font-mono text-[11px]" title="{{ $fileName }}">{{ $fileName }}</span>
                                        <a href="{{ $url }}" target="_blank" class="text-primary hover:underline font-semibold flex items-center gap-0.5 text-xs">
                                            <span>Buka</span>
                                            <span class="material-symbols-outlined text-xs">open_in_new</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($complaint->action_taken)
                    <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                        <span class="font-bold text-emerald-900 block mb-1">Tindakan Penanganan Petugas:</span>
                        <p class="text-emerald-800 leading-relaxed">{{ $complaint->action_taken }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
