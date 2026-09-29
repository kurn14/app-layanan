<?php

namespace App\Filament\Widgets;

use App\Enums\ComplaintStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Referral;
use App\Models\ReferralInstitution;
use App\Models\ServiceType;
use App\Services\DashboardQueryService;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class ServiceStatusChartWidget extends ChartWidget
{
    use HasFiltersSchema, InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Tahapan & Status Layanan';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ! $user->hasRole('masyarakat');
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('module')
                ->label('Pilihan Modul Layanan')
                ->options($this->getModuleOptions())
                ->default('pbi')
                ->selectablePlaceholder(false),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function getModuleOptions(): array
    {
        $queryService = DashboardQueryService::make([], auth()->user());

        $options = [
            'pbi' => 'Reaktivasi KIS / PBI-JK',
            'dtsen' => 'Surat Keterangan DTSEN',
            'complaint' => 'Pengaduan Sosial',
            'general' => 'Pengajuan Layanan Lain / Umum',
        ];

        // Sembunyikan opsi Rehabilitasi dari Operator Wilayah
        if ($queryService->canAccessRehabilitation()) {
            $options['rehabilitation'] = 'Kasus Rehabilitasi Sosial';
            $options['rehab_referrals'] = 'Rujukan Rehsos per Lembaga';
        }

        return $options;
    }

    protected function getData(): array
    {
        $selectedModule = $this->filters['module'] ?? 'pbi';
        $queryService = DashboardQueryService::make($this->pageFilters);

        // Jika user tidak berhak tapi modul rehab terpilih, alihkan ke pbi
        if (in_array($selectedModule, ['rehabilitation', 'rehab_referrals']) && ! $queryService->canAccessRehabilitation()) {
            $selectedModule = 'pbi';
        }

        return match ($selectedModule) {
            'dtsen' => $this->getDtsenData($queryService),
            'complaint' => $this->getComplaintData($queryService),
            'general' => $this->getGeneralServiceData($queryService),
            'rehabilitation' => $this->getRehabilitationData($queryService),
            'rehab_referrals' => $this->getRehabReferralsData($queryService),
            default => $this->getPbiData($queryService),
        };
    }

    /**
     * Data Alur Reaktivasi PBI-JK.
     */
    protected function getPbiData(DashboardQueryService $queryService): array
    {
        $pbiTypeId = ServiceType::where('handler', 'pbi')->orWhere('code', 'PBI')->value('id');

        $query = (clone $queryService->serviceRequestsQuery());
        if ($pbiTypeId) {
            $query->where('service_type_id', $pbiTypeId);
        }

        $countsByStatus = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $flow = [
            ServiceRequestStatus::SUBMITTED->value => 'Diajukan',
            ServiceRequestStatus::DOCUMENT_CHECK->value => 'Cek Berkas',
            ServiceRequestStatus::ELIGIBILITY_VERIFICATION->value => 'Verif. Kelayakan',
            ServiceRequestStatus::RECOMMENDATION_ISSUED->value => 'Rekom. Terbit',
            ServiceRequestStatus::PROPOSED_TO_MINISTRY->value => 'SIKS-NG Kemensos',
            ServiceRequestStatus::MINISTRY_APPROVED->value => 'Disetujui Kemensos',
            ServiceRequestStatus::REACTIVATED->value => 'Aktif Kembali',
            ServiceRequestStatus::COMPLETED->value => 'Selesai',
            ServiceRequestStatus::MINISTRY_REJECTED->value => 'Ditolak Kemensos',
            ServiceRequestStatus::REJECTED->value => 'Ditolak',
        ];

        $labels = array_values($flow);
        $data = [];
        foreach (array_keys($flow) as $statusKey) {
            $data[] = (int) ($countsByStatus[$statusKey] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket PBI-JK',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9', // Diajukan (Sky)
                        '#38bdf8', // Cek Berkas
                        '#6366f1', // Verif Kelayakan (Indigo)
                        '#8b5cf6', // Rekom Terbit (Purple)
                        '#f59e0b', // SIKS-NG Kemensos (Amber)
                        '#10b981', // Disetujui (Emerald)
                        '#059669', // Aktif Kembali (Green)
                        '#14b8a6', // Selesai (Teal)
                        '#f43f5e', // Ditolak Kemensos (Rose)
                        '#ef4444', // Ditolak (Red)
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Data Alur SK DTSEN.
     */
    protected function getDtsenData(DashboardQueryService $queryService): array
    {
        $dtsenTypeId = ServiceType::where('handler', 'dtsen')->orWhere('code', 'DTSEN')->value('id');

        $query = (clone $queryService->serviceRequestsQuery());
        if ($dtsenTypeId) {
            $query->where('service_type_id', $dtsenTypeId);
        }

        $countsByStatus = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $flow = [
            ServiceRequestStatus::SUBMITTED->value => 'Diajukan',
            ServiceRequestStatus::DOCUMENT_CHECK->value => 'Cek Berkas',
            ServiceRequestStatus::REVISION_REQUESTED->value => 'Revisi Berkas',
            ServiceRequestStatus::DATA_VERIFICATION->value => 'Cek SIKS-NG',
            ServiceRequestStatus::AWAITING_APPROVAL->value => 'Menunggu TTD/Paraf',
            ServiceRequestStatus::ISSUED->value => 'Surat Terbit',
            ServiceRequestStatus::COMPLETED->value => 'Selesai',
            ServiceRequestStatus::REJECTED->value => 'Ditolak',
        ];

        $labels = array_values($flow);
        $data = [];
        foreach (array_keys($flow) as $statusKey) {
            $data[] = (int) ($countsByStatus[$statusKey] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket DTSEN',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9',
                        '#38bdf8',
                        '#fbbf24',
                        '#6366f1',
                        '#f59e0b',
                        '#10b981',
                        '#14b8a6',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Data Alur Pengaduan Sosial.
     */
    protected function getComplaintData(DashboardQueryService $queryService): array
    {
        $countsByStatus = (clone $queryService->complaintsQuery())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $flow = [
            ComplaintStatus::RECEIVED->value => 'Diterima',
            ComplaintStatus::VERIFICATION->value => 'Verifikasi',
            ComplaintStatus::CLARIFICATION_REQUESTED->value => 'Klarifikasi',
            ComplaintStatus::DISPATCHED->value => 'Disposisi',
            ComplaintStatus::IN_HANDLING->value => 'Penanganan',
            ComplaintStatus::RESOLVED->value => 'Selesai Ditangani',
            ComplaintStatus::DUPLICATE->value => 'Duplikat',
            ComplaintStatus::INVALID->value => 'Tidak Valid',
        ];

        $labels = array_values($flow);
        $data = [];
        foreach (array_keys($flow) as $statusKey) {
            $data[] = (int) ($countsByStatus[$statusKey] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pengaduan Warga',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9',
                        '#6366f1',
                        '#f59e0b',
                        '#8b5cf6',
                        '#ec4899',
                        '#10b981',
                        '#94a3b8',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Data Alur Layanan Umum.
     */
    protected function getGeneralServiceData(DashboardQueryService $queryService): array
    {
        $generalTypeIds = ServiceType::whereNotIn('handler', ['pbi', 'dtsen'])->pluck('id')->all();

        $query = (clone $queryService->serviceRequestsQuery());
        if (! empty($generalTypeIds)) {
            $query->whereIn('service_type_id', $generalTypeIds);
        }

        $countsByStatus = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $flow = [
            ServiceRequestStatus::SUBMITTED->value => 'Diajukan',
            ServiceRequestStatus::DOCUMENT_CHECK->value => 'Cek Berkas',
            ServiceRequestStatus::VERIFICATION->value => 'Verifikasi',
            ServiceRequestStatus::ASSESSMENT->value => 'Assessment',
            ServiceRequestStatus::IN_PROCESS->value => 'Diproses',
            ServiceRequestStatus::COMPLETED->value => 'Selesai',
            ServiceRequestStatus::REJECTED->value => 'Ditolak',
        ];

        $labels = array_values($flow);
        $data = [];
        foreach (array_keys($flow) as $statusKey) {
            $data[] = (int) ($countsByStatus[$statusKey] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pengajuan Umum',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9',
                        '#38bdf8',
                        '#6366f1',
                        '#8b5cf6',
                        '#f59e0b',
                        '#10b981',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Data Kasus Rehabilitasi.
     */
    protected function getRehabilitationData(DashboardQueryService $queryService): array
    {
        $countsByStatus = (clone $queryService->rehabilitationCasesQuery())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $flow = [
            RehabilitationCaseStatus::RECEIVED->value => 'Diterima',
            RehabilitationCaseStatus::ASSESSMENT->value => 'Assessment',
            RehabilitationCaseStatus::SERVICE_PLANNING->value => 'Rencana Pelayanan',
            RehabilitationCaseStatus::IN_SERVICE->value => 'Dalam Pelayanan',
            RehabilitationCaseStatus::MONITORING->value => 'Monitoring',
            RehabilitationCaseStatus::CLOSED->value => 'Kasus Selesai / Ditutup',
        ];

        $labels = array_values($flow);
        $data = [];
        foreach (array_keys($flow) as $statusKey) {
            $data[] = (int) ($countsByStatus[$statusKey] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus Rehsos',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9',
                        '#6366f1',
                        '#f59e0b',
                        '#ec4899',
                        '#8b5cf6',
                        '#10b981',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Data Rujukan Rehsos per Lembaga.
     */
    protected function getRehabReferralsData(DashboardQueryService $queryService): array
    {
        $institutions = ReferralInstitution::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        $referralCounts = Referral::query()
            ->whereHas('rehabilitationCase', function (Builder $rq) use ($queryService) {
                if ($queryService->getStartDate()) {
                    $rq->where('received_at', '>=', $queryService->getStartDate());
                }
                if ($queryService->getEndDate()) {
                    $rq->where('received_at', '<=', $queryService->getEndDate());
                }
            })
            ->selectRaw('referral_institution_id, count(*) as count')
            ->groupBy('referral_institution_id')
            ->pluck('count', 'referral_institution_id')
            ->all();

        $labels = [];
        $data = [];
        foreach ($institutions as $instId => $name) {
            $labels[] = $name;
            $data[] = (int) ($referralCounts[$instId] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Klien Dirujuk',
                    'data' => $data,
                    'backgroundColor' => '#14b8a6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
