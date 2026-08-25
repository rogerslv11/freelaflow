<div class="space-y-5 p-4 lg:p-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('proposals.index') }}" class="flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-brand">
            <x-icon name="chevron-down" class="h-4 w-4 rotate-90" /> Propostas
        </a>
        <div class="flex gap-2">
            @if($proposal->status === 'draft')
                <button wire:click="send" class="btn-primary"><x-icon name="arrow-right" class="w-4 h-4" /> Enviar</button>
            @endif
            <a href="{{ $publicUrl }}" target="_blank" class="btn-secondary"><x-icon name="eye" class="w-4 h-4" /> Ver página pública</a>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $proposal->title }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $proposal->client?->name ?? '—' }}</p>
            </div>
            <x-status-badge :status="$proposal->status" />
        </div>

        @if($proposal->description)<p class="mt-4 text-sm text-gray-400">{{ $proposal->description }}</p>@endif

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
                    <tr><td colspan="3" class="px-4 py-2 text-right text-gray-400">Subtotal</td><td class="px-4 py-2 text-right text-gray-200">{{ money($proposal->items->sum('total')) }}</td></tr>
                    <tr><td colspan="3" class="px-4 py-2 text-right text-gray-400">Desconto</td><td class="px-4 py-2 text-right text-gray-200">- {{ money($proposal->discount) }}</td></tr>
                    <tr><td colspan="3" class="px-4 py-2 text-right text-gray-400">Impostos</td><td class="px-4 py-2 text-right text-gray-200">{{ money($proposal->tax) }}</td></tr>
                    <tr><td colspan="3" class="px-4 py-2 text-right font-semibold text-white">Total</td><td class="px-4 py-2 text-right font-semibold text-brand">{{ money($proposal->total) }}</td></tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div><p class="text-xs text-gray-500">Validade</p><p class="text-sm text-gray-200">{{ $proposal->valid_until?->format('d/m/Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500">Condições de pagamento</p><p class="text-sm text-gray-200">{{ $proposal->payment_terms ?? '—' }}</p></div>
        </div>
        @if($proposal->notes)<p class="mt-4 text-sm text-gray-400">{{ $proposal->notes }}</p>@endif
    </div>
</div>
