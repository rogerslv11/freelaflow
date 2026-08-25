@props(['id', 'title' => null, 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
@endphp

<div
    x-data
    x-show="$store.modal.open === '{{ $id }}'"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="$store.modal.open === '{{ $id }}'"
        x-transition.opacity
        class="fixed inset-0 bg-black/70 backdrop-blur-sm"
        @click="$store.modal.close()"
    ></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="$store.modal.open === '{{ $id }}'"
            x-transition.scale-in
            class="card w-full {{ $sizes[$size] ?? $sizes['md'] }} animate-scale-in"
        >
            <div class="flex items-center justify-between border-b border-ink-500/60 px-5 py-4">
                <h3 class="text-base font-semibold text-white">{{ $title }}</h3>
                <button type="button" @click="$store.modal.close()" class="rounded-lg p-1.5 text-gray-400 hover:bg-ink-700 hover:text-white transition">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>
            <div class="px-5 py-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
