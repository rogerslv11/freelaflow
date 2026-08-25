<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Faturas" subtitle="Controle suas cobranças">
        <x-slot name="actions">
            <button class="btn-primary" wire:click="create"><x-icon name="plus" class="w-4 h-4" /> Nova fatura</button>
        </x-slot>
    </x-page-header>

    <div class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
            <input wire:model.live="search" type="text" placeholder="Buscar faturas..." class="input pl-9">
        </div>
        <select wire:model.live="statusFilter" class="input sm:w-44">
            <option value="">Todos os status</option>
            @foreach($statuses as $s)<option value="{{ $s }}">{{ status_label($s) }}</option>@endforeach
        </select>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-ink-500/60 bg-ink-900/50">
                    <tr><th class="th">Número</th><th class="th">Cliente</th><th class="th">Vencimento</th><th class="th">Total</th><th class="th">Status</th><th class="th text-right">Ações</th></tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($rows as $invoice)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td font-medium text-gray-200">{{ $invoice->number ?? ('#' . $invoice->id) }}</td>
                            <td class="td">{{ $invoice->client?->name ?? '—' }}</td>
                            <td class="td text-gray-400 {{ $invoice->due_date && $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-400' : '' }}">{{ $invoice->due_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="td text-gray-300">{{ money($invoice->total) }}</td>
                            <td class="td"><x-status-badge :status="$invoice->status" /></td>
                            <td class="td">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="eye" class="w-4 h-4" /></a>
                                    @if(!in_array($invoice->status, ['paid','cancelled']))
                                        <button wire:click="markPaid({{ $invoice->id }})" wire:confirm="Marcar como paga?" class="rounded-lg p-2 text-gray-400 transition hover:bg-green-500/10 hover:text-green-400" title="Marcar paga"><x-icon name="check" class="w-4 h-4" /></button>
                                        <button wire:click="send({{ $invoice->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white" title="Enviar"><x-icon name="arrow-right" class="w-4 h-4" /></button>
                                    @endif
                                    <button wire:click="duplicate({{ $invoice->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white" title="Duplicar"><x-icon name="duplicate" class="w-4 h-4" /></button>
                                    <button wire:click="edit({{ $invoice->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="edit" class="w-4 h-4" /></button>
                                    <button wire:click="delete({{ $invoice->id }})" wire:confirm="Excluir esta fatura?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="invoices" title="Nenhuma fatura" >Clique em "Nova fatura" para começar.</x-empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
    </div>

    <x-modal id="invoice-form" :title="$editingId ? 'Editar fatura' : 'Nova fatura'" size="xl">
        <form wire:submit="save" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-input wire:model="number" label="Número" />
                <x-input wire:model="issue_date" label="Emissão" type="date" />
                <x-input wire:model="due_date" label="Vencimento" type="date" />
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" required />
                <x-select wire:model="project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-select wire:model="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
            </div>

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

            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model="discount" label="Desconto" type="number" step="0.01" />
                <x-input wire:model="tax" label="Impostos" type="number" step="0.01" />
            </div>
            <x-textarea wire:model="note" label="Observações" />

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Salvar</button>
            </div>
        </form>
    </x-modal>
</div>
