<div
    x-data
    x-cloak
    class="fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2"
    x-cloak
>
    <template x-for="t in $store.toast.items" :key="t.id">
        <div
            x-show="true"
            x-transition
            class="flex items-center gap-3 rounded-lg border px-4 py-3 text-sm shadow-card-hover animate-fade-in"
            :class="{
                'bg-ink-800 border-ink-500 text-gray-100': t.type === 'success' || t.type === 'info',
                'bg-red-500/10 border-red-500/30 text-red-300': t.type === 'error' || t.type === 'danger',
                'bg-amber-500/10 border-amber-500/30 text-amber-300': t.type === 'warning',
            }"
            @click="$store.toast.remove(t.id)"
        >
            <span
                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                :class="t.type === 'error' || t.type === 'danger' ? 'bg-red-500/20' : 'bg-brand-soft text-brand'"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </span>
            <span x-text="t.message" class="flex-1"></span>
        </div>
    </template>
</div>
