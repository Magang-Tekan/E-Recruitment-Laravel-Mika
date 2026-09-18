<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Major;
use App\Livewire\Traits\WithTableSkeleton;

class MajorTable extends Component
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
        $majors = Major::when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.major.table', [
            'majors' => $majors,
        ]);
    }
}
