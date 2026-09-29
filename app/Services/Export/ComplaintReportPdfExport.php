<?php

namespace App\Services\Export;

use App\Services\DashboardQueryService;

class ComplaintReportPdfExport extends BasePdfExporter
{
    public function __construct(array $filters = [])
    {
        parent::__construct($filters);
        $this->orientation = 'landscape';
    }

    protected function getViewName(): string
    {
        return 'exports.pdf.complaint-report';
    }

    protected function getViewData(): array
    {
        $complaints = DashboardQueryService::make($this->filters)->complaintsQuery()
            ->with(['complaintCategory', 'village.district', 'officer'])
            ->latest('reported_at')
            ->get();

        $subtitle = 'Rekapitulasi Pengaduan Penanganan Masalah Sosial';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'LAPORAN REKAPITULASI PENGADUAN SOSIAL',
            'subtitle' => $subtitle,
            'records' => $complaints,
            'totalRecords' => $complaints->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'laporan-pengaduan-'.now()->format('YmdHis');
    }
}
