@props([
    'variant' => 'neutral',
    'pill' => true,
])

@php
    $baseClasses = 'inline-flex items-center gap-1 font-mono text-xs font-medium leading-none select-none';
    $radiusClass = $pill ? 'rounded-full px-2.5 py-1' : 'rounded-[2px] px-2 py-0.5';

    $variantClasses = match ($variant) {
        'primary' => 'bg-primary-tint border border-primary/30 text-primary font-semibold',
        'success' => 'bg-emerald-50 border border-emerald-300 text-emerald-800 font-medium',
        'warning' => 'bg-amber-50 border border-amber-300 text-amber-900 font-medium',
        'danger' => 'bg-rose-50 border border-rose-300 text-rose-800 font-medium',
        default => 'bg-[#F2F4F7] border border-border-subtle text-on-surface font-medium',
    };

    $classes = "{$baseClasses} {$radiusClass} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
