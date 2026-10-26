@props([
    'disabled' => false,
    'error' => null,
    'mono' => false,
    'rows' => 5,
])

@php
    $fontClass = $mono ? 'font-mono text-xs leading-relaxed' : 'font-sans text-sm';
    $baseClasses = "w-full bg-white text-on-surface rounded-[2px] px-3 py-2 border transition-colors placeholder:text-tertiary disabled:bg-surface disabled:cursor-not-allowed focus:outline-hidden {$fontClass}";
    $borderClasses = $error
        ? 'border-[#E45757] focus:border-[#E45757] focus:ring-1 focus:ring-[#E45757]'
        : 'border-border-subtle focus:border-primary focus:ring-1 focus:ring-primary';
    $classes = "{$baseClasses} {$borderClasses}";
@endphp

<textarea rows="{{ $rows }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</textarea>
