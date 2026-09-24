<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MonitoringRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'monitoringRecords';

    protected static ?string $title = 'Catatan Monitoring Berkala';

    protected static ?string $modelLabel = 'Catatan Monitoring';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    DatePicker::make('monitoring_date')
                        ->label('Tanggal Monitoring')
                        ->default(now())
                        ->required(),
                    Select::make('officer_id')
                        ->label('Petugas Monitoring')
                        ->relationship('officer', 'name')
                        ->default(Auth::id())
                        ->required(),
                    Select::make('referral_id')
                        ->label('Terkait Rujukan (Bila ada)')
                        ->relationship('referral', 'referral_number')
                        ->placeholder('Monitoring pelayanan umum/langsung')
                        ->columnSpanFull(),
                    Textarea::make('progress')
                        ->label('Perkembangan Klien / Situasi Terkini')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('result_notes')
                        ->label('Rekomendasi Tindak Lanjut Monitoring')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('progress')
            ->columns([
                TextColumn::make('monitoring_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('progress')
                    ->label('Perkembangan')
                    ->limit(45)
                    ->wrap(),
                TextColumn::make('result_notes')
                    ->label('Tindak Lanjut')
                    ->limit(35),
            ])
            ->defaultSort('monitoring_date', 'desc')
            ->headerActions([
                CreateAction::make()->label('Tambah Monitoring'),
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
