<?php

namespace App\Livewire;

use App\Livewire\Concerns\SearchableTable;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsIndex extends Component
{
    use SearchableTable;

    public function mount()
    {
        $this->sortField = 'created_at';
        $this->sortDir = 'desc';
        $this->perPage = 15;
    }

    public function getRowsProperty()
    {
        return Notification::where('user_id', Auth::id())
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('body', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);
    }

    public function markRead($id)
    {
        Notification::where('user_id', Auth::id())->findOrFail($id)->update(['read' => true]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())->where('read', false)->update(['read' => true]);
        $this->dispatch('toast', message: 'Todas marcadas como lidas.', type: 'success');
    }

    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        $this->dispatch('toast', message: 'Notificações limpas.', type: 'success');
    }

    public function render()
    {
        return view('livewire.notifications-index', [
            'rows' => $this->rows,
        ])->title('Notificações');
    }
}
