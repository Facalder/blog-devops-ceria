@props([
    'heading' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if ($heading)
        <div class="px-3 pb-1 text-[11px] font-mono uppercase tracking-wider text-tertiary">
            {{ $heading }}
        </div>
    @endif
    <div class="space-y-0.5">
        {{ $slot }}
    </div>
</div>
