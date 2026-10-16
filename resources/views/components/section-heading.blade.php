@props([
    'kicker' => null,
    'title',
    'subtitle' => null,
    'align' => 'center',
])

@php
    $alignClasses = [
        'center' => 'text-center mx-auto max-w-2xl',
        'left' => 'text-left max-w-2xl',
        'right' => 'text-right ml-auto max-w-2xl',
    ][$align] ?? 'text-center mx-auto max-w-2xl';
@endphp

<div {{ $attributes->merge(['class' => $alignClasses]) }}>
    @if ($kicker)
        <p class="text-xs font-semibold tracking-wider text-primary uppercase mb-2">
            {{ $kicker }}
        </p>
    @endif

    <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-hospital-heading">
        {{ $title }}
    </h2>

    @if ($subtitle)
        <p class="mt-2 text-sm sm:text-base text-hospital-body leading-relaxed">
            {{ $subtitle }}
        </p>
    @endif

    {{ $slot }}
</div>
