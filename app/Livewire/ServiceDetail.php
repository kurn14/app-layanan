<?php

namespace App\Livewire;

use App\Models\DtsenPurpose;
use App\Models\Faq;
use App\Models\ServiceType;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Layanan — SAPA SOSIAL')]
class ServiceDetail extends Component
{
    public string $slug = 'dtsen';

    public string $activeTab = 'tentang';

    public function mount(string $slug = 'dtsen'): void
    {
        $this->slug = strtolower($slug);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        $serviceType = ServiceType::whereRaw('LOWER(code) = ?', [$this->slug])->first()
            ?? ServiceType::where('code', 'DTSEN')->first();

        $dtsenPurposes = DtsenPurpose::where('is_active', true)->get();
        $faqs = Faq::where('is_active', true)->take(4)->get();

        return view('livewire.service-detail', [
            'service' => $serviceType,
            'purposes' => $dtsenPurposes,
            'faqs' => $faqs,
        ]);
    }
}
