@props(['title', 'subtitle' => null])

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold tracking-tight text-white">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-0.5 text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
