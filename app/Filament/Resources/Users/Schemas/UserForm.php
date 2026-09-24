<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Village;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description('Data login dan identitas akun pengguna')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->placeholder('Misal: Budi Santoso, S.Sos.')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->rule(Password::default()),
                            TextInput::make('phone')
                                ->label('Nomor WhatsApp / HP')
                                ->tel()
                                ->maxLength(20),
                            TextInput::make('nik')
                                ->label('NIK (16 Digit)')
                                ->length(16)
                                ->numeric(),
                            Toggle::make('is_active')
                                ->label('Akun Aktif')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),

                Section::make('Penugasan & Wilayah')
                    ->description('Unit kerja kedinasan atau wilayah penugasan operator desa/kecamatan')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('work_unit_id')
                                ->label('Unit Kerja / Bidang')
                                ->relationship('workUnit', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Pilih Unit Kerja'),
                            Select::make('district_id')
                                ->label('Kecamatan (Operator)')
                                ->relationship('district', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('village_id', null))
                                ->placeholder('Pilih Kecamatan'),
                            Select::make('village_id')
                                ->label('Desa/Kelurahan (Operator)')
                                ->options(function (Get $get) {
                                    $districtId = $get('district_id');
                                    if (! $districtId) {
                                        return [];
                                    }

                                    return Village::where('district_id', $districtId)->pluck('name', 'id');
                                })
                                ->searchable()
                                ->placeholder('Pilih Desa'),
                        ]),
                    ]),
            ]);
    }
}
