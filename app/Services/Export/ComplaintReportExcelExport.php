<?php

namespace App\Services\Export;

use App\Enums\ComplaintStatus;
use App\Models\ComplaintCategory;
use App\Models\District;
use App\Services\DashboardQueryService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintReportExcelExport extends BaseExcelExporter
{
    protected string $title = 'Laporan Pengaduan Sosial';

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
        return 'laporan-pengaduan-'.now()->format('YmdHis');
    }

    public function build(): void
    {
        $complaints = DashboardQueryService::make($this->filters)->complaintsQuery()
            ->with(['complaintCategory', 'village.district', 'officer'])
            ->latest('reported_at')
            ->get();

        // ===== SHEET 1: RINGKASAN PER KATEGORI =====
        $sheetRingkasan = $this->spreadsheet->getActiveSheet();
        $sheetRingkasan->setTitle('Ringkasan');
        $this->buildSummarySheet($sheetRingkasan, $complaints);

        // ===== SHEET 2: DETAIL PENGADUAN =====
        $sheetDetail = $this->spreadsheet->createSheet();
        $sheetDetail->setTitle('Daftar Pengaduan');
        $this->buildDetailSheet($sheetDetail, $complaints);

        // ===== SHEET 3: PER KECAMATAN =====
        $sheetKecamatan = $this->spreadsheet->createSheet();
        $sheetKecamatan->setTitle('Per Kecamatan');
        $this->buildDistrictSheet($sheetKecamatan, $complaints);

        $this->spreadsheet->setActiveSheetIndex(0);
    }

    protected function buildSummarySheet(Worksheet $sheet, $complaints): void
    {
        $headers = ['No', 'Kategori Pengaduan', 'Diterima', 'Investigasi / Penanganan', 'Selesai', 'Tidak Valid / Duplikat', 'Total Laporan'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $categories = ComplaintCategory::where('is_active', true)->orderBy('name')->get();
        $currentRow = $headerRow + 1;
        $no = 1;

        $totMasuk = 0;
        $totProses = 0;
        $totSelesai = 0;
        $totTolak = 0;
        $totGrand = 0;

        foreach ($categories as $cat) {
            $matching = $complaints->where('complaint_category_id', $cat->id);

            $masuk = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['received']))->count();
            $proses = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['verified', 'disposition', 'in_investigation', 'in_handling']))->count();
            $selesai = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['resolved']))->count();
            $tolak = $matching->filter(fn ($c) => in_array($c->status?->value ?? (string) $c->status, ['invalid', 'duplicate']))->count();
            $totalRow = $matching->count();

            $totMasuk += $masuk;
            $totProses += $proses;
            $totSelesai += $selesai;
            $totTolak += $tolak;
            $totGrand += $totalRow;

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $cat->name);
            $sheet->setCellValue([3, $currentRow], $masuk);
            $sheet->setCellValue([4, $currentRow], $proses);
            $sheet->setCellValue([5, $currentRow], $selesai);
            $sheet->setCellValue([6, $currentRow], $tolak);
            $sheet->setCellValue([7, $currentRow], $totalRow);

            $currentRow++;
        }

        // Total
        $sheet->setCellValue([1, $currentRow], '');
        $sheet->setCellValue([2, $currentRow], 'TOTAL KESELURUHAN');
        $sheet->setCellValue([3, $currentRow], $totMasuk);
        $sheet->setCellValue([4, $currentRow], $totProses);
        $sheet->setCellValue([5, $currentRow], $totSelesai);
        $sheet->setCellValue([6, $currentRow], $totTolak);
        $sheet->setCellValue([7, $currentRow], $totGrand);

        $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFont()->setBold(true);
        $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$currentRow}");
        $this->applyZebraRowStyles($sheet, $headerRow + 1, $currentRow - 1, $colCount);
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDetailSheet(Worksheet $sheet, $complaints): void
    {
        $headers = [
            'No',
            'No. Aduan',
            'Nama Pelapor',
            'Telepon',
            'Kategori Aduan',
            'Lokasi / Alamat',
            'Desa / Kelurahan',
            'Kecamatan',
            'Status',
            'Tanggal Laporan',
            'Petugas Penangan',
            'Tindakan Diambil',
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

        foreach ($complaints as $c) {
            $statusLabel = $c->status instanceof ComplaintStatus ? $c->status->label() : (ComplaintStatus::tryFrom((string) $c->status)?->label() ?? (string) $c->status);

            $sheet->setCellValue([1, $currentRow], $no++);
            $sheet->setCellValue([2, $currentRow], $c->complaint_number);
            $sheet->setCellValue([3, $currentRow], $c->reporter_name);
            $sheet->setCellValue([4, $currentRow], $c->reporter_phone ? "'".$c->reporter_phone : '-');
            $sheet->setCellValue([5, $currentRow], $c->complaintCategory?->name ?? '-');
            $sheet->setCellValue([6, $currentRow], $c->location_detail ?? '-');
            $sheet->setCellValue([7, $currentRow], $c->village?->name ?? '-');
            $sheet->setCellValue([8, $currentRow], $c->village?->district?->name ?? '-');
            $sheet->setCellValue([9, $currentRow], $statusLabel);
            $sheet->setCellValue([10, $currentRow], $c->reported_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-');
            $sheet->setCellValue([11, $currentRow], $c->officer?->name ?? '-');
            $sheet->setCellValue([12, $currentRow], $c->action_taken ?? $c->verification_result ?? '-');
            $sheet->setCellValue([13, $currentRow], $c->resolved_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-');

            $currentRow++;
        }

        $lastDataRow = max($currentRow - 1, $headerRow);
        if ($complaints->isNotEmpty()) {
            $this->applyTableBorders($sheet, "A{$headerRow}:{$lastColLetter}{$lastDataRow}");
            $this->applyZebraRowStyles($sheet, $headerRow + 1, $lastDataRow, $colCount);
        }
        $this->autoFitColumns($sheet, $colCount);
    }

    protected function buildDistrictSheet(Worksheet $sheet, $complaints): void
    {
        $headers = ['No', 'Kecamatan', 'Jumlah Aduan Masuk', 'Persentase'];
        $colCount = count($headers);

        $startRow = $this->writeKopSurat($sheet, $colCount);
        $headerRow = $startRow;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue([$index + 1, $headerRow], $header);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);
        $this->applyHeaderStyle($sheet, "A{$headerRow}:{$lastColLetter}{$headerRow}");

        $districts = District::orderBy('name')->get();
        $total = $complaints->count();
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($districts as $district) {
            $count = $complaints->filter(fn ($c) => $c->village?->district_id === $district->id)->count();
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
