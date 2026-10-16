@props([
    'headers' => [],
])

<div {{ $attributes->merge(['class' => 'w-full bg-white border border-hospital-border rounded-hospital overflow-hidden shadow-xs']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            @if (isset($header) || count($headers) > 0)
                <thead class="bg-hospital-subtle border-b border-hospital-border text-xs font-semibold uppercase tracking-wider text-hospital-heading">
                    @if (isset($header))
                        {{ $header }}
                    @else
                        <tr>
                            @foreach ($headers as $h)
                                <th scope="col" class="px-4 py-3 text-left">
                                    {{ $h }}
                                </th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
            @endif

            <tbody class="divide-y divide-hospital-border text-hospital-body">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if (isset($footer))
        <div class="px-4 py-3 bg-hospital-subtle/40 border-t border-hospital-border text-xs text-hospital-caption">
            {{ $footer }}
        </div>
    @endif
</div>
