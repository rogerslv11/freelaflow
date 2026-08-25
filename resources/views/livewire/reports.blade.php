<div class="space-y-6 p-4 lg:p-6" x-data>

    <x-page-header title="Relatórios" subtitle="Acompanhe a performance do seu negócio">
        <x-slot name="actions">
            <button wire:click="exportCsv" class="btn-secondary"><x-icon name="arrow-right" class="w-4 h-4" /> Exportar CSV</button>
        </x-slot>
    </x-page-header>

    <div class="card flex flex-col gap-3 p-4 sm:flex-row sm:items-end">
        <div>
            <label class="label">Período</label>
            <select wire:model.live="period" class="input sm:w-48">
                <option value="today">Hoje</option>
                <option value="week">Semana</option>
                <option value="month">Mês</option>
                <option value="year">Ano</option>
                <option value="custom">Personalizado</option>
            </select>
        </div>
        @if($period === 'custom')
            <x-input wire:model.live="startDate" label="Início" type="date" />
            <x-input wire:model.live="endDate" label="Fim" type="date" />
        @endif
        <div class="hidden text-sm text-gray-500 sm:block">
            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Receita" :value="money($stats['revenue'])" icon="currency" accent />
        <x-stat-card label="Despesas" :value="money($stats['expenses'])" icon="reports" />
        <x-stat-card label="Lucro" :value="money($stats['profit'])" icon="check" accent />
        <x-stat-card label="Margem" :value="$stats['margin'] . '%'" icon="reports" />
        <x-stat-card label="Horas" :value="round($stats['hours']/3600, 1) . 'h'" icon="clock" />
        <x-stat-card label="Faturas emitidas" :value="$stats['invoices']" icon="invoices" />
        <x-stat-card label="Faturas pagas" :value="$stats['paid_invoices']" icon="check" />
        <x-stat-card label="Novos clientes" :value="$stats['new_clients']" icon="clients" />
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl border border-[#272727] bg-[#111111] p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-200">Receita vs. Despesas (6 meses)</h3>
            @php
                $trendTotal = array_sum(array_map(fn($s) => array_sum($s['values']), $trend['series']));
            @endphp
            @if($trendTotal > 0)
                <x-charts.bar-chart :labels="$trend['labels']" :series="$trend['series']" :format="fn($v) => money($v)" height="220" />
            @else
                <x-empty-state title="Sem dados" description="Não há movimentações nos últimos 6 meses." />
            @endif
        </div>

        <div class="rounded-xl border border-[#272727] bg-[#111111] p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-200">Faturas por status</h3>
            @if(count($invoiceStatus) > 0)
                <x-charts.donut-chart :data="$invoiceStatus" size="140" />
            @else
                <x-empty-state title="Sem faturas" description="Nenhuma fatura no período." />
            @endif
        </div>
    </div>

    <div class="rounded-xl border border-[#272727] bg-[#111111] p-5">
        <h3 class="mb-4 text-sm font-semibold text-gray-200">Top clientes por receita</h3>
        @if(count($topClients) > 0)
            <div class="space-y-3">
                @foreach($topClients as $client)
                    @php
                        $max = max(array_column($topClients, 'value')) ?: 1;
                        $pct = $max > 0 ? ($client['value'] / $max) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-32 truncate text-sm text-gray-300">{{ $client['name'] }}</span>
                        <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-[#272727]">
                            <div class="h-full rounded-full bg-[#FF6B00]" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-24 text-right text-sm font-medium text-gray-200">{{ money($client['value']) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <x-empty-state title="Sem receita" description="Nenhum pagamento registrado no período." />
        @endif
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-[#272727] bg-[#111111] p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-200">Despesas por categoria</h3>
            @if(count($expenseByCategory) > 0)
                <x-charts.donut-chart :data="$expenseByCategory" size="140" />
            @else
                <x-empty-state title="Sem despesas" description="Nenhuma despesa no período." />
            @endif
        </div>

        <div class="rounded-xl border border-[#272727] bg-[#111111] p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-200">Tarefas por status</h3>
            @if(count($tasksByStatus) > 0)
                <x-charts.donut-chart :data="$tasksByStatus" size="140" />
            @else
                <x-empty-state title="Sem tarefas" />
            @endif
        </div>

        <div class="rounded-xl border border-[#272727] bg-[#111111] p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-200">Horas registradas (6 meses)</h3>
            @if(array_sum($hoursTrend['values']) > 0)
                <x-charts.bar-chart :labels="$hoursTrend['labels']" :values="$hoursTrend['values']" color="#A855F7" height="180" />
            @else
                <x-empty-state title="Sem horas" description="Nenhum registro de tempo no período." />
            @endif
        </div>
    </div>

    <div class="card p-6 text-center">
        <p class="text-sm text-gray-500">Use "Exportar CSV" para baixar os dados deste período em planilha.</p>
    </div>
</div>
