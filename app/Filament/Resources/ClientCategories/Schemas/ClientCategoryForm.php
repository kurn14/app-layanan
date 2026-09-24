<?php

namespace App\Filament\Resources\ClientCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kategori Klien')
                    ->placeholder('Misal: Lansia Terlantar, Disabilitas, ODGJ Terlantar')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
