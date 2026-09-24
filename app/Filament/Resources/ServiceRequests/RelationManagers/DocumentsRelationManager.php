<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Models\ServiceRequestDocument;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen Persyaratan';

    protected static ?string $modelLabel = 'Dokumen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('service_requirement_id')
                        ->label('Jenis Persyaratan')
                        ->relationship('requirement', 'name')
                        ->required(),
                    TextInput::make('original_name')
                        ->label('Nama File Asli')
                        ->required()
                        ->maxLength(255),
                    FileUpload::make('file_path')
                        ->label('File Dokumen')
                        ->directory('service-documents')
                        ->disk('local')
                        ->required()
                        ->columnSpanFull(),
                    Select::make('verification_status')
                        ->label('Status Verifikasi')
                        ->options([
                            'pending' => 'Menunggu Pemeriksaan',
                            'valid' => 'Valid / Diterima',
                            'revision_needed' => 'Butuh Revisi / Buram',
                        ])
                        ->default('pending')
                        ->required(),
                    Textarea::make('notes')
                        ->label('Catatan Pemeriksa')
                        ->rows(2),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                TextColumn::make('requirement.name')
                    ->label('Persyaratan')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('original_name')
                    ->label('Nama File')
                    ->searchable()
                    ->limit(35),
                TextColumn::make('verification_status')
                    ->label('Status Berkas')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'valid' => 'Valid',
                        'revision_needed' => 'Perlu Revisi',
                        default => 'Menunggu',
                    })
                    ->color(fn ($state) => match ($state) {
                        'valid' => 'success',
                        'revision_needed' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->limit(30),
                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah Dokumen'),
            ])
            ->recordActions([
                Action::make('markValid')
                    ->label('Valid')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->action(fn (ServiceRequestDocument $record) => $record->update(['verification_status' => 'valid'])),
                Action::make('markRevision')
                    ->label('Revisi')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->schema([
                        Textarea::make('notes')->label('Alasan Revisi')->required(),
                    ])
                    ->action(fn (ServiceRequestDocument $record, array $data) => $record->update([
                        'verification_status' => 'revision_needed',
                        'notes' => $data['notes'],
                    ])),
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
