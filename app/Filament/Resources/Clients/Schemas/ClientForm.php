<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Klien')
                    ->description('Data diri klien penerima pelayanan rehabilitasi sosial')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap Klien')
                                ->required()
                                ->maxLength(255),
                            Select::make('client_category_id')
                                ->label('Kategori Permasalahan Klien')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('nik')
                                ->label('NIK Klien (Bila ada)')
                                ->length(16)
                                ->numeric(),
                            DatePicker::make('birth_date')
                                ->label('Tanggal Lahir')
                                ->maxDate(now()),
                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options([
                                    Gender::MALE->value => 'Laki-laki',
                                    Gender::FEMALE->value => 'Perempuan',
                                ])
                                ->required(),
                            TextInput::make('phone')
                                ->label('Telepon / Kontak Keluarga')
                                ->tel()
                                ->maxLength(20),
                        ]),
                    ]),

                Section::make('Domisili Klien')
                    ->description('Alamat tempat tinggal atau lokasi penemuan klien')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('village_id')
                                ->label('Desa / Kelurahan')
                                ->relationship('village', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Textarea::make('address')
                                ->label('Alamat Lengkap / Keterangan Lokasi')
                                ->required()
                                ->rows(2),
                        ]),
                    ]),
            ]);
    }
}
