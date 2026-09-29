<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Enums\PermissionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'operator' => 'primary',
                        'pimpinan' => 'success',
                        default => 'info',
                    }),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('users_count')
                    ->label('Jumlah Pengguna')
                    ->counts('users')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label('Jumlah Hak Akses')
                    ->counts('permissions')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('permissions.name')
                    ->label('Daftar Hak Akses')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->formatStateUsing(function (string $state): string {
                        return PermissionType::tryFrom($state)?->label() ?? $state;
                    }),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Role $record): bool => in_array($record->name, ['administrator', 'operator', 'pimpinan']))
                    ->tooltip(fn (Role $record): ?string => in_array($record->name, ['administrator', 'operator', 'pimpinan'])
                        ? 'Role bawaan sistem tidak dapat dihapus'
                        : null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
