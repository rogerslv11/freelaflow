<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Contratos" subtitle="Gerencie seus contratos">
        <x-slot name="actions">
            <button class="btn-primary" wire:click="create"><x-icon name="plus" class="w-4 h-4" /> Novo contrato</button>
        </x-slot>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-ink-500/60 p-4">
            <div class="relative max-w-md">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
                <input wire:model.live="search" type="text" placeholder="Buscar contratos..." class="input pl-9">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-ink-500/60 bg-ink-900/50">
                    <tr><th class="th">Título</th><th class="th">Cliente</th><th class="th">Valor</th><th class="th">Vigência</th><th class="th">Status</th><th class="th text-right">Ações</th></tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($rows as $contract)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td font-medium text-gray-200">{{ $contract->title }}</td>
                            <td class="td">{{ $contract->client?->name ?? '—' }}</td>
                            <td class="td text-gray-300">{{ money($contract->value) }}</td>
                            <td class="td text-gray-400">{{ $contract->start_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="td"><x-status-badge :status="$contract->status" /></td>
                            <td class="td">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('contracts.show', $contract) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="eye" class="w-4 h-4" /></a>
                                    <button wire:click="edit({{ $contract->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="edit" class="w-4 h-4" /></button>
                                    <button wire:click="delete({{ $contract->id }})" wire:confirm="Excluir este contrato?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="contracts" title="Nenhum contrato" >Clique em "Novo contrato" para começar.</x-empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
    </div>

    <x-modal id="contract-form" :title="$editingId ? 'Editar contrato' : 'Novo contrato'" size="lg">
        <form wire:submit="save" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model="title" label="Título" required />
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" required />
                <x-select wire:model="project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-input wire:model="value" label="Valor" type="number" step="0.01" />
                <x-input wire:model="start_date" label="Início" type="date" />
                <x-input wire:model="end_date" label="Término" type="date" />
                <x-select wire:model="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required class="sm:col-span-2" />
            </div>
            <x-textarea wire:model="description" label="Descrição" />
            <x-textarea wire:model="terms" label="Termos e condições" rows="6" />
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Salvar</button>
            </div>
        </form>
    </x-modal>
</div>
