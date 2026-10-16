@props([
    'title',
    'subtitle' => null,
    'breadcrumb' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 sm:mb-8 pb-5 border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4']) }}>
    <div>
        @if ($breadcrumb)
            <div class="mb-2 text-xs text-ink-muted">
                {{ $breadcrumb }}
            </div>
        @endif
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-ink-body">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex items-center gap-2.5 shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
