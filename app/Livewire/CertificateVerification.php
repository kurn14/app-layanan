<?php

namespace App\Livewire;

use App\Models\DtsenCertificate;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Verifikasi Keaslian Surat DTSEN — SAPA SOSIAL')]
class CertificateVerification extends Component
{
    #[Url]
    public string $code = '';

    public bool $hasChecked = false;

    public ?DtsenCertificate $certificate = null;

    public bool $isExpired = false;

    public function mount(?string $code = null): void
    {
        if ($code) {
            $this->code = trim($code);
            $this->verify();
        } elseif (! empty($this->code)) {
            $this->verify();
        }
    }

    public function verify(): void
    {
        $this->validate([
            'code' => 'required|string',
        ], [
            'code.required' => 'Masukkan kode verifikasi dokumen.',
        ]);

        $this->hasChecked = true;
        $this->certificate = null;
        $this->isExpired = false;

        $cleanCode = trim($this->code);

        $cert = DtsenCertificate::where('verification_code', $cleanCode)
            ->orWhere('certificate_number', $cleanCode)
            ->with(['serviceRequest.village.district', 'dtsenPurpose', 'signer'])
            ->first();

        if ($cert) {
            $this->certificate = $cert;
            if ($cert->valid_until && $cert->valid_until->isPast()) {
                $this->isExpired = true;
            }
        }
    }

    public function render(): View
    {
        return view('livewire.certificate-verification');
    }
}
