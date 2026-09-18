<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Degree;
use App\Livewire\Traits\WithTableSkeleton;

class DegreeTable extends Component
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

    public function render()
    {
        $degrees = Degree::when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
            })
            ->orderBy('rank', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.degree.table', [
            'degrees' => $degrees,
        ]);
    }
}
