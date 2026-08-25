<div class="space-y-6 p-4 lg:p-6" x-data>

    <x-page-header title="Controle de horas" subtitle="Registre o tempo trabalhado por projeto" />

    <!-- Timer -->
    <div class="card p-6">
        @if($running)
            <div x-data="{ start: {{ $runningSince }}, elapsed: 0,
                    tick() { this.elapsed = Math.max(0, Math.floor(Date.now()/1000) - this.start); },
                    fmt(s) { const h=Math.floor(s/3600); const m=Math.floor((s%3600)/60); const s2=s%60; return [h,m,s2].map(v=>String(v).padStart(2,'0')).join(':'); } }"
                 x-init="setInterval(() => tick(), 1000)"
                 class="flex flex-col items-center gap-5 sm:flex-row sm:justify-between">
                <div class="text-center sm:text-left">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Em andamento</p>
                    <p class="mt-1 text-5xl font-bold tabular-nums text-brand" x-text="fmt(elapsed)">00:00:00</p>
                    <p class="mt-1 text-sm text-gray-400">{{ $description ?: 'Sessão de trabalho' }}</p>
                </div>
                <button wire:click="stop" class="btn-danger">
                    <x-icon name="clock" class="w-4 h-4" /> Encerrar & salvar
                </button>
            </div>
        @else
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-input wire:model="description" label="O que você está fazendo?" placeholder="Ex: Desenvolvimento de layout" />
                </div>
                <div class="w-full sm:w-44">
                    <x-select wire:model="project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" />
                </div>
                <div class="w-full sm:w-44">
                    <x-select wire:model="client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" />
                </div>
                <div class="w-full sm:w-32">
                    <x-input wire:model="hourly_rate" label="Valor/h" type="number" step="0.01" />
                </div>
                <label class="flex items-center gap-2 pb-2.5 text-sm text-gray-300">
                    <input type="checkbox" wire:model="billable" class="rounded border-ink-500 bg-ink-900 text-brand focus:ring-brand"> Faturável
                </label>
                <button wire:click="start" class="btn-primary whitespace-nowrap">
                    <x-icon name="clock" class="w-4 h-4" /> Iniciar
                </button>
            </div>
        @endif
    </div>

    <!-- Totals -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <x-stat-card label="Tempo total" :value="duration_human($totals['seconds'])" icon="clock" />
        <x-stat-card label="Tempo faturável" :value="duration_human($totals['billable'])" icon="clock" accent />
        <x-stat-card label="Valor faturado" :value="money($totals['billable_amount'])" icon="currency" />
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <!-- Manual add -->
        <div class="card p-5 lg:col-span-1">
            <h3 class="mb-4 text-sm font-semibold text-white">Adicionar manualmente</h3>
            <div class="space-y-3">
                <x-input wire:model="manual_description" label="Descrição" />
                <div class="grid grid-cols-2 gap-2">
                    <x-input wire:model="manual_hours" label="Horas" type="number" min="0" step="1" />
                    <x-input wire:model="manual_minutes" label="Minutos" type="number" min="0" max="59" step="1" />
                </div>
                <x-select wire:model="manual_project_id" label="Projeto" :options="$projects->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-select wire:model="manual_client_id" label="Cliente" :options="$clients->pluck('name','id')->toArray()" placeholder="Selecione" />
                <x-input wire:model="manual_rate" label="Valor/h" type="number" step="0.01" />
                <label class="flex items-center gap-2 text-sm text-gray-300">
                    <input type="checkbox" wire:model="manual_billable" class="rounded border-ink-500 bg-ink-900 text-brand focus:ring-brand"> Faturável
                </label>
                <button wire:click="addManual" class="btn-primary w-full">Adicionar horas</button>
            </div>
        </div>

        <!-- Sessions -->
        <div class="card overflow-hidden lg:col-span-2">
            <div class="border-b border-ink-500/60 px-5 py-4 text-sm font-semibold text-white">Histórico de sessões</div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-ink-700/60">
                        @forelse($sessions as $s)
                            <tr class="transition hover:bg-ink-700/30">
                                <td class="td">
                                    <p class="text-sm font-medium text-gray-200">{{ $s->description ?? 'Sessão' }}</p>
                                    <p class="text-xs text-gray-500">{{ $s->project?->name ?? $s->client?->name ?? '—' }}</p>
                                </td>
                                <td class="td text-gray-300">{{ duration_human($s->duration) }}</td>
                                <td class="td">
                                    @if($s->billable)
                                        <span class="badge bg-brand-soft text-brand">Faturável</span>
                                    @else
                                        <span class="badge bg-ink-700 text-gray-400">Não</span>
                                    @endif
                                </td>
                                <td class="td text-gray-400">{{ $s->start_time?->format('d/m/Y') }}</td>
                                <td class="td text-right">
                                    <button wire:click="delete({{ $s->id }})" wire:confirm="Excluir sessão?" class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400"><x-icon name="trash" class="w-4 h-4" /></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty-state icon="clock" title="Sem sessões" >Inicie o timer ou adicione horas manualmente.</x-empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-500/60 px-4 py-3">{{ $sessions->links() }}</div>
        </div>
    </div>
</div>
