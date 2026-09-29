<?php

namespace Tests\Feature\Export;

use App\Services\Export\DtsenReportExcelExport;
use App\Services\Export\DtsenReportPdfExport;
use Tests\TestCase;

class DtsenReportExportTest extends TestCase
{
    public function test_dtsen_excel_report_generates_multi_sheet_file(): void
    {
        $exporter = new DtsenReportExcelExport(['startDate' => '2026-01-01', 'endDate' => '2026-12-31']);
        $filePath = storage_path('app/test_dtsen_report.xlsx');

        $exporter->saveToFile($filePath);

        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));

        @unlink($filePath);
    }

    public function test_dtsen_pdf_report_generates_file(): void
    {
        $exporter = new DtsenReportPdfExport(['startDate' => '2026-01-01', 'endDate' => '2026-12-31']);
        $filePath = storage_path('app/test_dtsen_report.pdf');

        $exporter->saveToFile($filePath);

        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));

        @unlink($filePath);
    }
}
