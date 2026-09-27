<?php

namespace App\Livewire;

use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\InformationPage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Informasi Layanan & FAQ — SAPA SOSIAL')]
class InformationFaq extends Component
{
    public string $search = '';

    public string $activeSection = 'faq'; // faq, forms, articles

    public function render(): View
    {
        $faqQuery = Faq::where('is_active', true)->orderBy('sort_order');
        if (! empty(trim($this->search))) {
            $term = '%'.trim($this->search).'%';
            $faqQuery->where(function ($q) use ($term) {
                $q->where('question', 'ilike', $term)
                    ->orWhere('answer', 'ilike', $term);
            });
        }
        $faqs = $faqQuery->get();

        $forms = DownloadableForm::where('is_current', true)->get();
        $articles = InformationPage::where('publish_status', 'published')->get();

        return view('livewire.information-faq', [
            'faqs' => $faqs,
            'forms' => $forms,
            'articles' => $articles,
        ]);
    }
}
