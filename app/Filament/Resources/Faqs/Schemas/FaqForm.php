<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kelola Tanya Jawab (FAQ)')
                    ->description('Pertanyaan yang sering ditanyakan masyarakat beserta jawabannya')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('information_page_id')
                                ->label('Terkait Halaman Informasi (Opsional)')
                                ->relationship('informationPage', 'title')
                                ->searchable()
                                ->preload()
                                ->placeholder('FAQ Umum / Tanpa Kaitan Halaman')
                                ->columnSpan(2),
                            TextInput::make('sort_order')
                                ->label('Urutan Tampil')
                                ->numeric()
                                ->default(1),
                        ]),
                        Textarea::make('question')
                            ->label('Pertanyaan (Question)')
                            ->placeholder('Misal: Berapa biaya pengurusan surat keterangan DTSEN?')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('answer')
                            ->label('Jawaban (Answer)')
                            ->placeholder('Seluruh pelayanan sosial di Dinas Sosial Kabupaten Blitar TIDAK DIPUNGUT BIAYA (GRATIS)...')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Tayangkan di Portal Publik')
                            ->default(true),
                    ]),
            ]);
    }
}
