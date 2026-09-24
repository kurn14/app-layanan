<?php

namespace App\Filament\Resources\RehabilitationCases\RelationManagers;

use App\Enums\ReferralStatus;
use App\Models\NumberSequence;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReferralsRelationManager extends RelationManager
{
    protected static string $relationship = 'referrals';

    protected static ?string $title = 'Rujukan ke Lembaga / Balai';

    protected static ?string $modelLabel = 'Rujukan';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('referral_number')
                        ->label('Nomor Surat Rujukan')
                        ->default(fn () => NumberSequence::generate('RJK'))
                        ->required(),
                    Select::make('referral_institution_id')
                        ->label('Lembaga Tujuan Rujukan')
                        ->relationship('institution', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    DatePicker::make('referral_date')
                        ->label('Tanggal Rujukan')
                        ->default(now())
                        ->required(),
                    Select::make('officer_id')
                        ->label('Petugas Pendamping')
                        ->relationship('officer', 'name')
                        ->default(Auth::id())
                        ->required(),
                    Select::make('status')
                        ->label('Status Rujukan')
                        ->options(collect(ReferralStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                        ->default('draft')
                        ->required(),
                    DateTimePicker::make('completed_at')
                        ->label('Tanggal Selesai Layanan di Lembaga'),
                    Textarea::make('service_result')
                        ->label('Hasil Pelayanan dari Lembaga Rujukan')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('referral_number')
            ->columns([
                TextColumn::make('referral_number')
                    ->label('No. Rujukan')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('institution.name')
                    ->label('Lembaga Tujuan')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('referral_date')
                    ->label('Tgl Rujukan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ReferralStatus ? $state->label() : ReferralStatus::tryFrom((string) $state)?->label() ?? $state)
                    ->color(fn ($state) => match ($state instanceof ReferralStatus ? $state->value : (string) $state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'accepted', 'in_service' => 'primary',
                        'completed' => 'success',
                        'declined', 'cancelled' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('service_result')
                    ->label('Hasil Pelayanan')
                    ->limit(30)
                    ->placeholder('Sedang berproses'),
            ])
            ->defaultSort('referral_date', 'desc')
            ->headerActions([
                CreateAction::make()->label('Buat Surat Rujukan'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
