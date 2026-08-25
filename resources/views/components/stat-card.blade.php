@props(['label', 'value', 'icon' => 'currency', 'trend' => null, 'trendUp' => true, 'accent' => false])

<div class="card card-hover p-5">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-gray-400">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-white">{{ $value }}</p>
        </div>
        <div @class([
            'flex h-10 w-10 items-center justify-center rounded-xl',
            'bg-brand-soft text-brand' => $accent,
            'bg-ink-700 text-gray-400' => !$accent,
        ])>
            <x-icon :name="$icon" class="w-5 h-5" />
        </div>
    </div>
    @if($trend)
        <p class="mt-3 flex items-center gap-1 text-xs {{ $trendUp ? 'text-green-400' : 'text-red-400' }}">
            <x-icon :name="$trendUp ? 'arrow-right' : 'arrow-right'" class="h-3 w-3 {{ $trendUp ? '' : 'rotate-90' }}" />
            {{ $trend }}
        </p>
    @endif
</div>
