@props(['data' => [], 'size' => 140, 'thickness' => 18])

@php
    $total = array_sum(array_column($data, 'value')) ?: 1;
    $radius = ($size - $thickness) / 2;
    $circumference = 2 * pi() * $radius;
    $offset = 0;
@endphp

<div class="flex items-center gap-4">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" class="-rotate-90">
        <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $radius }}" fill="none" stroke="#272727" stroke-width="{{ $thickness }}" />
        @foreach($data as $slice)
            @php
                $fraction = $slice['value'] / $total;
                $dash = $fraction * $circumference;
                $thisOffset = $offset;
                $offset += $dash;
            @endphp
            <circle
                cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $radius }}"
                fill="none"
                stroke="{{ $slice['color'] ?? '#FF6B00' }}"
                stroke-width="{{ $thickness }}"
                stroke-dasharray="{{ $dash }} {{ $circumference - $dash }}"
                stroke-dashoffset="{{ -$thisOffset }}"
            />
        @endforeach
    </svg>
    <div class="space-y-1.5">
        @foreach($data as $slice)
            <div class="flex items-center gap-2 text-sm">
                <span class="h-2.5 w-2.5 rounded-sm" style="background: {{ $slice['color'] ?? '#FF6B00' }}"></span>
                <span class="text-gray-300">{{ $slice['label'] }}</span>
                <span class="ml-auto font-medium text-gray-200">{{ number_format($slice['value'], 0) }}</span>
            </div>
        @endforeach
    </div>
</div>
