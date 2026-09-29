<?php

namespace App\Services\Export;

use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Services\DashboardQueryService;
use Illuminate\Database\Eloquent\Builder;

class RehabilitationCaseExcelExport extends BaseExcelExporter
{
    protected string $title = 'Laporan Kasus Rehabilitasi Sosial';

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
            'No. Kasus',
            'Nama Klien',
            'NIK Klien',
            'Kategori Klien',
            'Desa / Kelurahan',
            'Kecamatan',
            'Jenis Penanganan',
            'Status',
            'Petugas Penangan',
            'Tanggal Diterima',
            'Hasil Penanganan',
            'Tanggal Ditutup',
        ];
    }

    protected function getRows(): iterable
    {
        $query = $this->customQuery ?? DashboardQueryService::make($this->filters)->rehabilitationCasesQuery();

        $records = $query->with(['client.category', 'client.village.district', 'officer'])
            ->latest('received_at')
            ->get();

        $rows = [];
        $no = 1;

        foreach ($records as $record) {
            $statusLabel = $record->status instanceof RehabilitationCaseStatus
                ? $record->status->label()
                : (RehabilitationCaseStatus::tryFrom((string) $record->status)?->label() ?? (string) $record->status);

            $handlingTypeLabel = $record->handling_type instanceof RehabilitationHandlingType
                ? $record->handling_type->label()
                : (RehabilitationHandlingType::tryFrom((string) $record->handling_type)?->label() ?? (string) $record->handling_type);

            $rows[] = [
                $no++,
                $record->case_number,
                $record->client?->name ?? '-',
                $record->client?->nik ? "'".$record->client->nik : '-',
                $record->client?->category?->name ?? '-',
                $record->client?->village?->name ?? '-',
                $record->client?->village?->district?->name ?? '-',
                $handlingTypeLabel,
                $statusLabel,
                $record->officer?->name ?? '-',
                $record->received_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
                $record->handling_result ?? '-',
                $record->closed_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
            ];
        }

        return $rows;
    }

    protected function getFilename(): string
    {
        return 'kasus-rehabilitasi-'.now()->format('YmdHis');
    }
}
