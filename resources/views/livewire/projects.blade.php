<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Projetos" subtitle="Acompanhe o progresso dos seus trabalhos">
        <x-slot name="actions">
            <button class="btn-primary" wire:click="create">
                <x-icon name="plus" class="w-4 h-4" /> Novo projeto
            </button>
        </x-slot>
    </x-page-header>

    <div class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                <x-icon name="search" class="w-4 h-4" />
            </span>
            <input wire:model.live="search" type="text" placeholder="Buscar projetos..." class="input pl-9">
        </div>
        <select wire:model.live="statusFilter" class="input sm:w-44">
            <option value="">Todos os status</option>
            @foreach($statuses as $s)
                <option value="{{ $s }}">{{ status_label($s) }}</option>
            @endforeach
        </select>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-ink-500/60 bg-ink-900/50">
                    <tr>
                        <th class="th">Projeto</th>
                        <th class="th">Cliente</th>
                        <th class="th">Status</th>
                        <th class="th">Prioridade</th>
                        <th class="th">Valor</th>
                        <th class="th">Progresso</th>
                        <th class="th text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($rows as $project)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td">
                                <a href="{{ route('projects.show', $project) }}" class="font-medium text-gray-200 hover:text-brand">{{ $project->name }}</a>
                            </td>
                            <td class="td">{{ $project->client?->name ?? '—' }}</td>
                            <td class="td"><x-status-badge :status="$project->status" /></td>
                            <td class="td"><span class="badge {{ priority_badge_class($project->priority) }}">{{ status_label($project->priority) }}</span></td>
                            <td class="td text-gray-300">{{ money($project->value) }}</td>
                            <td class="td">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-20 overflow-hidden rounded-full bg-ink-600">
                                        <div class="h-full rounded-full bg-brand" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td class="td">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('projects.show', $project) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="eye" class="w-4 h-4" /></a>
                                    <button wire:click="edit({{ $project->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="edit" class="w-4 h-4" /></button>
                                    <button wire:click="delete({{ $project->id }})" wire:confirm="Excluir este projeto?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty-state icon="projects" title="Nenhum projeto" >Clique em "Novo projeto" para começar.</x-empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
    </div>

    <x-modal id="project-form" :title="$editingId ? 'Editar projeto' : 'Novo projeto'" size="lg">
        <form wire:submit="save" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input wire:model="name" label="Nome" required />
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" required />
                <x-input wire:model="start_date" label="Início" type="date" />
                <x-input wire:model="due_date" label="Entrega" type="date" />
                <x-input wire:model="value" label="Valor (R$)" type="number" step="0.01" />
                <x-input wire:model="progress" label="Progresso (%)" type="number" min="0" max="100" />
                <x-select wire:model="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
                <x-select wire:model="priority" label="Prioridade" :options="collect($priorities)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
            </div>
            <x-textarea wire:model="description" label="Descrição" />
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                    <span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Salvar
                </button>
            </div>
        </form>
    </x-modal>
</div>
