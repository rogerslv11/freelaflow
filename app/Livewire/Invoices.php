<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Invoices extends Component
{
    use SearchableTable;

    public $statusFilter = '';

    public $editingId = null;

    public $number = '';

    public $client_id = '';

    public $project_id = '';

    public $issue_date = '';

    public $due_date = '';

    public $discount = 0;

    public $tax = 0;

    public $status = 'draft';

    public $note = '';

    public $items = [];

    protected function rules()
    {
        return [
            'number' => 'nullable|max:40',
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'status' => 'required|in:'.implode(',', Invoice::STATUSES),
            'note' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|max:160',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function mount()
    {
        $this->sortField = 'created_at';
        $this->sortDir = 'desc';
    }

    public function getRowsProperty()
    {
        return Invoice::with('client')
            ->when($this->search, fn ($q) => $q->where('number', 'like', "%{$this->search}%")->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function nextNumber()
    {
        $last = Invoice::where('user_id', Auth::id())->orderByDesc('id')->first();
        $n = $last ? ((int) preg_replace('/\D/', '', $last->number) ?: 0) + 1 : 1;

        return 'INV-'.str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    public function addItem()
    {
        $this->items[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem($i)
    {
        unset($this->items[$i]);
        $this->items = array_values($this->items);
    }

    public function create()
    {
        $this->resetForm();
        $this->number = $this->nextNumber();
        $this->items = [['description' => '', 'quantity' => 1, 'unit_price' => 0]];
        $this->dispatch('modal-show', id: 'invoice-form');
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $this->editingId = $invoice->id;
        foreach (['number', 'client_id', 'project_id', 'issue_date', 'due_date', 'discount', 'tax', 'status', 'note'] as $f) {
            $this->$f = $invoice->$f;
        }
        $this->items = $invoice->items->map(fn ($i) => ['description' => $i->description, 'quantity' => $i->quantity, 'unit_price' => $i->unit_price])->toArray();
        $this->dispatch('modal-show', id: 'invoice-form');
    }

    public function save()
    {
        $data = $this->validate();
        $subtotal = collect($this->items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0));
        $total = round($subtotal - ($this->discount ?? 0) + ($this->tax ?? 0), 2);

        if ($this->editingId) {
            $invoice = Invoice::findOrFail($this->editingId);
            $invoice->update(array_merge(collect($data)->except('items')->toArray(), ['total' => $total]));
            $invoice->items()->delete();
        } else {
            $invoice = Invoice::create(array_merge(
                collect($data)->except('items')->toArray(),
                ['user_id' => Auth::id(), 'token' => Str::random(40), 'total' => $total]
            ));
        }
        foreach ($this->items as $idx => $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => round($qty * $price, 2),
                'order' => $idx,
            ]);
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: 'Fatura salva.', type: 'success');
    }

    public function send($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent', 'sent_at' => now()]);
        }
        $this->dispatch('toast', message: 'Fatura enviada.', type: 'success');
    }

    public function markPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        if (! in_array($invoice->status, ['paid'])) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            Payment::firstOrCreate(
                ['invoice_id' => $invoice->id, 'user_id' => Auth::id()],
                ['client_id' => $invoice->client_id, 'project_id' => $invoice->project_id, 'amount' => $invoice->total, 'paid_at' => now(), 'method' => 'pix']
            );
            $this->dispatch('toast', message: 'Fatura marcada como paga.', type: 'success');
        }
    }

    public function duplicate($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $clone = $invoice->replicate();
        $clone->number = $this->nextNumber();
        $clone->token = Str::random(40);
        $clone->status = 'draft';
        $clone->sent_at = null;
        $clone->paid_at = null;
        $clone->user_id = Auth::id();
        $clone->save();
        foreach ($invoice->items as $item) {
            $clone->items()->create($item->only(['description', 'quantity', 'unit_price', 'total', 'order']));
        }
        $this->dispatch('toast', message: 'Fatura duplicada.', type: 'success');
    }

    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Fatura excluída.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['number', 'client_id', 'project_id', 'issue_date', 'due_date', 'discount', 'tax', 'note'] as $f) {
            $this->$f = '';
        }
        $this->status = 'draft';
        $this->items = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.invoices', [
            'rows' => $this->rows,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'statuses' => Invoice::STATUSES,
        ])->title('Faturas');
    }
}
