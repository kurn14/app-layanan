<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Enums\ServiceRequestStatus;
use App\Models\Approval;
use App\Models\NumberSequence;
use App\Models\ServiceRequest;
use App\Services\StatusTransitionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('No. Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('applicant_name')
                    ->label('Nama Pemohon')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ServiceRequest $record): string => "NIK: {$record->applicant_nik}"),
                TextColumn::make('serviceType.name')
                    ->label('Layanan')
                    ->badge()
                    ->color(fn (ServiceRequest $record) => match ($record->serviceType?->handler?->value ?? $record->serviceType?->handler) {
                        'dtsen' => 'info',
                        'pbi' => 'success',
                        default => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ServiceRequestStatus ? $state->label() : ServiceRequestStatus::tryFrom((string) $state)?->label() ?? $state)
                    ->color(fn ($state) => match ($state instanceof ServiceRequestStatus ? $state->value : (string) $state) {
                        'submitted' => 'gray',
                        'document_check', 'verification', 'eligibility_verification', 'data_verification' => 'warning',
                        'revision_requested' => 'danger',
                        'awaiting_approval', 'recommendation_issued' => 'info',
                        'proposed_to_ministry' => 'purple',
                        'ministry_approved', 'reactivated', 'issued' => 'primary',
                        'completed' => 'success',
                        'rejected', 'ministry_rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_priority')
                    ->label('Prioritas')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedExclamationTriangle)
                    ->trueColor('danger')
                    ->falseIcon(null)
                    ->sortable(),
                TextColumn::make('village.name')
                    ->label('Desa/Kecamatan')
                    ->formatStateUsing(fn ($state, ServiceRequest $record) => "{$record->village?->district?->name} - {$record->village?->name}")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('officer.name')
                    ->label('Petugas')
                    ->placeholder('Belum ditugaskan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('submitted_at')
                    ->label('Tgl Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                SelectFilter::make('service_type_id')
                    ->label('Filter Jenis Layanan')
                    ->relationship('serviceType', 'name'),
                SelectFilter::make('village_id')
                    ->label('Filter Desa / Kelurahan')
                    ->relationship('village', 'name')
                    ->searchable(),
                Filter::make('is_priority')
                    ->label('Hanya Prioritas / Darurat')
                    ->query(fn (Builder $query) => $query->where('is_priority', true)),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // 1. Action: Periksa Dokumen
                    Action::make('checkDocuments')
                        ->label('Periksa Berkas')
                        ->icon(Heroicon::OutlinedDocumentCheck)
                        ->color('warning')
                        ->visible(fn (ServiceRequest $record): bool => in_array($record->status?->value ?? (string) $record->status, ['submitted', 'revision_requested']))
                        ->schema([
                            Select::make('decision')
                                ->label('Hasil Pemeriksaan')
                                ->options([
                                    'valid' => 'Berkas Lengkap & Valid (Lanjut ke Verifikasi)',
                                    'revision' => 'Berkas Belum Lengkap / Butuh Revisi',
                                ])
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Pemeriksaan')
                                ->placeholder('Sebutkan dokumen yang kurang atau tidak sesuai')
                                ->required(fn ($get) => $get('decision') === 'revision'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            if ($data['decision'] === 'revision') {
                                StatusTransitionService::transition(
                                    $record,
                                    ServiceRequestStatus::REVISION_REQUESTED,
                                    $data['notes'],
                                    extraAttributes: ['verification_result' => $data['notes']]
                                );
                                Notification::make()->warning()->title('Permintaan perbaikan berkas dicatat')->send();
                            } else {
                                $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                                $nextStatus = match ($handler) {
                                    'dtsen' => ServiceRequestStatus::DATA_VERIFICATION,
                                    'pbi' => ServiceRequestStatus::ELIGIBILITY_VERIFICATION,
                                    default => ServiceRequestStatus::VERIFICATION,
                                };
                                StatusTransitionService::transition($record, $nextStatus, $data['notes'] ?? 'Berkas diverifikasi lengkap');
                                Notification::make()->success()->title('Berkas dinyatakan lengkap')->send();
                            }
                        }),

                    // 2. Action: Input Hasil SIKS-NG (DTSEN)
                    Action::make('verifySiksNg')
                        ->label('Input Hasil SIKS-NG')
                        ->icon(Heroicon::OutlinedCheckBadge)
                        ->color('info')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'dtsen' && in_array($status, ['data_verification', 'document_check']);
                        })
                        ->schema([
                            Toggle::make('is_registered')
                                ->label('Terdaftar di SIKS-NG')
                                ->default(true)
                                ->required(),
                            Select::make('decile')
                                ->label('Peringkat Desil')
                                ->options([
                                    1 => 'Desil 1', 2 => 'Desil 2', 3 => 'Desil 3', 4 => 'Desil 4', 5 => 'Desil 5',
                                    6 => 'Desil 6', 7 => 'Desil 7', 8 => 'Desil 8', 9 => 'Desil 9', 10 => 'Desil 10',
                                ])
                                ->required(fn ($get) => $get('is_registered')),
                            DateTimePicker::make('checked_at')
                                ->label('Tanggal Pengecekan SIKS-NG')
                                ->default(now())
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Pengecekan'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $certificate = $record->dtsenCertificate;
                            if ($certificate) {
                                $certificate->update([
                                    'is_registered' => $data['is_registered'],
                                    'decile' => $data['decile'] ?? null,
                                    'checked_at' => $data['checked_at'],
                                    'checker_id' => Auth::id(),
                                ]);
                            }
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::DATA_VERIFICATION,
                                'Pengecekan SIKS-NG: Terdaftar='.($data['is_registered'] ? 'Ya' : 'Tidak').', Desil='.($data['decile'] ?? '-')
                            );
                            Notification::make()->success()->title('Hasil SIKS-NG berhasil disimpan')->send();
                        }),

                    // 3. Action: Buat Draf Surat & Ajukan Persetujuan (DTSEN)
                    Action::make('submitForApprovalDtsen')
                        ->label('Ajukan Draf Surat ke Pejabat')
                        ->icon(Heroicon::OutlinedPaperAirplane)
                        ->color('primary')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'dtsen' && $status === 'data_verification';
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Ajukan Draf Surat Keterangan DTSEN')
                        ->modalDescription('Draf surat akan diajukan ke Kepala Bidang dan Kepala Dinas untuk persetujuan.')
                        ->action(function (ServiceRequest $record): void {
                            $cert = $record->dtsenCertificate;
                            if (! $cert || ! $cert->is_registered || empty($cert->decile)) {
                                Notification::make()->danger()->title('Gagal: Data SIKS-NG belum lengkap atau pemohon tidak terdaftar')->send();

                                return;
                            }
                            $purpose = $cert->dtsenPurpose;
                            if ($purpose && $cert->decile > $purpose->max_decile) {
                                Notification::make()
                                    ->danger()
                                    ->title("Desil pemohon ({$cert->decile}) melebihi batas maksimal tujuan {$purpose->name} (Maks Desil {$purpose->max_decile})")
                                    ->body('Pengajuan ini harus ditolak sesuai aturan dinas.')
                                    ->send();

                                return;
                            }

                            // Generate verification code if not present
                            if (empty($cert->verification_code)) {
                                $cert->update([
                                    'verification_code' => 'SK-'.strtoupper(Str::random(10)),
                                ]);
                            }

                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::AWAITING_APPROVAL,
                                'Draf surat dibuat dan diajukan ke pejabat penandatangan'
                            );
                            Notification::make()->success()->title('Draf surat diajukan untuk persetujuan')->send();
                        }),

                    // 4. Action: Persetujuan Pejabat (Kabid / Kadis)
                    Action::make('approveDtsen')
                        ->label('Setujui & Terbitkan SK')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $status = $record->status?->value ?? (string) $record->status;

                            return $status === 'awaiting_approval';
                        })
                        ->schema([
                            Select::make('step')
                                ->label('Tahapan Persetujuan')
                                ->options([
                                    1 => 'Tahap 1: Paraf Kepala Bidang',
                                    2 => 'Tahap 2: Tanda Tangan Kepala Dinas (Penerbitan Surat)',
                                ])
                                ->default(2)
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Persetujuan')
                                ->default('Disetujui untuk diterbitkan'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            Approval::create([
                                'approvable_type' => $record->dtsenCertificate ? $record->dtsenCertificate->getMorphClass() : $record->getMorphClass(),
                                'approvable_id' => $record->dtsenCertificate?->id ?? $record->id,
                                'step' => (int) $data['step'],
                                'approver_id' => Auth::id(),
                                'decision' => 'approved',
                                'notes' => $data['notes'],
                                'decided_at' => now(),
                            ]);

                            if ((int) $data['step'] === 2) {
                                $cert = $record->dtsenCertificate;
                                $certNumber = '400.9/'.NumberSequence::generate('DTSEN').'/409.105/'.date('Y');
                                if ($cert) {
                                    $validUntil = $cert->dtsenPurpose?->validity_days ? now()->addDays($cert->dtsenPurpose->validity_days) : null;
                                    $cert->update([
                                        'certificate_number' => $certNumber,
                                        'issued_at' => now(),
                                        'valid_until' => $validUntil,
                                        'signer_id' => Auth::id(),
                                        'verification_code' => $cert->verification_code ?: 'SK-'.strtoupper(Str::random(10)),
                                    ]);
                                }
                                StatusTransitionService::transition(
                                    $record,
                                    ServiceRequestStatus::ISSUED,
                                    "SK DTSEN Diterbitkan dengan Nomor: {$certNumber}"
                                );
                                Notification::make()->success()->title("SK DTSEN Terbit: {$certNumber}")->send();
                            } else {
                                StatusTransitionService::transition(
                                    $record,
                                    ServiceRequestStatus::AWAITING_APPROVAL,
                                    'Paraf Kepala Bidang telah diberikan'
                                );
                                Notification::make()->info()->title('Paraf Kabid tersimpan')->send();
                            }
                        }),

                    // 5. Action: Verifikasi Kelayakan PBI
                    Action::make('verifyPbi')
                        ->label('Verifikasi Kelayakan PBI')
                        ->icon(Heroicon::OutlinedShieldCheck)
                        ->color('info')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'pbi' && in_array($status, ['eligibility_verification', 'document_check']);
                        })
                        ->schema([
                            Select::make('decile')
                                ->label('Hasil Desil')
                                ->options([1 => 'Desil 1', 2 => 'Desil 2', 3 => 'Desil 3', 4 => 'Desil 4', 5 => 'Desil 5'])
                                ->required(),
                            Textarea::make('eligibility_notes')
                                ->label('Catatan Kelayakan Reaktivasi')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $pbi = $record->pbiReactivation;
                            if ($pbi) {
                                $pbi->update([
                                    'decile' => $data['decile'],
                                    'eligibility_notes' => $data['eligibility_notes'],
                                ]);
                            }
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::AWAITING_APPROVAL,
                                "Kelayakan PBI Diverifikasi: Desil {$data['decile']}"
                            );
                            Notification::make()->success()->title('Verifikasi kelayakan PBI dicatat')->send();
                        }),

                    // 6. Action: Terbitkan Rekomendasi PBI
                    Action::make('issuePbiRecommendation')
                        ->label('Terbitkan Rekomendasi PBI')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'pbi' && $status === 'awaiting_approval';
                        })
                        ->action(function (ServiceRequest $record): void {
                            $recNumber = '440/'.NumberSequence::generate('REK-PBI').'/409.105/'.date('Y');
                            $record->pbiReactivation?->update([
                                'recommendation_number' => $recNumber,
                                'recommendation_issued_at' => now(),
                                'signer_id' => Auth::id(),
                            ]);
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::RECOMMENDATION_ISSUED,
                                "Surat Rekomendasi Diterbitkan: {$recNumber}"
                            );
                            Notification::make()->success()->title("Rekomendasi PBI Terbit: {$recNumber}")->send();
                        }),

                    // 7. Action: Usulkan ke Kemensos (PBI)
                    Action::make('proposeToMinistry')
                        ->label('Catat Pengusulan SIKS-NG Kemensos')
                        ->icon(Heroicon::OutlinedArrowUpTray)
                        ->color('purple')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'pbi' && $status === 'recommendation_issued';
                        })
                        ->schema([
                            DateTimePicker::make('proposed_to_ministry_at')
                                ->label('Tanggal Input Usulan ke SIKS-NG')
                                ->default(now())
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Pengusulan'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->pbiReactivation?->update([
                                'proposed_to_ministry_at' => $data['proposed_to_ministry_at'],
                            ]);
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::PROPOSED_TO_MINISTRY,
                                $data['notes'] ?? 'Usulan reaktivasi telah diinput ke SIKS-NG Kemensos'
                            );
                            Notification::make()->success()->title('Status pengusulan ke Kemensos berhasil dicatat')->send();
                        }),

                    // 8. Action: Update Hasil Kemensos (PBI)
                    Action::make('updateMinistryDecision')
                        ->label('Input Keputusan Kemensos')
                        ->icon(Heroicon::OutlinedScale)
                        ->color('info')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'pbi' && $status === 'proposed_to_ministry';
                        })
                        ->schema([
                            Select::make('decision')
                                ->label('Keputusan Kementerian Sosial')
                                ->options([
                                    'approved' => 'Disetujui Kemensos',
                                    'rejected' => 'Ditolak Kemensos',
                                ])
                                ->required(),
                            DateTimePicker::make('decided_at')
                                ->label('Tanggal Keputusan')
                                ->default(now())
                                ->required(),
                            Textarea::make('notes')
                                ->label('Keterangan'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $isApproved = $data['decision'] === 'approved';
                            $record->pbiReactivation?->update([
                                'ministry_decision' => $data['decision'],
                                'ministry_decided_at' => $data['decided_at'],
                            ]);
                            $nextStatus = $isApproved ? ServiceRequestStatus::MINISTRY_APPROVED : ServiceRequestStatus::MINISTRY_REJECTED;
                            StatusTransitionService::transition(
                                $record,
                                $nextStatus,
                                $data['notes'] ?? ('Keputusan Kemensos: '.($isApproved ? 'Disetujui' : 'Ditolak'))
                            );
                            Notification::make()->info()->title('Keputusan Kemensos berhasil diperbarui')->send();
                        }),

                    // 9. Action: Konfirmasi Reaktivasi Aktif Kembali (PBI)
                    Action::make('markReactivated')
                        ->label('Konfirmasi Aktif BPJS')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->visible(function (ServiceRequest $record): bool {
                            $handler = $record->serviceType?->handler?->value ?? $record->serviceType?->handler;
                            $status = $record->status?->value ?? (string) $record->status;

                            return $handler === 'pbi' && $status === 'ministry_approved';
                        })
                        ->schema([
                            DatePicker::make('reactivated_date')
                                ->label('Tanggal Aktif Kembali di BPJS Kesehatan')
                                ->default(now())
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan Konfirmasi'),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            $record->pbiReactivation?->update([
                                'reactivated_date' => $data['reactivated_date'],
                            ]);
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::REACTIVATED,
                                "Kepesertaan aktif kembali per tanggal {$data['reactivated_date']}"
                            );
                            Notification::make()->success()->title('Kepesertaan PBI berhasil diaktifkan kembali')->send();
                        }),

                    // 10. Action: Selesaikan Pengajuan
                    Action::make('completeRequest')
                        ->label('Selesaikan Tiket Layanan')
                        ->icon(Heroicon::OutlinedArchiveBoxArrowDown)
                        ->color('success')
                        ->visible(fn (ServiceRequest $record): bool => in_array($record->status?->value ?? (string) $record->status, ['issued', 'reactivated', 'in_process']))
                        ->schema([
                            Textarea::make('service_result')
                                ->label('Hasil / Catatan Penyelesaian Layanan')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::COMPLETED,
                                $data['service_result'],
                                extraAttributes: [
                                    'service_result' => $data['service_result'],
                                    'completed_at' => now(),
                                ]
                            );
                            Notification::make()->success()->title('Tiket pengajuan telah selesai')->send();
                        }),

                    // 11. Action: Tolak Pengajuan
                    Action::make('rejectRequest')
                        ->label('Tolak Pengajuan')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->color('danger')
                        ->visible(fn (ServiceRequest $record): bool => ! in_array($record->status?->value ?? (string) $record->status, ['completed', 'rejected', 'ministry_rejected']))
                        ->schema([
                            Textarea::make('rejection_reason')
                                ->label('Alasan Penolakan')
                                ->placeholder('Jelaskan alasan penolakan dan informasi tindak lanjut bagi pemohon')
                                ->required(),
                        ])
                        ->action(function (ServiceRequest $record, array $data): void {
                            StatusTransitionService::transition(
                                $record,
                                ServiceRequestStatus::REJECTED,
                                $data['rejection_reason'],
                                extraAttributes: ['rejection_reason' => $data['rejection_reason']]
                            );
                            Notification::make()->danger()->title('Pengajuan telah ditolak')->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
