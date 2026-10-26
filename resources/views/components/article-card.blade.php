@props([
    'post',
])

<x-card :href="route('blog.show', $post->slug)" :hoverable="true" class="group flex flex-col justify-between h-full p-6">
    <div class="space-y-3">
        <div class="flex items-center justify-between gap-2 font-mono text-xs text-tertiary">
            @if ($post->category)
                <x-badge variant="neutral">{{ $post->category->name }}</x-badge>
            @else
                <span class="text-xs text-tertiary font-mono">Artikel</span>
            @endif
            <span class="font-mono text-xs text-tertiary font-medium">{{ $post->reading_time }} mnt baca</span>
        </div>

        <h2 class="font-serif text-xl font-normal text-on-surface group-hover:text-primary transition-colors line-clamp-2 leading-snug">
            {{ $post->title }}
        </h2>

        <p class="text-sm text-tertiary line-clamp-3 leading-relaxed">
            {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}
        </p>
    </div>

    <div class="mt-6 pt-4 border-t border-border-subtle flex items-center justify-between font-mono text-xs text-tertiary">
        <div class="flex items-center gap-2">
            <div class="size-6 rounded-full bg-primary-tint border border-primary/20 flex items-center justify-center font-mono text-[10px] text-primary font-bold">
                {{ $post->user->initials() }}
            </div>
            <span class="text-on-surface font-medium">{{ $post->user->name }}</span>
        </div>
        <time datetime="{{ $post->published_at?->toDateString() }}" class="font-medium">
            {{ $post->published_at?->format('d M Y') }}
        </time>
    </div>
</x-card>
