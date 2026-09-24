<?php

namespace App\Filament\Resources\RehabilitationCases\Tables;

use App\Enums\RehabilitationCaseStatus;
use App\Models\RehabilitationCase;
use App\Services\StatusTransitionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RehabilitationCasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('case_number')
                    ->label('No. Kasus')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('client.name')
                    ->label('Klien')
                    ->searchable()
                    ->sortable()
                    ->description(fn (RehabilitationCase $record) => $record->client?->category?->name ?? '-'),
                TextColumn::make('handling_type')
                    ->label('Bentuk Penanganan')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? (string) $state) {
                        'direct' => 'Langsung',
                        'referral' => 'Rujukan',
                        'both' => 'Kombinasi',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('Status Kasus')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof RehabilitationCaseStatus ? $state->label() : RehabilitationCaseStatus::tryFrom((string) $state)?->label() ?? $state)
                    ->color(fn ($state) => match ($state instanceof RehabilitationCaseStatus ? $state->value : (string) $state) {
                        'received' => 'gray',
                        'assessment' => 'warning',
                        'service_planning' => 'purple',
                        'in_service' => 'primary',
                        'monitoring' => 'info',
                        'closed' => 'success',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas PJ')
                    ->placeholder('Belum ditugaskan'),
                TextColumn::make('received_at')
                    ->label('Tgl Diterima')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(collect(RehabilitationCaseStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                SelectFilter::make('handling_type')
                    ->label('Filter Penanganan')
                    ->options([
                        'direct' => 'Langsung',
                        'referral' => 'Rujukan',
                        'both' => 'Kombinasi',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    Action::make('startAssessment')
                        ->label('Mulai Assessment')
                        ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                        ->color('warning')
                        ->visible(fn (RehabilitationCase $record) => ($record->status?->value ?? (string) $record->status) === 'received')
                        ->action(function (RehabilitationCase $record): void {
                            StatusTransitionService::transition($record, RehabilitationCaseStatus::ASSESSMENT, 'Memulai proses assessment klien');
                            Notification::make()->success()->title('Status diubah ke Assessment')->send();
                        }),

                    Action::make('planService')
                        ->label('Tetapkan Rencana Pelayanan')
                        ->icon(Heroicon::OutlinedLightBulb)
                        ->color('purple')
                        ->visible(fn (RehabilitationCase $record) => ($record->status?->value ?? (string) $record->status) === 'assessment')
                        ->action(function (RehabilitationCase $record): void {
                            if ($record->assessments()->count() === 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Assessment belum ada')
                                    ->body('Klien harus memiliki minimal 1 catatan hasil assessment sebelum menetapkan rencana.')
                                    ->send();

                                return;
                            }
                            StatusTransitionService::transition($record, RehabilitationCaseStatus::SERVICE_PLANNING, 'Rencana pelayanan ditetapkan berdasarkan hasil assessment');
                            Notification::make()->success()->title('Status diubah ke Perencanaan Pelayanan')->send();
                        }),

                    Action::make('startService')
                        ->label('Mulai Pelayanan / Rujukan')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('primary')
                        ->visible(fn (RehabilitationCase $record) => ($record->status?->value ?? (string) $record->status) === 'service_planning')
                        ->action(function (RehabilitationCase $record): void {
                            StatusTransitionService::transition($record, RehabilitationCaseStatus::IN_SERVICE, 'Pelayanan / rujukan aktif dimulai');
                            Notification::make()->success()->title('Kasus sedang dalam pelayanan')->send();
                        }),

                    Action::make('startMonitoring')
                        ->label('Monitoring Perkembangan')
                        ->icon(Heroicon::OutlinedEye)
                        ->color('info')
                        ->visible(fn (RehabilitationCase $record) => ($record->status?->value ?? (string) $record->status) === 'in_service')
                        ->action(function (RehabilitationCase $record): void {
                            StatusTransitionService::transition($record, RehabilitationCaseStatus::MONITORING, 'Pelayanan selesai, masuk tahap monitoring');
                            Notification::make()->info()->title('Kasus dalam tahap monitoring')->send();
                        }),

                    Action::make('closeCase')
                        ->label('Tutup Kasus (Selesai)')
                        ->icon(Heroicon::OutlinedCheckBadge)
                        ->color('success')
                        ->visible(fn (RehabilitationCase $record) => in_array($record->status?->value ?? (string) $record->status, ['in_service', 'monitoring']))
                        ->schema([
                            Textarea::make('handling_result')
                                ->label('Hasil Akhir Pelayanan / Kondisi Klien')
                                ->placeholder('Wajib diisi menjelaskan hasil penanganan dan kemandirian klien')
                                ->default(fn ($record) => $record->handling_result)
                                ->required(),
                        ])
                        ->action(function (RehabilitationCase $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                RehabilitationCaseStatus::CLOSED,
                                $data['handling_result'],
                                extraAttributes: [
                                    'handling_result' => $data['handling_result'],
                                    'closed_at' => now(),
                                ]
                            );
                            Notification::make()->success()->title('Kasus rehabilitasi telah berhasil ditutup')->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('received_at', 'desc');
    }
}
