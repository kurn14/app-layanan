<?php

namespace App\Livewire;

use App\Models\Complaint;
use App\Models\ServiceRequest;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Cek Status & Lacak Tiket — SAPA SOSIAL')]
class TicketTracking extends Component
{
    #[Url]
    public string $ticket = '';

    #[Url]
    public string $pin = '';

    public bool $hasSearched = false;

    public ?string $searchError = null;

    public ?ServiceRequest $serviceRequest = null;

    public ?Complaint $complaint = null;

    public function mount(): void
    {
        if (! empty($this->ticket)) {
            $this->performSearch();
        }
    }

    public function track(): void
    {
        $this->validate([
            'ticket' => 'required|string',
            'pin' => 'required|digits:4',
        ], [
            'ticket.required' => 'Nomor tiket wajib diisi.',
            'pin.required' => '4 digit NIK/HP wajib diisi untuk verifikasi keamanan.',
            'pin.digits' => 'PIN verifikasi harus berupa 4 digit angka.',
        ]);

        $this->performSearch();
    }

    private function performSearch(): void
    {
        $this->hasSearched = true;
        $this->searchError = null;
        $this->serviceRequest = null;
        $this->complaint = null;

        $ticketClean = trim($this->ticket);
        $pinClean = trim($this->pin);

        // 1. Try finding in ServiceRequest
        $sr = ServiceRequest::where('request_number', $ticketClean)
            ->with(['serviceType', 'village.district', 'dtsenCertificate', 'pbiReactivation', 'statusHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->first();

        if ($sr) {
            // Check pin if provided
            if (! empty($pinClean)) {
                $last4Nik = substr($sr->applicant_nik, -4);
                $last4Phone = substr(preg_replace('/[^0-9]/', '', $sr->phone), -4);

                if ($pinClean !== $last4Nik && $pinClean !== $last4Phone) {
                    $this->searchError = 'Nomor tiket ditemukan, tetapi 4 digit NIK/No. HP tidak sesuai demi keamanan privasi pemohon.';

                    return;
                }
            }

            $this->serviceRequest = $sr;

            return;
        }

        // 2. Try finding in Complaint
        $cp = Complaint::where('complaint_number', $ticketClean)
            ->with(['complaintCategory', 'village.district', 'attachments', 'statusHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->first();

        if ($cp) {
            if (! empty($pinClean)) {
                $last4Phone = substr(preg_replace('/[^0-9]/', '', $cp->reporter_phone), -4);
                if ($pinClean !== $last4Phone) {
                    $this->searchError = 'Nomor tiket pengaduan ditemukan, namun 4 digit terakhir nomor HP tidak sesuai.';

                    return;
                }
            }

            $this->complaint = $cp;

            return;
        }

        $this->searchError = 'Nomor tiket tidak ditemukan dalam sistem. Mohon periksa kembali penulisan nomor tiket Anda (contoh: DTSEN-202609-00001 atau ADU-202609-00001).';
    }

    public function render(): View
    {
        return view('livewire.ticket-tracking');
    }
}
