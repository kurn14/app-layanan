<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Enums\PermissionType;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Role')
                    ->description('Tentukan nama peran dan sistem pengamanan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Role')
                                ->placeholder('Misal: operator_wilayah')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->disabled(fn (?Role $record): bool => $record && in_array($record->name, ['administrator', 'operator', 'pimpinan']))
                                ->helperText(fn (?Role $record): string => $record && in_array($record->name, ['administrator', 'operator', 'pimpinan'])
                                    ? 'Nama role bawaan sistem (administrator, operator, pimpinan) tidak dapat diubah agar otorisasi sistem tetap konsisten.'
                                    : 'Gunakan huruf kecil atau snake_case untuk konsistensi sistem.'
                                ),
                            Hidden::make('guard_name')
                                ->default('web'),
                        ]),
                    ]),

                Section::make('Hak Akses (Permissions)')
                    ->description('Pilih daftar permission yang diberikan kepada peran ini')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Daftar Permission')
                            ->relationship(
                                name: 'permissions',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')
                            )
                            ->getOptionLabelFromRecordUsing(function (Permission $record): string {
                                $enum = PermissionType::tryFrom($record->name);

                                return $enum
                                    ? "{$enum->label()} [{$enum->group()}]"
                                    : $record->name;
                            })
                            ->descriptions(function (): array {
                                return Permission::all()->mapWithKeys(function (Permission $permission): array {
                                    $enum = PermissionType::tryFrom($permission->name);
                                    $group = $enum ? $enum->group() : 'Lainnya';

                                    return [$permission->id => "Kategori: {$group} · {$permission->name}"];
                                })->toArray();
                            })
                            ->bulkToggleable()
                            ->searchable()
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
