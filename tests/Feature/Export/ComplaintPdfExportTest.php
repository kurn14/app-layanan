<?php

namespace Tests\Feature\Export;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Village;
use App\Services\Export\ComplaintPdfExport;
use Tests\TestCase;

class ComplaintPdfExportTest extends TestCase
{
    public function test_complaint_pdf_export_generates_file(): void
    {
        $category = ComplaintCategory::first() ?? ComplaintCategory::create([
            'name' => 'Kategori Aduan Uji',
            'is_active' => true,
        ]);

        $village = Village::first();

        Complaint::create([
            'complaint_number' => 'ADU-TEST-'.uniqid(),
            'complaint_category_id' => $category->id,
            'reporter_name' => 'Pelapor Uji',
            'reporter_phone' => '081234567890',
            'location_detail' => 'Jl. Raya Kanigoro No. 12',
            'village_id' => $village?->id,
            'description' => 'Aduan uji untuk pengetesan PDF',
            'status' => 'received',
            'reported_at' => now(),
        ]);

        $exporter = new ComplaintPdfExport;
        $filePath = storage_path('app/test_export_complaint.pdf');

        $exporter->saveToFile($filePath);

        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));

        @unlink($filePath);
    }

    public function test_complaint_pdf_download_returns_response(): void
    {
        $exporter = new ComplaintPdfExport;
        $response = $exporter->download();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
