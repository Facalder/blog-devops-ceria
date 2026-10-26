@props([
    'as' => 'div',
    'href' => null,
    'hoverable' => false,
])

@php
    $tag = $href ? 'a' : $as;
    $baseClasses = 'block bg-white text-on-surface border border-border-subtle rounded-[2px] p-5 transition-all shadow-[0_1px_2px_rgba(16,24,40,0.04)]';
    $hoverClasses = ($hoverable || $href) ? 'hover:border-primary/60 hover:shadow-[0_4px_8px_rgba(19,72,220,0.08)] hover:-translate-y-0.5' : '';
    $classes = "{$baseClasses} {$hoverClasses}";
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>
