@props([
    'type' => 'info', // info, success, warning, danger
    'title' => null,
])

@php
    $typeClasses = [
        'info' => 'bg-surface-subtle text-ink border-line',
        'success' => 'bg-emerald-50 text-emerald-900 border-emerald-200',
        'warning' => 'bg-amber-50 text-amber-900 border-amber-200',
        'danger' => 'bg-red-50 text-red-900 border-red-200',
    ][$type] ?? 'bg-surface-subtle text-ink border-line';

    $iconColors = [
        'info' => 'text-primary',
        'success' => 'text-emerald-600',
        'warning' => 'text-amber-600',
        'danger' => 'text-red-600',
    ][$type] ?? 'text-primary';
@endphp

<div {{ $attributes->merge(['class' => 'p-4 rounded-hospital border text-xs sm:text-sm ' . $typeClasses]) }} role="alert">
    <div class="flex items-start gap-3">
        <div class="shrink-0 mt-0.5 {{ $iconColors }}">
            @if ($type === 'success')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif ($type === 'warning')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            @elseif ($type === 'danger')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
            @endif
        </div>
        <div class="flex-1">
            @if ($title)
                <h4 class="font-bold text-sm mb-1 leading-tight">{{ $title }}</h4>
            @endif
            <div class="leading-relaxed">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
