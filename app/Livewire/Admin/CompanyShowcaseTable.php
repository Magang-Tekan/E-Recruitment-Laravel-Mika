<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\WithTableSkeleton;
use App\Models\Company;
use App\Models\CompanyShowcase;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyShowcaseTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $showcase = CompanyShowcase::find($id);
        if ($showcase) {
            $showcase->is_active = !$showcase->is_active;
            $showcase->save();
            \Illuminate\Support\Facades\Cache::forget('frontend_company_showcases');
        }
    }

    public function render()
    {
        $showcases = CompanyShowcase::query()
            ->with('company')
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(tag) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $companies = Company::orderBy('name')->get();

        return view('livewire.admin.showcase.table', [
            'showcases' => $showcases,
            'companies' => $companies,
        ]);
    }
}
