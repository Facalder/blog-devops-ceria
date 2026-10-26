<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border-subtle pb-5">
        <div>
            <h1 class="font-serif text-3xl font-light text-on-surface">Management Blog</h1>
            <p class="mt-1 text-sm text-tertiary">Kelola semua artikel, draf, dan publikasi konten blog.</p>
        </div>
        <div>
            <x-button :href="route('posts.create')" wire:navigate variant="primary">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tulis Artikel</span>
            </x-button>
        </div>
    </div>

    {{-- Alert Flash --}}
    @if (session('status'))
        <div class="rounded-[2px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="w-full sm:max-w-xs">
            <x-input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari judul atau ringkasan..."
            />
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <select
                wire:model.live="status"
                class="rounded-[2px] border border-border-subtle bg-white px-3 py-2 text-sm text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
            >
                <option value="">Semua Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>

            <select
                wire:model.live="categoryId"
                class="rounded-[2px] border border-border-subtle bg-white px-3 py-2 text-sm text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
            >
                <option value="">Semua Kategori</option>
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Posts Table / List --}}
    <div class="overflow-hidden rounded-[2px] border border-border-subtle bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border-subtle bg-[#F2F4F7] text-xs font-mono uppercase tracking-wider text-[#344054] font-semibold">
                    <tr>
                        <th class="px-4 py-3">Artikel</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Penulis</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($this->posts as $post)
                        <tr class="hover:bg-[#FBFBFC] transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="font-serif text-base font-normal text-on-surface">
                                    {{ $post->title }}
                                </div>
                                <div class="mt-0.5 font-mono text-xs text-tertiary">
                                    /{{ $post->slug }} &bull; {{ $post->reading_time }} mnt baca
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($post->category)
                                    <x-badge variant="neutral">{{ $post->category->name }}</x-badge>
                                @else
                                    <span class="text-xs text-tertiary">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-medium text-on-surface">{{ $post->user->name }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($post->status === 'published')
                                    <x-badge variant="success">Published</x-badge>
                                @else
                                    <x-badge variant="neutral">Draft</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-mono text-xs text-tertiary">
                                {{ $post->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <x-button :href="route('posts.edit', $post)" wire:navigate size="sm" variant="secondary">
                                        Edit
                                    </x-button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $post->id }})"
                                        wire:confirm="Yakin ingin menghapus artikel ini?"
                                        class="h-8 px-2 text-xs font-medium text-tertiary hover:text-[#E45757] rounded-[3px] transition-colors cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-tertiary">
                                Belum ada artikel yang ditemukan. Silakan tulis artikel baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->posts->hasPages())
            <div class="border-t border-border-subtle p-4">
                {{ $this->posts->links() }}
            </div>
        @endif
    </div>
</div>
