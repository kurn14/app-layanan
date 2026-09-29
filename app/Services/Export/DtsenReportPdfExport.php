<?php

namespace App\Services\Export;

use App\Models\DtsenPurpose;
use App\Services\DashboardQueryService;

class DtsenReportPdfExport extends BasePdfExporter
{
    public function __construct(array $filters = [])
    {
        parent::__construct($filters);
        $this->orientation = 'landscape';
    }

    protected function getViewName(): string
    {
        return 'exports.pdf.dtsen-report';
    }

    protected function getViewData(): array
    {
        $certificates = DashboardQueryService::make($this->filters)->dtsenCertificatesQuery()
            ->with(['serviceRequest.village.district', 'dtsenPurpose', 'signer'])
            ->latest('issued_at')
            ->get();

        $purposes = DtsenPurpose::orderBy('name')->get();

        // Rekapitulasi per tujuan
        $summary = $purposes->map(function ($purpose) use ($certificates) {
            $matching = $certificates->where('dtsen_purpose_id', $purpose->id);

            return [
                'name' => $purpose->name,
                'd1' => $matching->where('decile', 1)->count(),
                'd2' => $matching->where('decile', 2)->count(),
                'd3' => $matching->where('decile', 3)->count(),
                'd4' => $matching->where('decile', 4)->count(),
                'non' => $matching->filter(fn ($c) => empty($c->decile) || $c->decile > 4)->count(),
                'total' => $matching->count(),
            ];
        });

        $subtitle = 'Rekapitulasi Penerbitan Surat Keterangan Data Terpadu Sosial Ekonomi Nasional';
        if (! empty($this->filters['startDate']) && ! empty($this->filters['endDate'])) {
            $subtitle .= ' (Periode: '.$this->filters['startDate'].' s/d '.$this->filters['endDate'].')';
        }

        return [
            'title' => 'REKAPITULASI PENERBITAN SK DTSEN',
            'subtitle' => $subtitle,
            'certificates' => $certificates,
            'summary' => $summary,
            'totalCertificates' => $certificates->count(),
        ];
    }

    protected function getFilename(): string
    {
        return 'rekap-sk-dtsen-'.now()->format('YmdHis');
    }
}
