<?php

namespace App\Filament\Resources\ReferralInstitutions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReferralInstitutionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'panti' => 'Panti Sosial',
                        'balai' => 'Balai / Sentra',
                        'RS' => 'Rumah Sakit',
                        'LKS' => 'LKS',
                        default => ucfirst($state),
                    }),
                TextColumn::make('contact')
                    ->label('Kontak')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'panti' => 'Panti Sosial',
                        'balai' => 'Balai / Sentra',
                        'RS' => 'Rumah Sakit',
                        'LKS' => 'LKS',
                    ]),
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
