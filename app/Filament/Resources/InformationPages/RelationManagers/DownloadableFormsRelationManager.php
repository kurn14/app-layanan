<?php

namespace App\Filament\Resources\InformationPages\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DownloadableFormsRelationManager extends RelationManager
{
    protected static string $relationship = 'downloadableForms';

    protected static ?string $title = 'Berkas Formulir Unduhan';

    protected static ?string $modelLabel = 'Formulir Unduhan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nama Formulir')
                        ->placeholder('Misal: Formulir Permohonan Rekomendasi Reaktivasi PBI-JK')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('version')
                        ->label('Versi Formulir')
                        ->placeholder('v1.0')
                        ->default('1.0')
                        ->required(),
                    Toggle::make('is_current')
                        ->label('Versi Berlaku Saat Ini')
                        ->default(true),
                    FileUpload::make('file_path')
                        ->label('File Formulir (PDF / Word / Excel)')
                        ->directory('downloadable-forms')
                        ->disk('public')
                        ->visibility('public')
                        ->required()
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Formulir')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('version')
                    ->label('Versi')
                    ->badge()
                    ->color('info'),
                IconColumn::make('is_current')
                    ->label('Berlaku')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Formulir'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
