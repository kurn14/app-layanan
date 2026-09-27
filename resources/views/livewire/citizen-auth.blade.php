<div class="w-full max-w-lg mx-auto px-4 sm:px-6 py-12 md:py-16">
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-3 group focus:outline-none mb-4">
            <div class="w-12 h-12 rounded-2xl bg-primary-container text-white flex items-center justify-center font-bold shadow-sm">
                <span class="material-symbols-outlined text-2xl">shield</span>
            </div>
        </a>
        <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface tracking-tight">
            {{ $mode === 'login' ? 'Masuk ke Akun SAPA SOSIAL' : 'Pendaftaran Akun Warga' }}
        </h1>
        <p class="text-xs md:text-sm text-on-surface-variant mt-1.5">
            {{ $mode === 'login' ? 'Pantau seluruh riwayat berkas dan pengaduan Anda dalam satu akun.' : 'Daftar sekali untuk mempermudah seluruh pengajuan layanan sosial.' }}
        </p>
    </div>

    <!-- Card Wrapper -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
        <!-- Toggle Tabs -->
        <div class="flex items-center bg-slate-100 p-1 rounded-xl mb-6">
            <button
                type="button"
                wire:click="switchMode('login')"
                class="flex-1 py-2 rounded-lg text-xs font-bold transition-all {{ $mode === 'login' ? 'bg-white text-primary shadow-xs' : 'text-slate-500 hover:text-slate-900' }}"
            >
                Masuk
            </button>
            <button
                type="button"
                wire:click="switchMode('register')"
                class="flex-1 py-2 rounded-lg text-xs font-bold transition-all {{ $mode === 'register' ? 'bg-white text-primary shadow-xs' : 'text-slate-500 hover:text-slate-900' }}"
            >
                Daftar Akun Baru
            </button>
        </div>

        <!-- FORM LOGIN -->
        @if ($mode === 'login')
            <form wire:submit="login" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                        Email atau Nomor NIK (16 Digit) <span class="text-status-error">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-lg">person</span>
                        </span>
                        <input
                            type="text"
                            wire:model="login_identifier"
                            placeholder="nama@email.com atau 3505xxxxxxxxxxxx"
                            class="w-full min-h-[46px] pl-10 pr-4 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            required
                        />
                    </div>
                    @error('login_identifier') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                        Kata Sandi <span class="text-status-error">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-lg">lock</span>
                        </span>
                        <input
                            type="password"
                            wire:model="login_password"
                            placeholder="••••••••"
                            class="w-full min-h-[46px] pl-10 pr-4 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            required
                        />
                    </div>
                    @error('login_password') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" wire:model="remember" class="rounded text-primary focus:ring-primary" />
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="text-primary font-semibold hover:underline">Lupa kata sandi?</a>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full min-h-[48px] rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center justify-center gap-2 mt-2"
                >
                    <span wire:loading.remove wire:target="login">Masuk ke Akun</span>
                    <span wire:loading wire:target="login">Memeriksa...</span>
                </button>
            </form>
        @else
            <!-- FORM REGISTER -->
            <form wire:submit="register" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                        Nama Lengkap Sesuai KTP <span class="text-status-error">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Nama lengkap"
                        class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        required
                    />
                    @error('name') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            NIK (16 Digit) <span class="text-status-error">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="nik"
                            maxlength="16"
                            placeholder="3505xxxxxxxxxxxx"
                            class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary"
                            required
                        />
                        @error('nik') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            No. WhatsApp <span class="text-status-error">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="phone"
                            placeholder="08xxxxxxxxxx"
                            class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            required
                        />
                        @error('phone') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            Kecamatan <span class="text-status-error">*</span>
                        </label>
                        <select
                            wire:model.live="district_id"
                            class="w-full min-h-[44px] px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
                            required
                        >
                            <option value="">-- Pilih --</option>
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
                            class="w-full min-h-[44px] px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white"
                            {{ empty($district_id) ? 'disabled' : '' }}
                            required
                        >
                            <option value="">-- Pilih --</option>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}">{{ $village->name }}</option>
                            @endforeach
                        </select>
                        @error('village_id') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-inverse-surface mb-1">
                        Alamat Email <span class="text-status-error">*</span>
                    </label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="contoh@gmail.com"
                        class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        required
                    />
                    @error('email') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            Kata Sandi <span class="text-status-error">*</span>
                        </label>
                        <input
                            type="password"
                            wire:model="password"
                            placeholder="Min. 8 karakter"
                            class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            required
                        />
                        @error('password') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-inverse-surface mb-1">
                            Ulangi Sandi <span class="text-status-error">*</span>
                        </label>
                        <input
                            type="password"
                            wire:model="password_confirmation"
                            placeholder="Ulangi sandi"
                            class="w-full min-h-[44px] px-3.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            required
                        />
                    </div>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="agree" class="mt-0.5 rounded text-primary focus:ring-primary" />
                        <span class="text-slate-600">Saya menyetujui pendaftaran akun dan keabsahan data kependudukan Kabupaten Blitar.</span>
                    </label>
                    @error('agree') <p class="text-xs text-status-error mt-1">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full min-h-[48px] rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs flex items-center justify-center gap-2 mt-2"
                >
                    <span wire:loading.remove wire:target="register">Buat Akun Sekarang</span>
                    <span wire:loading wire:target="register">Mendaftarkan...</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Quick tracking hint -->
    <div class="text-center mt-6 text-xs text-slate-500">
        Hanya ingin cek status berkas tanpa login?
        <a href="{{ route('cek-status') }}" wire:navigate class="text-primary font-bold hover:underline">Lacak dengan nomor tiket</a>
    </div>
</div>
