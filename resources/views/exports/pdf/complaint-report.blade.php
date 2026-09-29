@extends('exports.pdf._layout')

@section('content')
    <table class="meta-table">
        <tr>
            <td class="label">Total Pengaduan</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $totalRecords }} laporan</strong></td>
        </tr>
        @if(!empty($filters['status']))
            <tr>
                <td class="label">Filter Status</td>
                <td class="separator">:</td>
                <td class="value">{{ \App\Enums\ComplaintStatus::tryFrom($filters['status'])?->label() ?? $filters['status'] }}</td>
            </tr>
        @endif
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 85px;">No. Aduan</th>
                <th style="width: 100px;">Nama Pelapor</th>
                <th style="width: 95px;">Kategori Aduan</th>
                <th>Lokasi / Alamat Kejadian</th>
                <th style="width: 80px;">Desa/Kel.</th>
                <th style="width: 80px;">Kecamatan</th>
                <th style="width: 75px;">Status</th>
                <th style="width: 70px;">Tgl Laporan</th>
                <th style="width: 80px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                @php
                    $statusValue = $record->status instanceof \App\Enums\ComplaintStatus ? $record->status->value : (string)$record->status;
                    $statusLabel = $record->status instanceof \App\Enums\ComplaintStatus ? $record->status->label() : (\App\Enums\ComplaintStatus::tryFrom($statusValue)?->label() ?? $statusValue);
                    $badgeClass = match($statusValue) {
                        'resolved' => 'badge-success',
                        'received' => 'badge-gray',
                        'invalid', 'duplicate' => 'badge-danger',
                        'in_investigation', 'in_handling' => 'badge-warning',
                        default => 'badge-info',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $record->complaint_number }}</td>
                    <td>{{ $record->reporter_name }}</td>
                    <td>{{ $record->complaintCategory?->name ?? '-' }}</td>
                    <td>{{ $record->location_detail ?? '-' }}</td>
                    <td>{{ $record->village?->name ?? '-' }}</td>
                    <td>{{ $record->village?->district?->name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="text-center">{{ $record->reported_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $record->officer?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data pengaduan yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
