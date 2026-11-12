<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\QuestionBank;
use App\Models\TestCategory;
use App\Livewire\Traits\WithTableSkeleton;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    // Bulk selection state
    public array $selectedQuestions = [];
    public bool $selectAll = false;
    public bool $selectAllMatching = false;
    public bool $showBulkDeleteModal = false;

    public function updatingSearch()
    {
        $this->resetSelection();
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetSelection();
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetSelection();
        $this->resetPage();
    }

    public function updatingUsageFilter()
    {
        $this->resetSelection();
        $this->resetPage();
    }

    public function updatingHasImage()
    {
        $this->resetSelection();
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetSelection();
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetSelection();
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
        $this->resetSelection();
        $this->resetPage();
    }

    public function resetSelection()
    {
        $this->selectedQuestions = [];
        $this->selectAll = false;
        $this->selectAllMatching = false;
    }

    /**
     * Query builder dengan filter aktif saat ini.
     */
    protected function getFilteredQuery()
    {
        return QuestionBank::query()
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(question) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('category', function ($catQ) use ($search) {
                          $catQ->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      })
                      ->orWhereHas('subCategory', function ($subQ) use ($search) {
                          $subQ->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
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
            });
    }

    /**
     * Ambil ID soal yang tampil di halaman pagination saat ini.
     */
    protected function getCurrentPageIds(): array
    {
        $paginated = $this->getFilteredQuery()
            ->when($this->sortBy === 'oldest', fn($q) => $q->orderBy('id', 'asc'))
            ->when($this->sortBy === 'points_desc', fn($q) => $q->orderBy('points', 'desc')->orderBy('id', 'desc'))
            ->when($this->sortBy === 'points_asc', fn($q) => $q->orderBy('points', 'asc')->orderBy('id', 'desc'))
            ->when(!in_array($this->sortBy, ['oldest', 'points_desc', 'points_asc']), fn($q) => $q->orderBy('id', 'desc'))
            ->paginate($this->perPage);

        return $paginated->pluck('id')->map(fn($id) => (string)$id)->toArray();
    }

    /**
     * Toggle centang semua soal pada halaman saat ini.
     */
    public function updatedSelectAll($value)
    {
        $pageIds = $this->getCurrentPageIds();

        if ($value) {
            $this->selectedQuestions = array_values(array_unique(array_merge($this->selectedQuestions, $pageIds)));
        } else {
            $this->selectedQuestions = array_values(array_diff($this->selectedQuestions, $pageIds));
            $this->selectAllMatching = false;
        }
    }

    /**
     * Tangani perubahan centang per butir soal.
     */
    public function updatedSelectedQuestions()
    {
        $pageIds = $this->getCurrentPageIds();
        $intersect = array_intersect($pageIds, $this->selectedQuestions);
        $this->selectAll = count($pageIds) > 0 && count($intersect) === count($pageIds);

        if ($this->selectAllMatching && count($this->selectedQuestions) < $this->getFilteredQuery()->count()) {
            $this->selectAllMatching = false;
        }
    }

    /**
     * Pilih seluruh butir soal hasil filter (lintas halaman).
     */
    public function selectAllMatchingQuestions()
    {
        $this->selectAllMatching = true;
        $allIds = $this->getFilteredQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->selectedQuestions = $allIds;
        $this->selectAll = true;
    }

    /**
     * Buka modal konfirmasi hapus massal.
     */
    public function confirmBulkDelete()
    {
        if ($this->selectedCount === 0) {
            return;
        }
        $this->showBulkDeleteModal = true;
    }

    /**
     * Eksekusi hapus massal soal terpilih.
     */
    public function bulkDelete()
    {
        if ($this->selectedCount === 0) {
            $this->showBulkDeleteModal = false;
            return;
        }

        $query = $this->selectAllMatching
            ? $this->getFilteredQuery()
            : QuestionBank::whereIn('id', $this->selectedQuestions);

        $questionsToDelete = $query->get(['id', 'image_path']);
        $totalCount = $questionsToDelete->count();

        if ($totalCount === 0) {
            $this->showBulkDeleteModal = false;
            $this->resetSelection();
            return;
        }

        DB::beginTransaction();
        try {
            // Hapus berkas lampiran pendukung jika ada
            foreach ($questionsToDelete as $q) {
                if ($q->image_path && Storage::disk('public')->exists($q->image_path)) {
                    Storage::disk('public')->delete($q->image_path);
                }
            }

            // Hapus rekaman soal (cascade options, test_questions, test_answers)
            $ids = $questionsToDelete->pluck('id')->toArray();
            foreach (array_chunk($ids, 500) as $chunk) {
                QuestionBank::whereIn('id', $chunk)->delete();
            }

            DB::commit();

            session()->flash('delete', "Berhasil menghapus {$totalCount} butir soal sekaligus.");
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', "Gagal menghapus soal: " . $e->getMessage());
        }

        $this->showBulkDeleteModal = false;
        $this->resetSelection();
        $this->resetPage();
    }

    /**
     * Jumlah butir soal terpilih saat ini.
     */
    public function getSelectedCountProperty(): int
    {
        if ($this->selectAllMatching) {
            return $this->getFilteredQuery()->count();
        }
        return count($this->selectedQuestions);
    }

    /**
     * Total soal yang cocok dengan filter saat ini.
     */
    public function getTotalMatchingCountProperty(): int
    {
        return $this->getFilteredQuery()->count();
    }

    /**
     * Nama kategori aktif yang sedang difilter.
     */
    public function getActiveCategoryNameProperty(): ?string
    {
        if (!$this->categoryId) {
            return null;
        }
        return TestCategory::find($this->categoryId)?->name;
    }

    public function render()
    {
        $categories = TestCategory::orderBy('name', 'asc')->get();

        $questions = $this->getFilteredQuery()
            ->with(['category', 'subCategory', 'options'])
            ->withCount(['tests', 'options'])
            ->when($this->sortBy === 'oldest', fn($q) => $q->orderBy('id', 'asc'))
            ->when($this->sortBy === 'points_desc', fn($q) => $q->orderBy('points', 'desc')->orderBy('id', 'desc'))
            ->when($this->sortBy === 'points_asc', fn($q) => $q->orderBy('points', 'asc')->orderBy('id', 'desc'))
            ->when(!in_array($this->sortBy, ['oldest', 'points_desc', 'points_asc']), fn($q) => $q->orderBy('id', 'desc'))
            ->paginate($this->perPage);

        // Selaraskan status checkbox selectAll pada halaman saat ini
        $currentPageIds = $questions->pluck('id')->map(fn($id) => (string)$id)->toArray();
        if ($this->selectAllMatching) {
            $this->selectAll = true;
        } elseif (count($currentPageIds) > 0) {
            $intersect = array_intersect($currentPageIds, $this->selectedQuestions);
            $this->selectAll = count($intersect) === count($currentPageIds);
        } else {
            $this->selectAll = false;
        }

        return view('livewire.admin.question-bank.table', [
            'questions' => $questions,
            'categories' => $categories,
        ]);
    }
}
