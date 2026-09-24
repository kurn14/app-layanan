<?php

namespace App\Filament\Resources\ReferralInstitutions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReferralInstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lembaga Rujukan')
                    ->description('Lembaga atau balai sosial tujuan rujukan rehabilitasi klien')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lembaga Rujukan')
                                ->placeholder('Misal: Balai Rehabilitasi Sosial Anak "Antasena"')
                                ->required()
                                ->maxLength(255),
                            Select::make('type')
                                ->label('Tipe Lembaga')
                                ->options([
                                    'panti' => 'Panti Sosial',
                                    'balai' => 'Balai / Sentra Rehabilitasi',
                                    'RS' => 'Rumah Sakit / RS Jiwa',
                                    'LKS' => 'Lembaga Kesejahteraan Sosial (LKS)',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->required(),
                            TextInput::make('contact')
                                ->label('Kontak / Telepon PJ')
                                ->placeholder('Misal: (0342) 801xxx / 0812xxx')
                                ->maxLength(255),
                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true),
                            Textarea::make('address')
                                ->label('Alamat Lengkap')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
