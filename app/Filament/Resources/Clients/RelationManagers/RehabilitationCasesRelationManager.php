<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Enums\RehabilitationCaseStatus;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RehabilitationCasesRelationManager extends RelationManager
{
    protected static string $relationship = 'rehabilitationCases';

    protected static ?string $title = 'Riwayat Kasus Pelayanan Klien';

    protected static ?string $modelLabel = 'Kasus Rehabilitasi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('case_number')
            ->columns([
                TextColumn::make('case_number')
                    ->label('No. Kasus')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('handling_type')
                    ->label('Penanganan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? (string) $state) {
                        'direct' => 'Langsung',
                        'referral' => 'Rujukan',
                        'both' => 'Kombinasi',
                        default => $state,
                    })
                    ->color('info'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof RehabilitationCaseStatus ? $state->label() : RehabilitationCaseStatus::tryFrom((string) $state)?->label() ?? $state)
                    ->color(fn ($state) => match ($state instanceof RehabilitationCaseStatus ? $state->value : (string) $state) {
                        'received' => 'gray',
                        'assessment', 'service_planning' => 'warning',
                        'in_service' => 'primary',
                        'monitoring' => 'info',
                        'closed' => 'success',
                        default => 'secondary',
                    }),
                TextColumn::make('officer.name')
                    ->label('Petugas'),
                TextColumn::make('received_at')
                    ->label('Tgl Diterima')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('received_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
