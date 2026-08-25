<div class="space-y-6 p-4 lg:p-6">
    <x-page-header title="Financeiro" subtitle="Visão completa das suas finanças" />

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total recebido" :value="money($paymentsTotal)" icon="currency" accent />
        <x-stat-card label="Total gasto" :value="money($expensesTotal)" icon="reports" />
        <x-stat-card label="Receita do mês" :value="money($summary['revenue_month'])" icon="finance" />
        <x-stat-card label="Lucro do mês" :value="money($summary['profit_month'])" icon="check" accent />
    </div>

    <div class="card p-5">
        <h3 class="mb-4 text-sm font-semibold text-white">Receitas x Despesas (6 meses)</h3>
        <div class="space-y-5">
            <div>
                <p class="mb-1 text-xs font-medium text-brand">Receitas</p>
                <x-charts.bar-chart :labels="$monthLabels" :values="$revenueSeries" color="#FF6B00" />
            </div>
            <div>
                <p class="mb-1 text-xs font-medium text-gray-400">Despesas</p>
                <x-charts.bar-chart :labels="$monthLabels" :values="$expenseSeries" color="#3B82F6" />
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h3 class="mb-4 text-sm font-semibold text-white">Pagamentos recentes</h3>
            @forelse($recentPayments as $p)
                <div class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $p->client?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $p->paid_at?->format('d/m/Y') }} · {{ strtoupper($p->method) }}</p>
                    </div>
                    <span class="text-sm font-semibold text-green-400">{{ money($p->amount) }}</span>
                </div>
            @empty
                <x-empty-state icon="currency" title="Sem pagamentos" >Nenhum pagamento registrado.</x-empty-state>
            @endforelse
        </div>
        <div class="card p-5">
            <h3 class="mb-4 text-sm font-semibold text-white">Despesas recentes</h3>
            @forelse($recentExpenses as $e)
                <div class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-200">{{ $e->description }}</p>
                        <p class="text-xs text-gray-500">{{ $e->category?->name }} · {{ $e->incurred_at?->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-sm font-semibold text-red-400">{{ money($e->amount) }}</span>
                </div>
            @empty
                <x-empty-state icon="reports" title="Sem despesas" >Nenhuma despesa registrada.</x-empty-state>
            @endforelse
        </div>
    </div>
</div>
