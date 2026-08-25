<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Despesas" subtitle="Controle seus gastos">
        <x-slot name="actions">
            <button class="btn-primary" wire:click="create"><x-icon name="plus" class="w-4 h-4" /> Registrar despesa</button>
        </x-slot>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-ink-500/60 p-4">
            <div class="relative max-w-md">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
                <input wire:model.live="search" type="text" placeholder="Buscar despesas..." class="input pl-9">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-ink-500/60 bg-ink-900/50">
                    <tr><th class="th">Descrição</th><th class="th">Categoria</th><th class="th">Projeto</th><th class="th">Data</th><th class="th">Valor</th><th class="th text-right">Ações</th></tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($rows as $e)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td font-medium text-gray-200">{{ $e->description }}</td>
                            <td class="td"><span class="badge bg-ink-700 text-gray-300">{{ $e->category?->name ?? '—' }}</span></td>
                            <td class="td text-gray-400">{{ $e->project?->name ?? '—' }}</td>
                            <td class="td text-gray-400">{{ $e->incurred_at?->format('d/m/Y') }}</td>
                            <td class="td text-red-400">{{ money($e->amount) }}</td>
                            <td class="td">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="edit({{ $e->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="edit" class="w-4 h-4" /></button>
                                    <button wire:click="delete({{ $e->id }})" wire:confirm="Excluir esta despesa?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="reports" title="Nenhuma despesa" >Registre suas despesas.</x-empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
    </div>

    <x-modal id="expense-form" :title="$editingId ? 'Editar despesa' : 'Registrar despesa'" size="lg">
        <form wire:submit="save" class="space-y-4">
            <x-input wire:model="description" label="Descrição" required />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-select wire:model="category_id" label="Categoria" :options="$categories->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-input wire:model="amount" label="Valor" type="number" step="0.01" required />
                <x-select wire:model="project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-input wire:model="incurred_at" label="Data" type="date" class="sm:col-span-2" />
            </div>
            <x-textarea wire:model="note" label="Observação" />
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Salvar</button>
            </div>
        </form>
    </x-modal>
</div>
