<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TestCategory;
use App\Livewire\Traits\WithTableSkeleton;

class TestCategoryTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'latest';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $categories = TestCategory::withCount('questions')
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'has_questions') {
                    $query->has('questions');
                } elseif ($this->statusFilter === 'empty') {
                    $query->doesntHave('questions');
                }
            })
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'name_asc') {
                    $query->orderBy('name', 'asc');
                } elseif ($this->sortBy === 'name_desc') {
                    $query->orderBy('name', 'desc');
                } elseif ($this->sortBy === 'questions_desc') {
                    $query->orderBy('questions_count', 'desc');
                } else {
                    $query->orderBy('id', 'desc');
                }
            }, function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.test-category.table', [
            'categories' => $categories,
        ]);
    }
}

