<?php

namespace App\Filament\Resources\Complaints\Tables;

use App\Enums\ComplaintStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\RehabilitationHandlingType;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\RehabilitationCase;
use App\Models\User;
use App\Models\WorkUnit;
use App\Services\StatusTransitionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ComplaintsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('complaint_number')
                    ->label('No. Laporan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reporter_name')
                    ->label('Pelapor')
                    ->searchable()
                    ->description(fn (Complaint $record) => $record->reporter_phone),
                TextColumn::make('village.name')
                    ->label('Lokasi Kejadian')
                    ->formatStateUsing(fn ($state, Complaint $record) => "{$record->village?->district?->name} - {$record->village?->name}")
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ComplaintStatus ? $state->label() : ComplaintStatus::tryFrom((string) $state)?->label() ?? $state)
                    ->color(fn ($state) => match ($state instanceof ComplaintStatus ? $state->value : (string) $state) {
                        'received' => 'gray',
                        'verification' => 'warning',
                        'clarification_requested' => 'danger',
                        'dispatched' => 'info',
                        'in_handling' => 'primary',
                        'resolved' => 'success',
                        'duplicate', 'invalid' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas PJ')
                    ->placeholder('Belum ada'),
                TextColumn::make('reported_at')
                    ->label('Waktu Lapor')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(collect(ComplaintStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                SelectFilter::make('complaint_category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('village_id')
                    ->label('Filter Lokasi Desa')
                    ->relationship('village', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    Action::make('verifyComplaint')
                        ->label('Verifikasi Laporan')
                        ->icon(Heroicon::OutlinedCheckBadge)
                        ->color('warning')
                        ->visible(fn (Complaint $record) => in_array($record->status?->value ?? (string) $record->status, ['received', 'clarification_requested']))
                        ->schema([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi Laporan')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                ComplaintStatus::VERIFICATION,
                                $data['verification_result'],
                                extraAttributes: ['verification_result' => $data['verification_result']]
                            );
                            Notification::make()->success()->title('Laporan telah diverifikasi')->send();
                        }),

                    Action::make('requestClarification')
                        ->label('Minta Klarifikasi Pelapor')
                        ->icon(Heroicon::OutlinedQuestionMarkCircle)
                        ->color('danger')
                        ->visible(fn (Complaint $record) => ($record->status?->value ?? (string) $record->status) === 'verification')
                        ->schema([
                            Textarea::make('notes')
                                ->label('Keterangan / Informasi Tambahan yang Dibutuhkan')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                ComplaintStatus::CLARIFICATION_REQUESTED,
                                $data['notes']
                            );
                            Notification::make()->warning()->title('Permintaan klarifikasi dicatat')->send();
                        }),

                    Action::make('dispatchComplaint')
                        ->label('Disposisikan ke Bidang')
                        ->icon(Heroicon::OutlinedArrowRightCircle)
                        ->color('info')
                        ->visible(fn (Complaint $record) => ($record->status?->value ?? (string) $record->status) === 'verification')
                        ->schema([
                            Select::make('to_work_unit_id')
                                ->label('Unit Kerja / Bidang Tujuan')
                                ->options(WorkUnit::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('to_user_id')
                                ->label('Petugas (Opsional)')
                                ->options(User::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload(),
                            Textarea::make('instructions')
                                ->label('Instruksi Disposisi')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            Disposition::create([
                                'dispositionable_type' => $record->getMorphClass(),
                                'dispositionable_id' => $record->id,
                                'from_user_id' => Auth::id(),
                                'to_work_unit_id' => $data['to_work_unit_id'],
                                'to_user_id' => $data['to_user_id'] ?? null,
                                'instructions' => $data['instructions'],
                                'disposed_at' => now(),
                            ]);
                            StatusTransitionService::transition(
                                $record,
                                ComplaintStatus::DISPATCHED,
                                "Didisposisikan: {$data['instructions']}"
                            );
                            Notification::make()->success()->title('Laporan berhasil didisposisikan')->send();
                        }),

                    Action::make('startHandling')
                        ->label('Mulai Penanganan Lapangan')
                        ->icon(Heroicon::OutlinedPlay)
                        ->color('primary')
                        ->visible(fn (Complaint $record) => ($record->status?->value ?? (string) $record->status) === 'dispatched')
                        ->action(function (Complaint $record): void {
                            StatusTransitionService::transition(
                                $record,
                                ComplaintStatus::IN_HANDLING,
                                'Petugas memulai penanganan di lapangan'
                            );
                            Notification::make()->info()->title('Laporan dalam penanganan')->send();
                        }),

                    Action::make('resolveComplaint')
                        ->label('Selesaikan Penanganan')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(fn (Complaint $record) => ($record->status?->value ?? (string) $record->status) === 'in_handling')
                        ->schema([
                            Textarea::make('action_taken')
                                ->label('Tindakan & Solusi Penanganan yang Dilakukan')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                ComplaintStatus::RESOLVED,
                                $data['action_taken'],
                                extraAttributes: [
                                    'action_taken' => $data['action_taken'],
                                    'resolved_at' => now(),
                                ]
                            );
                            Notification::make()->success()->title('Pengaduan berhasil diselesaikan')->send();
                        }),

                    Action::make('createRehabilitationCase')
                        ->label('Jadikan Kasus Rehabilitasi')
                        ->icon(Heroicon::OutlinedHeart)
                        ->color('purple')
                        ->visible(fn (Complaint $record) => in_array($record->status?->value ?? (string) $record->status, ['verification', 'in_handling']))
                        ->schema([
                            Select::make('client_id')
                                ->label('Klien Penerima Layanan')
                                ->options(Client::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('handling_type')
                                ->label('Bentuk Penanganan')
                                ->options([
                                    RehabilitationHandlingType::DIRECT->value => 'Langsung',
                                    RehabilitationHandlingType::REFERRAL->value => 'Rujukan',
                                    RehabilitationHandlingType::BOTH->value => 'Kombinasi',
                                ])
                                ->default('direct')
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            $case = RehabilitationCase::create([
                                'client_id' => $data['client_id'],
                                'complaint_id' => $record->id,
                                'officer_id' => Auth::id(),
                                'handling_type' => $data['handling_type'],
                                'status' => RehabilitationCaseStatus::RECEIVED,
                                'received_at' => now(),
                            ]);
                            StatusTransitionService::transition(
                                $record,
                                $record->status,
                                "Diteruskan menjadi Kasus Rehabilitasi Sosial No. {$case->case_number}"
                            );
                            Notification::make()
                                ->success()
                                ->title("Kasus Rehabilitasi {$case->case_number} berhasil dibuat")
                                ->send();
                        }),

                    Action::make('markDuplicate')
                        ->label('Tandai Duplikat')
                        ->icon(Heroicon::OutlinedSquare2Stack)
                        ->color('gray')
                        ->visible(fn (Complaint $record) => ! in_array($record->status?->value ?? (string) $record->status, ['resolved', 'duplicate', 'invalid']))
                        ->schema([
                            Select::make('duplicate_of_id')
                                ->label('Laporan Induk yang Sudah Ada')
                                ->relationship('duplicateOf', 'complaint_number')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Complaint $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                ComplaintStatus::DUPLICATE,
                                'Ditandai sebagai duplikat dari laporan lain',
                                extraAttributes: ['duplicate_of_id' => $data['duplicate_of_id']]
                            );
                            Notification::make()->info()->title('Laporan ditandai duplikat')->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('reported_at', 'desc');
    }
}
