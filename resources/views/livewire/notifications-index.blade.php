<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Notificações" subtitle="Seu histórico de alertas">
        <x-slot name="actions">
            <button class="btn-secondary" wire:click="markAllRead">Marcar lidas</button>
            <button class="btn-ghost" wire:click="clearAll" wire:confirm="Limpar todas as notificações?">Limpar</button>
        </x-slot>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-ink-500/60 p-4">
            <div class="relative max-w-md">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
                <input wire:model.live="search" type="text" placeholder="Buscar notificações..." class="input pl-9">
            </div>
        </div>
        <div class="divide-y divide-ink-700/60">
            @forelse($rows as $n)
                <div class="flex items-start gap-3 p-4 transition hover:bg-ink-700/30 {{ $n->read ? '' : 'bg-brand-soft/20' }}">
                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $n->read ? 'bg-ink-700 text-gray-500' : 'bg-brand-soft text-brand' }}">
                        <x-icon :name="$n->icon" class="w-4 h-4" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-200">{{ $n->title }}</p>
                        @if($n->body)<p class="mt-0.5 text-sm text-gray-500">{{ $n->body }}</p>@endif
                        <p class="mt-1 text-xs text-ink-400">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$n->read)
                        <button wire:click="markRead({{ $n->id }})" class="rounded-lg px-2 py-1 text-xs font-medium text-brand hover:underline">Marcar lida</button>
                    @endif
                </div>
            @empty
                <x-empty-state icon="bell" title="Nenhuma notificação" >Você está em dia.</x-empty-state>
            @endforelse
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
    </div>
</div>
