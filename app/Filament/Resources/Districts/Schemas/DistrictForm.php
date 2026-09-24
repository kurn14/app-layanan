<?php

namespace App\Filament\Resources\Districts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DistrictForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Kecamatan')
                    ->placeholder('Misal: 35.05.01')
                    ->required()
                    ->maxLength(20),
                TextInput::make('name')
                    ->label('Nama Kecamatan')
                    ->placeholder('Misal: Bakung')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
