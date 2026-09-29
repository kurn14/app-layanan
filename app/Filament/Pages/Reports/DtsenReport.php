<?php

namespace App\Filament\Pages\Reports;

use App\Models\District;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\Village;
use App\Services\Export\DtsenReportExcelExport;
use App\Services\Export\DtsenReportPdfExport;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DtsenReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'Laporan & Rekapitulasi';

    protected static ?string $navigationLabel = 'Rekap SK DTSEN';

    protected static ?string $title = 'Laporan Rekapitulasi SK DTSEN';

    protected static ?int $navigationSort = 1;

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
                $query = DtsenCertificate::query()
                    ->with(['serviceRequest.village.district', 'dtsenPurpose', 'signer']);

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
                TextColumn::make('certificate_number')
                    ->label('No. SK DTSEN')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('subject_name')
                    ->label('Nama Subjek')
                    ->searchable()
                    ->sortable()
                    ->description(fn (DtsenCertificate $record) => 'NIK: '.($record->subject_nik ?? $record->serviceRequest?->applicant_nik ?? '-')),
                TextColumn::make('dtsenPurpose.name')
                    ->label('Tujuan Penggunaan')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('decile')
                    ->label('Desil')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? "Desil {$state}" : 'Non-Desil')
                    ->color(fn ($state) => match ((int) $state) {
                        1, 2 => 'danger',
                        3, 4 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_registered')
                    ->label('Terdaftar')
                    ->boolean(),
                TextColumn::make('serviceRequest.village.name')
                    ->label('Desa/Kel.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceRequest.village.district.name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->label('Tgl Terbit')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('verification_code')
                    ->label('Kode Verifikasi')
                    ->fontFamily('mono')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal Terbit')
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal Terbit')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['startDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('issued_at', '>=', $date))
                            ->when($data['endDate'] ?? null, fn (Builder $q, $date) => $q->whereDate('issued_at', '<=', $date));
                    }),

                SelectFilter::make('dtsen_purpose_id')
                    ->label('Tujuan Penggunaan')
                    ->options(fn () => DtsenPurpose::pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('decile')
                    ->label('Desil Kesejahteraan')
                    ->options([
                        '1' => 'Desil 1 (Sangat Miskin)',
                        '2' => 'Desil 2 (Miskin)',
                        '3' => 'Desil 3 (Hampir Miskin)',
                        '4' => 'Desil 4 (Rentan Miskin)',
                        '5' => 'Desil 5',
                        '6' => 'Desil 6',
                    ]),

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
                            'dtsen_purpose_id' => $filterState['dtsen_purpose_id']['value'] ?? null,
                            'decile' => $filterState['decile']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new DtsenReportExcelExport($flatFilters))->download();
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
                            'dtsen_purpose_id' => $filterState['dtsen_purpose_id']['value'] ?? null,
                            'decile' => $filterState['decile']['value'] ?? null,
                            'district_id' => $filterState['district_id']['value'] ?? null,
                            'village_id' => $filterState['village_id']['value'] ?? null,
                        ];

                        return (new DtsenReportPdfExport($flatFilters))->download();
                    }),
            ])
            ->defaultSort('issued_at', 'desc');
    }
}
