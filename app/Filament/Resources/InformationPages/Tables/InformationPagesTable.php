<?php

namespace App\Filament\Resources\InformationPages\Tables;

use App\Enums\InformationCategory;
use App\Enums\InformationPublishStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InformationPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Informasi')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof InformationCategory ? $state->label() : InformationCategory::tryFrom((string) $state)?->label() ?? $state)
                    ->color('info')
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label('Layanan Terkait')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('publish_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state instanceof InformationPublishStatus ? $state->value : (string) $state) {
                        'draft' => 'Draf',
                        'published' => 'Tayang',
                        'archived' => 'Arsip',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state instanceof InformationPublishStatus ? $state->value : (string) $state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Tgl Publikasi')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('manager.name')
                    ->label('Pengelola')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('publish_status')
                    ->label('Filter Status')
                    ->options([
                        'draft' => 'Draf',
                        'published' => 'Tayang (Published)',
                        'archived' => 'Diarsipkan',
                    ]),
                SelectFilter::make('category')
                    ->label('Filter Kategori')
                    ->options(collect(InformationCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }
}
