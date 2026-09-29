<?php

namespace App\Services\Export;

use App\Services\DashboardQueryService;
use Illuminate\Database\Eloquent\Builder;

class ServiceRequestPdfExport extends BasePdfExporter
{
    protected ?Builder $customQuery = null;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(array $filters = [], ?Builder $customQuery = null)
    {
        parent::__construct($filters);
        $this->customQuery = $customQuery;
        $this->orientation = 'landscape';
    }

    protected function getViewName(): string
    {
        return 'exports.pdf.service-requests';
    }

    protected function getViewData(): array
    {
        $query = $this->customQuery ?? DashboardQueryService::make($this->filters)->serviceRequestsQuery();

        $records = $query->with(['serviceType', 'village.district', 'officer'])
            ->latest('submitted_at')
            ->get();

        $subtitle = 'Rekap Data Pengajuan Layanan';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'LAPORAN PENGAJUAN PELAYANAN SOSIAL',
            'subtitle' => $subtitle,
            'records' => $records,
            'totalRecords' => $records->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'pengajuan-layanan-'.now()->format('YmdHis');
    }
}
