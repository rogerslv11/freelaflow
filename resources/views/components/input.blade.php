@props(['name' => null, 'label' => null, 'type' => 'text', 'placeholder' => null, 'required' => false, 'value' => null, 'error' => null])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $inputId = $name ?? ('i-'.substr(md5(($wireModel ?? '').mt_rand()), 0, 8));
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="label">{{ $label }} @if($required)<span class="text-brand">*</span>@endif</label>
    @endif
    <input
        id="{{ $inputId }}"
        name="{{ $name ?? $inputId }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($name ?? '', $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'input ' . ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500/40' : '')]) }}
    >
    @if($error)
        <p class="mt-1 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
