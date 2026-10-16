@props([
    'title' => null,
    'description' => null,
    'action' => null,
    'padding' => 'p-6',
    'headerBorder' => true,
    'footerBorder' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-hospital-border rounded-hospital shadow-xs transition-shadow']) }}>
    @if ($title || isset($header))
        <div class="px-6 py-4 flex items-center justify-between {{ $headerBorder ? 'border-b border-hospital-border' : '' }}">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-hospital-heading">{{ $title }}</h3>
                @endif
                @if ($description)
                    <p class="mt-0.5 text-xs text-hospital-caption">{{ $description }}</p>
                @endif
            </div>
            @if ($action || isset($actions))
                <div class="flex items-center gap-2">
                    {{ $action ?? $actions }}
                </div>
            @endif
            {{ $header ?? '' }}
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="px-6 py-3.5 bg-hospital-subtle/50 {{ $footerBorder ? 'border-t border-hospital-border' : '' }} rounded-b-hospital">
            {{ $footer }}
        </div>
    @endif
</div>
