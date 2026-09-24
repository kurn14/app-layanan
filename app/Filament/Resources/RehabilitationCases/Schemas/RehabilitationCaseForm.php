<?php

namespace App\Filament\Resources\RehabilitationCases\Schemas;

use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RehabilitationCaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kasus & Klien')
                    ->description('Data pokok kasus rehabilitasi sosial dan klien yang ditangani')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('case_number')
                                ->label('Nomor Kasus')
                                ->placeholder('Dibuat otomatis')
                                ->disabled()
                                ->dehydrated(false),
                            Select::make('client_id')
                                ->label('Klien Penerima Layanan')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('handling_type')
                                ->label('Bentuk Penanganan')
                                ->options([
                                    RehabilitationHandlingType::DIRECT->value => 'Pelayanan Langsung oleh Dinsos',
                                    RehabilitationHandlingType::REFERRAL->value => 'Rujukan ke Balai / Lembaga',
                                    RehabilitationHandlingType::BOTH->value => 'Kombinasi (Langsung & Rujukan)',
                                ])
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label('Status Kasus')
                                ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($status) => [
                                    $status->value => $status->label(),
                                ]))
                                ->required(),
                            Select::make('officer_id')
                                ->label('Petugas Pendamping')
                                ->relationship('officer', 'name')
                                ->searchable()
                                ->preload(),
                            DateTimePicker::make('received_at')
                                ->label('Tanggal & Waktu Diterima')
                                ->default(now()),
                        ]),
                    ]),

                Section::make('Asal Kasus / Rujukan Masuk')
                    ->description('Bila kasus berasal dari pengajuan masyarakat atau laporan pengaduan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('service_request_id')
                                ->label('Pengajuan Layanan Asal')
                                ->relationship('serviceRequest', 'request_number')
                                ->searchable()
                                ->placeholder('Kosongkan jika bukan dari pengajuan'),
                            Select::make('complaint_id')
                                ->label('Laporan Pengaduan Asal')
                                ->relationship('complaint', 'complaint_number')
                                ->searchable()
                                ->placeholder('Kosongkan jika bukan dari pengaduan'),
                        ]),
                    ]),

                Section::make('Hasil Pelayanan & Penutupan Kasus')
                    ->description('Catatan hasil akhir penanganan rehabilitasi sosial')
                    ->schema([
                        Textarea::make('handling_result')
                            ->label('Hasil Penanganan Akhir')
                            ->placeholder('Wajib diisi sebelum status kasus ditutup (Closed)')
                            ->rows(3)
                            ->columnSpanFull(),
                        DateTimePicker::make('closed_at')
                            ->label('Tanggal Penutupan Kasus')
                            ->disabled(),
                    ]),
            ]);
    }
}
