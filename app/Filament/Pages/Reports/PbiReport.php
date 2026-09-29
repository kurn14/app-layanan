<?php

namespace App\Filament\Pages\Reports;

use App\Enums\MinistryDecision;
use App\Enums\PbiReactivationReason;
use App\Models\District;
use App\Models\PbiReactivation;
use App\Models\Village;
use App\Services\Export\PbiReportExcelExport;
use App\Services\Export\PbiReportPdfExport;
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

class PbiReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Rekapitulasi';

    protected static ?string $navigationLabel = 'Rekap Reaktivasi PBI-JK';

    protected static ?string $title = 'Laporan Rekapitulasi Reaktivasi PBI-JK';

    protected static ?int $navigationSort = 2;

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
                $query = PbiReactivation::query()
                    ->with(['serviceRequest.village.district', 'signer']);

                $user = auth()->user();
                if ($user && ! $user->hasRole('administrator') && ! $user->hasRole('pimpinan')) {
                    if ($user->village_id) {
                        $query->whereHas('serviceRequest', fn (Builder $q) => $q->where('village_id', $user->village_id));
                    } elseif ($user->district_id) {
                        $query->whereHas('serviceRequest.village', fn (Builder $q) => $q->where('district_id', $user->district_id));
                    }
                }

                return $query;
            })
            ->columns([
                TextColumn::make('serviceRequest.request_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('participant_name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PbiReactivation $record) => "NIK: {$record->participant_nik}"),
                TextColumn::make('bpjs_card_number')
                    ->label('No. BPJS')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('reason')
                    ->label('Alasan Reaktivasi')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state instanceof PbiReactivationReason ? $state->label() : PbiReactivationReason::tryFrom((string) $state)?->label() ?? $state),
                TextColumn::make('serviceRequest.village.name')
                    ->label('Desa/Kel.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceRequest.village.district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recommendation_number')
                    ->label('No. Rekomendasi')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ministry_decision')
                    ->label('Keputusan Kemensos')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state instanceof MinistryDecision ? $state->value : (string) $state) {
                        'approved', 'reactivated' => 'Disetujui',
                        'rejected', 'ministry_rejected' => 'Ditolak',
                        'pending' => 'Proses Pengusulan',
                        default => '-'
                    })
                    ->color(fn ($state) => match ($state instanceof MinistryDecision ? $state->value : (string) $state) {
                        'approved', 'reactivated' => 'success',
                        'rejected', 'ministry_rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('reactivated_date')
                    ->label('Tgl Reaktivasi')
                    ->date('d/m/Y')
                    ->sortable(),
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
                            ->when($data['startDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['endDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                SelectFilter::make('reason')
                    ->label('Alasan Reaktivasi')
                    ->options(collect(PbiReactivationReason::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),

                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->options(fn () => District::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->hidden($isOperatorDistrict || $isOperatorVillage)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $q, $districtId) {
                            $q->whereHas('serviceRequest.village', fn (Builder $sq) => $sq->where('district_id', $districtId));
                        });
                    }),

                SelectFilter::make('village_id')
                    ->label('Desa / Kelurahan')
                    ->options(fn () => Village::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->hidden($isOperatorVillage)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['value'] ?? null, function (Builder $q, $villageId) {
                            $q->whereHas('serviceRequest', fn (Builder $sq) => $sq->where('village_id', $villageId));
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
                            'reason' => $filterState['reason']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new PbiReportExcelExport($flatFilters))->download();
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
                            'reason' => $filterState['reason']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new PbiReportPdfExport($flatFilters))->download();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
