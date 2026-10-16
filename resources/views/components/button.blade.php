@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none rounded-hospital';

    $sizeClasses = [
        'sm' => 'h-8 px-3 text-xs gap-1.5',
        'md' => 'h-10 px-4 text-sm gap-2',
        'lg' => 'h-11 px-5 text-base gap-2.5',
    ][$size] ?? 'h-10 px-4 text-sm gap-2';

    $variantClasses = [
        'primary' => 'bg-primary hover:bg-primary-hover text-white shadow-xs focus:ring-primary/40 border border-transparent',
        'outline' => 'bg-white hover:bg-hospital-subtle text-hospital-heading border border-hospital-border hover:border-gray-300 shadow-xs focus:ring-primary/30',
        'outline-primary' => 'bg-white hover:bg-primary-50 text-primary border border-primary/40 hover:border-primary shadow-xs focus:ring-primary/30',
        'secondary' => 'bg-hospital-subtle hover:bg-gray-100 text-hospital-heading border border-hospital-border focus:ring-gray-300',
        'ghost' => 'bg-transparent hover:bg-hospital-subtle text-hospital-body hover:text-hospital-heading focus:ring-primary/20',
        'danger' => 'bg-triage-red-bg hover:bg-red-100 text-triage-red-text border border-triage-red-border focus:ring-red-400',
        'success' => 'bg-accent hover:bg-accent-hover text-white shadow-xs focus:ring-accent/40 border border-transparent',
    ][$variant] ?? 'bg-primary hover:bg-primary-hover text-white shadow-xs focus:ring-primary/40 border border-transparent';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
