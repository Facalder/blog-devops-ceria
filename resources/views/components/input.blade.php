@props([
    'disabled' => false,
    'error' => null,
])

@php
    $baseClasses = 'w-full bg-white text-on-surface text-sm rounded-[2px] px-3 py-2 border transition-colors placeholder:text-tertiary disabled:bg-surface disabled:cursor-not-allowed focus:outline-hidden';
    $borderClasses = $error
        ? 'border-[#E45757] focus:border-[#E45757] focus:ring-1 focus:ring-[#E45757]'
        : 'border-border-subtle focus:border-primary focus:ring-1 focus:ring-primary';
    $classes = "{$baseClasses} {$borderClasses}";
@endphp

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
