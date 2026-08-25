<div class="space-y-5 p-4 lg:p-6" x-data>

    <x-page-header title="Tarefas" subtitle="Organize seu trabalho">
        <x-slot name="actions">
            <div class="flex items-center gap-1 rounded-lg bg-ink-800 p-1">
                <button @click="$wire.setView('list')" @class(['rounded-md px-3 py-1.5 text-sm font-medium transition', 'bg-ink-600 text-white' => $view === 'list', 'text-gray-400 hover:text-white' => $view !== 'list'])>Lista</button>
                <button @click="$wire.setView('kanban')" @class(['rounded-md px-3 py-1.5 text-sm font-medium transition', 'bg-ink-600 text-white' => $view === 'kanban', 'text-gray-400 hover:text-white' => $view !== 'kanban'])>Kanban</button>
            </div>
            <button class="btn-primary" wire:click="create">
                <x-icon name="plus" class="w-4 h-4" /> Nova tarefa
            </button>
        </x-slot>
    </x-page-header>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-stat-card label="Total" :value="$stats['total']" icon="tasks" />
        <x-stat-card label="Em andamento" :value="$stats['in_progress']" icon="clock" />
        <x-stat-card label="Concluídas" :value="$stats['done']" icon="check" accent />
        <x-stat-card label="Atrasadas" :value="$stats['overdue']" icon="alert" :accent="$stats['overdue'] > 0 ? 'red' : null" />
    </div>

    <div class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"><x-icon name="search" class="w-4 h-4" /></span>
            <input wire:model.live="search" type="text" placeholder="Buscar tarefas..." class="input pl-9">
        </div>
        <select wire:model.live="statusFilter" class="input sm:w-40">
            <option value="">Todos status</option>
            @foreach($statuses as $s)<option value="{{ $s }}">{{ status_label($s) }}</option>@endforeach
        </select>
        <select wire:model.live="priorityFilter" class="input sm:w-40">
            <option value="">Todas prioridades</option>
            @foreach($priorities as $p)<option value="{{ $p }}">{{ status_label($p) }}</option>@endforeach
        </select>
    </div>

    @if($view === 'list')
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-ink-500/60 bg-ink-900/50">
                        <tr>
                            <th class="th cursor-pointer select-none" wire:click="sortBy('title')">Tarefa @if($sortField === 'title')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                            <th class="th">Projeto</th>
                            <th class="th cursor-pointer select-none" wire:click="sortBy('priority')">Prioridade @if($sortField === 'priority')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                            <th class="th cursor-pointer select-none" wire:click="sortBy('status')">Status @if($sortField === 'status')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                            <th class="th cursor-pointer select-none" wire:click="sortBy('due_date')">Vencimento @if($sortField === 'due_date')<span class="text-brand">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>@endif</th>
                            <th class="th text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-700/60">
                        @forelse($rows as $task)
                            <tr class="transition hover:bg-ink-700/30">
                                <td class="td">
                                    <div class="font-medium text-gray-200">{{ $task->title }}</div>
                                    @if($task->assignee)<div class="text-xs text-gray-500">{{ $task->assignee }}</div>@endif
                                </td>
                                <td class="td">{{ $task->project?->name ?? '—' }}</td>
                                <td class="td"><span class="badge {{ priority_badge_class($task->priority) }}">{{ status_label($task->priority) }}</span></td>
                                <td class="td"><x-status-badge :status="$task->status" /></td>
                                <td class="td @if($task->due_date && $task->due_date->isPast() && $task->status !== 'done') text-red-400 font-medium @else text-gray-400 @endif">{{ $task->due_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="td">
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $task->id }})" class="rounded-lg p-2 text-gray-400 transition hover:bg-ink-700 hover:text-white"><x-icon name="edit" class="w-4 h-4" /></button>
                                        <button wire:click="delete({{ $task->id }})" wire:confirm="Excluir esta tarefa?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-empty-state icon="tasks" title="Nenhuma tarefa" >Clique em "Nova tarefa" para começar.</x-empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-500/60 px-4 py-3">{{ $rows->links() }}</div>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-4" x-data="{ dragging: null }">
            @foreach($board as $status => $cards)
                <div class="card flex max-h-[72vh] flex-col p-3"
                     @dragover.prevent
                     @drop.prevent="$wire.call('moveTask', dragging, '{{ $status }}')">
                    <div class="mb-3 flex items-center justify-between px-1">
                        <span class="text-sm font-semibold text-gray-200">{{ status_label($status) }}</span>
                        <div class="flex items-center gap-1">
                            <span class="badge bg-ink-700 text-gray-400">{{ $cards->count() }}</span>
                            <button wire:click="createFor('{{ $status }}')" class="rounded p-1 text-gray-500 transition hover:bg-ink-700 hover:text-brand" title="Nova tarefa nesta coluna"><x-icon name="plus" class="h-3.5 w-3.5" /></button>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col gap-2 overflow-y-auto">
                        @forelse($cards as $task)
                            <div draggable="true" @dragstart="dragging = {{ $task->id }}" @dragend="dragging = null"
                                 class="cursor-grab rounded-lg border border-ink-500/60 bg-ink-900/60 p-3 transition hover:border-brand/50 active:cursor-grabbing">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-200">{{ $task->title }}</p>
                                    <span class="h-2 w-2 shrink-0 rounded-full {{ priority_dot_class($task->priority) }}"></span>
                                </div>
                                @if($task->project)<p class="mt-1 text-xs text-gray-500">{{ $task->project->name }}</p>@endif
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $task->due_date?->format('d/m') ?? '—' }}</span>
                                    @if($task->estimated_hours)<span>{{ $task->logged_hours }}h / {{ $task->estimated_hours }}h</span>@endif
                                </div>
                            </div>
                        @empty
                            <p class="rounded-lg border border-dashed border-ink-500/60 px-3 py-6 text-center text-xs text-gray-600">Arraste tarefas para cá</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-modal id="task-form" :title="$editingId ? 'Editar tarefa' : 'Nova tarefa'" size="lg">
        <form wire:submit="save" class="space-y-4">
            <x-input wire:model="title" label="Título" required />
            <x-textarea wire:model="description" label="Descrição" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-select wire:model="project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-input wire:model="assignee" label="Responsável" />
                <x-input wire:model="due_date" label="Vencimento" type="date" />
                <x-select wire:model="priority" label="Prioridade" :options="collect($priorities)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
                <x-select wire:model="status" label="Status" :options="collect($statuses)->mapWithKeys(fn($s)=>[$s=>status_label($s)])->toArray()" required />
                <x-input wire:model="estimated_hours" label="Horas estimadas" type="number" step="0.5" />
                <x-input wire:model="logged_hours" label="Horas trabalhadas" type="number" step="0.5" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="$store.modal.close()" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled"><span wire:loading class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Salvar</button>
            </div>
        </form>
    </x-modal>
</div>
