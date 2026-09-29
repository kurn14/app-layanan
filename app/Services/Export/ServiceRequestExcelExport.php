<?php

namespace App\Services\Export;

use App\Enums\ServiceRequestStatus;
use App\Services\DashboardQueryService;
use Illuminate\Database\Eloquent\Builder;

class ServiceRequestExcelExport extends BaseExcelExporter
{
    protected string $title = 'Laporan Pengajuan Layanan';

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
            'No. Tiket',
            'Nama Pemohon',
            'NIK',
            'Jenis Layanan',
            'Desa / Kelurahan',
            'Kecamatan',
            'Status',
            'Tanggal Pengajuan',
            'Petugas',
            'Hasil Layanan',
            'Tanggal Selesai',
        ];
    }

    protected function getRows(): iterable
    {
        $query = $this->customQuery ?? DashboardQueryService::make($this->filters)->serviceRequestsQuery();

        $records = $query->with(['serviceType', 'village.district', 'officer'])
            ->latest('submitted_at')
            ->get();

        $rows = [];
        $no = 1;

        foreach ($records as $record) {
            $statusLabel = $record->status instanceof ServiceRequestStatus
                ? $record->status->label()
                : (ServiceRequestStatus::tryFrom((string) $record->status)?->label() ?? (string) $record->status);

            $rows[] = [
                $no++,
                $record->request_number,
                $record->applicant_name,
                "'".$record->applicant_nik, // Tanda petik tunggal agar NIK tidak terpotong sebagai floating number di Excel
                $record->serviceType?->name ?? '-',
                $record->village?->name ?? '-',
                $record->village?->district?->name ?? '-',
                $statusLabel,
                $record->submitted_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
                $record->officer?->name ?? '-',
                $record->service_result ?? $record->rejection_reason ?? '-',
                $record->completed_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
            ];
        }

        return $rows;
    }

    protected function getFilename(): string
    {
        return 'pengajuan-layanan-'.now()->format('YmdHis');
    }
}
