<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\PbiReactivationReason;
use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Layanan & Pemohon')
                    ->description('Data pemohon dan jenis layanan sosial yang diajukan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('request_number')
                                ->label('Nomor Tiket')
                                ->placeholder('Dibuat otomatis')
                                ->disabled()
                                ->dehydrated(false),
                            Select::make('service_type_id')
                                ->label('Jenis Layanan')
                                ->relationship('serviceType', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required(),
                            Toggle::make('is_priority')
                                ->label('Prioritas / Darurat Medis')
                                ->inline(false),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('applicant_name')
                                ->label('Nama Lengkap Pemohon')
                                ->placeholder('Sesuai KTP')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('applicant_nik')
                                ->label('NIK Pemohon (16 Digit)')
                                ->required()
                                ->length(16)
                                ->numeric(),
                            TextInput::make('family_card_number')
                                ->label('Nomor Kartu Keluarga (KK)')
                                ->required()
                                ->length(16)
                                ->numeric(),
                            TextInput::make('phone')
                                ->label('Nomor Telepon / WhatsApp')
                                ->tel()
                                ->required()
                                ->maxLength(20),
                            Select::make('village_id')
                                ->label('Desa / Kelurahan Domisili')
                                ->relationship('village', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Textarea::make('address')
                                ->label('Alamat Lengkap (RT/RW, Dusun)')
                                ->required()
                                ->rows(2),
                        ]),
                    ]),

                Section::make('Detail Khusus — Surat Keterangan DTSEN')
                    ->description('Data orang yang diterangkan dan hasil verifikasi desil SIKS-NG')
                    ->relationship('dtsenCertificate')
                    ->visible(function (Get $get, ?ServiceRequest $record): bool {
                        $serviceTypeId = $get('service_type_id') ?? $record?->service_type_id;
                        if (! $serviceTypeId) {
                            return false;
                        }
                        $serviceType = ServiceType::find($serviceTypeId);

                        return $serviceType?->handler?->value === 'dtsen' || $serviceType?->handler === 'dtsen';
                    })
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('dtsen_purpose_id')
                                ->label('Tujuan Penggunaan Surat')
                                ->relationship('dtsenPurpose', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('purpose_description')
                                ->label('Keterangan Keperluan')
                                ->placeholder('Misal: Pendaftaran SPMB SMAN 1 Garum jalur afirmasi'),
                            TextInput::make('subject_name')
                                ->label('Nama Orang yang Diterangkan')
                                ->placeholder('Nama siswa/keluarga')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('subject_nik')
                                ->label('NIK Orang yang Diterangkan')
                                ->required()
                                ->length(16)
                                ->numeric(),
                            Select::make('relationship_to_applicant')
                                ->label('Hubungan dengan Pemohon')
                                ->options([
                                    'Diri Sendiri' => 'Diri Sendiri',
                                    'Anak Kandung' => 'Anak Kandung',
                                    'Orang Tua' => 'Orang Tua',
                                    'Suami / Istri' => 'Suami / Istri',
                                    'Famili Lain' => 'Famili Lain',
                                ])
                                ->required(),
                            Toggle::make('is_registered')
                                ->label('Terdaftar di SIKS-NG / DTSEN')
                                ->default(false)
                                ->inline(false),
                            Select::make('decile')
                                ->label('Peringkat Desil (Hasil SIKS-NG)')
                                ->options([
                                    1 => 'Desil 1 (Sangat Miskin)',
                                    2 => 'Desil 2 (Miskin)',
                                    3 => 'Desil 3 (Hampir Miskin)',
                                    4 => 'Desil 4 (Rentan Miskin)',
                                    5 => 'Desil 5 (Menengah Bawah)',
                                    6 => 'Desil 6',
                                    7 => 'Desil 7',
                                    8 => 'Desil 8',
                                    9 => 'Desil 9',
                                    10 => 'Desil 10',
                                ]),
                            DateTimePicker::make('checked_at')
                                ->label('Tanggal Pengecekan SIKS-NG'),
                            TextInput::make('certificate_number')
                                ->label('Nomor Surat Keterangan')
                                ->disabled()
                                ->placeholder('Diterbitkan saat disetujui'),
                            DateTimePicker::make('issued_at')
                                ->label('Tanggal Terbit Surat')
                                ->disabled(),
                            TextInput::make('verification_code')
                                ->label('Kode Verifikasi QR')
                                ->disabled(),
                        ]),
                    ]),

                Section::make('Detail Khusus — Reaktivasi KIS / PBI-JK')
                    ->description('Data peserta dan progres pengusulan reaktivasi ke Kementerian Sosial')
                    ->relationship('pbiReactivation')
                    ->visible(function (Get $get, ?ServiceRequest $record): bool {
                        $serviceTypeId = $get('service_type_id') ?? $record?->service_type_id;
                        if (! $serviceTypeId) {
                            return false;
                        }
                        $serviceType = ServiceType::find($serviceTypeId);

                        return $serviceType?->handler?->value === 'pbi' || $serviceType?->handler === 'pbi';
                    })
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('participant_name')
                                ->label('Nama Peserta BPJS')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('participant_nik')
                                ->label('NIK Peserta')
                                ->required()
                                ->length(16)
                                ->numeric(),
                            TextInput::make('bpjs_card_number')
                                ->label('Nomor Kartu BPJS / KIS (13 Digit)')
                                ->required()
                                ->maxLength(20),
                            TextInput::make('deactivated_date')
                                ->label('Perkiraan Tanggal Nonaktif')
                                ->type('date'),
                            Select::make('reason')
                                ->label('Alasan Permohonan Reaktivasi')
                                ->options([
                                    PbiReactivationReason::CHRONIC->value => 'Penyakit Kronis',
                                    PbiReactivationReason::CATASTROPHIC->value => 'Penyakit Katastropik',
                                    PbiReactivationReason::EMERGENCY->value => 'Kondisi Darurat Medis (Gawat Darurat)',
                                    PbiReactivationReason::NEWBORN->value => 'Bayi Baru Lahir dari Ibu PBI',
                                    PbiReactivationReason::OTHER->value => 'Lainnya',
                                ])
                                ->required(),
                            TextInput::make('health_facility_name')
                                ->label('Nama Faskes Perujuk (RS / Puskesmas)'),
                            TextInput::make('health_letter_number')
                                ->label('Nomor Surat Keterangan Rawat/Medis'),
                            Select::make('decile')
                                ->label('Desil Kelayakan')
                                ->options([
                                    1 => 'Desil 1',
                                    2 => 'Desil 2',
                                    3 => 'Desil 3',
                                    4 => 'Desil 4',
                                    5 => 'Desil 5',
                                ]),
                            Textarea::make('eligibility_notes')
                                ->label('Catatan Verifikasi Kelayakan')
                                ->columnSpanFull(),
                            TextInput::make('recommendation_number')
                                ->label('Nomor Surat Rekomendasi')
                                ->disabled(),
                            DateTimePicker::make('proposed_to_ministry_at')
                                ->label('Tanggal Diusulkan ke SIKS-NG Kemensos'),
                            Select::make('ministry_decision')
                                ->label('Keputusan Kemensos')
                                ->options([
                                    'pending' => 'Menunggu Keputusan',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                ]),
                            DateTimePicker::make('ministry_decided_at')
                                ->label('Tanggal Keputusan Kemensos'),
                            TextInput::make('reactivated_date')
                                ->label('Tanggal Kepesertaan Aktif Kembali di BPJS')
                                ->type('date'),
                        ]),
                    ]),

                Section::make('Status & Penanganan Petugas')
                    ->description('Tahapan pemrosesan, disposisi, dan hasil verifikasi dinas')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label('Status Pengajuan')
                                ->options(collect(ServiceRequestStatus::cases())->mapWithKeys(fn ($status) => [
                                    $status->value => $status->label(),
                                ]))
                                ->required(),
                            Select::make('officer_id')
                                ->label('Petugas Penanggung Jawab')
                                ->relationship('officer', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('work_unit_id')
                                ->label('Unit Kerja / Bidang')
                                ->relationship('workUnit', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('verification_result')
                                ->label('Hasil Verifikasi Berkas & Data')
                                ->rows(2),
                            Textarea::make('officer_notes')
                                ->label('Catatan Petugas')
                                ->rows(2),
                            Textarea::make('assessment_notes')
                                ->label('Catatan Assessment (Jika diperlukan)')
                                ->rows(2),
                            Textarea::make('service_result')
                                ->label('Hasil / Keputusan Akhir Layanan')
                                ->rows(2),
                            Textarea::make('rejection_reason')
                                ->label('Alasan Penolakan (Bila Ditolak)')
                                ->columnSpanFull()
                                ->rows(2),
                        ]),
                    ]),
            ]);
    }
}
