<div class="relative" x-data="{ open: false }" @keydown.escape.window="open=false">
    <button
        @click="open = !open"
        class="relative rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"
        aria-label="Notificações"
    >
        <x-icon name="bell" class="w-5 h-5" />
        @if($this->unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand px-1 text-[10px] font-bold text-white">
                {{ $this->unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition
        @click.outside="open=false"
        x-cloak
        class="absolute right-0 mt-2 w-80 overflow-hidden rounded-xl border border-ink-500/60 bg-ink-800 shadow-card-hover animate-scale-in"
    >
        <div class="flex items-center justify-between border-b border-ink-500/60 px-4 py-3">
            <span class="text-sm font-semibold text-white">Notificações</span>
            @if($this->unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs font-medium text-brand hover:underline">Marcar todas como lidas</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($this->items as $item)
                <button wire:click="markRead({{ $item->id }})" class="flex w-full gap-3 border-b border-ink-700/60 px-4 py-3 text-left transition hover:bg-ink-700/50">
                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $item->read ? 'bg-ink-700 text-gray-500' : 'bg-brand-soft text-brand' }}">
                        <x-icon :name="$item->icon" class="w-4 h-4" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-200 {{ $item->read ? '' : 'text-white' }}">{{ $item->title }}</p>
                        @if($item->body)
                            <p class="truncate text-xs text-gray-500">{{ $item->body }}</p>
                        @endif
                        <p class="mt-0.5 text-[11px] text-ink-400">{{ $item->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$item->read)
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-brand"></span>
                    @endif
                </button>
            @empty
                <div class="px-4 py-10 text-center text-sm text-gray-500">Nenhuma notificação.</div>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}" @click="open=false" class="block border-t border-ink-500/60 px-4 py-3 text-center text-sm font-medium text-brand hover:bg-ink-700/50">
            Ver todas
        </a>
    </div>
</div>
