<?php

namespace App\Filament\Resources\Complaints\RelationManagers;

use App\Models\ComplaintAttachment;
use Filament\Actions\Action;
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
use Filament\Support\Icons\Heroicon;
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
                    ->disk('public')
                    ->visibility('public')
                    ->square()
                    ->size(60)
                    ->url(fn (ComplaintAttachment $record) => asset('storage/'.$record->file_path), shouldOpenInNewTab: true),
                TextColumn::make('file_path')
                    ->label('Nama Berkas')
                    ->formatStateUsing(fn ($state) => basename((string) $state))
                    ->url(fn (ComplaintAttachment $record) => asset('storage/'.$record->file_path), shouldOpenInNewTab: true)
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ($state === 'photo' || (is_object($state) && $state->value === 'photo')) ? 'Foto' : 'Dokumen')
                    ->color(fn ($state) => ($state === 'photo' || (is_object($state) && $state->value === 'photo')) ? 'info' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Unggah Lampiran'),
            ])
            ->recordActions([
                Action::make('openFile')
                    ->label('Buka')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('primary')
                    ->url(fn (ComplaintAttachment $record) => asset('storage/'.$record->file_path), shouldOpenInNewTab: true),
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
