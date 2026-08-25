<?php

namespace App\Livewire;

use App\Models\Proposal;
use Livewire\Component;

class ProposalShow extends Component
{
    public Proposal $proposal;

    public function mount(Proposal $proposal)
    {
        $this->proposal = $proposal;
    }

    public function send()
    {
        if ($this->proposal->status === 'draft') {
            $this->proposal->update(['status' => 'sent', 'sent_at' => now()]);
            $this->dispatch('toast', message: 'Proposta enviada.', type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.proposal-show', [
            'items' => $this->proposal->items,
            'publicUrl' => route('proposals.public', [$this->proposal, $this->proposal->token]),
        ])->title(fn () => $this->proposal->title);
    }
}
