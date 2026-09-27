<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Akun Saya &amp; Riwayat Berkas</span>
    </nav>

    <!-- Profile Header Banner -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-2xl flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl md:text-2xl font-bold text-inverse-surface">{{ $user->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                            Warga Terverifikasi
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-1">
                        @if($user->nik)
                            <span>NIK: <strong class="font-mono text-slate-700">{{ $user->nik }}</strong></span>
                        @endif
                        @if($user->phone)
                            <span>WA: {{ $user->phone }}</span>
                        @endif
                        @if($user->village)
                            <span>Wilayah: {{ $user->village->name }}, Kec. {{ $user->village->district?->name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('layanan.index') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-primary text-white font-semibold text-xs hover:bg-primary-container shadow-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">add</span>
                    <span>Ajukan Layanan Baru</span>
                </a>
            </div>
        </div>

        <!-- STAT METRICS STRIP -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-100">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <span class="text-xs text-slate-400 block mb-1">Total Permohonan</span>
                <span class="text-2xl font-bold text-slate-900">{{ $totalRequests }}</span>
            </div>
            <div class="bg-teal-50/60 p-4 rounded-xl border border-teal-100">
                <span class="text-xs text-teal-800 block mb-1">Sedang Diproses</span>
                <span class="text-2xl font-bold text-primary">{{ $inProcessCount }}</span>
            </div>
            <div class="bg-emerald-50/60 p-4 rounded-xl border border-emerald-100">
                <span class="text-xs text-emerald-800 block mb-1">Surat Selesai / Terbit</span>
                <span class="text-2xl font-bold text-status-success">{{ $completedCount }}</span>
            </div>
            <div class="bg-red-50/60 p-4 rounded-xl border border-red-100">
                <span class="text-xs text-red-800 block mb-1">Laporan Pengaduan</span>
                <span class="text-2xl font-bold text-status-error">{{ $totalComplaints }}</span>
            </div>
        </div>
    </div>

    <!-- TABS: RIWAYAT PERMOHONAN & RIWAYAT PENGADUAN -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card overflow-hidden">
        <div class="flex items-center border-b border-slate-200 px-6 pt-4 gap-4">
            <button
                type="button"
                wire:click="$set('activeTab', 'pengajuan')"
                class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'pengajuan' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                <span class="material-symbols-outlined text-lg">folder_open</span>
                <span>Riwayat Pengajuan Layanan ({{ $totalRequests }})</span>
            </button>
            <button
                type="button"
                wire:click="$set('activeTab', 'pengaduan')"
                class="pb-4 text-sm font-bold border-b-2 transition-all flex items-center gap-2 {{ $activeTab === 'pengaduan' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                <span class="material-symbols-outlined text-lg">campaign</span>
                <span>Riwayat Pengaduan ({{ $totalComplaints }})</span>
            </button>
        </div>

        <div class="p-6">
            <!-- TAB 1: SERVICE REQUESTS -->
            @if ($activeTab === 'pengajuan')
                @if ($serviceRequests->isEmpty())
                    <div class="py-12 text-center text-slate-500 text-xs">
                        <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">description</span>
                        Belum ada permohonan layanan sosial yang diajukan dengan akun ini.
                        <div class="mt-4">
                            <a href="{{ route('layanan.index') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-primary text-white font-semibold text-xs hover:bg-primary-container">
                                Mulai Ajukan Layanan
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">Nomor Tiket</th>
                                    <th class="py-3 px-4">Jenis Layanan</th>
                                    <th class="py-3 px-4">Nama Pemohon</th>
                                    <th class="py-3 px-4">Tanggal Pengajuan</th>
                                    <th class="py-3 px-4">Status Terkini</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($serviceRequests as $req)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 font-mono font-bold text-primary">
                                            {{ $req->request_number }}
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                                            {{ $req->serviceType?->name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600">
                                            {{ $req->applicant_name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $req->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @php
                                                $val = is_object($req->status) ? $req->status->value : $req->status;
                                            @endphp
                                            @if(in_array($val, ['issued', 'completed', 'reactivated']))
                                                <span class="inline-block px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                                                    Selesai / Terbit
                                                </span>
                                            @elseif($val === 'revision_requested')
                                                <span class="inline-block px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-bold text-[11px]">
                                                    Perlu Perbaikan
                                                </span>
                                            @elseif($val === 'rejected')
                                                <span class="inline-block px-2.5 py-1 rounded-full bg-red-100 text-red-900 font-bold text-[11px]">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="inline-block px-2.5 py-1 rounded-full bg-teal-100 text-teal-800 font-bold text-[11px]">
                                                    {{ ucfirst(str_replace('_', ' ', $val)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('cek-status', ['ticket' => $req->request_number]) }}" wire:navigate class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-white text-xs font-semibold inline-flex items-center gap-1 shadow-2xs">
                                                <span class="material-symbols-outlined text-sm">visibility</span>
                                                <span>Lacak</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            <!-- TAB 2: COMPLAINTS -->
            @if ($activeTab === 'pengaduan')
                @if ($complaints->isEmpty())
                    <div class="py-12 text-center text-slate-500 text-xs">
                        <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">campaign</span>
                        Belum ada laporan pengaduan sosial yang pernah Anda sampaikan.
                        <div class="mt-4">
                            <a href="{{ route('pengaduan.create') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-status-error text-white font-semibold text-xs hover:bg-red-700">
                                Sampaikan Pengaduan Baru
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">Nomor Laporan</th>
                                    <th class="py-3 px-4">Kategori Masalah</th>
                                    <th class="py-3 px-4">Lokasi Kejadian</th>
                                    <th class="py-3 px-4">Waktu Lapor</th>
                                    <th class="py-3 px-4">Status Penanganan</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($complaints as $c)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-4 font-mono font-bold text-status-error">
                                            {{ $c->complaint_number }}
                                        </td>
                                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                                            {{ $c->complaintCategory?->name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600">
                                            {{ $c->location_detail }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-500">
                                            {{ $c->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-block px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-[11px]">
                                                {{ ucfirst(str_replace('_', ' ', $c->status->value)) }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('cek-status', ['ticket' => $c->complaint_number]) }}" wire:navigate class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-white text-xs font-semibold inline-flex items-center gap-1 shadow-2xs">
                                                <span class="material-symbols-outlined text-sm">visibility</span>
                                                <span>Lacak</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
