<div class="space-y-6">
    <div class="border-b border-border-subtle pb-5">
        <h1 class="font-serif text-3xl font-light text-on-surface">Bookmark Saya</h1>
        <p class="mt-1 text-sm text-tertiary">Artikel yang kamu simpan untuk dibaca nanti.</p>
    </div>

    @if (session('status'))
        <div class="rounded-[2px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($this->posts->isEmpty())
        <x-card class="p-8 text-center space-y-4">
            <p class="font-serif text-xl text-on-surface">Belum ada artikel tersimpan</p>
            <p class="text-sm text-tertiary">Tekan tombol Simpan di halaman artikel untuk menambahkannya ke sini.</p>
            <x-button :href="route('home')" variant="primary" size="sm">Jelajahi Artikel</x-button>
        </x-card>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($this->posts as $post)
                <x-card wire:key="bookmark-{{ $post->id }}" class="flex flex-col justify-between h-full p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2 font-mono text-xs text-tertiary">
                            @if ($post->category)
                                <x-badge variant="neutral">{{ $post->category->name }}</x-badge>
                            @else
                                <span>Artikel</span>
                            @endif
                            <span class="font-medium">{{ $post->reading_time }} mnt baca</span>
                        </div>

                        <h2 class="font-serif text-xl font-normal text-on-surface leading-snug line-clamp-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-primary transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>

                        <p class="text-sm text-tertiary line-clamp-3 leading-relaxed">
                            {{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-border-subtle flex items-center justify-between gap-3 font-mono text-xs text-tertiary">
                        <span>{{ $post->user->name }} &bull; {{ $post->published_at?->format('d M Y') }}</span>

                        <div class="flex items-center gap-2">
                            <x-button :href="route('blog.show', $post->slug)" variant="secondary" size="sm">Baca</x-button>
                            <x-button
                                variant="ghost"
                                size="sm"
                                wire:click="remove({{ $post->id }})"
                                wire:confirm="Hapus artikel ini dari bookmark?"
                            >
                                Hapus
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>