<?php

namespace App\Livewire;

use App\Enums\ComplaintAttachmentType;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\District;
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
#[Title('Form Pengaduan & Laporan Sosial — SAPA SOSIAL')]
class ComplaintSubmission extends Component
{
    use WithFileUploads;

    public ?int $complaint_category_id = null;

    public string $reporter_name = '';

    public string $reporter_phone = '';

    public bool $is_anonymous = false;

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $location_detail = '';

    public string $description = '';

    public $attachment_file;

    public bool $agree_terms = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->reporter_name = $user->name ?? '';
            $this->reporter_phone = $user->phone ?? '';
            $this->district_id = $user->district_id;
            $this->village_id = $user->village_id;
        }

        $firstCategory = ComplaintCategory::where('is_active', true)->first();
        if ($firstCategory) {
            $this->complaint_category_id = $firstCategory->id;
        }
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    public function submit(): void
    {
        $this->validate([
            'complaint_category_id' => 'required|exists:complaint_categories,id',
            'reporter_name' => 'required|string|min:3|max:100',
            'reporter_phone' => 'required|string|min:9|max:20',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'location_detail' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:20|max:2000',
            'attachment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'agree_terms' => 'accepted',
        ], [
            'complaint_category_id.required' => 'Pilih kategori permasalahan sosial.',
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'reporter_phone.required' => 'Nomor kontak/WhatsApp wajib diisi agar petugas dapat mengonfirmasi.',
            'district_id.required' => 'Pilih kecamatan lokasi kejadian.',
            'village_id.required' => 'Pilih desa/kelurahan lokasi kejadian.',
            'location_detail.required' => 'Sebutkan alamat atau patokan lokasi dengan jelas.',
            'description.required' => 'Jelaskan kronologi permasalahan sosial.',
            'description.min' => 'Penjelasan minimal 20 karakter agar petugas memahami kronologi.',
            'agree_terms.accepted' => 'Anda harus menyetujui pernyataan keabsahan laporan.',
        ]);

        $complaint = DB::transaction(function () {
            $record = Complaint::create([
                'complaint_category_id' => $this->complaint_category_id,
                'reporter_id' => Auth::id(),
                'reporter_name' => $this->reporter_name,
                'reporter_phone' => $this->reporter_phone,
                'village_id' => $this->village_id,
                'location_detail' => $this->location_detail,
                'description' => $this->description,
                'status' => ComplaintStatus::RECEIVED,
                'reported_at' => now(),
            ]);

            // Save attachment if provided
            if ($this->attachment_file) {
                $ext = strtolower($this->attachment_file->getClientOriginalExtension());
                $type = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])
                    ? ComplaintAttachmentType::PHOTO
                    : ComplaintAttachmentType::DOCUMENT;

                $path = $this->attachment_file->store('complaint-attachments/'.$record->id, 'public');
                ComplaintAttachment::create([
                    'complaint_id' => $record->id,
                    'file_path' => $path,
                    'type' => $type,
                ]);
            }

            // Status history
            StatusHistory::create([
                'statusable_type' => Complaint::class,
                'statusable_id' => $record->id,
                'from_status' => null,
                'to_status' => ComplaintStatus::RECEIVED->value,
                'notes' => 'Laporan pengaduan sosial berhasil didaftarkan oleh warga.',
                'user_id' => Auth::id(),
            ]);

            return $record;
        });

        session()->flash('success', 'Laporan pengaduan berhasil disampaikan!');
        $this->redirect(route('sukses-tiket', $complaint->complaint_number), navigate: true);
    }

    public function render(): View
    {
        $categories = ComplaintCategory::where('is_active', true)->get();
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.complaint-submission', [
            'categories' => $categories,
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
