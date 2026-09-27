<?php

namespace App\Livewire;

use App\Models\Complaint;
use App\Models\ServiceRequest;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengajuan Berhasil — SAPA SOSIAL')]
class TicketSuccess extends Component
{
    public string $ticketNumber = '';

    public function mount(string $ticketNumber): void
    {
        $this->ticketNumber = trim($ticketNumber);
    }

    public function render(): View
    {
        $serviceRequest = ServiceRequest::where('request_number', $this->ticketNumber)->first();
        $complaint = null;

        if (! $serviceRequest) {
            $complaint = Complaint::where('complaint_number', $this->ticketNumber)->first();
        }

        $isComplaint = ($complaint !== null);
        $record = $serviceRequest ?? $complaint;

        return view('livewire.ticket-success', [
            'record' => $record,
            'isComplaint' => $isComplaint,
        ]);
    }
}
