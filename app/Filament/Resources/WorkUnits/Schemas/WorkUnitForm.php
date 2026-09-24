<?php

namespace App\Filament\Resources\WorkUnits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WorkUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Unit Kerja / Bidang')
                    ->placeholder('Misal: Bidang Perlindungan dan Jaminan Sosial')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
