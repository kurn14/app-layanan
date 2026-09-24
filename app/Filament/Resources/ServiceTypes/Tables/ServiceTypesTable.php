<?php

namespace App\Filament\Resources\ServiceTypes\Tables;

use App\Enums\ServiceHandler;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServiceTypesTable
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
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('handler')
                    ->label('Handler')
                    ->formatStateUsing(fn ($state) => match ($state instanceof ServiceHandler ? $state->value : $state) {
                        'dtsen' => 'DTSEN',
                        'pbi' => 'PBI-JK',
                        default => 'Umum',
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state instanceof ServiceHandler ? $state->value : $state) {
                        'dtsen' => 'info',
                        'pbi' => 'success',
                        default => 'secondary',
                    }),
                TextColumn::make('sla_days')
                    ->label('SLA')
                    ->suffix(' hari')
                    ->placeholder('-')
                    ->sortable(),
                IconColumn::make('needs_assessment')
                    ->label('Assessment')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('requirements_count')
                    ->label('Syarat')
                    ->counts('requirements')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('handler')
                    ->label('Filter Handler')
                    ->options([
                        'generic' => 'Umum',
                        'dtsen' => 'DTSEN',
                        'pbi' => 'PBI-JK',
                    ]),
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
