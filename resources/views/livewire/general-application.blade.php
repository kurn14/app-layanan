<div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <a href="{{ route('layanan.index') }}" wire:navigate class="hover:text-primary transition-colors">Layanan</a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Form Pengajuan {{ $service?->name ?? 'Layanan' }}</span>
    </nav>

    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-10">
        <div class="border-b border-slate-100 pb-6 mb-8">
            <span class="text-xs font-bold uppercase tracking-wider text-primary">{{ $service?->category ?? 'Layanan Sosial' }}</span>
            <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface mt-1">{{ $service?->name ?? 'Pengajuan Layanan Sosial' }}</h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-2 leading-relaxed">
                {{ $service?->description ?? 'Lengkapi formulir permohonan layanan sosial berikut ini.' }}
            </p>
        </div>

        <form wire:submit="submit">
            <!-- Data Identitas -->
            <div class="mb-8">
                <h3 class="text-sm font-bold uppercase tracking-wider text-primary mb-4 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">person</span>
                    Data Pemohon
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

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                        Alamat Lengkap <span class="text-status-error">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="address"
                        placeholder="RT / RW / Dusun / Nama Jalan"
                        class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                    />
                    @error('address') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                        Keterangan Tambahan / Permohonan Khusus
                    </label>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Jelaskan kebutuhan spesifik atau latar belakang pengajuan Anda..."
                        class="w-full rounded-xl border border-slate-300 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                    ></textarea>
                </div>
            </div>

            <!-- Dokumen Persyaratan -->
            @if($requirements->isNotEmpty())
                <div class="pt-6 border-t border-slate-100 mb-8">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-primary mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base">upload_file</span>
                        Dokumen Persyaratan Layanan
                    </h3>

                    <div class="space-y-4">
                        @foreach ($requirements as $req)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                                <label class="block text-xs font-bold text-inverse-surface mb-1">
                                    {{ $req->name }}
                                    @if($req->is_mandatory)
                                        <span class="text-status-error">* (Wajib)</span>
                                    @endif
                                </label>
                                <p class="text-[11px] text-slate-500 mb-2">Format: {{ $req->allowed_mimes ?? 'JPG, PNG, PDF' }} (Maks. 2 MB)</p>
                                <input
                                    type="file"
                                    wire:model="documents.{{ $req->id }}"
                                    class="text-xs file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-white cursor-pointer"
                                />
                                @if (isset($documents[$req->id]))
                                    <span class="text-xs text-status-success font-semibold ml-2">Berkas terpilih</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Agreement -->
            <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 mb-8">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="agree_terms" class="mt-0.5 rounded text-primary focus:ring-primary h-4 w-4" />
                    <span class="text-xs text-teal-950 leading-relaxed">
                        Saya menyatakan data yang saya isikan adalah benar dan bersedia diverifikasi oleh petugas Dinas Sosial Kabupaten Blitar.
                    </span>
                </label>
                @error('agree_terms') <p class="text-xs text-status-error mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-8 py-3.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-md flex items-center gap-2"
                >
                    <span wire:loading.remove wire:target="submit" class="material-symbols-outlined text-xl">send</span>
                    <span wire:loading wire:target="submit" class="material-symbols-outlined text-xl animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="submit">Kirim Pengajuan</span>
                    <span wire:loading wire:target="submit">Mengirimkan berkas...</span>
                </button>
            </div>
        </form>
    </div>
</div>
