<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label('Telepon / WA')
                    ->searchable(),
                TextColumn::make('workUnit.name')
                    ->label('Unit Kerja')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),
                TextColumn::make('district.name')
                    ->label('Wilayah Operator')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->village) {
                            return "{$record->district?->name} / {$record->village?->name}";
                        }

                        return $state ?? '-';
                    }),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('work_unit_id')
                    ->label('Filter Unit Kerja')
                    ->relationship('workUnit', 'name'),
                SelectFilter::make('district_id')
                    ->label('Filter Kecamatan')
                    ->relationship('district', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
