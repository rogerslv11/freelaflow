@props(['name' => null, 'label' => null, 'placeholder' => null, 'required' => false, 'value' => null, 'error' => null, 'rows' => 4])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $inputId = $name ?? ('i-'.substr(md5(($wireModel ?? '').mt_rand()), 0, 8));
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="label">{{ $label }} @if($required)<span class="text-brand">*</span>@endif</label>
    @endif
    <textarea
        id="{{ $inputId }}"
        name="{{ $name ?? $inputId }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'input resize-y ' . ($error ? 'border-red-500' : '')]) }}
    >{{ old($name ?? '', $value) }}</textarea>
    @if($error)
        <p class="mt-1 text-xs text-red-400">{{ $error }}</p>
    @endif
</div>
