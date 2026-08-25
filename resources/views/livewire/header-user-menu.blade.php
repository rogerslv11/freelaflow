<div class="relative" x-data="{ open: false }" @keydown.escape.window="open=false">
    <button @click="open = !open" class="flex items-center gap-2 rounded-lg p-1 pr-2 transition hover:bg-ink-700">
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-700 text-sm font-semibold text-brand">
            {{ mb_substr(auth()->user()->name, 0, 1) }}
        </div>
        <x-icon name="chevron-down" class="hidden h-4 w-4 text-gray-500 sm:block" />
    </button>

    <div
        x-show="open"
        x-transition
        @click.outside="open=false"
        x-cloak
        class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-ink-500/60 bg-ink-800 py-1 shadow-card-hover animate-scale-in"
    >
        <div class="border-b border-ink-500/60 px-4 py-3">
            <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
        </div>
        <a href="{{ route('settings.index') }}" @click="open=false" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-300 transition hover:bg-ink-700 hover:text-white">
            <x-icon name="settings" class="h-4 w-4" /> Configurações
        </a>
        <button wire:click="logout" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-gray-300 transition hover:bg-ink-700 hover:text-white">
            <x-icon name="logout" class="h-4 w-4" /> Sair
        </button>
    </div>
</div>
