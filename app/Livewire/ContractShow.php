<?php

namespace App\Livewire;

use App\Models\Contract;
use Livewire\Component;

class ContractShow extends Component
{
    public Contract $contract;

    public function mount(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function send()
    {
        if ($this->contract->status === 'draft') {
            $this->contract->update(['status' => 'sent', 'sent_at' => now()]);
            $this->dispatch('toast', message: 'Contrato enviado.', type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.contract-show', [
            'publicUrl' => route('contracts.public', [$this->contract, $this->contract->token]),
        ])->title(fn () => $this->contract->title);
    }
}
