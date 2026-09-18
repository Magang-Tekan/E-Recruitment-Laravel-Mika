<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\QuestionBank;
use App\Models\TestCategory;
use App\Livewire\Traits\WithTableSkeleton;

class QuestionBankTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $categoryId = '';
    public $type = '';
    public $usageFilter = '';
    public $hasImage = '';
    public $sortBy = 'latest';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingUsageFilter()
    {
        $this->resetPage();
    }

    public function updatingHasImage()
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
        $this->categoryId = '';
        $this->type = '';
        $this->usageFilter = '';
        $this->hasImage = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $categories = TestCategory::orderBy('name', 'asc')->get();

        $questions = QuestionBank::with(['category', 'options'])
            ->withCount(['tests', 'options'])
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(question) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('category', function ($catQ) use ($search) {
                          $catQ->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      })
                      ->orWhereHas('options', function ($optQ) use ($search) {
                          $optQ->whereRaw('LOWER(option_text) LIKE ?', ['%' . $search . '%']);
                      });
                });
            })
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->type, function ($query) {
                $query->where('question_type', $this->type);
            })
            ->when($this->usageFilter === 'used', function ($query) {
                $query->whereHas('tests');
            })
            ->when($this->usageFilter === 'unused', function ($query) {
                $query->whereDoesntHave('tests');
            })
            ->when($this->hasImage === 'with_image', function ($query) {
                $query->whereNotNull('image_path')->where('image_path', '!=', '');
            })
            ->when($this->hasImage === 'text_only', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('image_path')->orWhere('image_path', '');
                });
            })
            ->when($this->sortBy === 'oldest', function ($query) {
                $query->orderBy('id', 'asc');
            })
            ->when($this->sortBy === 'points_desc', function ($query) {
                $query->orderBy('points', 'desc')->orderBy('id', 'desc');
            })
            ->when($this->sortBy === 'points_asc', function ($query) {
                $query->orderBy('points', 'asc')->orderBy('id', 'desc');
            })
            ->when(!in_array($this->sortBy, ['oldest', 'points_desc', 'points_asc']), function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.question-bank.table', [
            'questions' => $questions,
            'categories' => $categories,
        ]);
    }
}
