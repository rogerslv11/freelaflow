<div class="relative w-full max-w-md" x-data="{ open: false }"
     @keydown.escape.window="open=false"
     x-on:search-open.window="open=true; $refs.input.focus()">
    <div class="relative">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
            <x-icon name="search" class="w-4 h-4" />
        </span>
        <input
            x-ref="input"
            wire:model.live="query"
            @focus="open=true"
            @click.outside="open=false"
            type="text"
            placeholder="Buscar... ( / )"
            class="input pl-9 pr-9 text-sm"
            autocomplete="off"
        >
        <kbd class="absolute right-2.5 top-1/2 hidden -translate-y-1/2 rounded border border-ink-500 bg-ink-700 px-1.5 py-0.5 text-[10px] font-medium text-gray-500 sm:block">/</kbd>
    </div>

    <div
        x-show="open && $wire.query.length >= 2"
        x-transition
        x-cloak
        class="absolute left-0 right-0 mt-2 max-h-[70vh] overflow-y-auto rounded-xl border border-ink-500/60 bg-ink-800 shadow-card-hover animate-scale-in"
    >
        @if(count($this->results) === 0)
            <div class="px-4 py-8 text-center text-sm text-gray-500">Nenhum resultado para "{{ $this->query }}".</div>
        @else
            @foreach($this->results as $group => $items)
                <div class="border-b border-ink-700/60 last:border-0">
                    <p class="px-4 pb-1 pt-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ $group }}</p>
                    @foreach($items as $item)
                        <a href="{{ $item['route'] }}" wire:navigate @click="open=false" class="flex items-center gap-3 px-4 py-2 transition hover:bg-ink-700/50">
                            <span class="text-gray-400"><x-icon name="search" class="w-4 h-4" /></span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm text-gray-200">{{ $item['label'] }}</span>
                                <span class="block truncate text-xs text-gray-500">{{ $item['sub'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('search-open'));
        }
    });
</script>
