<?php

namespace App\Filament\Resources\InformationPages\Schemas;

use App\Enums\InformationCategory;
use App\Enums\InformationPublishStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InformationPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Publik & Publikasi')
                    ->description('Pengaturan judul halaman, tautan slug, dan status tayang di portal masyarakat')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('title')
                                ->label('Judul Informasi')
                                ->placeholder('Misal: Panduan Pengajuan SK DTSEN untuk Beasiswa')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                            TextInput::make('slug')
                                ->label('Slug URL (Permalink)')
                                ->placeholder('panduan-sk-dtsen')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Select::make('category')
                                ->label('Kategori')
                                ->options(collect(InformationCategory::cases())->mapWithKeys(fn ($c) => [
                                    $c->value => $c->label(),
                                ]))
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('service_type_id')
                                ->label('Terkait Layanan Dinas (Opsional)')
                                ->relationship('serviceType', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Pilih layanan terkait'),
                            Select::make('publish_status')
                                ->label('Status Publikasi')
                                ->options([
                                    InformationPublishStatus::DRAFT->value => 'Draf (Belum Tayang)',
                                    InformationPublishStatus::PUBLISHED->value => 'Dipublikasikan (Aktif)',
                                    InformationPublishStatus::ARCHIVED->value => 'Diarsipkan',
                                ])
                                ->default('published')
                                ->required(),
                            DateTimePicker::make('published_at')
                                ->label('Waktu Publikasi')
                                ->default(now()),
                        ]),
                        Select::make('manager_id')
                            ->label('Petugas Pengelola Konten')
                            ->relationship('manager', 'name')
                            ->default(Auth::id())
                            ->searchable(),
                    ]),

                Section::make('Uraian Konten Pelayanan')
                    ->description('Detail deskripsi, persyaratan, dan alur pelayanan sosial untuk masyarakat')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Uraian / Ringkasan Informasi')
                            ->placeholder('Jelaskan secara ringkas maksud dan tujuan layanan...')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            Textarea::make('requirements')
                                ->label('Persyaratan yang Wajib Dipenuhi')
                                ->placeholder('1. KTP Asli Blitar&#10;2. Kartu Keluarga&#10;3. Surat...')
                                ->rows(5),
                            Textarea::make('procedure')
                                ->label('Alur / Prosedur Pelayanan')
                                ->placeholder('1. Pemohon mengajukan permohonan via portal&#10;2. Petugas memverifikasi...')
                                ->rows(5),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('service_hours')
                                ->label('Jam Pelayanan')
                                ->placeholder('Senin - Jumat, 08.00 - 15.00 WIB'),
                            TextInput::make('location')
                                ->label('Lokasi Kantor Pelayanan')
                                ->placeholder('Kantor Dinas Sosial Kab. Blitar'),
                            TextInput::make('contact')
                                ->label('Kontak Informasi / Hotline')
                                ->placeholder('(0342) 801xxx / 08123xxx'),
                        ]),
                    ]),
            ]);
    }
}
