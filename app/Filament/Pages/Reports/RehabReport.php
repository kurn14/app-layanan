<?php

namespace App\Filament\Pages\Reports;

use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\District;
use App\Models\RehabilitationCase;
use App\Models\Village;
use App\Services\DashboardQueryService;
use App\Services\Export\RehabReportExcelExport;
use App\Services\Export\RehabReportPdfExport;
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

class RehabReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Rekapitulasi';

    protected static ?string $navigationLabel = 'Laporan Rehabilitasi';

    protected static ?string $title = 'Laporan Pelayanan Rehabilitasi Sosial';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports.report-page';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user || ! $user->can('lihat_laporan')) {
            return false;
        }

        return DashboardQueryService::make([], $user)->canAccessRehabilitation();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function (): Builder {
                return RehabilitationCase::query()
                    ->with(['client.category', 'client.village.district', 'officer']);
            })
            ->columns([
                TextColumn::make('case_number')
                    ->label('No. Kasus')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('client.name')
                    ->label('Klien')
                    ->searchable()
                    ->sortable()
                    ->description(fn (RehabilitationCase $record) => 'NIK: '.($record->client?->nik ?? '-')),
                TextColumn::make('client.category.name')
                    ->label('Kategori Klien')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('handling_type')
                    ->label('Bentuk Penanganan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof RehabilitationHandlingType ? $state->label() : (RehabilitationHandlingType::tryFrom((string) $state)?->label() ?? $state)),
                TextColumn::make('status')
                    ->label('Status Kasus')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof RehabilitationCaseStatus ? $state->label() : (RehabilitationCaseStatus::tryFrom((string) $state)?->label() ?? $state))
                    ->color(fn ($state) => match ($state instanceof RehabilitationCaseStatus ? $state->value : (string) $state) {
                        'received' => 'gray',
                        'assessment', 'service_planning' => 'warning',
                        'in_service' => 'primary',
                        'monitoring' => 'info',
                        'closed' => 'success',
                        default => 'secondary',
                    }),
                TextColumn::make('client.village.name')
                    ->label('Desa/Kel.')
                    ->searchable(),
                TextColumn::make('client.village.district.name')
                    ->label('Kecamatan')
                    ->searchable(),
                TextColumn::make('received_at')
                    ->label('Tgl Diterima')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal Diterima')
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal Diterima')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['startDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('received_at', '>=', $date))
                            ->when($data['endDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('received_at', '<=', $date));
                    }),

                SelectFilter::make('client_category_id')
                    ->label('Kategori Klien')
                    ->relationship('client.category', 'name')
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('Status Kasus')
                    ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),

                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->options(fn () => District::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $q, $districtId) {
                            $q->whereHas('client.village', fn (Builder $sq) => $sq->where('district_id', $districtId));
                        });
                    }),

                SelectFilter::make('village_id')
                    ->label('Desa / Kelurahan')
                    ->options(fn () => Village::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $q, $villageId) {
                            $q->whereHas('client', fn (Builder $sq) => $sq->where('village_id', $villageId));
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
                            'client_category_id' => $filterState['client_category_id']['value'] ?? null,
                            'status' => $filterState['status']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new RehabReportExcelExport($flatFilters))->download();
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
                            'client_category_id' => $filterState['client_category_id']['value'] ?? null,
                            'status' => $filterState['status']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new RehabReportPdfExport($flatFilters))->download();
                    }),
            ])
            ->defaultSort('received_at', 'desc');
    }
}
