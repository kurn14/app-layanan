<?php

namespace App\Services\Export;

use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\ClientCategory;
use App\Models\Referral;
use App\Services\DashboardQueryService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RehabReportExcelExport extends BaseExcelExporter
{
    protected string $title = 'Laporan Pelayanan Rehabilitasi Sosial';

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
        return 'laporan-rehabilitasi-'.now()->format('YmdHis');
    }

    public function build(): void
    {
        $casesQuery = DashboardQueryService::make($this->filters)->rehabilitationCasesQuery()
            ->with(['client.category', 'client.village.district', 'officer'])
            ->latest('received_at');

        $cases = $casesQuery->get();

        // ===== SHEET 1: RINGKASAN PER KATEGORI KLIEN =====
        $sheetRingkasan = $this->spreadsheet->getActiveSheet();
        $sheetRingkasan->setTitle('Ringkasan');
        $this->buildSummarySheet($sheetRingkasan, $cases);

        // ===== SHEET 2: DETAIL KASUS =====
        $sheetKasus = $this->spreadsheet->createSheet();
        $sheetKasus->setTitle('Daftar Kasus');
        $this->buildCaseSheet($sheetKasus, $cases);

        // ===== SHEET 3: DETAIL RUJUKAN =====
        $sheetRujukan = $this->spreadsheet->createSheet();
        $sheetRujukan->setTitle('Daftar Rujukan');
        $this->buildReferralSheet($sheetRujukan, $cases);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    protected function buildSummarySheet(Worksheet $sheet, $cases): void
    {
        $headers = ['No', 'Kategori Klien', 'Diterima/Asesmen', 'Dalam Pelayanan', 'Monitoring', 'Selesai/Ditutup', 'Total Kasus'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $categories = ClientCategory::orderBy('name')->get();
        $currentRow = $headerRow + 1;
        $no = 1;

        $totAwal = 0;
        $totLayanan = 0;
        $totMonitor = 0;
        $totSelesai = 0;
        $totGrand = 0;

        foreach ($categories as $cat) {
            $matching = $cases->filter(fn ($c) => $c->client?->client_category_id === $cat->id);

            $awal = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['received', 'assessment', 'service_planning']))->count();
            $layanan = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['in_service']))->count();
            $monitor = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['monitoring']))->count();
            $selesai = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['closed']))->count();
            $totalRow = $matching->count();

            $totAwal += $awal;
            $totLayanan += $layanan;
            $totMonitor += $monitor;
            $totSelesai += $selesai;
            $totGrand += $totalRow;

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $cat->name);
            $sheet->setCellValue([3, $currentRow], $awal);
            $sheet->setCellValue([4, $currentRow], $layanan);
            $sheet->setCellValue([5, $currentRow], $monitor);
            $sheet->setCellValue([6, $currentRow], $selesai);
            $sheet->setCellValue([7, $currentRow], $totalRow);

            $currentRow++;
        }

        // Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL KESELURUHAN');
        $sheet->setCellValue([3, $currentRow], $totAwal);
        $sheet->setCellValue([4, $currentRow], $totLayanan);
        $sheet->setCellValue([5, $currentRow], $totMonitor);
        $sheet->setCellValue([6, $currentRow], $totSelesai);
        $sheet->setCellValue([7, $currentRow], $totGrand);

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildCaseSheet(Worksheet $sheet, $cases): void
    {
        $headers = [
            'No',
            'No. Kasus',
            'Nama Klien',
            'NIK Klien',
            'Kategori Klien',
            'Desa / Kelurahan',
            'Kecamatan',
            'Bentuk Penanganan',
            'Status',
            'Petugas',
            'Tanggal Diterima',
            'Hasil Penanganan',
            'Tanggal Ditutup',
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

        foreach ($cases as $c) {
            $statusLabel = $c->status instanceof RehabilitationCaseStatus ? $c->status->label() : (RehabilitationCaseStatus::tryFrom((string) $c->status)?->label() ?? (string) $c->status);
            $handlingLabel = $c->handling_type instanceof RehabilitationHandlingType ? $c->handling_type->label() : (RehabilitationHandlingType::tryFrom((string) $c->handling_type)?->label() ?? (string) $c->handling_type);

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $c->case_number);
            $sheet->setCellValue([3, $currentRow], $c->client?->name ?? '-');
            $sheet->setCellValue([4, $currentRow], "'".($c->client?->nik ?? ''));
            $sheet->setCellValue([5, $currentRow], $c->client?->category?->name ?? '-');
            $sheet->setCellValue([6, $currentRow], $c->client?->village?->name ?? '-');
            $sheet->setCellValue([7, $currentRow], $c->client?->village?->district?->name ?? '-');
            $sheet->setCellValue([8, $currentRow], $handlingLabel);
            $sheet->setCellValue([9, $currentRow], $statusLabel);
            $sheet->setCellValue([10, $currentRow], $c->officer?->name ?? '-');
            $sheet->setCellValue([11, $currentRow], $c->received_at?->timezone('Asia/Jakarta')->format('d/m/Y') ?? '-');
            $sheet->setCellValue([12, $currentRow], $c->handling_result ?? '-');
            $sheet->setCellValue([13, $currentRow], $c->closed_at?->timezone('Asia/Jakarta')->format('d/m/Y') ?? '-');

            $currentRow++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);
        if ($cases->isNotEmpty()) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildReferralSheet(Worksheet $sheet, $cases): void
    {
        $headers = ['No', 'No. Rujukan', 'No. Kasus', 'Nama Klien', 'Lembaga Rujukan Tujuan', 'Tanggal Rujukan', 'Status Rujukan', 'Hasil Layanan'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $caseIds = $cases->pluck('id');
        $referrals = Referral::whereIn('rehabilitation_case_id', $caseIds)
            ->with(['rehabilitationCase.client', 'institution'])
            ->latest('referral_date')
            ->get();

        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($referrals as $ref) {
            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $ref->referral_number ?? '-');
            $sheet->setCellValue([3, $currentRow], $ref->rehabilitationCase?->case_number ?? '-');
            $sheet->setCellValue([4, $currentRow], $ref->rehabilitationCase?->client?->name ?? '-');
            $sheet->setCellValue([5, $currentRow], $ref->institution?->name ?? '-');
            $sheet->setCellValue([6, $currentRow], $ref->referral_date?->format('d/m/Y') ?? '-');
            $sheet->setCellValue([7, $currentRow], $ref->status?->value ?? (string) $ref->status);
            $sheet->setCellValue([8, $currentRow], $ref->service_result ?? '-');

            $currentRow++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);
        if ($referrals->isNotEmpty()) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }
        $this->autoFitColumns($sheet, $colCount);
    }
}
