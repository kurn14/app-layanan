<?php

namespace App\Services\Export;

use App\Enums\PbiReactivationReason;
use App\Models\District;
use App\Services\DashboardQueryService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PbiReportExcelExport extends BaseExcelExporter
{
    protected string $title = 'Rekapitulasi Reaktivasi PBI-JK';

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
        return 'rekap-pbi-jk-'.now()->format('YmdHis');
    }

    public function build(): void
    {
        $pbiQuery = DashboardQueryService::make($this->filters)->pbiReactivationsQuery()
            ->with(['serviceRequest.village.district', 'signer'])
            ->latest('created_at');

        $reactivations = $pbiQuery->get();

        // ===== SHEET 1: RINGKASAN PER ALASAN & KEPUTUSAN =====
        $sheetRingkasan = $this->spreadsheet->getActiveSheet();
        $sheetRingkasan->setTitle('Ringkasan');
        $this->buildSummarySheet($sheetRingkasan, $reactivations);

        // ===== SHEET 2: DETAIL PENGAJUAN PESERTA =====
        $sheetDetail = $this->spreadsheet->createSheet();
        $sheetDetail->setTitle('Daftar Peserta');
        $this->buildDetailSheet($sheetDetail, $reactivations);

        // ===== SHEET 3: PER KECAMATAN =====
        $sheetKecamatan = $this->spreadsheet->createSheet();
        $sheetKecamatan->setTitle('Per Kecamatan');
        $this->buildDistrictSheet($sheetKecamatan, $reactivations);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    protected function buildSummarySheet(Worksheet $sheet, $reactivations): void
    {
        $headers = ['No', 'Alasan Pengajuan', 'Diajukan', 'Disetujui Kemensos', 'Ditolak Kemensos', 'Total Berkas'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $reasons = PbiReactivationReason::cases();
        $currentRow = $headerRow + 1;
        $no = 1;

        $totDiajukan = 0;
        $totDisetujui = 0;
        $totDitolak = 0;
        $totGrand = 0;

        foreach ($reasons as $reason) {
            $matching = $reactivations->filter(fn ($r) => ($r->reason instanceof PbiReactivationReason ? $r->reason->value : (string) $r->reason) === $reason->value);

            $disetujui = $matching->filter(fn ($r) => in_array($r->ministry_decision?->value ?? (string) $r->ministry_decision, ['approved', 'reactivated']))->count();
            $ditolak = $matching->filter(fn ($r) => in_array($r->ministry_decision?->value ?? (string) $r->ministry_decision, ['rejected', 'ministry_rejected']))->count();
            $diajukan = $matching->count() - $disetujui - $ditolak;
            $totalRow = $matching->count();

            $totDiajukan += $diajukan;
            $totDisetujui += $disetujui;
            $totDitolak += $ditolak;
            $totGrand += $totalRow;

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $reason->label());
            $sheet->setCellValue([3, $currentRow], $diajukan);
            $sheet->setCellValue([4, $currentRow], $disetujui);
            $sheet->setCellValue([5, $currentRow], $ditolak);
            $sheet->setCellValue([6, $currentRow], $totalRow);

            $currentRow++;
        }

        // Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL KESELURUHAN');
        $sheet->setCellValue([3, $currentRow], $totDiajukan);
        $sheet->setCellValue([4, $currentRow], $totDisetujui);
        $sheet->setCellValue([5, $currentRow], $totDitolak);
        $sheet->setCellValue([6, $currentRow], $totGrand);

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDetailSheet(Worksheet $sheet, $reactivations): void
    {
        $headers = [
            'No',
            'No. Tiket',
            'Nama Peserta',
            'NIK Peserta',
            'No. Kartu BPJS',
            'Alasan Reaktivasi',
            'Fasilitas Kesehatan',
            'Desa / Kelurahan',
            'Kecamatan',
            'No. Rekomendasi',
            'Keputusan Kemensos',
            'Tgl Reaktivasi',
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

        foreach ($reactivations as $item) {
            $village = $item->serviceRequest?->village;
            $district = $village?->district;

            $reasonLabel = $item->reason instanceof PbiReactivationReason ? $item->reason->label() : (PbiReactivationReason::tryFrom((string) $item->reason)?->label() ?? (string) $item->reason);

            $decisionLabel = match ($item->ministry_decision?->value ?? (string) $item->ministry_decision) {
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'pending' => 'Proses Pengusulan',
                default => '-'
            };

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $item->serviceRequest?->request_number ?? '-');
            $sheet->setCellValue([3, $currentRow], $item->participant_name ?? '-');
            $sheet->setCellValue([4, $currentRow], "'".($item->participant_nik ?? ''));
            $sheet->setCellValue([5, $currentRow], "'".($item->bpjs_card_number ?? ''));
            $sheet->setCellValue([6, $currentRow], $reasonLabel);
            $sheet->setCellValue([7, $currentRow], $item->health_facility_name ?? '-');
            $sheet->setCellValue([8, $currentRow], $village?->name ?? '-');
            $sheet->setCellValue([9, $currentRow], $district?->name ?? '-');
            $sheet->setCellValue([10, $currentRow], $item->recommendation_number ?? '-');
            $sheet->setCellValue([11, $currentRow], $decisionLabel);
            $sheet->setCellValue([12, $currentRow], $item->reactivated_date?->format('d/m/Y') ?? '-');

            $currentRow++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);
        if ($reactivations->isNotEmpty()) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDistrictSheet(Worksheet $sheet, $reactivations): void
    {
        $headers = ['No', 'Kecamatan', 'Jumlah Peserta Diajukan', 'Persentase'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $districts = District::orderBy('name')->get();
        $total = $reactivations->count();
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($districts as $district) {
            $count = $reactivations->filter(fn ($r) => $r->serviceRequest?->village?->district_id === $district->id)->count();
            $percentage = $total > 0 ? round(($count / $total) * 100, 1).'%' : '0%';

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $district->name);
            $sheet->setCellValue([3, $currentRow], $count);
            $sheet->setCellValue([4, $currentRow], $percentage);

            $currentRow++;
        }

        // Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL');
        $sheet->setCellValue([3, $currentRow], $total);
        $sheet->setCellValue([4, $currentRow], '100%');

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }
}
