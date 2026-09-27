<?php

namespace App\Livewire;

use App\Models\ServiceType;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Daftar Layanan — SAPA SOSIAL')]
class ServiceList extends Component
{
    public string $search = '';

    public string $selectedCategory = 'all';

    public function selectCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function render(): View
    {
        $query = ServiceType::where('is_active', true);

        if ($this->selectedCategory !== 'all') {
            $query->where('category', $this->selectedCategory);
        }

        if (! empty(trim($this->search))) {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', $searchTerm)
                    ->orWhere('description', 'ilike', $searchTerm)
                    ->orWhere('category', 'ilike', $searchTerm);
            });
        }

        $services = $query->orderBy('id')->get();

        return view('livewire.service-list', [
            'services' => $services,
        ]);
    }
}
