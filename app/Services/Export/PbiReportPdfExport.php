<?php

namespace App\Services\Export;

use App\Enums\PbiReactivationReason;
use App\Services\DashboardQueryService;

class PbiReportPdfExport extends BasePdfExporter
{
    public function __construct(array $filters = [])
    {
        parent::__construct($filters);
        $this->orientation = 'landscape';
    }

    protected function getViewName(): string
    {
        return 'exports.pdf.pbi-report';
    }

    protected function getViewData(): array
    {
        $reactivations = DashboardQueryService::make($this->filters)->pbiReactivationsQuery()
            ->with(['serviceRequest.village.district', 'signer'])
            ->latest('created_at')
            ->get();

        $reasons = PbiReactivationReason::cases();

        $summary = collect($reasons)->map(function ($reason) use ($reactivations) {
            $matching = $reactivations->filter(fn ($r) => ($r->reason instanceof PbiReactivationReason ? $r->reason->value : (string) $r->reason) === $reason->value);
            $disetujui = $matching->filter(fn ($r) => in_array($r->ministry_decision?->value ?? (string) $r->ministry_decision, ['approved', 'reactivated']))->count();
            $ditolak = $matching->filter(fn ($r) => in_array($r->ministry_decision?->value ?? (string) $r->ministry_decision, ['rejected', 'ministry_rejected']))->count();
            $proses = $matching->count() - $disetujui - $ditolak;

            return [
                'name' => $reason->label(),
                'proses' => $proses,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak,
                'total' => $matching->count(),
            ];
        });

        $subtitle = 'Rekapitulasi Pengajuan Reaktivasi Kepesertaan PBI Jaminan Kesehatan';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'REKAPITULASI REAKTIVASI PBI-JK',
            'subtitle' => $subtitle,
            'reactivations' => $reactivations,
            'summary' => $summary,
            'totalRecords' => $reactivations->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'rekap-pbi-jk-'.now()->format('YmdHis');
    }
}
