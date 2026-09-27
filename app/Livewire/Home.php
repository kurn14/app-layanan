<?php

namespace App\Livewire;

use App\Models\Faq;
use App\Models\ServiceType;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar')]
class Home extends Component
{
    public string $trackingNumber = '';

    public string $securityPin = '';

    public string $verificationCode = '';

    public function trackTicket()
    {
        $this->validate([
            'trackingNumber' => 'required|string',
            'securityPin' => 'required|string|size:4',
        ], [
            'trackingNumber.required' => 'Nomor tiket wajib diisi.',
            'securityPin.required' => '4 digit NIK/HP wajib diisi.',
            'securityPin.size' => 'PIN harus terdiri dari 4 digit.',
        ]);

        return redirect()->route('cek-status', [
            'ticket' => trim($this->trackingNumber),
            'pin' => trim($this->securityPin),
        ]);
    }

    public function verifyCode()
    {
        $this->validate([
            'verificationCode' => 'required|string',
        ], [
            'verificationCode.required' => 'Kode verifikasi wajib diisi.',
        ]);

        return redirect()->route('verifikasi', [
            'code' => trim($this->verificationCode),
        ]);
    }

    public function render(): View
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->take(5)->get();
        $services = ServiceType::where('is_active', true)->get();

        return view('livewire.home', [
            'faqs' => $faqs,
            'services' => $services,
        ]);
    }
}
