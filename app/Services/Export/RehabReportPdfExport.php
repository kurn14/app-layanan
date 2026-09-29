<?php

namespace App\Services\Export;

use App\Services\DashboardQueryService;

class RehabReportPdfExport extends BasePdfExporter
{
    public function __construct(array $filters = [])
    {
        parent::__construct($filters);
        $this->orientation = 'landscape';
    }

    protected function getViewName(): string
    {
        return 'exports.pdf.rehab-report';
    }

    protected function getViewData(): array
    {
        $cases = DashboardQueryService::make($this->filters)->rehabilitationCasesQuery()
            ->with(['client.category', 'client.village.district', 'officer'])
            ->latest('received_at')
            ->get();

        $subtitle = 'Laporan Berkala Penanganan dan Pelayanan Rehabilitasi Sosial';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'LAPORAN REHABILITASI SOSIAL',
            'subtitle' => $subtitle,
            'records' => $cases,
            'totalRecords' => $cases->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'laporan-rehabilitasi-'.now()->format('YmdHis');
    }
}
