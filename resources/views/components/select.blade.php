@props(['name' => null, 'label' => null, 'options' => [], 'placeholder' => null, 'required' => false, 'value' => null, 'error' => null])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $inputId = $name ?? ('i-'.substr(md5(($wireModel ?? '').mt_rand()), 0, 8));
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="label">{{ $label }} @if($required)<span class="text-brand">*</span>@endif</label>
    @endif
    <select
        id="{{ $inputId }}"
        name="{{ $name ?? $inputId }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'input ' . ($error ? 'border-red-500' : '')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $key => $val)
            <option value="{{ is_numeric($key) ? $val : $key }}" @selected(old($name ?? '', $value) == (is_numeric($key) ? $val : $key))>{{ $val }}</option>
        @endforeach
    </select>
    @if($error)
        <p class="mt-1 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
