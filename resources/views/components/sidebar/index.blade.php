@props([
    'collapsible' => true,
])

<aside {{ $attributes->merge(['class' => 'w-64 shrink-0 bg-surface border-r border-border-subtle flex flex-col min-h-screen text-on-surface']) }}>
    {{ $slot }}
</aside>
