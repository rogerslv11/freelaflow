<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Propostas" subtitle="Crie e acompanhe propostas comerciais">
        <x-slot name="actions">
            <button class="btn-primary" wire:click="create">
                <x-icon name="plus" class="w-4 h-4" /> Nova proposta
            </button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Propostas" :value="$stats['count']" icon="proposals" />
        <x-stat-card label="Valor total" :value="money($stats['value'])" icon="currency" accent />
        <x-stat-card label="Valor aceito" :value="money($stats['accepted'])" icon="check" accent />
    </div>

    <div class="card overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-ink-500/60 p-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative max-w-md">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
                <input wire:model.live="search" type="text" placeholder="Buscar propostas..." class="input pl-9">
            </div>
            <div class="flex flex-wrap items-center gap-1.5">
                <button wire:click="$set('statusFilter', '')" class="rounded-full px-3 py-1 text-xs font-medium transition-colors {{ $statusFilter === '' ? 'bg-brand text-white' : 'bg-ink-700/50 text-gray-400 hover:bg-ink-700' }}">Todos</button>
                @foreach($statuses as $s)
                    <button wire:click="$set('statusFilter', '{{ $s }}')" class="rounded-full px-3 py-1 text-xs font-medium transition-colors {{ $statusFilter === $s ? 'bg-brand text-white' : 'bg-ink-700/50 text-gray-400 hover:bg-ink-700' }}">{{ status_label($s) }}</button>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-ink-500/60 bg-ink-900/50">
                    <tr>
                        <th class="th cursor-pointer select-none" wire:click="sortBy('title')">Título @if($sortField === 'title')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                        <th class="th">Cliente</th>
                        <th class="th cursor-pointer select-none" wire:click="sortBy('total')">Valor @if($sortField === 'total')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                        <th class="th cursor-pointer select-none" wire:click="sortBy('valid_until')">Validade @if($sortField === 'valid_until')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                        <th class="th cursor-pointer select-none" wire:click="sortBy('status')">Status @if($sortField === 'status')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                        <th class="th text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($rows as $proposal)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td font-medium text-gray-200">{{ $proposal->title }}</td>
                            <td class="td">{{ $proposal->client?->name ?? '—' }}</td>
                            <td class="td text-gray-300">{{ money($proposal->total) }}</td>
                            <td class="td text-gray-400">{{ $proposal->valid_until?->format('d/m/Y') ?? '—' }}</td>
                            <td class="td"><x-status-badge :status="$proposal->status" /></td>
                            <td class="td">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('proposals.show', $proposal) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="eye" class="w-4 h-4" /></a>
                                    <button wire:click="edit({{ $proposal->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="edit" class="w-4 h-4" /></button>
                                    <button wire:click="delete({{ $proposal->id }})" wire:confirm="Excluir esta proposta?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="proposals" title="Nenhuma proposta" >Clique em "Nova proposta" para começar.</x-empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
    </div>

    <x-modal id="proposal-form" :title="$editingId ? 'Editar proposta' : 'Nova proposta'" size="xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model="title" label="Título" required />
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" required />
                <x-input wire:model="valid_until" label="Validade" type="date" />
                <x-select wire:model="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
            </div>
            <x-textarea wire:model="description" label="Descrição" />

            <div>
                <label class="label">Itens</label>
                <div class="space-y-2">
                    @foreach($items as $i => $item)
                        <div class="flex gap-2">
                            <input wire:model="items.{{ $i }}.description" placeholder="Descrição" class="input flex-1">
                            <input wire:model="items.{{ $i }}.quantity" type="number" step="0.01" placeholder="Qtd" class="input w-20">
                            <input wire:model="items.{{ $i }}.unit_price" type="number" step="0.01" placeholder="Preço" class="input w-28">
                            <button type="button" wire:click="removeItem({{ $i }})" class="rounded-lg px-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                        </div>
                    @endforeach
                </div>
                <button type="button" wire:click="addItem" class="mt-2 text-sm font-medium text-brand hover:underline">+ Adicionar item</button>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-input wire:model="discount" label="Desconto" type="number" step="0.01" />
                <x-input wire:model="tax" label="Impostos" type="number" step="0.01" />
                <div class="flex flex-col justify-end">
                    <span class="label">Total</span>
                    <span class="rounded-lg border border-ink-500/60 bg-ink-900/40 px-3 py-2 text-lg font-semibold text-brand">{{ money($formTotal) }}</span>
                </div>
            </div>
            <x-input wire:model="payment_terms" label="Condições de pagamento" />
            <x-textarea wire:model="notes" label="Observações" />

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Salvar</button>
            </div>
        </form>
    </x-modal>
</div>
