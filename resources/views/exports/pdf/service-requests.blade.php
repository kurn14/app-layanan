@extends('exports.pdf._layout')

@section('content')
    <table class="meta-table">
        <tr>
            <td class="label">Total Pengajuan</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $totalRecords }} berkas</strong></td>
        </tr>
        @if(!empty($filters['status']))
            <tr>
                <td class="label">Filter Status</td>
                <td class="separator">:</td>
                <td class="value">{{ \App\Enums\ServiceRequestStatus::tryFrom($filters['status'])?->label() ?? $filters['status'] }}</td>
            </tr>
        @endif
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">No. Tiket</th>
                <th style="width: 110px;">Nama Pemohon</th>
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
            @forelse($records as $index => $record)
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
                        Tidak ada data pengajuan layanan yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
