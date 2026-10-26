<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border-subtle pb-5">
        <div>
            <h1 class="font-serif text-3xl font-light text-on-surface">Management Kategori</h1>
            <p class="mt-1 text-sm text-tertiary">Kelola kategori dan taksonomi untuk artikel blog.</p>
        </div>
        <div>
            <x-button type="button" wire:click="create" variant="primary">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Kategori Baru</span>
            </x-button>
        </div>
    </div>

    {{-- Alert Flash --}}
    @if (session('status'))
        <div class="rounded-[2px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Search Filter --}}
    <div class="w-full sm:max-w-xs">
        <x-input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari kategori..."
        />
    </div>

    {{-- Categories Table --}}
    <div class="overflow-hidden rounded-[2px] border border-border-subtle bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border-subtle bg-[#F2F4F7] text-xs font-mono uppercase tracking-wider text-[#344054] font-semibold">
                    <tr>
                        <th class="px-4 py-3">Nama Kategori</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3">Jumlah Artikel</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($this->categories as $category)
                        <tr class="hover:bg-[#FBFBFC] transition-colors">
                            <td class="px-4 py-3.5 font-medium text-on-surface whitespace-nowrap">
                                {{ $category->name }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs text-tertiary whitespace-nowrap">
                                /{{ $category->slug }}
                            </td>
                            <td class="px-4 py-3.5 text-tertiary text-xs max-w-xs truncate">
                                {{ $category->description ?: '-' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <x-badge variant="neutral">{{ $category->posts_count }} artikel</x-badge>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <x-button type="button" wire:click="edit({{ $category->id }})" size="sm" variant="secondary">
                                        Edit
                                    </x-button>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $category->id }})"
                                        wire:confirm="Yakin ingin menghapus kategori ini?"
                                        class="h-8 px-2 text-xs font-medium text-tertiary hover:text-[#E45757] rounded-[3px] transition-colors cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-tertiary">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->categories->hasPages())
            <div class="border-t border-border-subtle p-4">
                {{ $this->categories->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-[2px] border border-border-subtle bg-white p-6 shadow-lg">
                <div class="flex items-center justify-between border-b border-border-subtle pb-3">
                    <h2 class="font-serif text-xl font-light text-on-surface">
                        {{ $editingCategoryId ? 'Edit Kategori' : 'Kategori Baru' }}
                    </h2>
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="text-tertiary hover:text-on-surface cursor-pointer text-lg leading-none"
                    >
                        &times;
                    </button>
                </div>

                <form wire:submit="save" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                            Nama Kategori *
                        </label>
                        <x-input wire:model.live.debounce.300ms="name" placeholder="contoh: DevOps & Cloud" />
                        @error('name')
                            <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                            Slug *
                        </label>
                        <x-input wire:model="slug" placeholder="devops-cloud" />
                        @error('slug')
                            <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                            Deskripsi Singkat
                        </label>
                        <x-textarea wire:model="description" rows="3" placeholder="Opsional penjelasan kategori..." />
                        @error('description')
                            <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border-subtle">
                        <x-button type="button" wire:click="$set('showModal', false)" variant="secondary" size="sm">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" size="sm">
                            Simpan Kategori
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
