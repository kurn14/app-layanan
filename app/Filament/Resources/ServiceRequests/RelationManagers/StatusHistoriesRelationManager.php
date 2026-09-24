<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Enums\ServiceRequestStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusHistories';

    protected static ?string $title = 'Riwayat Status / Jejak Audit Alur';

    protected static ?string $modelLabel = 'Riwayat Status';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('to_status')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Perubahan')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('from_status')
                    ->label('Dari Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? (ServiceRequestStatus::tryFrom((string) $state)?->label() ?? $state) : 'Awal')
                    ->color('gray'),
                TextColumn::make('to_status')
                    ->label('Menjadi Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ServiceRequestStatus::tryFrom((string) $state)?->label() ?? $state)
                    ->color('info'),
                TextColumn::make('user.name')
                    ->label('Diproses Oleh')
                    ->placeholder('Sistem / Masyarakat'),
                TextColumn::make('notes')
                    ->label('Catatan Alur')
                    ->wrap(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // Read-only audit log
            ])
            ->recordActions([
                //
            ]);
    }
}
