@extends('exports.pdf._layout')

@section('content')
    <table class="meta-table">
        <tr>
            <td class="label">Total SK Diterbitkan</td>
            <td class="separator">:</td>
            <td class="value"><strong>{{ $totalCertificates }} surat</strong></td>
        </tr>
    </table>

    <div class="summary-card">
        <div class="summary-card-title">1. Ringkasan Penerbitan per Tujuan Penggunaan & Desil</div>
        <table class="data-table" style="margin-top: 4px; margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Tujuan Penggunaan SK DTSEN</th>
                    <th style="width: 50px;">Desil 1</th>
                    <th style="width: 50px;">Desil 2</th>
                    <th style="width: 50px;">Desil 3</th>
                    <th style="width: 50px;">Desil 4</th>
                    <th style="width: 80px;">Non-Desil / >4</th>
                    <th style="width: 70px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $t1 = 0; $t2 = 0; $t3 = 0; $t4 = 0; $tn = 0; $tt = 0;
                @endphp
                @foreach($summary as $i => $row)
                    @php
                        $t1 += $row['d1'];
                        $t2 += $row['d2'];
                        $t3 += $row['d3'];
                        $t4 += $row['d4'];
                        $tn += $row['non'];
                        $tt += $row['total'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td class="text-center">{{ $row['d1'] }}</td>
                        <td class="text-center">{{ $row['d2'] }}</td>
                        <td class="text-center">{{ $row['d3'] }}</td>
                        <td class="text-center">{{ $row['d4'] }}</td>
                        <td class="text-center">{{ $row['non'] }}</td>
                        <td class="text-center font-bold">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ $t1 }}</td>
                    <td class="text-center">{{ $t2 }}</td>
                    <td class="text-center">{{ $t3 }}</td>
                    <td class="text-center">{{ $t4 }}</td>
                    <td class="text-center">{{ $tn }}</td>
                    <td class="text-center">{{ $tt }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 14px; margin-bottom: 4px; font-weight: bold; font-size: 8.5pt; text-transform: uppercase; color: #1e293b;">
        2. Daftar Surat Keterangan yang Diterbitkan
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 110px;">No. SK DTSEN</th>
                <th style="width: 110px;">Nama Subjek</th>
                <th style="width: 90px;">NIK</th>
                <th>Tujuan Surat</th>
                <th style="width: 50px;">Desil</th>
                <th style="width: 80px;">Desa/Kel.</th>
                <th style="width: 80px;">Kecamatan</th>
                <th style="width: 65px;">Tgl Terbit</th>
                <th style="width: 75px;">Kode Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($certificates as $index => $cert)
                @php
                    $village = $cert->serviceRequest?->village;
                    $district = $village?->district;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $cert->certificate_number ?? '-' }}</td>
                    <td>{{ $cert->subject_name ?? $cert->serviceRequest?->applicant_name ?? '-' }}</td>
                    <td>{{ $cert->subject_nik ?? $cert->serviceRequest?->applicant_nik ?? '-' }}</td>
                    <td>{{ $cert->dtsenPurpose?->name ?? $cert->purpose_description ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $cert->decile && $cert->decile <= 4 ? 'badge-primary' : 'badge-gray' }}">
                            {{ $cert->decile ? 'Desil ' . $cert->decile : '-' }}
                        </span>
                    </td>
                    <td>{{ $village?->name ?? '-' }}</td>
                    <td>{{ $district?->name ?? '-' }}</td>
                    <td class="text-center">{{ $cert->issued_at?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center" style="font-family: monospace; font-size: 7.5pt;">{{ $cert->verification_code ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada surat keterangan yang sesuai dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
