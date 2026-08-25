<div class="card p-6 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">Fatura</p>
            <h1 class="text-2xl font-bold text-white">{{ $invoice->number ?? ('Fatura #' . $invoice->id) }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $invoice->client?->name ?? '' }}</p>
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

    <div class="mt-4 flex flex-wrap justify-between text-sm text-gray-500">
        <span>Emitida em {{ $invoice->issued_at?->format('d/m/Y') ?? '—' }}</span>
        <span>Vencimento {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}</span>
    </div>

    @if($invoice->status !== 'paid')
        <div class="mt-6 rounded-lg border border-brand/30 bg-brand/10 px-4 py-3 text-sm text-brand">
            Valor em aberto: <strong>{{ money(max($invoice->total - $invoice->payments()->sum('amount'), 0)) }}</strong>.
            O pagamento deve ser realizado conforme combinado com o prestador.
        </div>
    @else
        <div class="mt-6 flex items-center gap-2 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400">
            <x-icon name="check" class="h-4 w-4" /> Fatura paga. Obrigado!
        </div>
    @endif
</div>
