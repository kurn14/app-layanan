@extends('exports.pdf._layout')

@section('content')
    <table class="meta-table">
        <tr>
            <td class="label">Total Kasus</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $totalRecords }} kasus</strong></td>
        </tr>
        @if(!empty($filters['status']))
            <tr>
                <td class="label">Filter Status</td>
                <td class="separator">:</td>
                <td class="value">{{ \App\Enums\RehabilitationCaseStatus::tryFrom($filters['status'])?->label() ?? $filters['status'] }}</td>
            </tr>
        @endif
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 85px;">No. Kasus</th>
                <th style="width: 105px;">Nama Klien</th>
                <th style="width: 95px;">Kategori Klien</th>
                <th style="width: 80px;">Desa/Kel.</th>
                <th style="width: 80px;">Kecamatan</th>
                <th style="width: 90px;">Jenis Penanganan</th>
                <th style="width: 75px;">Status</th>
                <th style="width: 70px;">Tgl Diterima</th>
                <th style="width: 80px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                @php
                    $statusValue = $record->status instanceof \App\Enums\RehabilitationCaseStatus ? $record->status->value : (string)$record->status;
                    $statusLabel = $record->status instanceof \App\Enums\RehabilitationCaseStatus ? $record->status->label() : (\App\Enums\RehabilitationCaseStatus::tryFrom($statusValue)?->label() ?? $statusValue);
                    $badgeClass = match($statusValue) {
                        'closed' => 'badge-success',
                        'received' => 'badge-gray',
                        'cancelled' => 'badge-danger',
                        'in_assessment', 'in_handling' => 'badge-warning',
                        'referred' => 'badge-purple',
                        default => 'badge-info',
                    };
                    $handlingTypeLabel = $record->handling_type instanceof \App\Enums\RehabilitationHandlingType
                        ? $record->handling_type->label()
                        : (\App\Enums\RehabilitationHandlingType::tryFrom((string)$record->handling_type)?->label() ?? (string)$record->handling_type);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $record->case_number }}</td>
                    <td>{{ $record->client?->name ?? '-' }}</td>
                    <td>{{ $record->client?->category?->name ?? '-' }}</td>
                    <td>{{ $record->client?->village?->name ?? '-' }}</td>
                    <td>{{ $record->client?->village?->district?->name ?? '-' }}</td>
                    <td>{{ $handlingTypeLabel }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="text-center">{{ $record->received_at?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $record->officer?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data kasus rehabilitasi yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
