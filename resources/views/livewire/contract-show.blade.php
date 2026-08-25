<div class="space-y-5 p-4 lg:p-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('contracts.index') }}" class="flex items-center gap-1.5 text-sm text-gray-400 transition hover:text-brand">
            <x-icon name="chevron-down" class="h-4 w-4 rotate-90" /> Contratos
        </a>
        <div class="flex gap-2">
            @if($contract->status === 'draft')
                <button wire:click="send" class="btn-primary"><x-icon name="arrow-right" class="w-4 h-4" /> Enviar</button>
            @endif
            <a href="{{ $publicUrl }}" target="_blank" class="btn-secondary"><x-icon name="eye" class="w-4 h-4" /> Ver página pública</a>
        </div>
    </div>

    <div class="card p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $contract->title }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $contract->client?->name ?? '—' }} · {{ $contract->project?->name ?? '' }}</p>
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

        @if($contract->signed_at)
            <div class="mt-4 flex items-center gap-2 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                <x-icon name="check" class="h-4 w-4" /> Assinado em {{ $contract->signed_at->format('d/m/Y H:i') }}
                @if($contract->signed_by) · por {{ $contract->signed_by }} @endif
            </div>
        @endif
    </div>
</div>
