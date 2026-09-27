<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-secondary mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-on-surface font-semibold text-primary">Layanan Sosial</span>
    </nav>

    <!-- Page Header -->
    <div class="mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold mb-3">
            <span class="material-symbols-outlined text-sm">dashboard</span>
            Katalog Layanan Publik
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-inverse-surface tracking-tight">
            Pusat Layanan Sosial Kabupaten Blitar
        </h1>
        <p class="text-base text-on-surface-variant mt-2 max-w-3xl leading-relaxed">
            Pilih jenis layanan yang Anda butuhkan. Seluruh pengajuan diproses secara digital, transparan, dan dapat dipantau setiap saat dengan nomor tiket.
        </p>
    </div>

    <!-- Search & Category Filters -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 custom-shadow-card mb-10">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search bar -->
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-xl">search</span>
                </span>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Cari layanan, kata kunci, syarat..."
                    class="w-full min-h-[46px] pl-11 pr-4 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                />
                @if(!empty($search))
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                @endif
            </div>

            <!-- Category filter chips -->
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 custom-scroll">
                <button
                    wire:click="selectCategory('all')"
                    class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'all' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Semua Layanan
                </button>
                <button
                    wire:click="selectCategory('Administrasi')"
                    class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'Administrasi' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Administrasi &amp; Surat
                </button>
                <button
                    wire:click="selectCategory('Kesehatan')"
                    class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'Kesehatan' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Jaminan Kesehatan
                </button>
                <button
                    wire:click="selectCategory('Rehabilitasi Sosial')"
                    class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $selectedCategory === 'Rehabilitasi Sosial' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Rehabilitasi Sosial
                </button>
            </div>
        </div>
    </div>

    <!-- SECTION: LAYANAN PRIORITAS -->
    <div class="mb-14">
        <div class="flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-status-warning text-2xl">star</span>
            <h2 class="text-xl font-bold text-inverse-surface">Layanan Prioritas Daerah</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Priority 1: DTSEN -->
            <div class="rounded-2xl border-2 border-teal-200 bg-white p-7 custom-shadow-card hover:shadow-lg transition-all flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-container text-white flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-2xl">description</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-teal-100 text-teal-800 text-[11px] font-bold">
                            SLA: 1-2 Hari
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-secondary uppercase">Administrasi Data Terpadu</span>
                    <h3 class="text-xl font-bold text-inverse-surface mt-1 mb-2 group-hover:text-primary transition-colors">
                        Surat Keterangan DTSEN
                    </h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                        Penerbitan surat keterangan status dan desil keluarga dalam Data Tunggal Sosial Ekonomi Nasional untuk syarat beasiswa PIP, KIP Kuliah, SPMB jalur afirmasi, dan bansos.
                    </p>
                    <div class="space-y-1.5 text-xs text-slate-600 mb-6 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-status-success">check_circle</span>
                            <span>Syarat: Scan/Foto KTP &amp; KK</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-status-success">check_circle</span>
                            <span>Dilengkapi QR Code verifikasi keaslian</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('layanan.detail', 'dtsen') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors">
                        Detail Syarat
                    </a>
                    <a href="{{ route('pengajuan.dtsen') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl bg-primary text-white text-center font-semibold text-xs hover:bg-primary-container transition-colors shadow-xs">
                        Ajukan Sekarang
                    </a>
                </div>
            </div>

            <!-- Priority 2: Reaktivasi PBI-JK -->
            <div class="rounded-2xl border-2 border-blue-200 bg-white p-7 custom-shadow-card hover:shadow-lg transition-all flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-secondary text-white flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-2xl">health_and_safety</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold">
                            Prioritas Medis
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-secondary uppercase">Jaminan Kesehatan</span>
                    <h3 class="text-xl font-bold text-inverse-surface mt-1 mb-2 group-hover:text-secondary transition-colors">
                        Reaktivasi KIS / PBI-JK
                    </h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                        Fasilitasi pengaktifan kembali kepesertaan JKN-KIS PBI yang dinonaktifkan dengan verifikasi kelayakan desil dan penerbitan rekomendasi ke Kemensos RI.
                    </p>
                    <div class="space-y-1.5 text-xs text-slate-600 mb-6 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-status-success">check_circle</span>
                            <span>Syarat: KTP, KK, Kartu BPJS &amp; Surat Faskes</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-status-success">check_circle</span>
                            <span>Jalur cepat untuk pasien rawat inap darurat</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('layanan.detail', 'pbi') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors">
                        Detail Syarat
                    </a>
                    <a href="{{ route('pengajuan.pbi') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl bg-secondary text-white text-center font-semibold text-xs hover:bg-slate-700 transition-colors shadow-xs">
                        Ajukan Sekarang
                    </a>
                </div>
            </div>

            <!-- Priority 3: Rehabilitasi Sosial -->
            <div class="rounded-2xl border-2 border-amber-200 bg-white p-7 custom-shadow-card hover:shadow-lg transition-all flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-tertiary-container text-white flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-2xl">support</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 text-[11px] font-bold">
                            Pendampingan Kasus
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-secondary uppercase">Rehabilitasi Sosial</span>
                    <h3 class="text-xl font-bold text-inverse-surface mt-1 mb-2 group-hover:text-tertiary-container transition-colors">
                        Pelayanan Rehabilitasi Sosial
                    </h3>
                    <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
                        Penanganan komprehensif bagi Pemerlu Pelayanan Kesejahteraan Sosial (PPKS): lansia terlantar, disabilitas, ODGJ terlantar, anak, dan korban kekerasan.
                    </p>
                    <div class="space-y-1.5 text-xs text-slate-600 mb-6 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-status-success">check_circle</span>
                            <span>Assessment kebutuhan dan rencana pelayanan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-status-success">check_circle</span>
                            <span>Pelayanan langsung dan/atau rujukan panti</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('layanan.detail', 'rehsos') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl border border-slate-300 text-slate-700 text-center font-semibold text-xs hover:bg-slate-50 transition-colors">
                        Detail Syarat
                    </a>
                    <a href="{{ route('pengaduan.create') }}" wire:navigate class="flex-1 py-2.5 px-3 rounded-xl bg-tertiary-container text-white text-center font-semibold text-xs hover:bg-tertiary transition-colors shadow-xs">
                        Lapor Kasus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: SEMUA JENIS LAYANAN LAINNYA -->
    <div>
        <h2 class="text-xl font-bold text-inverse-surface mb-6">Semua Jenis Layanan Terdaftar</h2>

        @if($services->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 custom-shadow-card">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl">search_off</span>
                </div>
                <h3 class="text-lg font-bold text-inverse-surface mb-1">Layanan Tidak Ditemukan</h3>
                <p class="text-sm text-on-surface-variant max-w-md mx-auto mb-6">
                    Tidak ada jenis layanan yang cocok dengan kata kunci "{{ $search }}" atau filter kategori yang dipilih.
                </p>
                <button wire:click="$set('search', ''); $set('selectedCategory', 'all');" class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-semibold hover:bg-primary-container">
                    Reset Pencarian
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($services as $service)
                    @php
                        $codeLower = strtolower($service->code);
                        $slug = match($codeLower) {
                            'dtsen' => 'dtsen',
                            'pbi' => 'pbi',
                            'rehsos' => 'rehsos',
                            default => $codeLower,
                        };
                        $routeTarget = match($codeLower) {
                            'dtsen' => route('pengajuan.dtsen'),
                            'pbi' => route('pengajuan.pbi'),
                            'rehsos' => route('pengaduan.create'),
                            default => route('pengajuan.umum', $slug),
                        };
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 p-6 custom-shadow-card hover:border-primary/40 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                    {{ $service->category }}
                                </span>
                                @if($service->sla_days)
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        {{ $service->sla_days }} hari
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-inverse-surface group-hover:text-primary transition-colors mb-2">
                                {{ $service->name }}
                            </h3>
                            <p class="text-xs text-on-surface-variant line-clamp-3 leading-relaxed mb-4">
                                {{ $service->description }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <a href="{{ route('layanan.detail', $slug) }}" wire:navigate class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                                <span>Informasi &amp; Syarat</span>
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                            <a href="{{ $routeTarget }}" wire:navigate class="px-3.5 py-1.5 rounded-lg bg-primary-container text-white text-xs font-semibold hover:bg-primary transition-colors shadow-xs">
                                Ajukan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- BOTTOM HELPLINE CTA -->
    <div class="mt-14 bg-surface-container-low border border-secondary-container/60 rounded-2xl p-6 md:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-xl bg-white text-primary flex items-center justify-center shadow-xs flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">contact_support</span>
            </div>
            <div>
                <h4 class="text-base font-bold text-inverse-surface">Bingung memilih jenis layanan sosial?</h4>
                <p class="text-xs text-on-surface-variant mt-0.5">Konsultasikan kebutuhan permohonan Anda kepada petugas layanan kami via WhatsApp.</p>
            </div>
        </div>
        <a href="https://wa.me/6281234567890?text=Halo%20Dinas%20Sosial%20Blitar,%20saya%20ingin%20konsultasi%20layanan" target="_blank" class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition-colors shadow-xs flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-lg">chat</span>
            <span>Tanya Petugas</span>
        </a>
    </div>
</div>
