<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use App\Enums\ServiceHandler;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Layanan')
                    ->description('Konfigurasi jenis layanan sosial dan alur prosesnya')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Layanan')
                                ->placeholder('Misal: DTSEN, PBI, REHSOS')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true),
                            TextInput::make('name')
                                ->label('Nama Layanan')
                                ->placeholder('Misal: Surat Keterangan DTSEN')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('category')
                                ->label('Kategori')
                                ->placeholder('Misal: Layanan Surat Keterangan')
                                ->required()
                                ->maxLength(100),
                            Select::make('handler')
                                ->label('Handler / Alur Pemrosesan')
                                ->options([
                                    ServiceHandler::GENERIC->value => 'Umum (Generic)',
                                    ServiceHandler::DTSEN->value => 'Surat Keterangan DTSEN',
                                    ServiceHandler::PBI->value => 'Reaktivasi KIS / PBI-JK',
                                ])
                                ->required(),
                            TextInput::make('sla_days')
                                ->label('SLA (Target Hari Kerja)')
                                ->numeric()
                                ->minValue(1)
                                ->suffix('Hari Kerja'),
                            Grid::make(2)->schema([
                                Toggle::make('needs_assessment')
                                    ->label('Perlu Assessment Lapangan')
                                    ->default(false),
                                Toggle::make('is_active')
                                    ->label('Layanan Aktif')
                                    ->default(true),
                            ]),
                            Textarea::make('description')
                                ->label('Deskripsi Layanan')
                                ->columnSpanFull()
                                ->rows(3),
                        ]),
                    ]),
            ]);
    }
}
