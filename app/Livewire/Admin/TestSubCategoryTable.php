<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use App\Livewire\Traits\WithTableSkeleton;

class TestSubCategoryTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $categoryId = '';
    public $sortBy = 'latest';
    public $perPage = 10;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategoryId() { $this->resetPage(); }
    public function updatingSortBy() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->search = '';
        $this->categoryId = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $categories = TestCategory::orderBy('name', 'asc')->get();

        $subCategories = TestSubCategory::with('category')
            ->withCount('questions')
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('category', function ($catQ) use ($search) {
                          $catQ->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      });
                });
            })
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->sortBy === 'name_asc', fn($q) => $q->orderBy('name', 'asc'))
            ->when($this->sortBy === 'name_desc', fn($q) => $q->orderBy('name', 'desc'))
            ->when($this->sortBy === 'questions_desc', fn($q) => $q->orderBy('questions_count', 'desc'))
            ->when($this->sortBy === 'latest', fn($q) => $q->orderBy('id', 'desc'))
            ->paginate($this->perPage);

        return view('livewire.admin.test-sub-category.table', [
            'subCategories' => $subCategories,
            'categories' => $categories,
        ]);
    }
}

