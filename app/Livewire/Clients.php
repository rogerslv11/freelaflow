<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Clients extends Component
{
    use SearchableTable;

    public $statusFilter = '';

    public $editingId = null;

    public $name = '';

    public $company = '';

    public $email = '';

    public $phone = '';

    public $whatsapp = '';

    public $document = '';

    public $address = '';

    public $city = '';

    public $state = '';

    public $country = 'Brasil';

    public $notes = '';

    public $status = 'active';

    protected function rules()
    {
        return [
            'name' => 'required|min:2|max:120',
            'company' => 'nullable|max:120',
            'email' => 'nullable|email|max:160',
            'phone' => 'nullable|max:40',
            'whatsapp' => 'nullable|max:40',
            'document' => 'nullable|max:40',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:80',
            'state' => 'nullable|max:40',
            'country' => 'nullable|max:80',
            'notes' => 'nullable',
            'status' => 'required|in:active,inactive,lead',
        ];
    }

    public function mount()
    {
        $this->sortField = 'name';
        $this->sortDir = 'asc';
    }

    public function getRowsProperty()
    {
        return Client::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('company', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('modal-show', id: 'client-form');
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $this->editingId = $client->id;
        foreach (['name', 'company', 'email', 'phone', 'whatsapp', 'document', 'address', 'city', 'state', 'country', 'notes', 'status'] as $f) {
            $this->$f = $client->$f;
        }
        $this->dispatch('modal-show', id: 'client-form');
    }

    public function save()
    {
        $data = $this->validate();
        if ($this->editingId) {
            Client::findOrFail($this->editingId)->update($data);
            $msg = 'Cliente atualizado.';
        } else {
            Client::create(array_merge($data, ['user_id' => Auth::id()]));
            $msg = 'Cliente criado.';
        }
        $this->resetForm();
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function delete($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        $this->dispatch('toast', message: 'Cliente excluído.', type: 'success');
    }

    public function resetForm()
    {
        $this->editingId = null;
        foreach (['name', 'company', 'email', 'phone', 'whatsapp', 'document', 'address', 'city', 'state', 'notes'] as $f) {
            $this->$f = '';
        }
        $this->country = 'Brasil';
        $this->status = 'active';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.clients', [
            'rows' => $this->rows,
            'statuses' => Client::STATUSES,
        ])->title('Clientes');
    }
}
