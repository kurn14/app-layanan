<?php

namespace App\Filament\Resources\ServiceTypes\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'requirements';

    protected static ?string $title = 'Dokumen Persyaratan';

    protected static ?string $modelLabel = 'Persyaratan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nama Dokumen Persyaratan')
                        ->placeholder('Misal: KTP Pemohon (PDF/JPG)')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('is_mandatory')
                        ->label('Wajib Diunggah')
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(1),
                    TextInput::make('allowed_mimes')
                        ->label('Format File Diizinkan (MIME / Ekstensi)')
                        ->placeholder('pdf,jpg,png')
                        ->default('pdf,jpg,png')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('No')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_mandatory')
                    ->label('Wajib')
                    ->boolean(),
                TextColumn::make('allowed_mimes')
                    ->label('Format')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Persyaratan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
