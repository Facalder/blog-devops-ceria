@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
    'badge' => null,
])

@php
    $baseClasses = 'group flex items-center justify-between px-3 py-2 text-sm font-medium rounded-[4px] transition-colors';
    $stateClasses = $active
        ? 'bg-primary-tint text-primary font-medium'
        : 'text-on-surface hover:bg-white hover:text-primary';
    $classes = "{$baseClasses} {$stateClasses}";
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-center gap-2.5 truncate">
        @if ($icon)
            <span class="size-4 shrink-0 {{ $active ? 'text-primary' : 'text-tertiary group-hover:text-primary' }}">
                {{ $icon }}
            </span>
        @endif
        <span class="truncate">{{ $slot }}</span>
    </div>

    @if ($badge)
        <span class="text-xs font-mono px-1.5 py-0.5 rounded-full {{ $active ? 'bg-primary text-white' : 'bg-white border border-border-subtle text-tertiary' }}">
            {{ $badge }}
        </span>
    @endif
</a>
