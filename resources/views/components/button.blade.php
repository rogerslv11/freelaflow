@props(['variant' => 'primary', 'type' => 'button', 'size' => 'md'])

@php
    $variants = [
        'primary' => 'bg-brand text-white hover:bg-brand-hover focus:ring-brand/50',
        'secondary' => 'bg-ink-700 text-gray-200 border border-ink-500 hover:bg-ink-600 focus:ring-ink-400/40',
        'ghost' => 'text-gray-300 hover:bg-ink-700 hover:text-white',
        'danger' => 'bg-red-600/90 text-white hover:bg-red-600 focus:ring-red-500/50',
        'subtle' => 'bg-brand-soft text-brand hover:bg-brand/20',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-ink-900 disabled:opacity-60 disabled:cursor-not-allowed ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $slot }}
</button>
