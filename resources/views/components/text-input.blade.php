@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-line bg-white text-ink rounded-hospital shadow-xs text-sm focus:border-primary focus:ring-1 focus:ring-primary placeholder-slate-400']) }}>
