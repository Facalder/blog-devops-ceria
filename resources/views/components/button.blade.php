@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-colors cursor-pointer select-none disabled:opacity-50 disabled:cursor-not-allowed focus:outline-hidden focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-1';

    $sizeClasses = match ($size) {
        'sm' => 'h-8 px-2.5 text-xs gap-1.5 rounded-[3px]',
        'lg' => 'h-10 px-4 text-base gap-2.5 rounded-[4px]',
        default => 'h-9 px-3.5 text-sm gap-2 rounded-[4px]',
    };

    $variantClasses = match ($variant) {
        'secondary' => 'bg-white text-on-surface border border-border-subtle hover:bg-surface hover:border-primary hover:text-primary active:bg-zinc-100 shadow-2xs',
        'tertiary', 'ghost' => 'bg-transparent text-tertiary hover:text-on-surface hover:bg-surface',
        'danger' => 'bg-[#D92D20] text-white hover:bg-[#B42318] active:bg-[#912018]',
        default => 'bg-primary text-white hover:bg-primary-hover active:bg-primary-active shadow-xs',
    };

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
