<?php

namespace App\Services\Export;

use App\Services\DashboardQueryService;
use Illuminate\Database\Eloquent\Builder;

class ComplaintPdfExport extends BasePdfExporter
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
        return 'exports.pdf.complaint-report';
    }

    protected function getViewData(): array
    {
        $query = $this->customQuery ?? DashboardQueryService::make($this->filters)->complaintsQuery();

        $records = $query->with(['complaintCategory', 'village.district', 'officer'])
            ->latest('reported_at')
            ->get();

        $subtitle = 'Rekap Data Pengaduan Masalah Kesejahteraan Sosial';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'LAPORAN PENGADUAN KESEJAHTERAAN SOSIAL',
            'subtitle' => $subtitle,
            'records' => $records,
            'totalRecords' => $records->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'pengaduan-sosial-'.now()->format('YmdHis');
    }
}
