@props(['type' => 'info', 'dismissible' => false])

@php
    $styles = [
        'info' => 'bg-ink-700 border-ink-500 text-gray-200',
        'success' => 'bg-green-500/10 border-green-500/30 text-green-400',
        'warning' => 'bg-amber-500/10 border-amber-500/30 text-amber-400',
        'danger' => 'bg-red-500/10 border-red-500/30 text-red-400',
        'brand' => 'bg-brand-soft border-brand/30 text-brand',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border px-4 py-3 text-sm ' . ($styles[$type] ?? $styles['info'])]) }}>
    <div class="flex items-start gap-3">
        <div class="flex-1">{{ $slot }}</div>
        @if($dismissible)
            <button type="button" @click="$el.parentElement.parentElement.remove()" class="text-gray-400 hover:text-white">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        @endif
    </div>
</div>
