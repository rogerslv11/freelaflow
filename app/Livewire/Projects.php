<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Projects extends Component
{
    use SearchableTable;

    public $statusFilter = '';

    public $editingId = null;

    public $name = '';

    public $client_id = '';

    public $description = '';

    public $start_date = '';

    public $due_date = '';

    public $value = '';

    public $status = 'planning';

    public $priority = 'medium';

    public $progress = 0;

    protected function rules()
    {
        return [
            'name' => 'required|min:2|max:160',
            'client_id' => 'required|exists:clients,id',
            'description' => 'nullable',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'value' => 'nullable|numeric|min:0',
            'status' => 'required|in:'.implode(',', Project::STATUSES),
            'priority' => 'required|in:'.implode(',', Project::PRIORITIES),
            'progress' => 'integer|min:0|max:100',
        ];
    }

    public function mount()
    {
        $this->sortField = 'created_at';
        $this->sortDir = 'desc';
    }

    public function getRowsProperty()
    {
        return Project::with('client')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('modal-show', id: 'project-form');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $this->editingId = $project->id;
        foreach (['name', 'client_id', 'description', 'start_date', 'due_date', 'value', 'status', 'priority', 'progress'] as $f) {
            $this->$f = $project->$f;
        }
        $this->dispatch('modal-show', id: 'project-form');
    }

    public function save()
    {
        $data = $this->validate();
        $data['value'] = $data['value'] ?? 0;
        if ($this->editingId) {
            Project::findOrFail($this->editingId)->update($data);
            $msg = 'Projeto atualizado.';
        } else {
            Project::create(array_merge($data, ['user_id' => Auth::id()]));
            $msg = 'Projeto criado.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        Project::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Projeto excluído.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['name', 'client_id', 'description', 'start_date', 'due_date', 'value'] as $f) {
            $this->$f = '';
        }
        $this->status = 'planning';
        $this->priority = 'medium';
        $this->progress = 0;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.projects', [
            'rows' => $this->rows,
            'clients' => Client::orderBy('name')->get(),
            'statuses' => Project::STATUSES,
            'priorities' => Project::PRIORITIES,
        ])->title('Projetos');
    }
}
