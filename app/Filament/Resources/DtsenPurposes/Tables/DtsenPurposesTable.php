<?php

namespace App\Filament\Resources\DtsenPurposes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DtsenPurposesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Tujuan Penggunaan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('max_decile')
                    ->label('Maks. Desil')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => "Desil {$state}")
                    ->sortable(),
                TextColumn::make('validity_days')
                    ->label('Masa Berlaku')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} Hari" : 'Permanen')
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'success')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                //
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
