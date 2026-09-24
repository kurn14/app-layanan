<?php

namespace App\Filament\Resources\InformationPages\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqsRelationManager extends RelationManager
{
    protected static string $relationship = 'faqs';

    protected static ?string $title = 'Tanya Jawab (FAQ) Terkait';

    protected static ?string $modelLabel = 'FAQ';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('sort_order')
                        ->label('Urutan Tampil')
                        ->numeric()
                        ->default(1),
                    Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true),
                    Textarea::make('question')
                        ->label('Pertanyaan (FAQ)')
                        ->placeholder('Misal: Berapa lama waktu penerbitan surat keterangan?')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),
                    Textarea::make('answer')
                        ->label('Jawaban Lengkap')
                        ->placeholder('Surat keterangan diterbitkan dalam waktu 1-3 hari kerja...')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('No')
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('answer')
                    ->label('Jawaban')
                    ->limit(50),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->headerActions([
                CreateAction::make()->label('Tambah FAQ'),
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
