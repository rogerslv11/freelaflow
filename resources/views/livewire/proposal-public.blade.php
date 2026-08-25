<div class="card p-6 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">Proposta</p>
            <h1 class="text-2xl font-bold text-white">{{ $proposal->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $proposal->client?->name ?? '' }}</p>
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

    @if($proposal->valid_until)<p class="mt-4 text-sm text-gray-500">Válida até {{ $proposal->valid_until->format('d/m/Y') }}</p>@endif

    @if($proposal->status === 'sent')
        <div class="mt-6 flex gap-3">
            <button wire:click="accept" wire:confirm="Aceitar esta proposta?" class="btn-success">Aceitar proposta</button>
            <button wire:click="reject" wire:confirm="Rejeitar esta proposta?" class="btn-outline-danger">Rejeitar</button>
        </div>
    @else
        <p class="mt-6 rounded-lg border border-ink-500/60 bg-ink-900/30 px-4 py-3 text-sm text-gray-400">
            Esta proposta já foi <strong class="text-gray-200">{{ status_label($proposal->status) }}</strong>.
        </p>
    @endif
</div>
