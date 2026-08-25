<div class="card p-6 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">Contrato</p>
            <h1 class="text-2xl font-bold text-white">{{ $contract->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $contract->client?->name ?? '' }}</p>
        </div>
        <x-status-badge :status="$contract->status" />
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div><p class="text-xs text-gray-500">Início</p><p class="text-sm text-gray-200">{{ $contract->start_date?->format('d/m/Y') ?? '—' }}</p></div>
        <div><p class="text-xs text-gray-500">Término</p><p class="text-sm text-gray-200">{{ $contract->end_date?->format('d/m/Y') ?? '—' }}</p></div>
        <div><p class="text-xs text-gray-500">Valor</p><p class="text-sm text-gray-200">{{ money($contract->value) }}</p></div>
    </div>

    @if($contract->content)
        <div class="mt-6 rounded-lg border border-ink-500/60 bg-ink-900/30 p-4 text-sm leading-relaxed text-gray-300">
            {!! nl2br(e($contract->content)) !!}
        </div>
    @endif

    @if($contract->status === 'sent')
        <form wire:submit="sign" class="mt-6 rounded-lg border border-ink-500/60 p-4">
            <label class="mb-1 block text-sm text-gray-300">Assinar como</label>
            <div class="flex gap-3">
                <input wire:model="signer_name" type="text" placeholder="Seu nome completo" class="input flex-1">
                <button type="submit" class="btn-success">Assinar contrato</button>
            </div>
            @error('signer_name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
        </form>
    @elseif($contract->signed_at)
        <div class="mt-6 flex items-center gap-2 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400">
            <x-icon name="check" class="h-4 w-4" /> Assinado em {{ $contract->signed_at->format('d/m/Y H:i') }}
            @if($contract->signed_by) · por {{ $contract->signed_by }} @endif
        </div>
    @endif
</div>
