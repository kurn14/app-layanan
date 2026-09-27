<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <a href="{{ route('layanan.index') }}" wire:navigate class="hover:text-primary transition-colors">Layanan</a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <a href="{{ route('layanan.detail', 'pbi') }}" wire:navigate class="hover:text-primary transition-colors">Reaktivasi KIS / PBI-JK</a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Formulir Pengajuan</span>
    </nav>

    <!-- Page Title & Progress Stepper -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8">
        <div class="mb-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold mb-2">
                <span class="material-symbols-outlined text-sm">health_and_safety</span>
                Jaminan Kesehatan Masyarakat
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface mt-1">Form Pengajuan Reaktivasi KIS / PBI-JK</h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-1">
                Fasilitasi pengaktifan kembali kepesertaan BPJS Kesehatan PBI bagi warga Kabupaten Blitar.
            </p>
        </div>

        <!-- Stepper Indicator -->
        <div class="max-w-4xl mx-auto pt-2 pb-4">
            <div class="relative flex items-center justify-between">
                <div class="absolute top-1/2 left-6 right-6 -translate-y-1/2 h-1 bg-slate-200 z-0"></div>
                <div class="absolute top-1/2 left-6 -translate-y-1/2 h-1 bg-secondary z-0 transition-all duration-300"
                    style="width: {{ match($step) { 1 => '0%', 2 => '33.3%', 3 => '66.6%', 4 => '100%', default => '0%' } }}">
                </div>

                <!-- Step 1 -->
                <button type="button" wire:click="goToStep(1)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step > 1 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($step === 1 ? 'bg-secondary text-white ring-4 ring-blue-50' : 'bg-white border-2 border-slate-300 text-slate-500') }}">
                        @if($step > 1) <span class="material-symbols-outlined text-lg">check</span> @else 1 @endif
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 1 ? 'text-secondary font-bold' : ($step > 1 ? 'text-status-success' : 'text-slate-500') }}">
                        1. Data Pemohon
                    </span>
                </button>

                <!-- Step 2 -->
                <button type="button" wire:click="goToStep(2)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step > 2 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($step === 2 ? 'bg-secondary text-white ring-4 ring-blue-50 pulse-step' : 'bg-white border-2 border-slate-300 text-slate-500') }}">
                        @if($step > 2) <span class="material-symbols-outlined text-lg">check</span> @else 2 @endif
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 2 ? 'text-secondary font-bold' : ($step > 2 ? 'text-status-success' : 'text-slate-500') }}">
                        2. Kepesertaan &amp; Alasan
                    </span>
                </button>

                <!-- Step 3 -->
                <button type="button" wire:click="goToStep(3)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step > 3 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($step === 3 ? 'bg-secondary text-white ring-4 ring-blue-50 pulse-step' : 'bg-white border-2 border-slate-300 text-slate-500') }}">
                        @if($step > 3) <span class="material-symbols-outlined text-lg">check</span> @else 3 @endif
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 3 ? 'text-secondary font-bold' : ($step > 3 ? 'text-status-success' : 'text-slate-500') }}">
                        3. Unggah Berkas
                    </span>
                </button>

                <!-- Step 4 -->
                <button type="button" wire:click="goToStep(4)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step === 4 ? 'bg-secondary text-white ring-4 ring-blue-50' : 'bg-white border-2 border-slate-300 text-slate-500' }}">
                        4
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 4 ? 'text-secondary font-bold' : 'text-slate-500' }}">
                        4. Konfirmasi &amp; Kirim
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Asymmetric 8+4 Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- FORM BODY (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
            <!-- STEP 1: DATA PEMOHON -->
            @if($step === 1)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 1: Identitas Pemohon &amp; Domisili</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Isi data pemohon (orang yang mengajukan permohonan reaktivasi).
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nama Lengkap Pemohon <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="applicant_name"
                                placeholder="Sesuai KTP"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary"
                            />
                            @error('applicant_name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                NIK Pemohon (16 Digit) <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="applicant_nik"
                                maxlength="16"
                                placeholder="3505xxxxxxxxxxxx"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-secondary"
                            />
                            @error('applicant_nik') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nomor Kartu Keluarga (16 Digit) <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="family_card_number"
                                maxlength="16"
                                placeholder="3505xxxxxxxxxxxx"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-secondary"
                            />
                            @error('family_card_number') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nomor HP / WhatsApp Aktif <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="phone"
                                placeholder="08xxxxxxxxxx"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary"
                            />
                            @error('phone') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Kecamatan Domisili <span class="text-status-error">*</span>
                            </label>
                            <select
                                wire:model.live="district_id"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary bg-white"
                            >
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                            @error('district_id') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Desa / Kelurahan <span class="text-status-error">*</span>
                            </label>
                            <select
                                wire:model="village_id"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary bg-white"
                                {{ empty($district_id) ? 'disabled' : '' }}
                            >
                                <option value="">-- Pilih Desa/Kelurahan --</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}">{{ $village->name }}</option>
                                @endforeach
                            </select>
                            @error('village_id') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            Alamat Lengkap (RT/RW/Dusun) <span class="text-status-error">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="address"
                            placeholder="Contoh: RT 01 RW 03 Dusun Mulyojoyo"
                            class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary"
                        />
                        @error('address') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="nextStep"
                            class="px-7 py-3 rounded-xl bg-secondary text-white font-semibold text-sm hover:bg-slate-700 active:scale-95 transition-all shadow-xs flex items-center gap-2"
                        >
                            <span>Lanjut Data Kepesertaan</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- STEP 2: DATA KEPESERTAAN & ALASAN -->
            @if($step === 2)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 2: Data Peserta BPJS &amp; Alasan Reaktivasi</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Cantumkan nomor kartu BPJS/KIS yang dinonaktifkan serta alasan reaktivasi.
                        </p>
                    </div>

                    <!-- Toggle Sama dengan Pemohon -->
                    <div class="flex items-center justify-between mb-4 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-xs font-semibold text-slate-700">Apakah peserta yang ingin direaktivasi adalah pemohon sendiri?</span>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-secondary">
                            <input type="checkbox" wire:model.live="is_same_as_applicant" class="rounded text-secondary focus:ring-secondary" />
                            <span>Ya, Pemohon Sendiri</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nama Peserta BPJS <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="participant_name"
                                placeholder="Sesuai kartu BPJS"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary"
                                {{ $is_same_as_applicant ? 'readonly' : '' }}
                            />
                            @error('participant_name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                NIK Peserta BPJS (16 Digit) <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="participant_nik"
                                maxlength="16"
                                placeholder="3505xxxxxxxxxxxx"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-secondary"
                                {{ $is_same_as_applicant ? 'readonly' : '' }}
                            />
                            @error('participant_nik') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nomor Kartu BPJS / KIS (13 Digit) <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="bpjs_card_number"
                                placeholder="000xxxxxxxxxx"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-secondary"
                            />
                            @error('bpjs_card_number') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Perkiraan Tanggal Nonaktif (Opsional)
                            </label>
                            <input
                                type="date"
                                wire:model="deactivated_date"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary"
                            />
                        </div>
                    </div>

                    <!-- Alasan Reaktivasi -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-inverse-surface mb-2">
                            Alasan Permohonan Reaktivasi <span class="text-status-error">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $reason === 'emergency' ? 'border-secondary bg-blue-50/50 ring-2 ring-secondary/20' : 'border-slate-200' }}">
                                <input type="radio" wire:model.live="reason" value="emergency" class="mt-1 text-secondary focus:ring-secondary" />
                                <div>
                                    <span class="text-xs font-bold text-inverse-surface block text-status-error">Gawat Darurat Medis / Rawat Inap</span>
                                    <span class="text-[11px] text-slate-500">Pasien sedang dirawat atau membutuhkan penanganan segera (Prioritas).</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $reason === 'chronic' ? 'border-secondary bg-blue-50/50 ring-2 ring-secondary/20' : 'border-slate-200' }}">
                                <input type="radio" wire:model.live="reason" value="chronic" class="mt-1 text-secondary focus:ring-secondary" />
                                <div>
                                    <span class="text-xs font-bold text-inverse-surface block">Penyakit Kronis / Katastropik</span>
                                    <span class="text-[11px] text-slate-500">Memerlukan pengobatan rutin jangka panjang (mis. hemodialisa, jantung, kanker).</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $reason === 'newborn' ? 'border-secondary bg-blue-50/50 ring-2 ring-secondary/20' : 'border-slate-200' }}">
                                <input type="radio" wire:model.live="reason" value="newborn" class="mt-1 text-secondary focus:ring-secondary" />
                                <div>
                                    <span class="text-xs font-bold text-inverse-surface block">Bayi Baru Lahir dari Ibu PBI</span>
                                    <span class="text-[11px] text-slate-500">Pendaftaran bayi dari ibu kandung peserta aktif PBI-JK.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer {{ $reason === 'other' ? 'border-secondary bg-blue-50/50 ring-2 ring-secondary/20' : 'border-slate-200' }}">
                                <input type="radio" wire:model.live="reason" value="other" class="mt-1 text-secondary focus:ring-secondary" />
                                <div>
                                    <span class="text-xs font-bold text-inverse-surface block">Keluarga Tidak Mampu / Desil Rendah</span>
                                    <span class="text-[11px] text-slate-500">Usulan reaktivasi rutin berdasarkan status kemiskinan di DTSEN.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Faskes details if medical reason -->
                    @if(in_array($reason, ['emergency', 'chronic']))
                        <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200 mb-8 space-y-4">
                            <span class="text-xs font-bold text-amber-900 block flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-status-warning">local_hospital</span>
                                Data Fasilitas Kesehatan (Faskes Perujuk / Tempat Rawat)
                            </span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                        Nama Rumah Sakit / Puskesmas <span class="text-status-error">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="health_facility_name"
                                        placeholder="Contoh: RSUD Ngudi Waluyo Wlingi"
                                        class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary bg-white"
                                    />
                                    @error('health_facility_name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                        Nomor Surat Keterangan Rawat (Opsional)
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="health_letter_number"
                                        placeholder="Nomor surat dari dokter/RS"
                                        class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-secondary bg-white"
                                    />
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="prevStep"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 flex items-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            <span>Sebelumnya</span>
                        </button>
                        <button
                            type="button"
                            wire:click="nextStep"
                            class="px-7 py-3 rounded-xl bg-secondary text-white font-semibold text-sm hover:bg-slate-700 active:scale-95 transition-all shadow-xs flex items-center gap-2"
                        >
                            <span>Lanjut Unggah Dokumen</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- STEP 3: UNGGAH DOKUMEN -->
            @if($step === 3)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 3: Unggah Berkas Dokumen Pendukung</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Unggah foto/scan dokumen resmi (JPG, PNG, atau PDF maksimal 2 MB per berkas).
                        </p>
                    </div>

                    <!-- Upload KTP -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            1. Foto KTP Pemohon / Peserta <span class="text-status-error">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center bg-slate-50/50">
                            @if ($ktp_file)
                                <p class="text-xs text-status-success font-semibold flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ $ktp_file->getClientOriginalName() }}
                                </p>
                            @endif
                            <input type="file" wire:model="ktp_file" class="text-xs file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-secondary file:text-white cursor-pointer" />
                        </div>
                        @error('ktp_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload KK -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            2. Kartu Keluarga (KK) Kabupaten Blitar <span class="text-status-error">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center bg-slate-50/50">
                            @if ($kk_file)
                                <p class="text-xs text-status-success font-semibold flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ $kk_file->getClientOriginalName() }}
                                </p>
                            @endif
                            <input type="file" wire:model="kk_file" class="text-xs file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-secondary file:text-white cursor-pointer" />
                        </div>
                        @error('kk_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload BPJS -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            3. Foto Kartu BPJS Kesehatan / KIS Fisik / Digital <span class="text-status-error">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center bg-slate-50/50">
                            @if ($bpjs_file)
                                <p class="text-xs text-status-success font-semibold flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ $bpjs_file->getClientOriginalName() }}
                                </p>
                            @endif
                            <input type="file" wire:model="bpjs_file" class="text-xs file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-secondary file:text-white cursor-pointer" />
                        </div>
                        @error('bpjs_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload Surat Faskes jika medis -->
                    @if(in_array($reason, ['emergency', 'chronic']))
                        <div class="mb-8">
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                4. Surat Keterangan Rawat Inap / Medis dari Faskes <span class="text-status-error">*</span>
                            </label>
                            <div class="border-2 border-dashed border-amber-300 rounded-xl p-4 text-center bg-amber-50/40">
                                @if ($faskes_file)
                                    <p class="text-xs text-status-success font-semibold flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        {{ $faskes_file->getClientOriginalName() }}
                                    </p>
                                @endif
                                <input type="file" wire:model="faskes_file" class="text-xs file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-status-warning file:text-slate-900 cursor-pointer" />
                            </div>
                            @error('faskes_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="prevStep"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 flex items-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            <span>Sebelumnya</span>
                        </button>
                        <button
                            type="button"
                            wire:click="nextStep"
                            class="px-7 py-3 rounded-xl bg-secondary text-white font-semibold text-sm hover:bg-slate-700 active:scale-95 transition-all shadow-xs flex items-center gap-2"
                        >
                            <span>Lanjut ke Konfirmasi</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- STEP 4: RINGKASAN & KONFIRMASI -->
            @if($step === 4)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 4: Tinjau &amp; Kirim Pengajuan</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Periksa kembali ringkasan permohonan reaktivasi kepesertaan KIS/PBI-JK Anda.
                        </p>
                    </div>

                    <div class="space-y-4 mb-6 text-xs">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px] block mb-2">Data Pemohon &amp; Peserta</span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-slate-700">
                                <div><span class="text-slate-400">Pemohon:</span> {{ $applicant_name }} ({{ $applicant_nik }})</div>
                                <div><span class="text-slate-400">No WhatsApp:</span> {{ $phone }}</div>
                                <div><span class="text-slate-400">Peserta BPJS:</span> <strong>{{ $participant_name }}</strong> ({{ $participant_nik }})</div>
                                <div><span class="text-slate-400">Nomor Kartu:</span> <strong>{{ $bpjs_card_number }}</strong></div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px] block mb-2">Alasan &amp; Faskes</span>
                            <p class="font-semibold text-slate-900">
                                Alasan: {{ match($reason) { 'emergency' => 'Gawat Darurat Medis / Rawat Inap (Prioritas)', 'chronic' => 'Penyakit Kronis / Katastropik', 'newborn' => 'Bayi Baru Lahir dari Ibu PBI', default => 'Keluarga Tidak Mampu' } }}
                            </p>
                            @if(!empty($health_facility_name))
                                <p class="text-slate-600 mt-1">Fasilitas Kesehatan: {{ $health_facility_name }} ({{ $health_letter_number ?? '-' }})</p>
                            @endif
                        </div>
                    </div>

                    <!-- Agreement -->
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 mb-8">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="agree_terms" class="mt-0.5 rounded text-secondary focus:ring-secondary h-4 w-4" />
                            <span class="text-xs text-blue-950 leading-relaxed">
                                Saya menyatakan dengan sesungguhnya bahwa seluruh data yang diisikan dan berkas yang dilampirkan adalah benar dan sah. Saya bersedia diverifikasi kelayakannya oleh Dinas Sosial Kabupaten Blitar dan Kementerian Sosial RI.
                            </span>
                        </label>
                        @error('agree_terms') <p class="text-xs text-status-error mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="prevStep"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 flex items-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            <span>Sebelumnya</span>
                        </button>
                        <button
                            type="button"
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            class="px-8 py-3.5 rounded-xl bg-secondary text-white font-bold text-sm hover:bg-slate-700 active:scale-95 transition-all shadow-md flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="submit" class="material-symbols-outlined text-xl">send</span>
                            <span wire:loading wire:target="submit" class="material-symbols-outlined text-xl animate-spin">progress_activity</span>
                            <span wire:loading.remove wire:target="submit">Kirim Pengajuan Reaktivasi</span>
                            <span wire:loading wire:target="submit">Mengirimkan berkas...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT SIDEBAR (4 cols) -->
        <div class="lg:col-span-4 flex flex-col gap-6 sticky top-24">
            <div class="bg-surface-container-low rounded-2xl p-6 border border-secondary-container/60 custom-shadow-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white text-secondary flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-2xl">emergency</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-inverse-surface">Pasien Rawat Inap Darurat?</h4>
                        <p class="text-[11px] text-on-surface-variant">Prioritas penanganan darurat 1x24 jam</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Untuk pasien yang sedang dirawat inap di rumah sakit, pastikan melampirkan Surat Keterangan Rawat agar tim verifikator segera menerbitkan rekomendasi pengusulan ke Kementerian Sosial.
                </p>

                <div class="pt-4 border-t border-slate-200 text-xs text-slate-500">
                    <span class="font-bold text-slate-700 block mb-1">Butuh konfirmasi cepat?</span>
                    <a href="https://wa.me/6281234567890" target="_blank" class="text-emerald-700 font-semibold hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">chat</span>
                        WhatsApp Layanan PBI Dinsos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
