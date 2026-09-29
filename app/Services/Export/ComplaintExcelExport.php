<?php

namespace App\Services\Export;

use App\Enums\ComplaintStatus;
use App\Services\DashboardQueryService;
use Illuminate\Database\Eloquent\Builder;

class ComplaintExcelExport extends BaseExcelExporter
{
    protected string $title = 'Laporan Pengaduan Sosial';

    protected ?Builder $customQuery = null;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(array $filters = [], ?Builder $customQuery = null)
    {
        parent::__construct($filters);
        $this->customQuery = $customQuery;

        if (! empty($filters['startDate']) && ! empty($filters['endDate'])) {
            $this->subtitle = 'Periode: '.$filters['startDate'].' s/d '.$filters['endDate'];
        }
    }

    protected function getHeaders(): array
    {
        return [
            'No',
            'No. Aduan',
            'Nama Pelapor',
            'No. Telepon',
            'Kategori Pengaduan',
            'Lokasi / Alamat',
            'Desa / Kelurahan',
            'Kecamatan',
            'Status',
            'Tanggal Laporan',
            'Petugas Penangan',
            'Tindakan Diambil',
            'Tanggal Selesai',
        ];
    }

    protected function getRows(): iterable
    {
        $query = $this->customQuery ?? DashboardQueryService::make($this->filters)->complaintsQuery();

        $records = $query->with(['complaintCategory', 'village.district', 'officer'])
            ->latest('reported_at')
            ->get();

        $rows = [];
        $no = 1;

        foreach ($records as $record) {
            $statusLabel = $record->status instanceof ComplaintStatus
                ? $record->status->label()
                : (ComplaintStatus::tryFrom((string) $record->status)?->label() ?? (string) $record->status);

            $rows[] = [
                $no++,
                $record->complaint_number,
                $record->reporter_name,
                $record->reporter_phone ? "'".$record->reporter_phone : '-',
                $record->complaintCategory?->name ?? '-',
                $record->location_detail ?? '-',
                $record->village?->name ?? '-',
                $record->village?->district?->name ?? '-',
                $statusLabel,
                $record->reported_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
                $record->officer?->name ?? '-',
                $record->action_taken ?? $record->verification_result ?? '-',
                $record->resolved_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
            ];
        }

        return $rows;
    }

    protected function getFilename(): string
    {
        return 'pengaduan-sosial-'.now()->format('YmdHis');
    }
}
