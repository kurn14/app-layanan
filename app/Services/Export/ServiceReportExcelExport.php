<?php

namespace App\Services\Export;

use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\ServiceType;
use App\Services\DashboardQueryService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceReportExcelExport extends BaseExcelExporter
{
    protected string $title = 'Laporan Pelayanan Sosial Terpadu';

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
        return 'laporan-pelayanan-'.now()->format('YmdHis');
    }

    public function build(): void
    {
        $requests = DashboardQueryService::make($this->filters)->serviceRequestsQuery()
            ->with(['serviceType', 'village.district', 'officer'])
            ->latest('submitted_at')
            ->get();

        // ===== SHEET 1: RINGKASAN PER JENIS LAYANAN =====
        $sheetRingkasan = $this->spreadsheet->getActiveSheet();
        $sheetRingkasan->setTitle('Ringkasan');
        $this->buildSummarySheet($sheetRingkasan, $requests);

        // ===== SHEET 2: DETAIL PENGAJUAN =====
        $sheetDetail = $this->spreadsheet->createSheet();
        $sheetDetail->setTitle('Daftar Pengajuan');
        $this->buildDetailSheet($sheetDetail, $requests);

        // ===== SHEET 3: PER KECAMATAN =====
        $sheetKecamatan = $this->spreadsheet->createSheet();
        $sheetKecamatan->setTitle('Per Kecamatan');
        $this->buildDistrictSheet($sheetKecamatan, $requests);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    protected function buildSummarySheet(Worksheet $sheet, $requests): void
    {
        $headers = ['No', 'Jenis Layanan', 'Menunggu / Proses', 'Disetujui / Terbit', 'Selesai', 'Ditolak', 'Total Berkas'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();
        $currentRow = $headerRow + 1;
        $no = 1;

        $totProses = 0;
        $totDisetujui = 0;
        $totSelesai = 0;
        $totDitolak = 0;
        $totGrand = 0;

        foreach ($serviceTypes as $type) {
            $matching = $requests->where('service_type_id', $type->id);

            $proses = $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['submitted', 'document_check', 'verification', 'eligibility_verification', 'data_verification', 'revision_requested', 'awaiting_approval']))->count();
            $disetujui = $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['recommendation_issued', 'proposed_to_ministry', 'ministry_approved', 'reactivated', 'issued']))->count();
            $selesai = $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['completed']))->count();
            $ditolak = $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['rejected', 'ministry_rejected']))->count();
            $totalRow = $matching->count();

            $totProses += $proses;
            $totDisetujui += $disetujui;
            $totSelesai += $selesai;
            $totDitolak += $ditolak;
            $totGrand += $totalRow;

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $type->name);
            $sheet->setCellValue([3, $currentRow], $proses);
            $sheet->setCellValue([4, $currentRow], $disetujui);
            $sheet->setCellValue([5, $currentRow], $selesai);
            $sheet->setCellValue([6, $currentRow], $ditolak);
            $sheet->setCellValue([7, $currentRow], $totalRow);

            $currentRow++;
        }

        // Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL KESELURUHAN');
        $sheet->setCellValue([3, $currentRow], $totProses);
        $sheet->setCellValue([4, $currentRow], $totDisetujui);
        $sheet->setCellValue([5, $currentRow], $totSelesai);
        $sheet->setCellValue([6, $currentRow], $totDitolak);
        $sheet->setCellValue([7, $currentRow], $totGrand);

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDetailSheet(Worksheet $sheet, $requests): void
    {
        $headers = [
            'No',
            'No. Tiket',
            'Nama Pemohon',
            'NIK',
            'Jenis Layanan',
            'Desa / Kelurahan',
            'Kecamatan',
            'Status',
            'Prioritas',
            'Tanggal Pengajuan',
            'Petugas',
            'Hasil Layanan / Catatan',
            'Tanggal Selesai',
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

        foreach ($requests as $r) {
            $statusLabel = $r->status instanceof ServiceRequestStatus ? $r->status->label() : (ServiceRequestStatus::tryFrom((string) $r->status)?->label() ?? (string) $r->status);

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $r->request_number);
            $sheet->setCellValue([3, $currentRow], $r->applicant_name);
            $sheet->setCellValue([4, $currentRow], "'".$r->applicant_nik);
            $sheet->setCellValue([5, $currentRow], $r->serviceType?->name ?? '-');
            $sheet->setCellValue([6, $currentRow], $r->village?->name ?? '-');
            $sheet->setCellValue([7, $currentRow], $r->village?->district?->name ?? '-');
            $sheet->setCellValue([8, $currentRow], $statusLabel);
            $sheet->setCellValue([9, $currentRow], $r->is_priority ? 'Prioritas' : 'Reguler');
            $sheet->setCellValue([10, $currentRow], $r->submitted_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-');
            $sheet->setCellValue([11, $currentRow], $r->officer?->name ?? '-');
            $sheet->setCellValue([12, $currentRow], $r->service_result ?? $r->rejection_reason ?? '-');
            $sheet->setCellValue([13, $currentRow], $r->completed_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-');

            $currentRow++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);
        if ($requests->isNotEmpty()) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDistrictSheet(Worksheet $sheet, $requests): void
    {
        $headers = ['No', 'Kecamatan', 'Jumlah Pengajuan', 'Persentase'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $districts = District::orderBy('name')->get();
        $total = $requests->count();
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($districts as $district) {
            $count = $requests->filter(fn ($r) => $r->village?->district_id === $district->id)->count();
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
