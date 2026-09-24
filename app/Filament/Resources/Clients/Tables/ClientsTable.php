<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Klien')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->nik ? "NIK: {$record->nik}" : 'NIK tidak ada'),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'male' ? 'L' : 'P')
                    ->color(fn ($state) => $state === 'male' ? 'info' : 'danger'),
                TextColumn::make('village.name')
                    ->label('Wilayah')
                    ->formatStateUsing(fn ($state, $record) => "{$record->village?->district?->name} / {$record->village?->name}")
                    ->searchable(),
                TextColumn::make('rehabilitation_cases_count')
                    ->label('Kasus')
                    ->counts('rehabilitationCases')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Terdata')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('client_category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('village_id')
                    ->label('Filter Desa')
                    ->relationship('village', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
