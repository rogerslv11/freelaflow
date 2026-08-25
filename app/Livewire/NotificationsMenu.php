<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsMenu extends Component
{
    public $open = false;

    public function getUnreadCountProperty()
    {
        return Notification::where('user_id', Auth::id())->where('read', false)->count();
    }

    public function getItemsProperty()
    {
        return Notification::where('user_id', Auth::id())->latest()->take(8)->get();
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())->where('read', false)->update(['read' => true]);
    }

    public function markRead($id)
    {
        $n = Notification::where('user_id', Auth::id())->findOrFail($id);
        $n->update(['read' => true]);
        if ($n->link) {
            return $this->redirect($n->link, navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.notifications-menu');
    }
}
