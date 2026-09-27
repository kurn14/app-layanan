<?php

namespace App\Livewire;

use App\Enums\PbiReactivationReason;
use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\PbiReactivation;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Form Pengajuan Reaktivasi KIS / PBI-JK — SAPA SOSIAL')]
class PbiApplication extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1: Data Pemohon
    public string $applicant_name = '';

    public string $applicant_nik = '';

    public string $family_card_number = '';

    public string $address = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $phone = '';

    // Step 2: Data Peserta BPJS
    public bool $is_same_as_applicant = true;

    public string $participant_name = '';

    public string $participant_nik = '';

    public string $bpjs_card_number = '';

    public ?string $deactivated_date = null;

    public string $reason = 'chronic';

    public string $health_facility_name = '';

    public string $health_letter_number = '';

    // Step 3: Berkas Dokumen
    public $ktp_file;

    public $kk_file;

    public $bpjs_file;

    public $faskes_file;

    // Step 4: Pernyataan
    public bool $agree_terms = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->applicant_name = $user->name ?? '';
            $this->applicant_nik = $user->nik ?? '';
            $this->phone = $user->phone ?? '';
            $this->district_id = $user->district_id;
            $this->village_id = $user->village_id;
        }

        $this->syncParticipantData();
    }

    public function updatedIsSameAsApplicant(): void
    {
        $this->syncParticipantData();
    }

    public function updatedApplicantName(): void
    {
        if ($this->is_same_as_applicant) {
            $this->participant_name = $this->applicant_name;
        }
    }

    public function updatedApplicantNik(): void
    {
        if ($this->is_same_as_applicant) {
            $this->participant_nik = $this->applicant_nik;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    private function syncParticipantData(): void
    {
        if ($this->is_same_as_applicant) {
            $this->participant_name = $this->applicant_name;
            $this->participant_nik = $this->applicant_nik;
        }
    }

    public function goToStep(int $targetStep): void
    {
        if ($targetStep < $this->step) {
            $this->step = $targetStep;

            return;
        }

        $this->validateCurrentStep();
        $this->step = $targetStep;
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    private function validateCurrentStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'applicant_name' => 'required|string|min:3|max:100',
                'applicant_nik' => 'required|digits:16',
                'family_card_number' => 'required|digits:16',
                'address' => 'required|string|min:5|max:255',
                'district_id' => 'required|exists:districts,id',
                'village_id' => 'required|exists:villages,id',
                'phone' => 'required|string|min:9|max:20',
            ], [
                'applicant_name.required' => 'Nama lengkap pemohon wajib diisi.',
                'applicant_nik.digits' => 'NIK pemohon harus tepat 16 digit.',
                'family_card_number.digits' => 'Nomor KK harus tepat 16 digit.',
                'district_id.required' => 'Pilih kecamatan tempat tinggal Anda.',
                'village_id.required' => 'Pilih desa tempat tinggal Anda.',
                'phone.required' => 'Nomor telepon/WhatsApp aktif wajib diisi.',
            ]);
        } elseif ($this->step === 2) {
            $rules = [
                'participant_name' => 'required|string|min:3|max:100',
                'participant_nik' => 'required|digits:16',
                'bpjs_card_number' => 'required|string|min:11|max:16',
                'reason' => 'required|string',
            ];

            if (in_array($this->reason, ['chronic', 'emergency'])) {
                $rules['health_facility_name'] = 'required|string|max:150';
            }

            $this->validate($rules, [
                'participant_name.required' => 'Nama peserta BPJS wajib diisi.',
                'participant_nik.digits' => 'NIK peserta BPJS harus tepat 16 digit.',
                'bpjs_card_number.required' => 'Nomor kartu BPJS/KIS wajib diisi.',
                'health_facility_name.required' => 'Nama faskes wajib diisi untuk alasan darurat medis/kronis.',
            ]);
        } elseif ($this->step === 3) {
            $rules = [
                'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'kk_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'bpjs_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ];

            if (in_array($this->reason, ['chronic', 'emergency'])) {
                $rules['faskes_file'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            }

            $this->validate($rules, [
                'ktp_file.required' => 'File KTP wajib diunggah.',
                'kk_file.required' => 'File Kartu Keluarga wajib diunggah.',
                'bpjs_file.required' => 'File kartu BPJS/KIS wajib diunggah.',
                'faskes_file.required' => 'Surat keterangan faskes wajib diunggah untuk alasan medis.',
            ]);
        }
    }

    public function submit(): void
    {
        $this->validateCurrentStep();

        $this->validate([
            'agree_terms' => 'accepted',
        ], [
            'agree_terms.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ]);

        $serviceRequest = DB::transaction(function () {
            $serviceType = ServiceType::where('code', 'PBI')->firstOrFail();

            $isEmergency = ($this->reason === 'emergency');

            $request = ServiceRequest::create([
                'service_type_id' => $serviceType->id,
                'submitter_id' => Auth::id(),
                'applicant_name' => $this->applicant_name,
                'applicant_nik' => $this->applicant_nik,
                'family_card_number' => $this->family_card_number,
                'address' => $this->address,
                'village_id' => $this->village_id,
                'phone' => $this->phone,
                'is_priority' => $isEmergency,
                'status' => ServiceRequestStatus::SUBMITTED,
                'submitted_at' => now(),
            ]);

            // Save KTP Document
            $ktpPath = $this->ktp_file->store('service_documents/'.$request->id, 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'file_path' => $ktpPath,
                'original_name' => $this->ktp_file->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);

            // Save KK Document
            $kkPath = $this->kk_file->store('service_documents/'.$request->id, 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'file_path' => $kkPath,
                'original_name' => $this->kk_file->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);

            // Save BPJS Document
            $bpjsPath = $this->bpjs_file->store('service_documents/'.$request->id, 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'file_path' => $bpjsPath,
                'original_name' => $this->bpjs_file->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);

            // Save Faskes Document if exists
            if ($this->faskes_file) {
                $faskesPath = $this->faskes_file->store('service_documents/'.$request->id, 'local');
                ServiceRequestDocument::create([
                    'service_request_id' => $request->id,
                    'file_path' => $faskesPath,
                    'original_name' => $this->faskes_file->getClientOriginalName(),
                    'verification_status' => 'pending',
                ]);
            }

            // Create PBI Reactivation record
            PbiReactivation::create([
                'service_request_id' => $request->id,
                'participant_name' => $this->participant_name,
                'participant_nik' => $this->participant_nik,
                'bpjs_card_number' => $this->bpjs_card_number,
                'deactivated_date' => $this->deactivated_date,
                'reason' => PbiReactivationReason::tryFrom($this->reason) ?? PbiReactivationReason::CHRONIC,
                'health_facility_name' => $this->health_facility_name,
                'health_letter_number' => $this->health_letter_number,
            ]);

            // Status history
            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $request->id,
                'from_status' => null,
                'to_status' => ServiceRequestStatus::SUBMITTED->value,
                'notes' => 'Pengajuan reaktivasi KIS/PBI-JK berhasil dikirim oleh pemohon secara online.'.($isEmergency ? ' (Prioritas Darurat Medis)' : ''),
                'user_id' => Auth::id(),
            ]);

            return $request;
        });

        session()->flash('success', 'Pengajuan reaktivasi KIS/PBI-JK berhasil dikirimkan!');
        $this->redirect(route('sukses-tiket', $serviceRequest->request_number), navigate: true);
    }

    public function render(): View
    {
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.pbi-application', [
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
