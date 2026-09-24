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
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assessments';

    protected static ?string $title = 'Hasil Assessment Klien';

    protected static ?string $modelLabel = 'Assessment';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    DatePicker::make('assessment_date')
                        ->label('Tanggal Assessment')
                        ->default(now())
                        ->required(),
                    Select::make('officer_id')
                        ->label('Petugas Asesor')
                        ->relationship('officer', 'name')
                        ->default(Auth::id())
                        ->required(),
                    Toggle::make('needs_referral')
                        ->label('Membutuhkan Rujukan ke Lembaga Luar')
                        ->default(false)
                        ->columnSpanFull(),
                    Textarea::make('result')
                        ->label('Hasil Assessment (Kondisi Fisik/Psikis/Sosial)')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('service_needs')
                        ->label('Kebutuhan Pelayanan yang Diperlukan')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),
                    Textarea::make('recommendation')
                        ->label('Rekomendasi Rencana Penanganan')
                        ->required()
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('result')
            ->columns([
                TextColumn::make('assessment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Asesor')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('result')
                    ->label('Hasil Assessment')
                    ->limit(40)
                    ->wrap(),
                IconColumn::make('needs_referral')
                    ->label('Perlu Rujukan')
                    ->boolean(),
            ])
            ->defaultSort('assessment_date', 'desc')
            ->headerActions([
                CreateAction::make()->label('Tambah Assessment'),
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
