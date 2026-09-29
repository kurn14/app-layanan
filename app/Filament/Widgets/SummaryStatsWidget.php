<?php

namespace App\Filament\Widgets;

use App\Services\DashboardQueryService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SummaryStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ! $user->hasRole('masyarakat');
    }

    protected function getStats(): array
    {
        $queryService = DashboardQueryService::make($this->pageFilters);

        // 1. SK DTSEN Terbit (berdasarkan tanggal terbit issued_at)
        $dtsenCount = (clone $queryService->dtsenCertificatesQuery())
            ->whereNotNull('issued_at')
            ->count();

        // 2. Pengajuan Layanan Masuk (berdasarkan submitted_at)
        $serviceRequestsCount = (clone $queryService->serviceRequestsQuery())->count();

        // 3. Pengaduan Sosial Masuk (berdasarkan reported_at)
        $complaintsCount = (clone $queryService->complaintsQuery())->count();

        // 4. Kasus Rehabilitasi Aktif (status bukan closed)
        $rehabActiveCount = 0;
        $canSeeRehab = $queryService->canAccessRehabilitation();
        if ($canSeeRehab) {
            $rehabActiveCount = (clone $queryService->rehabilitationCasesQuery())
                ->where('status', '!=', 'closed')
                ->count();
        }

        // 5. Tiket Dalam Proses vs Selesai (kombinasi pengajuan + pengaduan)
        $completedReqs = (clone $queryService->serviceRequestsQuery())
            ->whereIn('status', DashboardQueryService::completedServiceRequestStatuses())
            ->count();
        $inProcessReqs = max(0, $serviceRequestsCount - $completedReqs);

        $completedComplaints = (clone $queryService->complaintsQuery())
            ->whereIn('status', DashboardQueryService::completedComplaintStatuses())
            ->count();
        $inProcessComplaints = max(0, $complaintsCount - $completedComplaints);

        $totalCompleted = $completedReqs + $completedComplaints;
        $totalInProcess = $inProcessReqs + $inProcessComplaints;
        $totalAll = $totalCompleted + $totalInProcess;

        $completionRate = $totalAll > 0 ? round(($totalCompleted / $totalAll) * 100) : 0;

        $stats = [
            Stat::make('SK DTSEN Terbit', number_format($dtsenCount))
                ->description('Surat keterangan resmi yang telah terbit')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make('Pengajuan Layanan Masuk', number_format($serviceRequestsCount))
                ->description('Total berkas permohonan layanan')
                ->descriptionIcon(Heroicon::OutlinedInboxArrowDown)
                ->color('info'),

            Stat::make('Pengaduan Sosial Masuk', number_format($complaintsCount))
                ->description('Total laporan dan aduan warga')
                ->descriptionIcon(Heroicon::OutlinedMegaphone)
                ->color('warning'),
        ];

        if ($canSeeRehab) {
            $stats[] = Stat::make('Kasus Rehsos Aktif', number_format($rehabActiveCount))
                ->description('Klien dalam penanganan & monitoring')
                ->descriptionIcon(Heroicon::OutlinedHeart)
                ->color('danger');
        }

        $stats[] = Stat::make(
            'Penyelesaian Tiket',
            "{$totalCompleted} Selesai / {$totalInProcess} Proses"
        )
            ->description("Tingkat efektivitas penyelesaian: {$completionRate}%")
            ->descriptionIcon(Heroicon::OutlinedArrowPath)
            ->color($completionRate >= 70 ? 'success' : ($completionRate >= 40 ? 'warning' : 'danger'));

        return $stats;
    }
}
