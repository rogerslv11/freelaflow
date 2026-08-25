<div class="space-y-5 p-4 lg:p-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-brand">
            <x-icon name="chevron-down" class="h-4 w-4 rotate-90" /> Faturas
        </a>
        <div class="flex gap-2">
            @if($invoice->status === 'draft')
                <button wire:click="send" class="btn-primary"><x-icon name="arrow-right" class="w-4 h-4" /> Enviar</button>
            @endif
            @if(! in_array($invoice->status, ['paid', 'cancelled']))
                <button wire:click="markPaid" wire:confirm="Marcar como paga?" class="btn-success"><x-icon name="check" class="w-4 h-4" /> Marcar paga</button>
            @endif
            <a href="{{ $publicUrl }}" target="_blank" class="btn-secondary"><x-icon name="eye" class="w-4 h-4" /> Ver página pública</a>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $invoice->number ?? ('Fatura #' . $invoice->id) }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $invoice->client?->name ?? '—' }} · Vencimento {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <x-status-badge :status="$invoice->status" />
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-ink-500/60">
            <table class="w-full text-sm">
                <thead class="bg-ink-900/50 text-left text-xs uppercase text-gray-500">
                    <tr><th class="px-4 py-3">Item</th><th class="px-4 py-3 text-right">Qtd</th><th class="px-4 py-3 text-right">Preço</th><th class="px-4 py-3 text-right">Total</th></tr>
                </thead>
                <tbody class="divide-y divide-ink-700/60">
                    @foreach($items as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-200">{{ $item->description }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">{{ money($item->unit_price) }}</td>
                            <td class="px-4 py-3 text-right text-gray-200">{{ money($item->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-ink-900/50">
                    <tr><td colspan="3" class="px-4 py-2 text-right text-gray-400">Subtotal</td><td class="px-4 py-2 text-right text-gray-200">{{ money($invoice->items->sum('total')) }}</td></tr>
                    <tr><td colspan="3" class="px-4 py-2 text-right text-gray-400">Desconto</td><td class="px-4 py-2 text-right text-gray-200">- {{ money($invoice->discount) }}</td></tr>
                    <tr><td colspan="3" class="px-4 py-2 text-right text-gray-400">Impostos</td><td class="px-4 py-2 text-right text-gray-200">{{ money($invoice->tax) }}</td></tr>
                    <tr><td colspan="3" class="px-4 py-2 text-right font-semibold text-white">Total</td><td class="px-4 py-2 text-right font-semibold text-brand">{{ money($invoice->total) }}</td></tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-gray-500">Emitida em</p><p class="text-sm text-gray-200">{{ $invoice->issued_at?->format('d/m/Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Paga em</p><p class="text-sm text-gray-200">{{ $invoice->paid_at?->format('d/m/Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Saldo</p><p class="text-sm text-brand">{{ money(max($balance, 0)) }}</p></div>
        </div>
    </div>

    <div class="card p-5">
        <h3 class="mb-3 text-sm font-semibold text-white">Pagamentos</h3>
        @forelse($payments as $p)
            <div class="flex items-center justify-between border-b border-ink-700/60 py-2.5 last:border-0">
                <span class="text-sm text-gray-200">{{ $p->paid_at?->format('d/m/Y') }} · {{ strtoupper($p->method) }}</span>
                <span class="text-sm font-semibold text-green-400">{{ money($p->amount) }}</span>
            </div>
        @empty
            <x-empty-state icon="currency" title="Nenhum pagamento registrado" />
        @endforelse
    </div>
</div>
