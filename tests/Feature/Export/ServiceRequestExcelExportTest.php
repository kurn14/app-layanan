<?php

namespace Tests\Feature\Export;

use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\Village;
use App\Services\Export\ServiceRequestExcelExport;
use Tests\TestCase;

class ServiceRequestExcelExportTest extends TestCase
{
    public function test_service_request_excel_export_generates_file(): void
    {
        $serviceType = ServiceType::first() ?? ServiceType::create([
            'name' => 'Layanan Uji',
            'code' => 'TEST',
            'handler' => 'general',
            'is_active' => true,
        ]);

        $village = Village::first();

        ServiceRequest::create([
            'request_number' => 'REQ-TEST-'.uniqid(),
            'service_type_id' => $serviceType->id,
            'applicant_name' => 'Warga Uji',
            'applicant_nik' => '3505010101010001',
            'family_card_number' => '3505010101010002',
            'address' => 'Jl. Uji No. 1',
            'village_id' => $village?->id,
            'phone' => '081234567890',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $exporter = new ServiceRequestExcelExport;
        $filePath = storage_path('app/test_export_sr.xlsx');

        $exporter->saveToFile($filePath);

        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));

        @unlink($filePath);
    }

    public function test_service_request_excel_download_returns_streamed_response(): void
    {
        $exporter = new ServiceRequestExcelExport;
        $response = $exporter->download();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
    }
}
