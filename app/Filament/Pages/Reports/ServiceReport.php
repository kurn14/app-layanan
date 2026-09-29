<?php

namespace App\Filament\Pages\Reports;

use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Models\Village;
use App\Services\Export\ServiceReportExcelExport;
use App\Services\Export\ServiceReportPdfExport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Rekapitulasi';

    protected static ?string $navigationLabel = 'Laporan Pelayanan';

    protected static ?string $title = 'Laporan Pelayanan Sosial Terpadu';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.reports.report-page';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('lihat_laporan') ?? false;
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isOperatorVillage = (bool) ($user?->village_id && ! $user->hasRole('administrator') && ! $user->hasRole('pimpinan'));
        $isOperatorDistrict = (bool) ($user?->district_id && ! $user->hasRole('administrator') && ! $user->hasRole('pimpinan'));

        return $table
            ->query(function (): Builder {
                $query = ServiceRequest::query()
                    ->with(['serviceType', 'village.district', 'officer']);

                $user = auth()->user();
                if ($user && ! $user->hasRole('administrator') && ! $user->hasRole('pimpinan')) {
                    if ($user->village_id) {
                        $query->where('village_id', $user->village_id);
                    } elseif ($user->district_id) {
                        $query->whereHas('village', fn (Builder $q) => $q->where('district_id', $user->district_id));
                    }
                }

                return $query;
            })
            ->columns([
                TextColumn::make('request_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('applicant_name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ServiceRequest $record) => "NIK: {$record->applicant_nik}"),
                TextColumn::make('serviceType.name')
                    ->label('Jenis Layanan')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ServiceRequestStatus ? $state->label() : (ServiceRequestStatus::tryFrom((string) $state)?->label() ?? $state))
                    ->color(fn ($state) => match ($state instanceof ServiceRequestStatus ? $state->value : (string) $state) {
                        'completed' => 'success',
                        'rejected', 'ministry_rejected' => 'danger',
                        'submitted' => 'gray',
                        'revision_requested' => 'warning',
                        'issued', 'reactivated' => 'primary',
                        default => 'info',
                    })
                    ->sortable(),
                TextColumn::make('village.name')
                    ->label('Desa/Kel.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('village.district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Tgl Pengajuan')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal Pengajuan')
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal Pengajuan')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['startDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('submitted_at', '>=', $date))
                            ->when($data['endDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('submitted_at', '<=', $date));
                    }),

                SelectFilter::make('service_type_id')
                    ->label('Jenis Layanan')
                    ->options(fn () => ServiceType::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),

                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->options(fn () => District::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->hidden($isOperatorDistrict || $isOperatorVillage)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $q, $districtId) {
                            $q->whereHas('village', fn (Builder $sq) => $sq->where('district_id', $districtId));
                        });
                    }),

                SelectFilter::make('village_id')
                    ->label('Desa / Kelurahan')
                    ->options(fn () => Village::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->hidden($isOperatorVillage)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $q, $villageId) {
                            $q->where('village_id', $villageId);
                        });
                    }),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Ekspor Excel (Multi-Sheet)')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('success')
                    ->visible(fn () => auth()->user()?->can('ekspor_laporan'))
                    ->action(function () {
                        $filterState = $this->tableFilters ?? [];
                        $flatFilters = [
                            'startDate' => $filterState['period']['startDate'] ?? null,
                            'endDate' => $filterState['period']['endDate'] ?? null,
                            'service_type_id' => $filterState['service_type_id']['value'] ?? null,
                            'status' => $filterState['status']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new ServiceReportExcelExport($flatFilters))->download();
                    }),

                Action::make('exportPdf')
                    ->label('Ekspor PDF')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('danger')
                    ->visible(fn () => auth()->user()?->can('ekspor_laporan'))
                    ->action(function () {
                        $filterState = $this->tableFilters ?? [];
                        $flatFilters = [
                            'startDate' => $filterState['period']['startDate'] ?? null,
                            'endDate' => $filterState['period']['endDate'] ?? null,
                            'service_type_id' => $filterState['service_type_id']['value'] ?? null,
                            'status' => $filterState['status']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new ServiceReportPdfExport($flatFilters))->download();
                    }),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
