<?php

namespace App\Filament\Widgets;

use App\Enums\ServiceRequestStatus;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Services\DashboardQueryService;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingActionsTableWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.pending-actions-table-widget';

    public string $activeTab = 'approvals';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ! $user->hasRole('masyarakat');
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getPendingApprovalsCount(): int
    {
        $queryService = DashboardQueryService::make($this->pageFilters);

        return (clone $queryService->serviceRequestsQuery())
            ->where(function (Builder $q) {
                $q->where('status', ServiceRequestStatus::AWAITING_APPROVAL)
                    ->orWhereHas('dtsenCertificate.approvals', fn ($aq) => $aq->where('decision', 'pending'))
                    ->orWhereHas('pbiReactivation.approvals', fn ($aq) => $aq->where('decision', 'pending'));
            })
            ->count();
    }

    public function getEmergencyCount(): int
    {
        $queryService = DashboardQueryService::make($this->pageFilters);

        return (clone $queryService->serviceRequestsQuery())
            ->where('is_priority', true)
            ->whereNotIn('status', [ServiceRequestStatus::COMPLETED, ServiceRequestStatus::REJECTED])
            ->count();
    }

    public function getStalledPbiCount(): int
    {
        $queryService = DashboardQueryService::make($this->pageFilters);

        return (clone $queryService->serviceRequestsQuery())
            ->where('status', ServiceRequestStatus::PROPOSED_TO_MINISTRY)
            ->whereHas('pbiReactivation', function (Builder $pq) {
                $pq->whereNotNull('proposed_to_ministry_at')
                    ->where('proposed_to_ministry_at', '<=', now()->subDays(3))
                    ->where(function ($sub) {
                        $sub->whereNull('ministry_decision')
                            ->orWhere('ministry_decision', 'pending');
                    });
            })
            ->count();
    }

    public function table(Table $table): Table
    {
        $queryService = DashboardQueryService::make($this->pageFilters);

        $baseQuery = match ($this->activeTab) {
            'emergency' => (clone $queryService->serviceRequestsQuery())
                ->where('is_priority', true)
                ->whereNotIn('status', [ServiceRequestStatus::COMPLETED, ServiceRequestStatus::REJECTED])
                ->with(['serviceType', 'village.district', 'pbiReactivation'])
                ->orderBy('submitted_at', 'asc'),

            'stalled_pbi' => (clone $queryService->serviceRequestsQuery())
                ->where('status', ServiceRequestStatus::PROPOSED_TO_MINISTRY)
                ->whereHas('pbiReactivation', function (Builder $pq) {
                    $pq->whereNotNull('proposed_to_ministry_at')
                        ->where('proposed_to_ministry_at', '<=', now()->subDays(3))
                        ->where(function ($sub) {
                            $sub->whereNull('ministry_decision')
                                ->orWhere('ministry_decision', 'pending');
                        });
                })
                ->with(['serviceType', 'village.district', 'pbiReactivation'])
                ->orderBy('submitted_at', 'asc'),

            default => (clone $queryService->serviceRequestsQuery())
                ->where(function (Builder $q) {
                    $q->where('status', ServiceRequestStatus::AWAITING_APPROVAL)
                        ->orWhereHas('dtsenCertificate.approvals', fn ($aq) => $aq->where('decision', 'pending'))
                        ->orWhereHas('pbiReactivation.approvals', fn ($aq) => $aq->where('decision', 'pending'));
                })
                ->with(['serviceType', 'village.district', 'dtsenCertificate', 'pbiReactivation'])
                ->orderBy('submitted_at', 'asc'),
        };

        return $table
            ->query($baseQuery)
            ->heading(match ($this->activeTab) {
                'emergency' => 'Daftar Pengajuan Darurat Medis — Perlu Penanganan Cepat',
                'stalled_pbi' => 'Daftar Pengajuan PBI-JK Tertahan di SIKS-NG Kemensos (> 3 Hari)',
                default => 'Daftar Pengajuan Menunggu Paraf / Tanda Tangan Pejabat',
            })
            ->description('Diurutkan dari tanggal pengajuan terlama untuk memastikan SLA terpenuhi')
            ->columns([
                TextColumn::make('request_number')
                    ->label('Nomor Tiket')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable(),

                TextColumn::make('applicant_name')
                    ->label('Pemohon / Peserta')
                    ->description(fn (ServiceRequest $record): string => match ($this->activeTab) {
                        'emergency', 'stalled_pbi' => 'Peserta: '.($record->pbiReactivation?->participant_name ?? $record->applicant_name).' ('.($record->pbiReactivation?->participant_nik ?? $record->applicant_nik).')',
                        default => 'NIK: '.$record->applicant_nik,
                    })
                    ->searchable(),

                TextColumn::make('serviceType.name')
                    ->label('Jenis Layanan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('village.name')
                    ->label('Wilayah')
                    ->description(fn (ServiceRequest $record): string => $record->village?->district?->name ?? '-')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ServiceRequestStatus ? $state->label() : (ServiceRequestStatus::tryFrom($state)?->label() ?? $state))
                    ->color(fn ($state) => match ($state instanceof ServiceRequestStatus ? $state->value : $state) {
                        'awaiting_approval' => 'warning',
                        'submitted', 'document_check' => 'info',
                        'proposed_to_ministry' => 'danger',
                        default => 'primary',
                    }),

                TextColumn::make('detail_info')
                    ->label(match ($this->activeTab) {
                        'emergency' => 'Fasilitas Kesehatan',
                        'stalled_pbi' => 'Diusulkan ke Kemensos',
                        default => 'Keterangan Tindakan',
                    })
                    ->state(function (ServiceRequest $record): string {
                        if ($this->activeTab === 'emergency') {
                            return $record->pbiReactivation?->health_facility_name
                                ?? 'Kondisi Darurat Medis';
                        }

                        if ($this->activeTab === 'stalled_pbi') {
                            $proposedAt = $record->pbiReactivation?->proposed_to_ministry_at;
                            if (! $proposedAt) {
                                return '-';
                            }
                            $days = (int) $proposedAt->diffInDays(now());

                            return $proposedAt->format('d/m/Y')." (Tertahan {$days} hari)";
                        }

                        return 'Menunggu Persetujuan / Paraf';
                    })
                    ->color(match ($this->activeTab) {
                        'emergency' => 'danger',
                        'stalled_pbi' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('submitted_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d/m/Y H:i')
                    ->description(fn (ServiceRequest $record): string => $record->submitted_at?->diffForHumans() ?? '-')
                    ->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('Buka Berkas')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->button()
                    ->color('primary')
                    ->url(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Tidak Ada Berkas yang Memerlukan Tindakan')
            ->emptyStateDescription('Seluruh berkas pada kategori ini telah ditindaklanjuti dengan baik.')
            ->emptyStateIcon(Heroicon::OutlinedCheckBadge)
            ->paginated([5, 10, 25]);
    }
}
