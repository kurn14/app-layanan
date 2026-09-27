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
        <a href="{{ route('layanan.detail', 'dtsen') }}" wire:navigate class="hover:text-primary transition-colors">Surat Keterangan DTSEN</a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Formulir Pengajuan</span>
    </nav>

    <!-- Page Title & Progress Stepper -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8">
        <div class="mb-6">
            <span class="text-xs font-bold uppercase tracking-wider text-primary">Layanan Prioritas 1</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface mt-1">Pengajuan Surat Keterangan DTSEN</h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-1">
                Lengkapi empat tahapan di bawah ini dengan data yang benar dan berkas yang terbaca jelas.
            </p>
        </div>

        <!-- Stepper Indicator -->
        <div class="max-w-4xl mx-auto pt-2 pb-4">
            <div class="relative flex items-center justify-between">
                <!-- Background Line -->
                <div class="absolute top-1/2 left-6 right-6 -translate-y-1/2 h-1 bg-slate-200 z-0"></div>
                <!-- Progress Line -->
                <div class="absolute top-1/2 left-6 -translate-y-1/2 h-1 bg-primary z-0 transition-all duration-300"
                    style="width: {{ match($step) { 1 => '0%', 2 => '33.3%', 3 => '66.6%', 4 => '100%', default => '0%' } }}">
                </div>

                <!-- Step 1 -->
                <button type="button" wire:click="goToStep(1)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step > 1 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($step === 1 ? 'bg-primary text-white ring-4 ring-teal-50' : 'bg-white border-2 border-slate-300 text-slate-500') }}">
                        @if($step > 1)
                            <span class="material-symbols-outlined text-lg">check</span>
                        @else
                            1
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 1 ? 'text-primary font-bold' : ($step > 1 ? 'text-status-success' : 'text-slate-500') }}">
                        1. Tujuan Penggunaan
                    </span>
                </button>

                <!-- Step 2 -->
                <button type="button" wire:click="goToStep(2)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step > 2 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($step === 2 ? 'bg-primary text-white ring-4 ring-teal-50 pulse-step' : 'bg-white border-2 border-slate-300 text-slate-500') }}">
                        @if($step > 2)
                            <span class="material-symbols-outlined text-lg">check</span>
                        @else
                            2
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 2 ? 'text-primary font-bold' : ($step > 2 ? 'text-status-success' : 'text-slate-500') }}">
                        2. Identitas Diri
                    </span>
                </button>

                <!-- Step 3 -->
                <button type="button" wire:click="goToStep(3)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step > 3 ? 'bg-status-success text-white ring-4 ring-emerald-50' : ($step === 3 ? 'bg-primary text-white ring-4 ring-teal-50 pulse-step' : 'bg-white border-2 border-slate-300 text-slate-500') }}">
                        @if($step > 3)
                            <span class="material-symbols-outlined text-lg">check</span>
                        @else
                            3
                        @endif
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 3 ? 'text-primary font-bold' : ($step > 3 ? 'text-status-success' : 'text-slate-500') }}">
                        3. Unggah Berkas
                    </span>
                </button>

                <!-- Step 4 -->
                <button type="button" wire:click="goToStep(4)" class="relative z-10 flex flex-col items-center focus:outline-none">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-all {{ $step === 4 ? 'bg-primary text-white ring-4 ring-teal-50' : 'bg-white border-2 border-slate-300 text-slate-500' }}">
                        4
                    </div>
                    <span class="mt-2 text-xs font-semibold {{ $step === 4 ? 'text-primary font-bold' : 'text-slate-500' }}">
                        4. Tinjau &amp; Kirim
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Asymmetric Layout: 8 cols Form + 4 cols Sticky Assistant -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- FORM BODY (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
            <!-- STEP 1: TUJUAN PENGGUNAAN -->
            @if($step === 1)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 1: Pilih Tujuan Penggunaan Surat</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Setiap tujuan memiliki batas desil maksimal yang telah ditentukan oleh regulasi program.
                        </p>
                    </div>

                    <div class="space-y-4 mb-6">
                        <label class="block text-sm font-semibold text-inverse-surface">
                            Tujuan Penggunaan Surat DTSEN <span class="text-status-error">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($purposes as $purpose)
                                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all {{ $dtsen_purpose_id === $purpose->id ? 'border-primary bg-teal-50/50 ring-2 ring-primary/20' : 'border-slate-200 hover:bg-slate-50' }}">
                                    <input
                                        type="radio"
                                        wire:model.live="dtsen_purpose_id"
                                        value="{{ $purpose->id }}"
                                        class="mt-1 text-primary focus:ring-primary h-4 w-4"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-inverse-surface">{{ $purpose->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 text-[10px] font-bold">
                                                Maks Desil {{ $purpose->max_decile }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Masa berlaku: {{ $purpose->validity_days ? $purpose->validity_days . ' hari' : '1 semester' }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('dtsen_purpose_id') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-inverse-surface mb-2">
                            Catatan Keterangan Tujuan Tambahan (Opsional)
                        </label>
                        <textarea
                            wire:model="purpose_description"
                            rows="3"
                            placeholder="Contoh: Untuk persyaratan pendaftaran KIP Kuliah di Universitas Negeri Malang semester ganjil."
                            class="w-full rounded-xl border border-slate-300 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                        ></textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="nextStep"
                            class="px-7 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center gap-2"
                        >
                            <span>Lanjut ke Data Identitas</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- STEP 2: DATA PEMOHON & SUBJEK -->
            @if($step === 2)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 2: Data Identitas Pemohon &amp; Subjek</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Isi data sesuai dengan Kartu Tanda Penduduk (KTP) dan Kartu Keluarga (KK) Kabupaten Blitar.
                        </p>
                    </div>

                    <!-- Bagian A: Data Pemohon -->
                    <div class="mb-8">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-primary mb-4 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">person</span>
                            Identitas Pemohon (Orang Tua / Wali / Pemohon Sendiri)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                    Nama Lengkap Pemohon <span class="text-status-error">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="applicant_name"
                                    placeholder="Sesuai KTP"
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
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
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary"
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
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary"
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
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
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
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
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
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
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

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Alamat Lengkap (RT/RW/Dusun/Jalan) <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="address"
                                placeholder="Contoh: RT 02 RW 01 Dusun Krajan"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                            @error('address') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Bagian B: Subjek yang Diterangkan -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-primary flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">badge</span>
                                Orang yang Diterangkan dalam Surat
                            </h3>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                                <input
                                    type="checkbox"
                                    wire:model.live="is_same_as_applicant"
                                    class="rounded text-primary focus:ring-primary"
                                />
                                <span>Sama dengan Pemohon</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                    Hubungan dengan Pemohon <span class="text-status-error">*</span>
                                </label>
                                <select
                                    wire:model="relationship_to_applicant"
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
                                    {{ $is_same_as_applicant ? 'disabled' : '' }}
                                >
                                    <option value="Diri Sendiri">Diri Sendiri</option>
                                    <option value="Anak">Anak</option>
                                    <option value="Suami / Istri">Suami / Istri</option>
                                    <option value="Orang Tua">Orang Tua</option>
                                    <option value="Saudara">Saudara</option>
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                    Nama Orang yang Diterangkan <span class="text-status-error">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="subject_name"
                                    placeholder="Nama calon siswa/mahasiswa"
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                    {{ $is_same_as_applicant ? 'readonly' : '' }}
                                />
                                @error('subject_name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                    NIK yang Diterangkan <span class="text-status-error">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="subject_nik"
                                    maxlength="16"
                                    placeholder="3505xxxxxxxxxxxx"
                                    class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary"
                                    {{ $is_same_as_applicant ? 'readonly' : '' }}
                                />
                                @error('subject_nik') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
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
                            wire:click="nextStep"
                            class="px-7 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center gap-2"
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
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 3: Unggah Berkas Persyaratan</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Unggah foto atau scan dokumen resmi yang jelas. Format diperbolehkan: JPG, PNG, atau PDF (maks. 2 MB per berkas).
                        </p>
                    </div>

                    <!-- Upload KTP -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-inverse-surface mb-1">
                            1. Kartu Tanda Penduduk (KTP) Pemohon <span class="text-status-error">*</span>
                        </label>
                        <p class="text-xs text-slate-500 mb-2">Foto asli KTP pemohon, pastikan NIK dan nama terbaca jelas.</p>

                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-primary/50 transition-colors bg-slate-50/50">
                            @if ($ktp_file)
                                <div class="flex items-center justify-center gap-3 text-status-success font-semibold text-xs">
                                    <span class="material-symbols-outlined text-xl">check_circle</span>
                                    <span>{{ $ktp_file->getClientOriginalName() }} (Siap diunggah)</span>
                                </div>
                            @else
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">upload_file</span>
                                <p class="text-xs text-slate-600 mb-2">Pilih file KTP dari komputer/hp Anda</p>
                            @endif

                            <input type="file" wire:model="ktp_file" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-container cursor-pointer" />
                            <div wire:loading wire:target="ktp_file" class="text-xs text-primary font-medium mt-2">Mengunggah file KTP...</div>
                        </div>
                        @error('ktp_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload KK -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-inverse-surface mb-1">
                            2. Kartu Keluarga (KK) Kabupaten Blitar <span class="text-status-error">*</span>
                        </label>
                        <p class="text-xs text-slate-500 mb-2">Scan/foto Kartu Keluarga lengkap yang memuat nama pemohon dan subjek.</p>

                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-primary/50 transition-colors bg-slate-50/50">
                            @if ($kk_file)
                                <div class="flex items-center justify-center gap-3 text-status-success font-semibold text-xs">
                                    <span class="material-symbols-outlined text-xl">check_circle</span>
                                    <span>{{ $kk_file->getClientOriginalName() }} (Siap diunggah)</span>
                                </div>
                            @else
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">upload_file</span>
                                <p class="text-xs text-slate-600 mb-2">Pilih file KK dari komputer/hp Anda</p>
                            @endif

                            <input type="file" wire:model="kk_file" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-container cursor-pointer" />
                            <div wire:loading wire:target="kk_file" class="text-xs text-primary font-medium mt-2">Mengunggah file KK...</div>
                        </div>
                        @error('kk_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
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
                            wire:click="nextStep"
                            class="px-7 py-3 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center gap-2"
                        >
                            <span>Lanjut ke Tinjauan Akhir</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- STEP 4: RINGKASAN & KONFIRMASI -->
            @if($step === 4)
                <div>
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h2 class="text-lg font-bold text-inverse-surface">Langkah 4: Tinjau &amp; Konfirmasi Pengajuan</h2>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            Periksa kembali seluruh data sebelum mengirimkan permohonan ke Dinas Sosial Kabupaten Blitar.
                        </p>
                    </div>

                    <!-- Summary Cards -->
                    <div class="space-y-4 mb-6 text-xs">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Tujuan Permohonan</span>
                                <button type="button" wire:click="goToStep(1)" class="text-primary font-semibold hover:underline">Ubah</button>
                            </div>
                            <p class="font-semibold text-slate-900 text-sm">{{ $selectedPurpose?->name }} (Batas Maks. Desil {{ $selectedPurpose?->max_decile }})</p>
                            @if(!empty($purpose_description))
                                <p class="text-slate-500 mt-1 italic">"{{ $purpose_description }}"</p>
                            @endif
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Data Pemohon &amp; Subjek</span>
                                <button type="button" wire:click="goToStep(2)" class="text-primary font-semibold hover:underline">Ubah</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-slate-700">
                                <div><span class="text-slate-400">Pemohon:</span> {{ $applicant_name }} (NIK: {{ $applicant_nik }})</div>
                                <div><span class="text-slate-400">No KK:</span> {{ $family_card_number }}</div>
                                <div><span class="text-slate-400">No HP/WA:</span> {{ $phone }}</div>
                                <div><span class="text-slate-400">Alamat:</span> {{ $address }}</div>
                                <div class="col-span-full pt-1 border-t border-slate-200">
                                    <span class="text-slate-400">Orang yang diterangkan:</span> <strong>{{ $subject_name }}</strong> (NIK: {{ $subject_nik }}) &bull; Hubungan: {{ $relationship_to_applicant }}
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Dokumen Terlampir</span>
                                <button type="button" wire:click="goToStep(3)" class="text-primary font-semibold hover:underline">Ubah</button>
                            </div>
                            <div class="flex items-center gap-4 text-emerald-700 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base">verified</span>
                                    <span>KTP: {{ $ktp_file ? $ktp_file->getClientOriginalName() : 'Terpilih' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base">verified</span>
                                    <span>KK: {{ $kk_file ? $kk_file->getClientOriginalName() : 'Terpilih' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox Agreement -->
                    <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 mb-8">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="agree_terms"
                                class="mt-0.5 rounded text-primary focus:ring-primary h-4 w-4"
                            />
                            <span class="text-xs text-teal-950 leading-relaxed">
                                Saya menyatakan dengan sesungguhnya bahwa data dan berkas yang saya berikan adalah benar, sah, dan dapat dipertanggungjawabkan sesuai peraturan perundang-undangan. Apabila di kemudian hari ditemukan ketidakbenaran, saya bersedia menerima sanksi yang berlaku.
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
                            class="px-8 py-3.5 rounded-xl bg-status-success text-white font-bold text-sm hover:bg-emerald-600 active:scale-95 transition-all shadow-md flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="submit" class="material-symbols-outlined text-xl">send</span>
                            <span wire:loading wire:target="submit" class="material-symbols-outlined text-xl animate-spin">progress_activity</span>
                            <span wire:loading.remove wire:target="submit">Kirim Pengajuan Sekarang</span>
                            <span wire:loading wire:target="submit">Memproses...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- RIGHT SIDEBAR CONTEXT (4 cols) -->
        <div class="lg:col-span-4 flex flex-col gap-6 sticky top-24">
            <div class="bg-surface-container-low rounded-2xl p-6 border border-secondary-container/60 custom-shadow-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white text-primary flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-2xl">help</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-inverse-surface">Panduan Pengisian</h4>
                        <p class="text-[11px] text-on-surface-variant">Tips agar pengajuan cepat diverifikasi</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs text-slate-600">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-status-success mt-0.5">check_circle</span>
                        <span>Pastikan nomor NIK terdiri dari 16 digit dan berdomisili Kabupaten Blitar.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-status-success mt-0.5">check_circle</span>
                        <span>Nomor WhatsApp aktif digunakan untuk pengiriman status dan tautan tiket.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-status-success mt-0.5">check_circle</span>
                        <span>Surat akan dicek kesesuaian desilnya di SIKS-NG oleh petugas verifikator dinas.</span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <p class="text-[11px] text-slate-500 mb-2">Butuh bantuan darurat?</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="w-full py-2 px-3 rounded-lg bg-white border border-slate-300 text-slate-700 text-xs font-semibold flex items-center justify-center gap-1.5 hover:bg-slate-50 transition-colors">
                        <span class="material-symbols-outlined text-sm text-emerald-600">chat</span>
                        <span>Chat WhatsApp Petugas</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
