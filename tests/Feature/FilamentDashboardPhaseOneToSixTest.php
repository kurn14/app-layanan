<?php

namespace Tests\Feature;

use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Village;
use App\Services\StatusTransitionService;
use Tests\TestCase;

class FilamentDashboardPhaseOneToSixTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@dinsos.blitarkab.go.id'],
            [
                'name' => 'Administrator Dinsos',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );
    }

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertSuccessful();
    }

    public function test_admin_can_access_master_data_resources(): void
    {
        $this->actingAs($this->admin);

        $resources = [
            '/admin/districts',
            '/admin/villages',
            '/admin/work-units',
            '/admin/users',
            '/admin/service-types',
            '/admin/dtsen-purposes',
            '/admin/client-categories',
            '/admin/referral-institutions',
            '/admin/complaint-categories',
        ];

        foreach ($resources as $uri) {
            $response = $this->get($uri);
            $response->assertSuccessful();
        }
    }

    public function test_admin_can_access_service_requests_and_cases(): void
    {
        $this->actingAs($this->admin);

        $routes = [
            '/admin/service-requests',
            '/admin/clients',
            '/admin/rehabilitation-cases',
            '/admin/complaints',
            '/admin/information-pages',
            '/admin/faqs',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $response->assertSuccessful();
        }
    }

    public function test_service_request_status_transition_logs_history(): void
    {
        $district = District::firstOrCreate(['code' => 'TEST01'], ['name' => 'Kecamatan Uji']);
        $village = Village::firstOrCreate(['code' => 'TEST0101'], ['district_id' => $district->id, 'name' => 'Desa Uji']);
        $serviceType = ServiceType::firstOrCreate(
            ['code' => 'DTSEN_TEST'],
            [
                'name' => 'SK DTSEN Test',
                'category' => 'Uji',
                'handler' => 'dtsen',
                'is_active' => true,
            ]
        );

        $request = ServiceRequest::create([
            'service_type_id' => $serviceType->id,
            'applicant_name' => 'Warga Percobaan',
            'applicant_nik' => '3505010101010001',
            'family_card_number' => '3505010101010002',
            'address' => 'Jl. Pengujian No. 1',
            'village_id' => $village->id,
            'phone' => '08123456789',
        ]);

        $this->assertNotNull($request->request_number);
        $this->assertEquals(ServiceRequestStatus::SUBMITTED, $request->status);

        StatusTransitionService::transition(
            $request,
            ServiceRequestStatus::DOCUMENT_CHECK,
            'Pemeriksaan berkas dimulai',
            $this->admin
        );

        $request->refresh();
        $this->assertEquals(ServiceRequestStatus::DOCUMENT_CHECK, $request->status);
        $this->assertDatabaseHas('status_histories', [
            'statusable_type' => $request->getMorphClass(),
            'statusable_id' => $request->id,
            'from_status' => 'submitted',
            'to_status' => 'document_check',
            'user_id' => $this->admin->id,
        ]);
    }
}
