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
    public $subCategoryFilter = '';
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

    public function updatingSubCategoryFilter()
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
        $this->subCategoryFilter = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $categories = TestCategory::withCount(['questions', 'subCategories'])
            ->with(['subCategories' => function ($query) {
                $query->withCount('questions')->orderBy('name', 'asc');
            }])
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('subCategories', function ($subQ) use ($search) {
                          $subQ->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'has_questions') {
                    $query->has('questions');
                } elseif ($this->statusFilter === 'empty') {
                    $query->doesntHave('questions');
                }
            })
            ->when($this->subCategoryFilter, function ($query) {
                if ($this->subCategoryFilter === 'with_sub') {
                    $query->has('subCategories');
                } elseif ($this->subCategoryFilter === 'without_sub') {
                    $query->doesntHave('subCategories');
                }
            })
            ->when($this->sortBy, function ($query) {
                if ($this->sortBy === 'name_asc') {
                    $query->orderBy('name', 'asc');
                } elseif ($this->sortBy === 'name_desc') {
                    $query->orderBy('name', 'desc');
                } elseif ($this->sortBy === 'questions_desc') {
                    $query->orderBy('questions_count', 'desc');
                } elseif ($this->sortBy === 'sub_categories_desc') {
                    $query->orderBy('sub_categories_count', 'desc');
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
