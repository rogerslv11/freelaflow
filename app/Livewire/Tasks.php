<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Tasks extends Component
{
    use SearchableTable;

    public $view = 'list'; // list | kanban

    public $statusFilter = '';

    public $priorityFilter = '';

    public $editingId = null;

    public $title = '';

    public $description = '';

    public $project_id = '';

    public $client_id = '';

    public $assignee = '';

    public $priority = 'medium';

    public $status = 'todo';

    public $due_date = '';

    public $estimated_hours = '';

    public $logged_hours = '';

    protected function rules()
    {
        return [
            'title' => 'required|min:2|max:160',
            'description' => 'nullable',
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'assignee' => 'nullable|max:120',
            'priority' => 'required|in:'.implode(',', Task::PRIORITIES),
            'status' => 'required|in:'.implode(',', Task::STATUSES),
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'logged_hours' => 'nullable|numeric|min:0',
        ];
    }

    public function mount()
    {
        $this->sortField = 'due_date';
        $this->sortDir = 'asc';
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $userId = Auth::id();
        $base = Task::where('user_id', $userId);

        return [
            'total' => (clone $base)->count(),
            'todo' => (clone $base)->where('status', 'todo')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'done' => (clone $base)->where('status', 'done')->count(),
            'overdue' => (clone $base)->where('status', '!=', 'done')->whereNotNull('due_date')->where('due_date', '<', now())->count(),
        ];
    }

    public function getRowsProperty()
    {
        return Task::with('project', 'client')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('assignee', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function getBoardProperty()
    {
        return collect(Task::STATUSES)->mapWithKeys(fn ($s) => [
            $s => Task::with('project')
                ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
                ->where('status', $s)
                ->orderBy('order')
                ->get(),
        ]);
    }

    #[On('moveTask')]
    public function moveTask($id, $status)
    {
        $task = Task::findOrFail($id);
        $task->update(['status' => $status]);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('modal-show', id: 'task-form');
    }

    public function createFor($status)
    {
        $this->resetForm();
        $this->status = $status;
        $this->dispatch('modal-show', id: 'task-form');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $this->editingId = $task->id;
        foreach (['title', 'description', 'project_id', 'client_id', 'assignee', 'priority', 'status', 'due_date', 'estimated_hours', 'logged_hours'] as $f) {
            $this->$f = $task->$f;
        }
        $this->dispatch('modal-show', id: 'task-form');
    }

    public function save()
    {
        $data = $this->validate();
        if ($this->editingId) {
            Task::findOrFail($this->editingId)->update($data);
            $msg = 'Tarefa atualizada.';
        } else {
            Task::create(array_merge($data, ['user_id' => Auth::id()]));
            $msg = 'Tarefa criada.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        Task::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Tarefa excluída.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['title', 'description', 'project_id', 'client_id', 'assignee', 'due_date', 'estimated_hours', 'logged_hours'] as $f) {
            $this->$f = '';
        }
        $this->priority = 'medium';
        $this->status = 'todo';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.tasks', [
            'rows' => $this->rows,
            'board' => $this->board,
            'stats' => $this->stats,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ])->title('Tarefas');
    }
}
