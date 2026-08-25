<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectShow extends Component
{
    public Project $project;

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        return view('livewire.project-show', [
            'tasks' => $this->project->tasks()->latest()->get(),
            'invoices' => $this->project->invoices()->latest()->get(),
            'payments' => $this->project->payments()->latest()->get(),
            'files' => $this->project->files()->latest()->get(),
            'members' => $this->project->members()->get(),
            'timeEntries' => $this->project->timeEntries()->latest()->get(),
            'loggedSeconds' => (int) $this->project->timeEntries()->sum('duration'),
            'billedSeconds' => (int) $this->project->timeEntries()->where('billable', true)->sum('duration'),
        ])->title(fn () => $this->project->name);
    }
}
