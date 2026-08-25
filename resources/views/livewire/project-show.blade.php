<div class="space-y-6 p-4 lg:p-6" x-data="{ tab: 'overview' }">

    <div class="flex items-center justify-between">
        <a href="{{ route('projects.index') }}" class="flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-brand">
            <x-icon name="chevron-down" class="h-4 w-4 rotate-90" /> Projetos
        </a>
        <a href="{{ route('projects.index') }}" class="btn-secondary"><x-icon name="edit" class="w-4 h-4" /> Editar</a>
    </div>

    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $project->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $project->client?->name ?? '—' }}</p>
            </div>
            <div class="flex gap-2">
                <x-status-badge :status="$project->status" />
                <span class="badge {{ priority_badge_class($project->priority) }}">{{ status_label($project->priority) }}</span>
            </div>
        </div>
        <div class="mt-6">
            <div class="mb-1 flex justify-between text-xs text-gray-500"><span>Progresso</span><span>{{ $project->progress }}%</span></div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-ink-600">
                <div class="h-full rounded-full bg-brand" style="width: {{ $project->progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-1 border-b border-ink-500/60">
        @foreach(['overview' => 'Visão geral', 'tasks' => 'Tarefas', 'invoices' => 'Faturas', 'payments' => 'Pagamentos', 'files' => 'Arquivos', 'notes' => 'Notas'] as $key => $label)
            <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'border-brand text-brand' : 'border-transparent text-gray-400 hover:text-gray-200'" class="border-b-2 px-4 py-2.5 text-sm font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    <div x-show="tab === 'overview'" class="grid gap-4 lg:grid-cols-4">
        <x-stat-card label="Valor" :value="money($project->value)" icon="currency" accent />
        <x-stat-card label="Tempo registrado" :value="duration_human($loggedSeconds)" icon="clock" />
        <x-stat-card label="Tempo faturável" :value="duration_human($billedSeconds)" icon="clock" accent />
        <x-stat-card label="Tarefas" :value="$tasks->count()" icon="tasks" />
        <div class="card p-5 lg:col-span-2">
            <h3 class="mb-2 text-sm font-semibold text-white">Detalhes</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-xs text-gray-500">Início</p><p class="text-gray-200">{{ $project->start_date?->format('d/m/Y') ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500">Entrega</p><p class="text-gray-200">{{ $project->due_date?->format('d/m/Y') ?? '—' }}</p></div>
            </div>
            @if($project->description)<p class="mt-3 text-sm text-gray-400">{{ $project->description }}</p>@endif
        </div>
        <div class="card p-5 lg:col-span-2">
            <h3 class="mb-2 text-sm font-semibold text-white">Equipe</h3>
            @forelse($members as $m)
                <div class="flex items-center gap-2 border-b border-ink-700/60 py-2 last:border-0">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-ink-700 text-xs text-brand">{{ mb_substr($m->name,0,1) }}</span>
                    <span class="text-sm text-gray-200">{{ $m->name }}</span>
                    <span class="ml-auto text-xs text-gray-500">{{ $m->role }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Nenhum membro.</p>
            @endforelse
        </div>
    </div>

    <div x-show="tab === 'tasks'" class="card overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full">
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($tasks as $t)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td"><span class="h-2 w-2 mr-2 inline-block rounded-full {{ priority_dot_class($t->priority) }}"></span>{{ $t->title }}</td>
                            <td class="td"><x-status-badge :status="$t->status" /></td>
                            <td class="td text-gray-400">{{ $t->due_date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td><x-empty-state icon="tasks" title="Nenhuma tarefa" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="tab === 'invoices'" class="card overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full">
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($invoices as $inv)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td">{{ $inv->number ?? ('#'.$inv->id) }}</td>
                            <td class="td text-gray-300">{{ money($inv->total) }}</td>
                            <td class="td"><x-status-badge :status="$inv->status" /></td>
                        </tr>
                    @empty
                        <tr><td><x-empty-state icon="invoices" title="Nenhuma fatura" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="tab === 'payments'" class="card overflow-hidden" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full">
                <tbody class="divide-y divide-ink-700/60">
                    @forelse($payments as $p)
                        <tr class="transition hover:bg-ink-700/30">
                            <td class="td">{{ $p->paid_at?->format('d/m/Y') }}</td>
                            <td class="td uppercase">{{ $p->method }}</td>
                            <td class="td text-green-400">{{ money($p->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td><x-empty-state icon="currency" title="Nenhum pagamento" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="tab === 'files'" class="card overflow-hidden" x-cloak>
        <table class="w-full">
            <tbody class="divide-y divide-ink-700/60">
                @forelse($files as $f)
                    <tr class="transition hover:bg-ink-700/30">
                        <td class="td">{{ $f->original_name }}</td>
                        <td class="td text-gray-400">{{ $f->human_size }}</td>
                        <td class="td text-right"><a href="{{ route('files.download', $f) }}" class="rounded-lg p-2 text-gray-400 hover:text-brand"><x-icon name="arrow-right" class="w-4 h-4 rotate-90" /></a></td>
                    </tr>
                @empty
                    <tr><td><x-empty-state icon="files" title="Nenhum arquivo" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="tab === 'notes'" class="card p-5" x-cloak>
        <h3 class="mb-2 text-sm font-semibold text-white">Notas</h3>
        <p class="text-sm text-gray-400">{{ $project->description ?? 'Sem anotações.' }}</p>
    </div>
</div>
