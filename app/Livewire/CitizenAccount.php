<?php

namespace App\Livewire;

use App\Models\Complaint;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Akun Saya — SAPA SOSIAL')]
class CitizenAccount extends Component
{
    public string $activeTab = 'pengajuan'; // 'pengajuan' or 'pengaduan'

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('masuk'), navigate: true);
        }
    }

    public function render(): View
    {
        $user = Auth::user();

        $serviceRequests = ServiceRequest::where(function ($q) use ($user) {
            $q->where('submitter_id', $user->id);
            if (! empty($user->nik)) {
                $q->orWhere('applicant_nik', $user->nik);
            }
        })
            ->with(['serviceType', 'village.district'])
            ->orderBy('created_at', 'desc')
            ->get();

        $complaints = Complaint::where(function ($q) use ($user) {
            $q->where('reporter_id', $user->id);
            if (! empty($user->phone)) {
                $q->orWhere('reporter_phone', $user->phone);
            }
        })
            ->with(['complaintCategory', 'village.district'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRequests = $serviceRequests->count();
        $inProcessCount = $serviceRequests->whereIn('status', ['submitted', 'document_check', 'data_verification', 'awaiting_approval'])->count();
        $completedCount = $serviceRequests->whereIn('status', ['issued', 'completed', 'reactivated'])->count();
        $totalComplaints = $complaints->count();

        return view('livewire.citizen-account', [
            'user' => $user,
            'serviceRequests' => $serviceRequests,
            'complaints' => $complaints,
            'totalRequests' => $totalRequests,
            'inProcessCount' => $inProcessCount,
            'completedCount' => $completedCount,
            'totalComplaints' => $totalComplaints,
        ]);
    }
}
