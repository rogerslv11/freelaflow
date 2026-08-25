<div class="space-y-6 p-4 lg:p-6">

    <!-- Stat cards -->
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Receita do mês" :value="money($summary['revenue_month'], $currency)" icon="currency" accent />
        <x-stat-card label="Receita pendente" :value="money($summary['pending'], $currency)" icon="clock" />
        <x-stat-card label="Despesas" :value="money($summary['expenses_month'], $currency)" icon="reports" />
        <x-stat-card label="Lucro" :value="money($summary['profit_month'], $currency)" icon="finance" accent />
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Projetos ativos" :value="$summary['active_projects']" icon="projects" />
        <x-stat-card label="Clientes ativos" :value="$summary['active_clients']" icon="clients" />
        <x-stat-card label="Faturas vencidas" :value="$summary['overdue']" icon="invoices" :accent="$summary['overdue'] > 0" />
        <div class="card card-hover flex items-center justify-center p-5">
            <a href="{{ route('reports.index') }}" class="flex flex-col items-center gap-1 text-gray-400 transition hover:text-brand">
                <x-icon name="reports" class="h-7 w-7" />
                <span class="text-sm font-medium">Ver relatórios</span>
            </a>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold text-white">Receita, despesas e lucro (6 meses)</h3>
            <x-charts.bar-chart
                :labels="$monthLabels"
                :series="[
                    ['label' => 'Receita', 'values' => $revenueSeries, 'color' => '#FF6B00'],
                    ['label' => 'Despesas', 'values' => $expenseSeries, 'color' => '#3B82F6'],
                    ['label' => 'Lucro', 'values' => $profitSeries, 'color' => '#10B981'],
                ]"
                :format="fn($v) => money($v, $currency)"
                height="230"
            />
        </div>

        <div class="card p-5">
            <h3 class="mb-4 text-sm font-semibold text-white">Projetos por status</h3>
            @if(count($projectsByStatus))
                <x-charts.donut-chart :data="$projectsByStatus" />
            @else
                <x-empty-state icon="projects" title="Sem projetos" >Crie projetos para visualizar o status.</x-empty-state>
            @endif
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <h3 class="mb-4 text-sm font-semibold text-white">Receita por cliente</h3>
            @if(count($revenueByClient))
                <x-charts.donut-chart :data="$revenueByClient" />
            @else
                <x-empty-state icon="clients" title="Sem receita" >As receitas por cliente aparecerão aqui.</x-empty-state>
            @endif
        </div>

        <div class="card p-5">
            <h3 class="mb-4 text-sm font-semibold text-white">Tarefas por status</h3>
            @if(count($tasksByStatus))
                <x-charts.donut-chart :data="$tasksByStatus" />
            @else
                <x-empty-state icon="tasks" title="Sem tarefas" >Nenhuma tarefa cadastrada.</x-empty-state>
            @endif
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <!-- Upcoming invoices -->
        <div class="card p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Próximos vencimentos</h3>
                <a href="{{ route('invoices.index') }}" class="text-xs font-medium text-brand hover:underline">Ver todas</a>
            </div>
            @forelse($upcomingInvoices as $inv)
                <a href="{{ route('invoices.show', $inv) }}" class="flex items-center gap-3 border-b border-ink-700/60 py-2.5 last:border-0 hover:text-brand">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-ink-700 text-xs font-semibold text-brand">
                        {{ $inv->client?->initials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-200">{{ $inv->client?->name }}</p>
                        <p class="text-xs text-gray-500">{{ $inv->number ?? 'Fatura #'.$inv->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-white">{{ money($inv->total, $currency) }}</p>
                        <p class="text-xs {{ $inv->due_date && $inv->due_date->isPast() ? 'text-red-400' : 'text-gray-500' }}">{{ $inv->due_date?->format('d/m') }}</p>
                    </div>
                </a>
            @empty
                <x-empty-state icon="invoices" title="Nada pendente" >Não há faturas pendentes.</x-empty-state>
            @endforelse
        </div>

        <!-- Due tasks -->
        <div class="card p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white">Tarefas a vencer</h3>
                <a href="{{ route('tasks.index') }}" class="text-xs font-medium text-brand hover:underline">Ver todas</a>
            </div>
            @forelse($dueTasks as $task)
                <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 border-b border-ink-700/60 py-2.5 last:border-0 hover:text-brand">
                    <span class="h-2 w-2 shrink-0 rounded-full {{ priority_dot_class($task->priority) }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-200">{{ $task->title }}</p>
                        <p class="text-xs text-gray-500">{{ $task->project?->name ?? 'Sem projeto' }}</p>
                    </div>
                    <span class="text-xs {{ $task->due_date && $task->due_date->isPast() ? 'text-red-400' : 'text-gray-500' }}">{{ $task->due_date?->format('d/m') }}</span>
                </a>
            @empty
                <x-empty-state icon="tasks" title="Sem tarefas" >Nenhuma tarefa próxima do vencimento.</x-empty-state>
            @endforelse
        </div>
    </div>

    <!-- Recent projects -->
    <div class="card p-5">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">Projetos recentes</h3>
            <a href="{{ route('projects.index') }}" class="text-xs font-medium text-brand hover:underline">Ver todos</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($recentProjects as $project)
                <a href="{{ route('projects.show', $project) }}" class="rounded-lg border border-ink-500/60 bg-ink-900/40 p-4 transition hover:border-brand/50">
                    <div class="flex items-center justify-between">
                        <p class="truncate text-sm font-semibold text-white">{{ $project->name }}</p>
                        <x-status-badge :status="$project->status" />
                    </div>
                    <p class="mt-1 truncate text-xs text-gray-500">{{ $project->client?->name }}</p>
                    <div class="mt-3">
                        <div class="mb-1 flex justify-between text-xs text-gray-500">
                            <span>Progresso</span>
                            <span>{{ $project->progress }}%</span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-ink-600">
                            <div class="h-full rounded-full bg-brand" style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <x-empty-state icon="projects" title="Nenhum projeto" >Crie seu primeiro projeto.</x-empty-state>
                </div>
            @endforelse
        </div>
    </div>
</div>
