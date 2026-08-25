<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function send()
    {
        if ($this->invoice->status === 'draft') {
            $this->invoice->update(['status' => 'sent', 'sent_at' => now()]);
            $this->dispatch('toast', message: 'Fatura enviada.', type: 'success');
        }
    }

    public function markPaid()
    {
        if (! in_array($this->invoice->status, ['paid', 'cancelled'])) {
            $this->invoice->update([
                'status' => 'paid',
                'paid_at' => $this->invoice->paid_at ?? now(),
            ]);
            $this->dispatch('toast', message: 'Fatura marcada como paga.', type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.invoice-show', [
            'items' => $this->invoice->items,
            'payments' => $this->invoice->payments()->latest()->get(),
            'publicUrl' => route('invoices.public', [$this->invoice, $this->invoice->token]),
            'balance' => $this->invoice->total - $this->invoice->payments()->sum('amount'),
        ])->title(fn () => $this->invoice->number ?? ('Fatura #'.$this->invoice->id));
    }
}
