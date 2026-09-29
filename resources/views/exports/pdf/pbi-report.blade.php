@extends('exports.pdf._layout')

@section('content')
    <table class="meta-table">
        <tr>
            <td class="label">Total Pengajuan Peserta</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $totalRecords }} peserta</strong></td>
        </tr>
    </table>

    <div class="summary-card">
        <div class="summary-card-title">1. Ringkasan Pengajuan per Alasan & Keputusan Kemensos</div>
        <table class="data-table" style="margin-top: 4px; margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Alasan Reaktivasi PBI-JK</th>
                    <th style="width: 80px;">Dalam Proses</th>
                    <th style="width: 80px;">Disetujui</th>
                    <th style="width: 80px;">Ditolak</th>
                    <th style="width: 80px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tp = 0; $ts = 0; $td = 0; $tt = 0;
                @endphp
                @foreach($summary as $i => $row)
                    @php
                        $tp += $row['proses'];
                        $ts += $row['disetujui'];
                        $td += $row['ditolak'];
                        $tt += $row['total'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-center">{{ $row['proses'] }}</td>
                        <td class="text-center font-bold" style="color: #15803d;">{{ $row['disetujui'] }}</td>
                        <td class="text-center font-bold" style="color: #b91c1c;">{{ $row['ditolak'] }}</td>
                        <td class="text-center font-bold">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $tp }}</td>
                    <td class="text-center" style="color: #15803d;">{{ $ts }}</td>
                    <td class="text-center" style="color: #b91c1c;">{{ $td }}</td>
                    <td class="text-center">{{ $tt }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 14px; margin-bottom: 4px; font-weight: bold; font-size: 8.5pt; text-transform: uppercase; color: #1e293b;">
        2. Daftar Detail Pengajuan Peserta PBI-JK
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">No. Tiket</th>
                <th style="width: 105px;">Nama Peserta</th>
                <th style="width: 85px;">NIK</th>
                <th style="width: 85px;">No. BPJS</th>
                <th>Alasan Reaktivasi</th>
                <th style="width: 80px;">Desa/Kel.</th>
                <th style="width: 80px;">Kecamatan</th>
                <th style="width: 85px;">Keputusan Kemensos</th>
                <th style="width: 65px;">Tgl Reaktivasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reactivations as $index => $item)
                @php
                    $village = $item->serviceRequest?->village;
                    $district = $village?->district;
                    $reasonLabel = $item->reason instanceof \App\Enums\PbiReactivationReason ? $item->reason->label() : (\App\Enums\PbiReactivationReason::tryFrom((string)$item->reason)?->label() ?? (string)$item->reason);
                    $decisionValue = $item->ministry_decision?->value ?? (string)$item->ministry_decision;
                    $decisionBadge = match($decisionValue) {
                        'approved', 'reactivated' => 'badge-success',
                        'rejected', 'ministry_rejected' => 'badge-danger',
                        'pending' => 'badge-warning',
                        default => 'badge-gray',
                    };
                    $decisionText = match($decisionValue) {
                        'approved', 'reactivated' => 'Disetujui',
                        'rejected', 'ministry_rejected' => 'Ditolak',
                        'pending' => 'Diajukan',
                        default => '-',
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->serviceRequest?->request_number ?? '-' }}</td>
                    <td>{{ $item->participant_name ?? '-' }}</td>
                    <td>{{ $item->participant_nik ?? '-' }}</td>
                    <td>{{ $item->bpjs_card_number ?? '-' }}</td>
                    <td>{{ $reasonLabel }}</td>
                    <td>{{ $village?->name ?? '-' }}</td>
                    <td>{{ $district?->name ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $decisionBadge }}">{{ $decisionText }}</span>
                    </td>
                    <td class="text-center">{{ $item->reactivated_date?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data pengajuan reaktivasi PBI-JK yang sesuai kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
