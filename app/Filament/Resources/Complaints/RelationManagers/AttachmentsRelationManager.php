<?php

namespace App\Filament\Resources\Complaints\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran Foto & Dokumen Pendukung';

    protected static ?string $modelLabel = 'Lampiran';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('type')
                        ->label('Tipe Lampiran')
                        ->options([
                            'photo' => 'Foto Dokumentasi Kejadian',
                            'document' => 'Dokumen Pendukung (PDF/KTP/Surat)',
                        ])
                        ->default('photo')
                        ->required(),
                    FileUpload::make('file_path')
                        ->label('File Foto / Dokumen')
                        ->directory('complaint-attachments')
                        ->disk('public')
                        ->visibility('public')
                        ->imagePreviewHeight('250')
                        ->required()
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Pratinjau Foto')
                    ->square()
                    ->size(60),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'photo' ? 'Foto' : 'Dokumen')
                    ->color(fn ($state) => $state === 'photo' ? 'info' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Unggah Lampiran'),
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
