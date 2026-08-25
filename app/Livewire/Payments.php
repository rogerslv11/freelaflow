<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Payments extends Component
{
    use SearchableTable;

    public $editingId = null;

    public $client_id = '';

    public $project_id = '';

    public $invoice_id = '';

    public $amount = '';

    public $paid_at = '';

    public $method = 'pix';

    public $note = '';

    protected function rules()
    {
        return [
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'nullable|date',
            'method' => 'required|in:'.implode(',', Payment::METHODS),
            'note' => 'nullable',
        ];
    }

    public function mount()
    {
        $this->sortField = 'paid_at';
        $this->sortDir = 'desc';
        $this->paid_at = now()->format('Y-m-d');
    }

    public function getRowsProperty()
    {
        return Payment::with('client', 'invoice')
            ->when($this->search, fn ($q) => $q->where('note', 'like', "%{$this->search}%")->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$this->search}%")))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->resetForm();
        $this->paid_at = now()->format('Y-m-d');
        $this->dispatch('modal-show', id: 'payment-form');
    }

    public function edit($id)
    {
        $p = Payment::findOrFail($id);
        $this->editingId = $p->id;
        foreach (['client_id', 'project_id', 'invoice_id', 'amount', 'paid_at', 'method', 'note'] as $f) {
            $this->$f = $p->$f;
        }
        $this->dispatch('modal-show', id: 'payment-form');
    }

    public function save()
    {
        $data = $this->validate();
        if ($this->editingId) {
            Payment::findOrFail($this->editingId)->update($data);
            $msg = 'Pagamento atualizado.';
        } else {
            Payment::create(array_merge($data, ['user_id' => Auth::id()]));
            $msg = 'Pagamento registrado.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        Payment::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Pagamento excluído.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['client_id', 'project_id', 'invoice_id', 'amount', 'note'] as $f) {
            $this->$f = '';
        }
        $this->method = 'pix';
        $this->paid_at = now()->format('Y-m-d');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.payments', [
            'rows' => $this->rows,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'invoices' => Invoice::whereNotIn('status', ['paid', 'cancelled'])->orderBy('number')->get(),
            'methods' => Payment::METHODS,
        ])->title('Pagamentos');
    }
}
