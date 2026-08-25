<?php

namespace App\Livewire;

use App\Models\Contract;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ContractPublic extends Component
{
    public Contract $contract;

    public string $signer_name = '';

    public function mount(Contract $contract, string $token)
    {
        abort_unless($contract->token === $token, 404);
        $this->contract = $contract;
    }

    public function sign()
    {
        $this->validate(['signer_name' => 'required|min:2']);
        if ($this->contract->status === 'sent') {
            $this->contract->update([
                'status' => 'signed',
                'signed_at' => now(),
                'signed_by' => $this->signer_name,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.contract-public')
            ->title(fn () => 'Contrato: '.$this->contract->title);
    }
}
