<?php

namespace App\Filament\Widgets;

use App\Models\District;
use App\Models\Village;
use App\Services\DashboardQueryService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;

class RegionalDistributionChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '420px';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ! $user->hasRole('masyarakat');
    }

    public function getHeading(): string|Htmlable|null
    {
        $queryService = DashboardQueryService::make($this->pageFilters);
        $districtId = $queryService->getDistrictId();

        if ($districtId) {
            $districtName = District::find($districtId)?->name ?? 'Kecamatan Terpilih';

            return "Sebaran Layanan & Pengaduan — Kecamatan {$districtName} (Tingkat Desa/Kelurahan)";
        }

        return 'Sebaran Layanan & Pengaduan per Kecamatan (Kabupaten Blitar)';
    }

    public function getDescription(): string|Htmlable|null
    {
        $queryService = DashboardQueryService::make($this->pageFilters);
        if ($queryService->getDistrictId()) {
            return 'Rincian permohonan layanan dan pengaduan sosial di tiap desa/kelurahan pada kecamatan terpilih';
        }

        return 'Perbandingan volume pengajuan layanan sosial dan aduan warga di 22 kecamatan se-Kabupaten Blitar';
    }

    protected function getData(): array
    {
        $queryService = DashboardQueryService::make($this->pageFilters);
        $districtId = $queryService->getDistrictId();

        if ($districtId) {
            return $this->getVillageLevelData($queryService, $districtId);
        }

        return $this->getDistrictLevelData($queryService);
    }

    /**
     * Data tingkat Kecamatan se-Kabupaten Blitar.
     */
    protected function getDistrictLevelData(DashboardQueryService $queryService): array
    {
        $districts = District::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        // Agregasi pengajuan layanan per kecamatan via join desa
        $serviceCounts = (clone $queryService->serviceRequestsQuery())
            ->join('villages', 'villages.id', '=', 'service_requests.village_id')
            ->selectRaw('villages.district_id, count(service_requests.id) as count')
            ->groupBy('villages.district_id')
            ->pluck('count', 'villages.district_id')
            ->all();

        // Agregasi pengaduan per kecamatan via join desa
        $complaintCounts = (clone $queryService->complaintsQuery())
            ->join('villages', 'villages.id', '=', 'complaints.village_id')
            ->selectRaw('villages.district_id, count(complaints.id) as count')
            ->groupBy('villages.district_id')
            ->pluck('count', 'villages.district_id')
            ->all();

        $labels = [];
        $serviceData = [];
        $complaintData = [];

        foreach ($districts as $dId => $dName) {
            $labels[] = $dName;
            $serviceData[] = (int) ($serviceCounts[$dId] ?? 0);
            $complaintData[] = (int) ($complaintCounts[$dId] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Layanan Masuk',
                    'data' => $serviceData,
                    'backgroundColor' => '#0ea5e9', // Sky blue
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Pengaduan Sosial Masuk',
                    'data' => $complaintData,
                    'backgroundColor' => '#f59e0b', // Amber
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Data tingkat Desa / Kelurahan (saat filter kecamatan aktif).
     */
    protected function getVillageLevelData(DashboardQueryService $queryService, int $districtId): array
    {
        $villages = Village::query()
            ->where('district_id', $districtId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        $villageIds = array_keys($villages);

        $serviceCounts = (clone $queryService->serviceRequestsQuery())
            ->whereIn('village_id', $villageIds)
            ->selectRaw('village_id, count(*) as count')
            ->groupBy('village_id')
            ->pluck('count', 'village_id')
            ->all();

        $complaintCounts = (clone $queryService->complaintsQuery())
            ->whereIn('village_id', $villageIds)
            ->selectRaw('village_id, count(*) as count')
            ->groupBy('village_id')
            ->pluck('count', 'village_id')
            ->all();

        $labels = [];
        $serviceData = [];
        $complaintData = [];

        foreach ($villages as $vId => $vName) {
            $labels[] = $vName;
            $serviceData[] = (int) ($serviceCounts[$vId] ?? 0);
            $complaintData[] = (int) ($complaintCounts[$vId] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Layanan',
                    'data' => $serviceData,
                    'backgroundColor' => '#0ea5e9',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Pengaduan Sosial',
                    'data' => $complaintData,
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
