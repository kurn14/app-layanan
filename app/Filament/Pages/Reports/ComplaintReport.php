<?php

namespace App\Filament\Pages\Reports;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\District;
use App\Models\Village;
use App\Services\Export\ComplaintReportExcelExport;
use App\Services\Export\ComplaintReportPdfExport;
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

class ComplaintReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Rekapitulasi';

    protected static ?string $navigationLabel = 'Laporan Pengaduan';

    protected static ?string $title = 'Laporan Pengaduan Masalah Sosial';

    protected static ?int $navigationSort = 5;

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
                $query = Complaint::query()
                    ->with(['complaintCategory', 'village.district', 'officer']);

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
                TextColumn::make('complaint_number')
                    ->label('No. Laporan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                TextColumn::make('reporter_name')
                    ->label('Pelapor')
                    ->searchable()
                    ->description(fn (Complaint $record) => $record->reporter_phone ? "Telp: {$record->reporter_phone}" : ''),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ComplaintStatus ? $state->label() : (ComplaintStatus::tryFrom((string) $state)?->label() ?? $state))
                    ->color(fn ($state) => match ($state instanceof ComplaintStatus ? $state->value : (string) $state) {
                        'received' => 'gray',
                        'verified', 'disposition' => 'info',
                        'in_investigation', 'in_handling' => 'warning',
                        'resolved' => 'success',
                        'invalid', 'duplicate' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('village.name')
                    ->label('Desa/Kel.')
                    ->searchable(),
                TextColumn::make('village.district.name')
                    ->label('Kecamatan')
                    ->searchable(),
                TextColumn::make('reported_at')
                    ->label('Tgl Laporan')
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
                            ->label('Dari Tanggal Laporan')
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal Laporan')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['startDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('reported_at', '>=', $date))
                            ->when($data['endDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('reported_at', '<=', $date));
                    }),

                SelectFilter::make('complaint_category_id')
                    ->label('Kategori Aduan')
                    ->options(fn () => ComplaintCategory::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),

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
                            'complaint_category_id' => $filterState['complaint_category_id']['value'] ?? null,
                            'status' => $filterState['status']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new ComplaintReportExcelExport($flatFilters))->download();
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
                            'complaint_category_id' => $filterState['complaint_category_id']['value'] ?? null,
                            'status' => $filterState['status']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new ComplaintReportPdfExport($flatFilters))->download();
                    }),
            ])
            ->defaultSort('reported_at', 'desc');
    }
}
