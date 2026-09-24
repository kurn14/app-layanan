<?php

namespace App\Filament\Resources\Complaints\Schemas;

use App\Enums\ComplaintStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComplaintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Laporan Pengaduan')
                    ->description('Data pokok pelapor dan permasalahan sosial yang dilaporkan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('complaint_number')
                                ->label('Nomor Laporan')
                                ->placeholder('Dibuat otomatis')
                                ->disabled()
                                ->dehydrated(false),
                            Select::make('complaint_category_id')
                                ->label('Kategori Permasalahan')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DateTimePicker::make('reported_at')
                                ->label('Waktu Kejadian / Laporan')
                                ->default(now())
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('reporter_name')
                                ->label('Nama Pelapor')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('reporter_phone')
                                ->label('Nomor Telepon / WhatsApp')
                                ->tel()
                                ->required()
                                ->maxLength(20),
                        ]),
                    ]),

                Section::make('Lokasi Kejadian & Deskripsi Masalah')
                    ->description('Rincian lokasi dan uraian lengkap kondisi di lapangan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('village_id')
                                ->label('Desa / Kelurahan Kejadian')
                                ->relationship('village', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('location_detail')
                                ->label('Alamat / Titik Acuan Lokasi')
                                ->placeholder('Misal: Depan Pasar Wlingi RT 02 RW 03')
                                ->required(),
                        ]),
                        Textarea::make('description')
                            ->label('Uraian Permasalahan Sosial')
                            ->placeholder('Jelaskan kondisi orang/keluarga yang membutuhkan penanganan')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Penanganan & Tindak Lanjut Dinas')
                    ->description('Hasil verifikasi, tindak lanjut penanganan, dan penyelesaian laporan')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('Status Laporan')
                                ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($s) => [
                                    $s->value => $s->label(),
                                ]))
                                ->required(),
                            Select::make('officer_id')
                                ->label('Petugas Penangan')
                                ->relationship('officer', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('duplicate_of_id')
                                ->label('Laporan Induk (Jika Laporan Ini Duplikat)')
                                ->relationship('duplicateOf', 'complaint_number')
                                ->searchable()
                                ->placeholder('Pilih nomor laporan sebelumnya'),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi / Klarifikasi Lapangan')
                                ->rows(3),
                            Textarea::make('action_taken')
                                ->label('Tindakan Penanganan yang Telah Dilakukan')
                                ->placeholder('Wajib diisi sebelum status diubah ke Selesai')
                                ->rows(3),
                        ]),
                        DateTimePicker::make('resolved_at')
                            ->label('Waktu Selesai Ditangani')
                            ->disabled(),
                    ]),
            ]);
    }
}
