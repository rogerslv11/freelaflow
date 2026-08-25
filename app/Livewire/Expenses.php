<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Expenses extends Component
{
    use SearchableTable;

    public $editingId = null;

    public $description = '';

    public $category_id = '';

    public $project_id = '';

    public $client_id = '';

    public $amount = '';

    public $incurred_at = '';

    public $note = '';

    protected function rules()
    {
        return [
            'description' => 'required|min:2|max:160',
            'category_id' => 'nullable|exists:expense_categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_id' => 'nullable|exists:clients,id',
            'amount' => 'required|numeric|min:0',
            'incurred_at' => 'nullable|date',
            'note' => 'nullable',
        ];
    }

    public function mount()
    {
        $this->sortField = 'incurred_at';
        $this->sortDir = 'desc';
        $this->incurred_at = now()->format('Y-m-d');
    }

    public function getRowsProperty()
    {
        return Expense::with('category', 'project')
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->resetForm();
        $this->incurred_at = now()->format('Y-m-d');
        $this->dispatch('modal-show', id: 'expense-form');
    }

    public function edit($id)
    {
        $e = Expense::findOrFail($id);
        $this->editingId = $e->id;
        foreach (['description', 'category_id', 'project_id', 'client_id', 'amount', 'incurred_at', 'note'] as $f) {
            $this->$f = $e->$f;
        }
        $this->dispatch('modal-show', id: 'expense-form');
    }

    public function save()
    {
        $data = $this->validate();
        if ($this->editingId) {
            Expense::findOrFail($this->editingId)->update($data);
            $msg = 'Despesa atualizada.';
        } else {
            Expense::create(array_merge($data, ['user_id' => Auth::id()]));
            $msg = 'Despesa registrada.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        Expense::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Despesa excluída.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['description', 'category_id', 'project_id', 'client_id', 'amount', 'note'] as $f) {
            $this->$f = '';
        }
        $this->incurred_at = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.expenses', [
            'rows' => $this->rows,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'categories' => ExpenseCategory::orderBy('name')->get(),
        ])->title('Despesas');
    }
}
