<?php

namespace App\Services\Export;

use App\Models\District;
use App\Models\DtsenPurpose;
use App\Services\DashboardQueryService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DtsenReportExcelExport extends BaseExcelExporter
{
    protected string $title = 'Rekapitulasi Surat Keterangan DTSEN';

    public function __construct(array $filters = [])
    {
        parent::__construct($filters);

        if (! empty($filters['startDate']) && ! empty($filters['endDate'])) {
            $this->subtitle = 'Periode: '.$filters['startDate'].' s/d '.$filters['endDate'];
        }
    }

    protected function getHeaders(): array
    {
        return [];
    }

    protected function getRows(): iterable
    {
        return [];
    }

    protected function getFilename(): string
    {
        return 'rekap-sk-dtsen-'.now()->format('YmdHis');
    }

    public function build(): void
    {
        $dtsenQuery = DashboardQueryService::make($this->filters)->dtsenCertificatesQuery()
            ->with(['serviceRequest.village.district', 'dtsenPurpose', 'signer'])
            ->latest('issued_at');

        $certificates = $dtsenQuery->get();

        // ===== SHEET 1: RINGKASAN PER TUJUAN & DESIL =====
        $sheetRingkasan = $this->spreadsheet->getActiveSheet();
        $sheetRingkasan->setTitle('Ringkasan');
        $this->buildSummarySheet($sheetRingkasan, $certificates);

        // ===== SHEET 2: DETAIL SURAT =====
        $sheetDetail = $this->spreadsheet->createSheet();
        $sheetDetail->setTitle('Daftar Surat');
        $this->buildDetailSheet($sheetDetail, $certificates);

        // ===== SHEET 3: PER KECAMATAN =====
        $sheetKecamatan = $this->spreadsheet->createSheet();
        $sheetKecamatan->setTitle('Per Kecamatan');
        $this->buildDistrictSheet($sheetKecamatan, $certificates);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    protected function buildSummarySheet(Worksheet $sheet, $certificates): void
    {
        $headers = ['No', 'Tujuan Penggunaan', 'Desil 1', 'Desil 2', 'Desil 3', 'Desil 4', 'Non-Desil / >4', 'Total Surat'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $purposes = DtsenPurpose::orderBy('name')->get();
        $currentRow = $headerRow + 1;
        $no = 1;

        $totalDesil1 = 0;
        $totalDesil2 = 0;
        $totalDesil3 = 0;
        $totalDesil4 = 0;
        $totalNonDesil = 0;
        $grandTotal = 0;

        foreach ($purposes as $purpose) {
            $matching = $certificates->where('dtsen_purpose_id', $purpose->id);
            $d1 = $matching->where('decile', 1)->count();
            $d2 = $matching->where('decile', 2)->count();
            $d3 = $matching->where('decile', 3)->count();
            $d4 = $matching->where('decile', 4)->count();
            $non = $matching->filter(fn ($c) => empty($c->decile) || $c->decile > 4)->count();
            $totalRow = $matching->count();

            $totalDesil1 += $d1;
            $totalDesil2 += $d2;
            $totalDesil3 += $d3;
            $totalDesil4 += $d4;
            $totalNonDesil += $non;
            $grandTotal += $totalRow;

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $purpose->name);
            $sheet->setCellValue([3, $currentRow], $d1);
            $sheet->setCellValue([4, $currentRow], $d2);
            $sheet->setCellValue([5, $currentRow], $d3);
            $sheet->setCellValue([6, $currentRow], $d4);
            $sheet->setCellValue([7, $currentRow], $non);
            $sheet->setCellValue([8, $currentRow], $totalRow);

            $currentRow++;
        }

        // Baris Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL KESELURUHAN');
        $sheet->setCellValue([3, $currentRow], $totalDesil1);
        $sheet->setCellValue([4, $currentRow], $totalDesil2);
        $sheet->setCellValue([5, $currentRow], $totalDesil3);
        $sheet->setCellValue([6, $currentRow], $totalDesil4);
        $sheet->setCellValue([7, $currentRow], $totalNonDesil);
        $sheet->setCellValue([8, $currentRow], $grandTotal);

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDetailSheet(Worksheet $sheet, $certificates): void
    {
        $headers = [
            'No',
            'No. SK DTSEN',
            'Nama Terdaftar',
            'NIK',
            'Tujuan Surat',
            'Desil',
            'Status Terdaftar',
            'Desa / Kelurahan',
            'Kecamatan',
            'Tanggal Terbit',
            'Masa Berlaku',
            'Kode Verifikasi',
        ];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($certificates as $cert) {
            $village = $cert->serviceRequest?->village;
            $district = $village?->district;

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $cert->certificate_number ?? '-');
            $sheet->setCellValue([3, $currentRow], $cert->subject_name ?? $cert->serviceRequest?->applicant_name ?? '-');
            $sheet->setCellValue([4, $currentRow], "'".($cert->subject_nik ?? $cert->serviceRequest?->applicant_nik ?? ''));
            $sheet->setCellValue([5, $currentRow], $cert->dtsenPurpose?->name ?? $cert->purpose_description ?? '-');
            $sheet->setCellValue([6, $currentRow], $cert->decile ? "Desil {$cert->decile}" : '-');
            $sheet->setCellValue([7, $currentRow], $cert->is_registered ? 'Terdaftar' : 'Tidak Terdaftar');
            $sheet->setCellValue([8, $currentRow], $village?->name ?? '-');
            $sheet->setCellValue([9, $currentRow], $district?->name ?? '-');
            $sheet->setCellValue([10, $currentRow], $cert->issued_at?->timezone('Asia/Jakarta')->format('d/m/Y') ?? '-');
            $sheet->setCellValue([11, $currentRow], $cert->valid_until?->format('d/m/Y') ?? '-');
            $sheet->setCellValue([12, $currentRow], $cert->verification_code ?? '-');

            $currentRow++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);
        if ($certificates->isNotEmpty()) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDistrictSheet(Worksheet $sheet, $certificates): void
    {
        $headers = ['No', 'Kecamatan', 'Jumlah Surat Terbit', 'Persentase'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $districts = District::orderBy('name')->get();
        $totalCertificates = $certificates->count();
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($districts as $district) {
            $count = $certificates->filter(fn ($c) => $c->serviceRequest?->village?->district_id === $district->id)->count();
            $percentage = $totalCertificates > 0 ? round(($count / $totalCertificates) * 100, 1).'%' : '0%';

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $district->name);
            $sheet->setCellValue([3, $currentRow], $count);
            $sheet->setCellValue([4, $currentRow], $percentage);

            $currentRow++;
        }

        // Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL');
        $sheet->setCellValue([3, $currentRow], $totalCertificates);
        $sheet->setCellValue([4, $currentRow], '100%');

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }
}
