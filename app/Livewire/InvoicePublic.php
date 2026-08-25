<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class InvoicePublic extends Component
{
    public Invoice $invoice;

    public string $token = '';

    public function mount(Invoice $invoice, string $token)
    {
        abort_unless($invoice->token === $token, 404);
        $this->invoice = $invoice;
    }

    public function render()
    {
        return view('livewire.invoice-public', [
            'items' => $this->invoice->items,
            'payments' => $this->invoice->payments()->latest()->get(),
        ])->title(fn () => 'Fatura: '.($this->invoice->number ?? '#'.$this->invoice->id));
    }
}
