@props([
    'labels' => [],
    'values' => [],
    'color' => '#FF6B00',
    'series' => [],
    'height' => 200,
    'format' => null,
])

@php
    $values = $values instanceof \Illuminate\Support\Collection ? $values->toArray() : $values;
    $labels = $labels instanceof \Illuminate\Support\Collection ? $labels->toArray() : $labels;

    if (empty($series) && ! empty($values)) {
        $series = [['label' => '', 'values' => $values, 'color' => $color]];
    }

    $series = collect($series)->map(function ($s) {
        $s['values'] = $s['values'] instanceof \Illuminate\Support\Collection ? $s['values']->toArray() : $s['values'];

        return $s;
    })->all();

    $max = collect($series)->flatMap(fn ($s) => $s['values'])->max() ?: 1;
    $count = count($labels);
@endphp

@if(count($series) > 1)
    <div class="mb-3 flex flex-wrap gap-x-4 gap-y-1">
        @foreach($series as $s)
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $s['color'] }}"></span>
                {{ $s['label'] }}
            </div>
        @endforeach
    </div>
@endif

<div class="flex items-end gap-2 sm:gap-4" style="height: {{ $height }}px">
    @foreach($labels as $i => $label)
        <div class="flex flex-1 flex-col items-center justify-end gap-1.5" style="height: 100%">
            <div class="flex h-full items-end gap-1">
                @foreach($series as $s)
                    @php
                        $v = $s['values'][$i] ?? 0;
                        $pct = $max > 0 ? ($v / $max) * 100 : 0;
                    @endphp
                    <div class="w-2.5 rounded-t sm:w-4 transition-all hover:opacity-80"
                         style="height: {{ $pct }}%; background: {{ $s['color'] }};"
                         title="{{ $label }}: {{ is_callable($format) ? $format($v) : $v }}"></div>
                @endforeach
            </div>
            <span class="max-w-full truncate text-[10px] text-gray-500">{{ $label }}</span>
        </div>
    @endforeach
</div>
