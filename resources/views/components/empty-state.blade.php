@props(['title' => null, 'icon' => null])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-ink-700 text-gray-500">
        <x-icon :name="$icon ?? 'reports'" class="w-7 h-7" />
    </div>
    @if($title)
        <h3 class="text-sm font-semibold text-gray-200">{{ $title }}</h3>
    @endif
    <p class="mt-1 max-w-sm text-sm text-gray-500">{{ $slot }}</p>
</div>
