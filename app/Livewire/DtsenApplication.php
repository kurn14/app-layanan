<?php

namespace App\Livewire;

use App\Enums\ServiceRequestStatus;
use App\Models\District;
use App\Models\DtsenCertificate;
use App\Models\DtsenPurpose;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestDocument;
use App\Models\ServiceType;
use App\Models\StatusHistory;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Form Pengajuan Surat Keterangan DTSEN — SAPA SOSIAL')]
class DtsenApplication extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1: Tujuan Penggunaan
    public ?int $dtsen_purpose_id = null;

    public string $purpose_description = '';

    // Step 2: Data Pemohon & Subjek
    public string $applicant_name = '';

    public string $applicant_nik = '';

    public string $family_card_number = '';

    public string $address = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $phone = '';

    public bool $is_same_as_applicant = true;

    public string $subject_name = '';

    public string $subject_nik = '';

    public string $relationship_to_applicant = 'Diri Sendiri';

    // Step 3: Berkas Dokumen
    public $ktp_file;

    public $kk_file;

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

        $firstPurpose = DtsenPurpose::where('is_active', true)->first();
        if ($firstPurpose) {
            $this->dtsen_purpose_id = $firstPurpose->id;
        }

        $this->syncSubjectData();
    }

    public function updatedIsSameAsApplicant(): void
    {
        $this->syncSubjectData();
    }

    public function updatedApplicantName(): void
    {
        if ($this->is_same_as_applicant) {
            $this->subject_name = $this->applicant_name;
        }
    }

    public function updatedApplicantNik(): void
    {
        if ($this->is_same_as_applicant) {
            $this->subject_nik = $this->applicant_nik;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    private function syncSubjectData(): void
    {
        if ($this->is_same_as_applicant) {
            $this->subject_name = $this->applicant_name;
            $this->subject_nik = $this->applicant_nik;
            $this->relationship_to_applicant = 'Diri Sendiri';
        } else {
            if ($this->relationship_to_applicant === 'Diri Sendiri') {
                $this->relationship_to_applicant = 'Anak';
            }
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
                'dtsen_purpose_id' => 'required|exists:dtsen_purposes,id',
                'purpose_description' => 'nullable|string|max:500',
            ], [
                'dtsen_purpose_id.required' => 'Pilih salah satu tujuan penggunaan surat.',
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'applicant_name' => 'required|string|min:3|max:100',
                'applicant_nik' => 'required|digits:16',
                'family_card_number' => 'required|digits:16',
                'address' => 'required|string|min:5|max:255',
                'district_id' => 'required|exists:districts,id',
                'village_id' => 'required|exists:villages,id',
                'phone' => 'required|string|min:9|max:20',
                'subject_name' => 'required|string|min:3|max:100',
                'subject_nik' => 'required|digits:16',
                'relationship_to_applicant' => 'required|string|max:50',
            ], [
                'applicant_name.required' => 'Nama lengkap pemohon wajib diisi.',
                'applicant_nik.digits' => 'NIK pemohon harus tepat 16 digit angka.',
                'family_card_number.digits' => 'Nomor KK harus tepat 16 digit angka.',
                'district_id.required' => 'Pilih kecamatan tempat tinggal Anda.',
                'village_id.required' => 'Pilih desa/kelurahan tempat tinggal Anda.',
                'phone.required' => 'Nomor telepon/WhatsApp wajib diisi untuk notifikasi.',
                'subject_name.required' => 'Nama orang yang diterangkan wajib diisi.',
                'subject_nik.digits' => 'NIK yang diterangkan harus tepat 16 digit angka.',
            ]);
        } elseif ($this->step === 3) {
            $this->validate([
                'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'kk_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ], [
                'ktp_file.required' => 'File KTP pemohon wajib diunggah.',
                'ktp_file.mimes' => 'Format file KTP harus berupa JPG, PNG, atau PDF.',
                'ktp_file.max' => 'Ukuran file KTP maksimal 2 MB.',
                'kk_file.required' => 'File Kartu Keluarga wajib diunggah.',
                'kk_file.mimes' => 'Format file KK harus berupa JPG, PNG, atau PDF.',
                'kk_file.max' => 'Ukuran file KK maksimal 2 MB.',
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
            $serviceType = ServiceType::where('code', 'DTSEN')->firstOrFail();

            $request = ServiceRequest::create([
                'service_type_id' => $serviceType->id,
                'submitter_id' => Auth::id(),
                'applicant_name' => $this->applicant_name,
                'applicant_nik' => $this->applicant_nik,
                'family_card_number' => $this->family_card_number,
                'address' => $this->address,
                'village_id' => $this->village_id,
                'phone' => $this->phone,
                'status' => ServiceRequestStatus::SUBMITTED,
                'submitted_at' => now(),
            ]);

            // Fetch service requirements
            $requirements = $serviceType->requirements;
            $ktpRequirement = $requirements->first(fn ($r) => str_contains(strtolower($r->name), 'ktp'))
                ?? $requirements->firstWhere('sort_order', 1)
                ?? $requirements->first();
            $kkRequirement = $requirements->first(fn ($r) => str_contains(strtolower($r->name), 'kk') || str_contains(strtolower($r->name), 'kartu keluarga'))
                ?? $requirements->firstWhere('sort_order', 2)
                ?? $requirements->skip(1)->first();

            // Save KTP Document
            $ktpPath = $this->ktp_file->store('service_documents/'.$request->id, 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'service_requirement_id' => $ktpRequirement?->id,
                'file_path' => $ktpPath,
                'original_name' => $this->ktp_file->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);

            // Save KK Document
            $kkPath = $this->kk_file->store('service_documents/'.$request->id, 'local');
            ServiceRequestDocument::create([
                'service_request_id' => $request->id,
                'service_requirement_id' => $kkRequirement?->id,
                'file_path' => $kkPath,
                'original_name' => $this->kk_file->getClientOriginalName(),
                'verification_status' => 'pending',
            ]);

            // Create DTSEN Certificate record
            DtsenCertificate::create([
                'service_request_id' => $request->id,
                'dtsen_purpose_id' => $this->dtsen_purpose_id,
                'purpose_description' => $this->purpose_description,
                'subject_name' => $this->subject_name,
                'subject_nik' => $this->subject_nik,
                'relationship_to_applicant' => $this->relationship_to_applicant,
                'is_registered' => false,
                'verification_code' => 'DTSEN-'.strtoupper(Str::random(10)),
            ]);

            // Status history
            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $request->id,
                'from_status' => null,
                'to_status' => ServiceRequestStatus::SUBMITTED->value,
                'notes' => 'Permohonan surat keterangan DTSEN berhasil diajukan oleh pemohon secara online.',
                'user_id' => Auth::id(),
            ]);

            return $request;
        });

        session()->flash('success', 'Pengajuan berhasil dikirimkan!');
        $this->redirect(route('sukses-tiket', $serviceRequest->request_number), navigate: true);
    }

    public function render(): View
    {
        $purposes = DtsenPurpose::where('is_active', true)->get();
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        $selectedPurpose = DtsenPurpose::find($this->dtsen_purpose_id);

        return view('livewire.dtsen-application', [
            'purposes' => $purposes,
            'districts' => $districts,
            'villages' => $villages,
            'selectedPurpose' => $selectedPurpose,
        ]);
    }
}
