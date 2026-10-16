@props([
    'status' => 'default', // red, yellow, green, blue, gray (or alias names like darurat, menunggu, etc)
    'dot' => false,
])

@php
    $normalized = strtolower(trim((string)$status));

    // Map common puskesmas statuses to color tokens
    $colorMap = [
        // Red
        'red' => 'red',
        'merah' => 'red',
        'darurat' => 'red',
        'emergency' => 'red',
        'p1' => 'red',
        'danger' => 'red',

        // Yellow / Orange
        'yellow' => 'yellow',
        'kuning' => 'yellow',
        'urgent' => 'yellow',
        'p2' => 'yellow',
        'warning' => 'yellow',
        'proses' => 'yellow',
        'sedang_diperiksa' => 'yellow',

        // Green
        'green' => 'green',
        'hijau' => 'green',
        'non-urgent' => 'green',
        'non urgent' => 'green',
        'p3' => 'green',
        'success' => 'green',
        'selesai' => 'green',
        'checked_in' => 'green',
        'hadir' => 'green',

        // Blue
        'blue' => 'blue',
        'biru' => 'blue',
        'menunggu' => 'blue',
        'waiting' => 'blue',
        'info' => 'blue',
        'terdaftar' => 'blue',
        'p4' => 'blue',

        // Gray
        'gray' => 'gray',
        'abu' => 'gray',
        'batal' => 'gray',
        'cancelled' => 'gray',
        'default' => 'gray',
    ];

    $resolvedColor = $colorMap[$normalized] ?? 'gray';

    $styles = [
        'red' => [
            'badge' => 'bg-triage-red-bg text-triage-red-text border-triage-red-border',
            'dot' => 'bg-triage-red-DEFAULT',
        ],
        'yellow' => [
            'badge' => 'bg-triage-yellow-bg text-triage-yellow-text border-triage-yellow-border',
            'dot' => 'bg-triage-yellow-DEFAULT',
        ],
        'green' => [
            'badge' => 'bg-triage-green-bg text-triage-green-text border-triage-green-border',
            'dot' => 'bg-triage-green-DEFAULT',
        ],
        'blue' => [
            'badge' => 'bg-triage-blue-bg text-triage-blue-text border-triage-blue-border',
            'dot' => 'bg-triage-blue-DEFAULT',
        ],
        'gray' => [
            'badge' => 'bg-gray-100 text-gray-700 border-gray-200',
            'dot' => 'bg-gray-400',
        ],
    ][$resolvedColor];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-medium border ' . $styles['badge']
]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $styles['dot'] }}"></span>
    @endif
    {{ $slot }}
</span>
