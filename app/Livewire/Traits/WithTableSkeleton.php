<?php

namespace App\Livewire\Traits;

trait WithTableSkeleton
{
    /**
     * Placeholder view untuk skeleton loading otomatis saat komponen Livewire di-load secara lazy.
     */
    public function placeholder()
    {
        return view('components.skeleton.table');
    }
}
