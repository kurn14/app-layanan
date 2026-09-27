<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-status-error font-bold">Laporan &amp; Pengaduan Sosial</span>
    </nav>

    <!-- Header Section -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 text-status-error text-xs font-bold mb-3">
                <span class="material-symbols-outlined text-sm">campaign</span>
                Kanal Aspirasi &amp; Pengaduan Masyarakat
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface tracking-tight">
                Sampaikan Laporan &amp; Masalah Sosial di Sekitar Anda
            </h1>
            <p class="text-xs md:text-sm text-on-surface-variant mt-2 leading-relaxed">
                Temukan lansia terlantar, anak putus sekolah, orang dengan gangguan jiwa (ODGJ) butuh pertolongan, atau warga rentan di Kabupaten Blitar? Laporkan di sini, setiap laporan akan diverifikasi dan ditindaklanjuti oleh petugas Dinas Sosial.
            </p>
        </div>
    </div>

    <!-- Asymmetric Grid: 8 cols Form + 4 cols Guidelines -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Canvas (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
            <form wire:submit="submit">
                <!-- Section 1: Kategori Masalah -->
                <div class="mb-8">
                    <label class="block text-sm font-bold text-inverse-surface mb-2">
                        Pilih Kategori Permasalahan Sosial <span class="text-status-error">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($categories as $cat)
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all {{ $complaint_category_id === $cat->id ? 'border-status-error bg-red-50/50 ring-2 ring-red-200' : 'border-slate-200 hover:bg-slate-50' }}">
                                <input
                                    type="radio"
                                    wire:model.live="complaint_category_id"
                                    value="{{ $cat->id }}"
                                    class="text-status-error focus:ring-status-error h-4 w-4"
                                />
                                <span class="text-xs font-semibold text-slate-800">{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('complaint_category_id') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Section 2: Data Pelapor -->
                <div class="mb-8 pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-primary">person</span>
                            Data Diri Pelapor
                        </h3>
                        <div class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                            <span class="material-symbols-outlined text-sm text-status-success">lock</span>
                            Identitas Dijamin Rahasia
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nama Lengkap Anda <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="reporter_name"
                                placeholder="Nama pelapor"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                            @error('reporter_name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Nomor WhatsApp / Telepon Aktif <span class="text-status-error">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="reporter_phone"
                                placeholder="08xxxxxxxxxx"
                                class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            />
                            <p class="text-[11px] text-slate-400 mt-1">Petugas akan menghubungi nomor ini untuk konfirmasi awal lokasi.</p>
                            @error('reporter_phone') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Lokasi Kejadian -->
                <div class="mb-8 pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-status-error">location_on</span>
                        Lokasi Kejadian / Keberadaan Klien
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-inverse-surface mb-1">
                                Kecamatan <span class="text-status-error">*</span>
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
                            Detail Patokan Alamat / Lokasi Spesifik <span class="text-status-error">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="location_detail"
                            placeholder="Contoh: Depan Pasar Wlingi dekat pos kamling RT 03 RW 01"
                            class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        />
                        @error('location_detail') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Section 4: Deskripsi & Bukti Foto -->
                <div class="mb-8 pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-primary">description</span>
                        Uraian Masalah &amp; Bukti Foto
                    </h3>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            Uraian Lengkap Kejadian / Kondisi <span class="text-status-error">*</span>
                        </label>
                        <textarea
                            wire:model="description"
                            rows="4"
                            placeholder="Ceritakan kondisi warga yang bersangkutan, kebutuhan yang mendesak, atau bahaya yang mungkin timbul..."
                            class="w-full rounded-xl border border-slate-300 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        ></textarea>
                        @error('description') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            Lampiran Foto / Bukti Pendukung (Opsional namun sangat disarankan)
                        </label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-5 text-center bg-slate-50/50">
                            @if ($attachment_file)
                                <p class="text-xs text-status-success font-semibold flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    {{ $attachment_file->getClientOriginalName() }}
                                </p>
                            @else
                                <span class="material-symbols-outlined text-2xl text-slate-400 mb-1">add_a_photo</span>
                                <p class="text-[11px] text-slate-500 mb-2">Unggah foto kondisi klien atau tempat kejadian (Maks 3 MB, JPG/PNG)</p>
                            @endif
                            <input type="file" wire:model="attachment_file" class="text-xs file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-white cursor-pointer" />
                        </div>
                        @error('attachment_file') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Agreement -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mb-8">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="agree_terms" class="mt-0.5 rounded text-status-error focus:ring-status-error h-4 w-4" />
                        <span class="text-xs text-slate-700 leading-relaxed">
                            Saya menyatakan bahwa laporan yang saya sampaikan dibuat dengan itikad baik dan berdasarkan pengamatan atau informasi yang dapat dipertanggungjawabkan (bukan fitnah atau hoaks).
                        </span>
                    </label>
                    @error('agree_terms') <p class="text-xs text-status-error mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-8 py-3.5 rounded-xl bg-status-error text-white font-bold text-sm hover:bg-red-700 active:scale-95 transition-all shadow-md flex items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="submit" class="material-symbols-outlined text-xl">send</span>
                        <span wire:loading wire:target="submit" class="material-symbols-outlined text-xl animate-spin">progress_activity</span>
                        <span wire:loading.remove wire:target="submit">Kirim Laporan Pengaduan</span>
                        <span wire:loading wire:target="submit">Mengirimkan laporan...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Guidelines (4 cols) -->
        <div class="lg:col-span-4 flex flex-col gap-6 sticky top-24">
            <div class="bg-surface-container-low rounded-2xl p-6 border border-secondary-container/60 custom-shadow-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white text-status-error flex items-center justify-center shadow-xs">
                        <span class="material-symbols-outlined text-2xl">emergency_home</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-inverse-surface">Alur Respon Pengaduan</h4>
                        <p class="text-[11px] text-on-surface-variant">Langkah penanganan oleh Dinsos</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs text-slate-600">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-status-success mt-0.5">verified</span>
                        <span><strong>1. Verifikasi Laporan:</strong> Petugas memeriksa keabsahan laporan dan menghubungi pelapor bila diperlukan klarifikasi.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-status-success mt-0.5">verified</span>
                        <span><strong>2. Disposisi Petugas:</strong> Laporan diteruskan ke tim penanganan kasus rehabilitasi sosial atau relawan Tagana/Puskesos terdekat.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm text-status-success mt-0.5">verified</span>
                        <span><strong>3. Penanganan Lapangan:</strong> Dilakukan penjangkauan, assessment, dan penanganan darurat atau rujukan ke panti.</span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-200 text-xs text-slate-500">
                    <span class="font-bold text-slate-800 block mb-1">Nomor Pengaduan Unik</span>
                    <p class="text-[11px] text-slate-600">Setelah mengirim, Anda akan menerima nomor tiket pengaduan berawalan <code>ADU-YYYYMM-NNNNN</code> untuk memantau tindakan petugas.</p>
                </div>
            </div>
        </div>
    </div>
</div>
