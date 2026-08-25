@php
    $user = auth()->user();
    $profile = $user->profile;
    $plan = $profile?->plan ?? 'free';
@endphp

<div class="border-t border-ink-500/60 p-3">
    <div class="flex items-center gap-3 rounded-lg p-2">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-ink-700 text-sm font-semibold text-brand">
            {{ mb_substr($user->name, 0, 1) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-white">{{ $user->name }}</p>
            <p class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="badge bg-brand-soft text-brand capitalize">{{ $plan }}</span>
            </p>
        </div>
    </div>
</div>
