<?php

namespace App\Services\Export;

use App\Models\ServiceType;
use App\Services\DashboardQueryService;

class ServiceReportPdfExport extends BasePdfExporter
{
    public function __construct(array $filters = [])
    {
        parent::__construct($filters);
        $this->orientation = 'landscape';
    }

    protected function getViewName(): string
    {
        return 'exports.pdf.service-report';
    }

    protected function getViewData(): array
    {
        $requests = DashboardQueryService::make($this->filters)->serviceRequestsQuery()
            ->with(['serviceType', 'village.district', 'officer'])
            ->latest('submitted_at')
            ->get();

        $serviceTypes = ServiceType::where('is_active', true)->orderBy('name')->get();

        $summary = $serviceTypes->map(function ($type) use ($requests) {
            $matching = $requests->where('service_type_id', $type->id);

            return [
                'name' => $type->name,
                'proses' => $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['submitted', 'document_check', 'verification', 'eligibility_verification', 'data_verification', 'revision_requested', 'awaiting_approval']))->count(),
                'disetujui' => $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['recommendation_issued', 'proposed_to_ministry', 'ministry_approved', 'reactivated', 'issued']))->count(),
                'selesai' => $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['completed']))->count(),
                'ditolak' => $matching->filter(fn ($r) => in_array($r->status?->value ?? (string) $r->status, ['rejected', 'ministry_rejected']))->count(),
                'total' => $matching->count(),
            ];
        });

        $subtitle = 'Rekapitulasi Pelayanan Terpadu Masalah Kesejahteraan Sosial';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'LAPORAN REKAPITULASI PELAYANAN SOSIAL',
            'subtitle' => $subtitle,
            'requests' => $requests,
            'summary' => $summary,
            'totalRecords' => $requests->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'laporan-pelayanan-'.now()->format('YmdHis');
    }
}
