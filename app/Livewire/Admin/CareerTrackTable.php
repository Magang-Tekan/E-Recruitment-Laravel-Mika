<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\WithTableSkeleton;
use App\Models\CareerTrack;
use Livewire\Component;
use Livewire\WithPagination;

class CareerTrackTable extends Component
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
        $track = CareerTrack::find($id);
        if ($track) {
            $track->is_active = !$track->is_active;
            $track->save();
            CareerTrack::clearCaches();
        }
    }

    public function render()
    {
        $tracks = CareerTrack::query()
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(badge) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(prefix) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(accent) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(back_title) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.career-tracks.table', [
            'tracks' => $tracks,
        ]);
    }
}
