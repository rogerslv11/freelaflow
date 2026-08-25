<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientShow extends Component
{
    public Client $client;

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function render()
    {
        $userId = Auth::id();

        return view('livewire.client-show', [
            'projects' => $this->client->projects()->latest()->get(),
            'proposals' => $this->client->proposals()->latest()->get(),
            'contracts' => $this->client->contracts()->latest()->get(),
            'invoices' => $this->client->invoices()->latest()->get(),
            'payments' => $this->client->payments()->latest()->get(),
            'tasks' => $this->client->tasks()->latest()->get(),
            'files' => $this->client->files()->latest()->get(),
            'totalRevenue' => $this->client->payments()->sum('amount'),
            'openInvoices' => $this->client->invoices()->whereNotIn('status', ['paid', 'cancelled'])->sum('total'),
        ])->title(fn () => $this->client->name);
    }
}
