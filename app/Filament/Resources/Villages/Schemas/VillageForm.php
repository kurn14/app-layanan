<?php

namespace App\Filament\Resources\Villages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VillageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('district_id')
                    ->label('Kecamatan')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->label('Kode Desa/Kelurahan')
                    ->placeholder('Misal: 35.05.01.2001')
                    ->required()
                    ->maxLength(20),
                TextInput::make('name')
                    ->label('Nama Desa/Kelurahan')
                    ->placeholder('Misal: Bakung')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
