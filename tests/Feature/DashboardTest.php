<?php

namespace Tests\Feature;

use App\Filament\Widgets\DtsenPurposeDecileChartWidget;
use App\Filament\Widgets\PendingActionsTableWidget;
use App\Filament\Widgets\RegionalDistributionChartWidget;
use App\Filament\Widgets\ServiceStatusChartWidget;
use App\Filament\Widgets\SummaryStatsWidget;
use App\Models\District;
use App\Models\User;
use App\Models\Village;
use App\Services\DashboardQueryService;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_admin_can_access_dashboard_page(): void
    {
        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        $admin->assignRole('administrator');

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
        $response->assertSee('Dashboard SAPA SOSIAL');
        $response->assertSee('Filter Terpadu Dashboard');
    }

    public function test_pimpinan_can_access_dashboard_page(): void
    {
        $pimpinan = User::factory()->create([
            'is_active' => true,
        ]);
        $pimpinan->assignRole('pimpinan');

        $response = $this->actingAs($pimpinan)->get('/admin');

        $response->assertSuccessful();
        $response->assertSee('Dashboard SAPA SOSIAL');
    }

    public function test_summary_stats_widget_renders(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        Livewire::test(SummaryStatsWidget::class)
            ->assertSuccessful()
            ->assertSee('SK DTSEN Terbit')
            ->assertSee('Pengajuan Layanan Masuk')
            ->assertSee('Pengaduan Sosial Masuk')
            ->assertSee('Penyelesaian Tiket');
    }

    public function test_pending_actions_table_widget_renders_and_switches_tabs(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        Livewire::test(PendingActionsTableWidget::class)
            ->assertSuccessful()
            ->assertSee('Menunggu Paraf / TTD')
            ->assertSee('Darurat Medis Belum Selesai')
            ->assertSee('PBI-JK Tertahan Kemensos')
            ->call('setTab', 'emergency')
            ->assertSet('activeTab', 'emergency')
            ->call('setTab', 'stalled_pbi')
            ->assertSet('activeTab', 'stalled_pbi');
    }

    public function test_service_status_chart_widget_renders(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        Livewire::test(ServiceStatusChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Tahapan & Status Layanan');
    }

    public function test_dtsen_purpose_decile_chart_widget_renders(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        Livewire::test(DtsenPurposeDecileChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Distribusi SK DTSEN Terbit');
    }

    public function test_regional_distribution_chart_widget_renders(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        Livewire::test(RegionalDistributionChartWidget::class)
            ->assertSuccessful()
            ->assertSee('Sebaran Layanan & Pengaduan');
    }

    public function test_operator_geographic_scoping(): void
    {
        $district = District::firstOrCreate(
            ['code' => 'TEST_DIST'],
            ['name' => 'Kecamatan Uji Coba']
        );
        $village = Village::firstOrCreate(
            ['code' => 'TEST_VIL'],
            ['name' => 'Desa Uji Coba', 'district_id' => $district->id]
        );

        $operator = User::factory()->create([
            'is_active' => true,
            'district_id' => $district->id,
            'village_id' => $village->id,
        ]);
        $operator->assignRole('operator');

        $service = DashboardQueryService::make([], $operator);

        // Operator desa harus terkunci ke desa dan kecamatannya
        $this->assertEquals($village->id, $service->getVillageId());
        $this->assertEquals($district->id, $service->getDistrictId());
        $this->assertFalse($service->canAccessRehabilitation());
    }

    protected function getWidgetChartData($component): array
    {
        $instance = $component->instance();
        $reflection = new \ReflectionClass($instance);
        $method = $reflection->getMethod('getCachedData');
        $method->setAccessible(true);

        return $method->invoke($instance);
    }

    public function test_service_status_chart_widget_switches_modules(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        $component = Livewire::test(ServiceStatusChartWidget::class)
            ->set('filters', ['module' => 'dtsen'])
            ->assertSuccessful();

        $chartData = $this->getWidgetChartData($component);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertContains('Surat Terbit', $chartData['labels']);

        // Switch to complaints
        $component->set('filters', ['module' => 'complaint'])
            ->assertSuccessful();

        $complaintData = $this->getWidgetChartData($component);
        $this->assertContains('Selesai Ditangani', $complaintData['labels']);
    }

    public function test_dtsen_purpose_decile_chart_widget_switches_breakdown(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        $component = Livewire::test(DtsenPurposeDecileChartWidget::class)
            ->set('filters', ['breakdown_by' => 'decile'])
            ->assertSuccessful();

        $chartData = $this->getWidgetChartData($component);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertContains('Desil 1', $chartData['labels']);
        $this->assertContains('Desil 10', $chartData['labels']);
    }

    public function test_regional_distribution_chart_widget_drills_down_to_villages(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('administrator');

        $this->actingAs($admin);

        $district = District::first();

        $component = Livewire::test(RegionalDistributionChartWidget::class, [
            'pageFilters' => [
                'district_id' => $district->id,
            ],
        ])->assertSuccessful();

        $heading = $component->instance()->getHeading();
        $this->assertStringContainsString($district->name, $heading);

        $chartData = $this->getWidgetChartData($component);
        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertCount(2, $chartData['datasets']); // Layanan & Pengaduan
    }
}
