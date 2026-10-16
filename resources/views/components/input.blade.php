@props([
    'disabled' => false,
    'label' => null,
    'error' => null,
    'hint' => null,
    'id' => null,
    'type' => 'text',
])

@php
    $inputId = $id ?? ($attributes->get('name') ?? 'input-' . uniqid());
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-hospital-heading mb-1.5">
            {{ $label }}
            @if ($attributes->has('required'))
                <span class="text-triage-red-DEFAULT">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input
            id="{{ $inputId }}"
            type="{{ $type }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full h-10 px-3 py-2 text-sm text-hospital-heading bg-white border ' .
                    ($error ? 'border-triage-red-DEFAULT focus:border-triage-red-DEFAULT focus:ring-triage-red-DEFAULT/20' : 'border-hospital-border focus:border-primary focus:ring-primary/20') .
                    ' rounded-hospital placeholder:text-hospital-caption focus:outline-none focus:ring-2 transition-colors disabled:bg-hospital-subtle disabled:text-hospital-caption disabled:cursor-not-allowed shadow-xs'
            ]) }}
        >
    </div>

    @if ($error)
        <p class="mt-1 text-xs text-triage-red-text font-medium">{{ $error }}</p>
    @elseif ($hint)
        <p class="mt-1 text-xs text-hospital-caption">{{ $hint }}</p>
    @endif
</div>
