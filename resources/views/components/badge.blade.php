@props(['color' => 'gray', 'dot' => false])

@php
    $colors = [
        'gray' => 'bg-ink-600 text-gray-300',
        'brand' => 'bg-brand-soft text-brand',
        'green' => 'bg-green-500/15 text-green-400',
        'red' => 'bg-red-500/15 text-red-400',
        'amber' => 'bg-amber-500/15 text-amber-400',
        'blue' => 'bg-blue-500/15 text-blue-400',
        'purple' => 'bg-purple-500/15 text-purple-400',
        'ink' => 'bg-ink-700 text-gray-300',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . ($colors[$color] ?? $colors['gray'])]) }}>
    @if($dot)<span class="w-1.5 h-1.5 rounded-full bg-current"></span>@endif
    {{ $slot }}
</span>
