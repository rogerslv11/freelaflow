<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Proposals extends Component
{
    use SearchableTable;

    public $editingId = null;

    public $title = '';

    public $description = '';

    public $client_id = '';

    public $valid_until = '';

    public $payment_terms = '';

    public $notes = '';

    public $status = 'draft';

    public $statusFilter = '';

    public $discount = 0;

    public $tax = 0;

    public $items = [];

    protected function rules()
    {
        return [
            'title' => 'required|min:2|max:160',
            'description' => 'nullable',
            'client_id' => 'required|exists:clients,id',
            'valid_until' => 'nullable|date',
            'payment_terms' => 'nullable',
            'notes' => 'nullable',
            'status' => 'required|in:'.implode(',', Proposal::STATUSES),
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
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

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getRowsProperty()
    {
        $sortable = ['title', 'status', 'valid_until', 'total', 'created_at'];
        $field = in_array($this->sortField, $sortable) ? $this->sortField : 'created_at';

        return Proposal::with('client')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($field, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function getStatsProperty()
    {
        $userId = Auth::id();
        $all = Proposal::with('items')->where('user_id', $userId)->get();

        return [
            'count' => $all->count(),
            'value' => $all->sum('total'),
            'accepted' => $all->where('status', 'accepted')->sum('total'),
        ];
    }

    public function getFormTotalProperty()
    {
        $subtotal = collect($this->items)->sum(fn ($i) => (float) ($i['quantity'] ?? 0) * (float) ($i['unit_price'] ?? 0));

        return round($subtotal - (float) $this->discount + (float) $this->tax, 2);
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
        $this->items = [['description' => '', 'quantity' => 1, 'unit_price' => 0]];
        $this->dispatch('modal-show', id: 'proposal-form');
    }

    public function edit($id)
    {
        $proposal = Proposal::with('items')->findOrFail($id);
        $this->editingId = $proposal->id;
        foreach (['title', 'description', 'client_id', 'valid_until', 'payment_terms', 'notes', 'status', 'discount', 'tax'] as $f) {
            $this->$f = $proposal->$f;
        }
        $this->items = $proposal->items->map(fn ($i) => ['description' => $i->description, 'quantity' => $i->quantity, 'unit_price' => $i->unit_price])->toArray();
        $this->dispatch('modal-show', id: 'proposal-form');
    }

    public function save()
    {
        $data = $this->validate();
        $subtotal = collect($this->items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0));
        $total = round($subtotal - ($this->discount ?? 0) + ($this->tax ?? 0), 2);

        if ($this->editingId) {
            $proposal = Proposal::findOrFail($this->editingId);
            $proposal->update(array_merge(collect($data)->except('items')->toArray(), ['total' => $total]));
            $proposal->items()->delete();
        } else {
            $proposal = Proposal::create(array_merge(
                collect($data)->except('items')->toArray(),
                ['user_id' => Auth::id(), 'token' => Str::random(40), 'total' => $total]
            ));
        }
        foreach ($this->items as $idx => $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            ProposalItem::create([
                'proposal_id' => $proposal->id,
                'description' => $item['description'],
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => round($qty * $price, 2),
                'order' => $idx,
            ]);
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: 'Proposta salva.', type: 'success');
    }

    public function delete($id)
    {
        Proposal::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Proposta excluída.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['title', 'description', 'client_id', 'valid_until', 'payment_terms', 'notes', 'discount', 'tax'] as $f) {
            $this->$f = '';
        }
        $this->status = 'draft';
        $this->items = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.proposals', [
            'rows' => $this->rows,
            'stats' => $this->stats,
            'formTotal' => $this->formTotal,
            'clients' => Client::orderBy('name')->get(),
            'statuses' => Proposal::STATUSES,
        ])->title('Propostas');
    }
}
