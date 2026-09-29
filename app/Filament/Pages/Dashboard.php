<?php

namespace App\Filament\Pages;

use App\Models\District;
use App\Models\ServiceType;
use App\Models\Village;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard SAPA SOSIAL';

    protected static ?string $navigationLabel = 'Dashboard Utama';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public function getColumns(): int|array
    {
        return 2;
    }

    public function filtersForm(Schema $schema): Schema
    {
        $user = auth()->user();
        $isOperatorVillage = (bool) ($user?->village_id && ! $user->hasRole('administrator') && ! $user->hasRole('pimpinan'));
        $isOperatorDistrict = (bool) ($user?->district_id && ! $user->hasRole('administrator') && ! $user->hasRole('pimpinan'));

        $defaultDistrictId = $user?->village?->district_id ?? $user?->district_id;
        $defaultVillageId = $user?->village_id;

        return $schema
            ->columns(1)
            ->components([
                Section::make('Filter Terpadu Dashboard')
                    ->description('Saring data periode, jenis layanan, dan wilayah untuk seluruh widget dashboard')
                    ->icon(Heroicon::OutlinedFunnel)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 5,
                    ])
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal')
                            ->placeholder('Pilih tanggal awal')
                            ->native(false)
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),

                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal')
                            ->placeholder('Pilih tanggal akhir')
                            ->native(false)
                            ->minDate(fn (Get $get) => $get('startDate')),

                        Select::make('service_type_id')
                            ->label('Jenis Layanan')
                            ->placeholder('Semua Layanan')
                            ->options(fn () => ServiceType::query()
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                            )
                            ->searchable(),

                        Select::make('district_id')
                            ->label('Kecamatan')
                            ->placeholder('Semua Kecamatan')
                            ->options(fn () => District::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->default($defaultDistrictId)
                            ->disabled($isOperatorDistrict || $isOperatorVillage)
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('village_id', null)),

                        Select::make('village_id')
                            ->label('Desa / Kelurahan')
                            ->placeholder('Semua Desa / Kelurahan')
                            ->options(function (Get $get) use ($defaultDistrictId) {
                                $districtId = $get('district_id') ?: $defaultDistrictId;
                                if (! $districtId) {
                                    return [];
                                }

                                return Village::query()
                                    ->where('district_id', $districtId)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->default($defaultVillageId)
                            ->disabled($isOperatorVillage)
                            ->dehydrated(),
                    ]),
            ]);
    }
}
