@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'font-mono text-xs uppercase tracking-widest px-3 py-2 rounded-sm bg-ration-green text-paper'
            : 'font-mono text-xs uppercase tracking-widest px-3 py-2 rounded-sm text-ink-light hover:bg-ink/5';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>