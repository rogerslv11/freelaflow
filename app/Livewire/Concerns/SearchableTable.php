<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

trait SearchableTable
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'id';

    public $sortDir = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}
