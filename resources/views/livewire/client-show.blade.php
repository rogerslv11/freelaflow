<div class="space-y-6 p-4 lg:p-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('clients.index') }}" class="flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-brand">
            <x-icon name="chevron-down" class="h-4 w-4 rotate-90" /> Clientes
        </a>
        <a href="{{ route('clients.index') }}" class="btn-secondary"><x-icon name="edit" class="w-4 h-4" /> Editar</a>
    </div>

    <div class="card p-6">
        <div class="flex items-center gap-4">
            <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-ink-700 text-2xl font-bold text-brand">{{ $client->initials }}</span>
            <div class="min-w-0">
                <h1 class="text-2xl font-bold text-white">{{ $client->name }}</h1>
                <p class="text-sm text-gray-500">{{ $client->company ?? 'Cliente' }}</p>
            </div>
            <div class="ml-auto flex gap-2">
                <x-status-badge :status="$client->status" />
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div><p class="text-xs text-gray-500">E-mail</p><p class="text-sm text-gray-200">{{ $client->email ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Telefone</p><p class="text-sm text-gray-200">{{ $client->phone ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">WhatsApp</p><p class="text-sm text-gray-200">{{ $client->whatsapp ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Documento</p><p class="text-sm text-gray-200">{{ $client->document ?? '—' }}</p></div>
            <div class="col-span-2"><p class="text-xs text-gray-500">Endereço</p><p class="text-sm text-gray-200">{{ $client->address ? $client->address . ', ' . ($client->city ?? '') . '/' . ($client->state ?? '') : '—' }}</p></div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Receita total" :value="money($totalRevenue)" icon="currency" accent />
        <x-stat-card label="Faturas em aberto" :value="money($openInvoices)" icon="invoices" />
        <x-stat-card label="Projetos" :value="$projects->count()" icon="projects" />
        <x-stat-card label="Tarefas" :value="$tasks->count()" icon="tasks" />
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h3 class="mb-3 text-sm font-semibold text-white">Projetos</h3>
            @forelse($projects as $p)
                <a href="{{ route('projects.show', $p) }}" class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0 hover:text-brand">
                    <span class="text-sm text-gray-200">{{ $p->name }}</span>
                    <x-status-badge :status="$p->status" />
                </a>
            @empty
                <x-empty-state icon="projects" title="Nenhum projeto" />
            @endforelse
        </div>
        <div class="card p-5">
            <h3 class="mb-3 text-sm font-semibold text-white">Faturas</h3>
            @forelse($invoices as $inv)
                <a href="{{ route('invoices.show', $inv) }}" class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0 hover:text-brand">
                    <span class="text-sm text-gray-200">{{ $inv->number ?? ('#'.$inv->id) }}</span>
                    <span class="flex items-center gap-2"><span class="text-sm text-gray-400">{{ money($inv->total) }}</span><x-status-badge :status="$inv->status" /></span>
                </a>
            @empty
                <x-empty-state icon="invoices" title="Nenhuma fatura" />
            @endforelse
        </div>
        <div class="card p-5">
            <h3 class="mb-3 text-sm font-semibold text-white">Propostas</h3>
            @forelse($proposals as $pr)
                <a href="{{ route('proposals.show', $pr) }}" class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0 hover:text-brand">
                    <span class="text-sm text-gray-200">{{ $pr->title }}</span>
                    <x-status-badge :status="$pr->status" />
                </a>
            @empty
                <x-empty-state icon="proposals" title="Nenhuma proposta" />
            @endforelse
        </div>
        <div class="card p-5">
            <h3 class="mb-3 text-sm font-semibold text-white">Pagamentos</h3>
            @forelse($payments as $pay)
                <div class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0">
                    <span class="text-sm text-gray-200">{{ $pay->paid_at?->format('d/m/Y') }} · {{ strtoupper($pay->method) }}</span>
                    <span class="text-sm font-semibold text-green-400">{{ money($pay->amount) }}</span>
                </div>
            @empty
                <x-empty-state icon="currency" title="Nenhum pagamento" />
            @endforelse
        </div>
    </div>

    @if($client->notes)
        <div class="card p-5">
            <h3 class="mb-2 text-sm font-semibold text-white">Observações</h3>
            <p class="text-sm text-gray-400">{{ $client->notes }}</p>
        </div>
    @endif
</div>
