<?php

namespace App\Services\Export;

use App\Services\DashboardQueryService;
use Illuminate\Database\Eloquent\Builder;

class RehabilitationCasePdfExport extends BasePdfExporter
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
        return 'exports.pdf.rehab-report';
    }

    protected function getViewData(): array
    {
        $query = $this->customQuery ?? DashboardQueryService::make($this->filters)->rehabilitationCasesQuery();

        $records = $query->with(['client.category', 'client.village.district', 'officer'])
            ->latest('received_at')
            ->get();

        $subtitle = 'Rekap Kasus dan Penanganan Rehabilitasi Sosial';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'LAPORAN REHABILITASI SOSIAL',
            'subtitle' => $subtitle,
            'records' => $records,
            'totalRecords' => $records->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'kasus-rehabilitasi-'.now()->format('YmdHis');
    }
}
