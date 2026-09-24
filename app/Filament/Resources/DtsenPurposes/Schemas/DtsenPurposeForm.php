<?php

namespace App\Filament\Resources\DtsenPurposes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DtsenPurposeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Tujuan Surat Keterangan DTSEN')
                    ->description('Menentukan batas kelayakan desil dan masa berlaku surat')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode')
                                ->placeholder('Misal: spmb, pip, kip_kuliah')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true),
                            TextInput::make('name')
                                ->label('Nama Tujuan Penggunaan')
                                ->placeholder('Misal: SPMB Jalur Afirmasi')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('max_decile')
                                ->label('Batas Maksimal Desil')
                                ->helperText('Hanya pemohon dengan desil ≤ batas ini yang dapat diterbitkan suratnya')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->default(5),
                            TextInput::make('validity_days')
                                ->label('Masa Berlaku (Hari)')
                                ->helperText('Kosongkan jika berlaku selamanya')
                                ->numeric()
                                ->minValue(1)
                                ->suffix('Hari'),
                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
