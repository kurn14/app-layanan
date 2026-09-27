<?php

namespace App\Livewire;

use App\Enums\ServiceRequestStatus;
use App\Models\District;
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
#[Title('Pengajuan Layanan Sosial — SAPA SOSIAL')]
class GeneralApplication extends Component
{
    use WithFileUploads;

    public string $slug = '';

    public ?int $service_type_id = null;

    // Applicant data
    public string $applicant_name = '';

    public string $applicant_nik = '';

    public string $family_card_number = '';

    public string $address = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $phone = '';

    public string $notes = '';

    // Dynamic uploads array
    public array $documents = [];

    public bool $agree_terms = false;

    public function mount(string $slug): void
    {
        $this->slug = strtolower($slug);
        $service = ServiceType::whereRaw('LOWER(code) = ?', [$this->slug])->first();
        if ($service) {
            $this->service_type_id = $service->id;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->applicant_name = $user->name ?? '';
            $this->applicant_nik = $user->nik ?? '';
            $this->phone = $user->phone ?? '';
            $this->district_id = $user->district_id;
            $this->village_id = $user->village_id;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    public function submit(): void
    {
        $this->validate([
            'applicant_name' => 'required|string|min:3|max:100',
            'applicant_nik' => 'required|digits:16',
            'family_card_number' => 'required|digits:16',
            'address' => 'required|string|min:5|max:255',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'phone' => 'required|string|min:9|max:20',
            'agree_terms' => 'accepted',
        ], [
            'applicant_name.required' => 'Nama pemohon wajib diisi.',
            'applicant_nik.digits' => 'NIK harus 16 digit angka.',
            'family_card_number.digits' => 'Nomor KK harus 16 digit angka.',
            'district_id.required' => 'Pilih kecamatan tempat tinggal Anda.',
            'village_id.required' => 'Pilih desa/kelurahan.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'agree_terms.accepted' => 'Anda harus menyetujui pernyataan kebenaran data.',
        ]);

        $serviceRequest = DB::transaction(function () {
            $service = ServiceType::findOrFail($this->service_type_id);

            $request = ServiceRequest::create([
                'service_type_id' => $service->id,
                'submitter_id' => Auth::id(),
                'applicant_name' => $this->applicant_name,
                'applicant_nik' => $this->applicant_nik,
                'family_card_number' => $this->family_card_number,
                'address' => $this->address,
                'village_id' => $this->village_id,
                'phone' => $this->phone,
                'officer_notes' => ! empty($this->notes) ? 'Catatan pemohon: '.$this->notes : null,
                'status' => ServiceRequestStatus::SUBMITTED,
                'submitted_at' => now(),
            ]);

            // Save uploaded documents
            foreach ($this->documents as $reqId => $file) {
                if ($file) {
                    $path = $file->store('service_documents/'.$request->id, 'local');
                    ServiceRequestDocument::create([
                        'service_request_id' => $request->id,
                        'service_requirement_id' => $reqId,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'verification_status' => 'pending',
                    ]);
                }
            }

            StatusHistory::create([
                'statusable_type' => ServiceRequest::class,
                'statusable_id' => $request->id,
                'from_status' => null,
                'to_status' => ServiceRequestStatus::SUBMITTED->value,
                'notes' => 'Pengajuan layanan baru dibuat oleh pemohon secara online.',
                'user_id' => Auth::id(),
            ]);

            return $request;
        });

        session()->flash('success', 'Pengajuan layanan berhasil dikirimkan!');
        $this->redirect(route('sukses-tiket', $serviceRequest->request_number), navigate: true);
    }

    public function render(): View
    {
        $service = ServiceType::find($this->service_type_id);
        $requirements = $service ? $service->requirements()->orderBy('sort_order')->get() : collect();
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.general-application', [
            'service' => $service,
            'requirements' => $requirements,
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
