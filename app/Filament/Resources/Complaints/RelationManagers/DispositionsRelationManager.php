<?php

namespace App\Filament\Resources\Complaints\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DispositionsRelationManager extends RelationManager
{
    protected static string $relationship = 'dispositions';

    protected static ?string $title = 'Disposisi Penanganan Pengaduan';

    protected static ?string $modelLabel = 'Disposisi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('to_work_unit_id')
                        ->label('Unit Kerja / Bidang Tujuan')
                        ->relationship('toWorkUnit', 'name')
                        ->required(),
                    Select::make('to_user_id')
                        ->label('Petugas (Opsional)')
                        ->relationship('toUser', 'name')
                        ->searchable(),
                    DateTimePicker::make('disposed_at')
                        ->label('Waktu Disposisi')
                        ->default(now())
                        ->required(),
                    Select::make('from_user_id')
                        ->label('Pemberi Disposisi')
                        ->relationship('fromUser', 'name')
                        ->default(Auth::id())
                        ->required(),
                    Textarea::make('instructions')
                        ->label('Instruksi Penanganan')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('instructions')
            ->columns([
                TextColumn::make('disposed_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('fromUser.name')
                    ->label('Dari')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('toWorkUnit.name')
                    ->label('Tujuan Bidang')
                    ->badge()
                    ->color('info'),
                TextColumn::make('toUser.name')
                    ->label('Petugas')
                    ->placeholder('Semua Petugas Bidang'),
                TextColumn::make('instructions')
                    ->label('Instruksi')
                    ->wrap(),
            ])
            ->defaultSort('disposed_at', 'desc')
            ->headerActions([
                CreateAction::make()->label('Buat Disposisi'),
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
