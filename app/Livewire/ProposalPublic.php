<?php

namespace App\Livewire;

use App\Models\Proposal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ProposalPublic extends Component
{
    public Proposal $proposal;

    public string $token = '';

    public function mount(Proposal $proposal, string $token)
    {
        abort_unless($proposal->token === $token, 404);
        $this->proposal = $proposal;
    }

    public function accept()
    {
        if ($this->proposal->status === 'sent') {
            $this->proposal->update(['status' => 'accepted', 'accepted_at' => now()]);
        }
    }

    public function reject()
    {
        if ($this->proposal->status === 'sent') {
            $this->proposal->update(['status' => 'rejected', 'rejected_at' => now()]);
        }
    }

    public function render()
    {
        return view('livewire.proposal-public', [
            'items' => $this->proposal->items,
        ])->title(fn () => 'Proposta: '.$this->proposal->title);
    }
}
