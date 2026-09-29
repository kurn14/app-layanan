@extends('exports.pdf._layout')

@section('content')
    <table class="meta-table">
        <tr>
            <td class="label">Total Pengajuan</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $totalRecords }} berkas pelayanan</strong></td>
        </tr>
    </table>

    <div class="summary-card">
        <div class="summary-card-title">1. Ringkasan Pengajuan per Jenis Layanan & Status</div>
        <table class="data-table" style="margin-top: 4px; margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Jenis Layanan Sosial</th>
                    <th style="width: 75px;">Proses</th>
                    <th style="width: 75px;">Disetujui</th>
                    <th style="width: 75px;">Selesai</th>
                    <th style="width: 75px;">Ditolak</th>
                    <th style="width: 75px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tp = 0; $td = 0; $ts = 0; $tr = 0; $tt = 0;
                @endphp
                @foreach($summary as $i => $row)
                    @php
                        $tp += $row['proses'];
                        $td += $row['disetujui'];
                        $ts += $row['selesai'];
                        $tr += $row['ditolak'];
                        $tt += $row['total'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-center">{{ $row['proses'] }}</td>
                        <td class="text-center">{{ $row['disetujui'] }}</td>
                        <td class="text-center font-bold" style="color: #15803d;">{{ $row['selesai'] }}</td>
                        <td class="text-center" style="color: #b91c1c;">{{ $row['ditolak'] }}</td>
                        <td class="text-center font-bold">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $tp }}</td>
                    <td class="text-center">{{ $td }}</td>
                    <td class="text-center" style="color: #15803d;">{{ $ts }}</td>
                    <td class="text-center" style="color: #b91c1c;">{{ $tr }}</td>
                    <td class="text-center">{{ $tt }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 14px; margin-bottom: 4px; font-weight: bold; font-size: 8.5pt; text-transform: uppercase; color: #1e293b;">
        2. Daftar Pengajuan Pelayanan
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">No. Tiket</th>
                <th style="width: 105px;">Nama Pemohon</th>
                <th style="width: 85px;">NIK</th>
                <th>Jenis Layanan</th>
                <th style="width: 80px;">Desa/Kel.</th>
                <th style="width: 80px;">Kecamatan</th>
                <th style="width: 75px;">Status</th>
                <th style="width: 65px;">Tgl Pengajuan</th>
                <th style="width: 80px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $index => $record)
                @php
                    $statusValue = $record->status instanceof \App\Enums\ServiceRequestStatus ? $record->status->value : (string)$record->status;
                    $statusLabel = $record->status instanceof \App\Enums\ServiceRequestStatus ? $record->status->label() : (\App\Enums\ServiceRequestStatus::tryFrom($statusValue)?->label() ?? $statusValue);
                    $badgeClass = match($statusValue) {
                        'completed' => 'badge-success',
                        'submitted' => 'badge-gray',
                        'rejected', 'ministry_rejected' => 'badge-danger',
                        'revision_requested' => 'badge-warning',
                        'issued', 'reactivated' => 'badge-primary',
                        default => 'badge-info',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $record->request_number }}</td>
                    <td>{{ $record->applicant_name }}</td>
                    <td>{{ $record->applicant_nik }}</td>
                    <td>{{ $record->serviceType?->name ?? '-' }}</td>
                    <td>{{ $record->village?->name ?? '-' }}</td>
                    <td>{{ $record->village?->district?->name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="text-center">{{ $record->submitted_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $record->officer?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada pengajuan pelayanan yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
