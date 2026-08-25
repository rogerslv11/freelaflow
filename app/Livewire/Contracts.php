<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Contracts extends Component
{
    use SearchableTable;

    public $editingId = null;

    public $title = '';

    public $client_id = '';

    public $project_id = '';

    public $description = '';

    public $value = '';

    public $start_date = '';

    public $end_date = '';

    public $terms = '';

    public $status = 'draft';

    protected function rules()
    {
        return [
            'title' => 'required|min:2|max:160',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'nullable',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'terms' => 'nullable',
            'status' => 'required|in:'.implode(',', Contract::STATUSES),
        ];
    }

    public function mount()
    {
        $this->sortField = 'created_at';
        $this->sortDir = 'desc';
    }

    public function getRowsProperty()
    {
        return Contract::with('client')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('modal-show', id: 'contract-form');
    }

    public function edit($id)
    {
        $contract = Contract::findOrFail($id);
        $this->editingId = $contract->id;
        foreach (['title', 'client_id', 'project_id', 'description', 'value', 'start_date', 'end_date', 'terms', 'status'] as $f) {
            $this->$f = $contract->$f;
        }
        $this->dispatch('modal-show', id: 'contract-form');
    }

    public function save()
    {
        $data = $this->validate();
        if ($this->editingId) {
            Contract::findOrFail($this->editingId)->update($data);
            $msg = 'Contrato atualizado.';
        } else {
            Contract::create(array_merge($data, ['user_id' => Auth::id(), 'token' => Str::random(40)]));
            $msg = 'Contrato criado.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        Contract::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Contrato excluído.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['title', 'client_id', 'project_id', 'description', 'value', 'start_date', 'end_date', 'terms'] as $f) {
            $this->$f = '';
        }
        $this->status = 'draft';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.contracts', [
            'rows' => $this->rows,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'statuses' => Contract::STATUSES,
        ])->title('Contratos');
    }
}
