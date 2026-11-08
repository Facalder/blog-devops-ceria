<form wire:submit="save" class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border-subtle pb-5">
        <div class="flex items-center gap-3">
            <x-button :href="route('posts.index')" wire:navigate size="sm" variant="secondary">
                &larr; Kembali
            </x-button>
            <h1 class="font-serif text-2xl font-light text-on-surface">Edit Artikel</h1>
        </div>
        <div class="flex items-center gap-2">
            <x-button type="submit" variant="primary">
                Perbarui Artikel
            </x-button>
        </div>
    </div>

    @if (session('status'))
    <div class="rounded-[2px] border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 font-mono text-xs">
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="rounded-[2px] border border-red-200 bg-red-50 p-4 text-sm text-[#E45757]">
        <div class="font-medium font-mono text-xs uppercase mb-1">Terdapat kesalahan pengisian:</div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div>
                <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                    Judul Artikel *
                </label>
                <input type="text" wire:model.live.debounce.400ms="title" placeholder="Masukkan judul artikel..."
                    class="w-full font-serif text-2xl font-light bg-white text-on-surface border border-border-subtle rounded-[2px] px-3.5 py-2.5 focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary placeholder:text-tertiary" />
                @error('title')
                <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                    Slug URL *
                </label>
                <div class="flex items-center rounded-[2px] border border-border-subtle bg-surface px-3 py-1.5">
                    <span class="font-mono text-xs text-tertiary mr-1">/blog/</span>
                    <input type="text" wire:model="slug" placeholder="judul-artikel"
                        class="w-full font-mono text-xs text-on-surface bg-transparent focus:outline-hidden" />
                </div>
                @error('slug')
                <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                    Konten Markdown *
                </label>
                <x-markdown-editor wire:model="content" minHeight="420px" :error="$errors->first('content')" />
            </div>

            <div>
                <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                    Ringkasan Singkat (Excerpt)
                </label>
                <x-textarea wire:model="excerpt" rows="3" placeholder="Opsional, ringkasan pendek artikel..." />
                @error('excerpt')
                <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="space-y-5">
            <x-card class="space-y-5">
                <div class="border-b border-border-subtle pb-3">
                    <h2 class="text-sm font-mono uppercase tracking-wider text-on-surface font-semibold">Pengaturan
                        Publikasi</h2>
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                        Status Artikel
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-on-surface cursor-pointer">
                            <input type="radio" wire:model="status" value="draft"
                                class="text-primary focus:ring-primary" />
                            <span>Simpan sebagai Draft</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-on-surface cursor-pointer">
                            <input type="radio" wire:model="status" value="published"
                                class="text-primary focus:ring-primary" />
                            <span>Publikasikan Sekarang</span>
                        </label>
                    </div>
                    @error('status')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                        Kategori Artikel
                    </label>
                    <select wire:model="category_id"
                        class="w-full rounded-[2px] border border-border-subtle bg-white px-3 py-2 text-sm text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                        <option value="">Pilih Kategori (Opsional)</option>
                        @foreach ($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-3 border-t border-border-subtle">
                    <x-button type="submit" variant="primary" class="w-full">
                        Perbarui Artikel
                    </x-button>
                </div>
            </x-card>

            <x-card class="space-y-4">
                <div class="border-b border-border-subtle pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-mono uppercase tracking-wider text-on-surface font-semibold">Riwayat Revisi
                    </h2>
                    <span class="font-mono text-xs text-tertiary">{{ $this->revisions->count() }} versi</span>
                </div>

                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @forelse ($this->revisions as $rev)
                    <div class="rounded-[2px] border border-border-subtle bg-surface p-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-medium text-primary">Versi #{{ $rev->revision_number }}</span>
                            <span class="font-mono text-tertiary">{{ $rev->created_at?->diffForHumans() }}</span>
                        </div>

                        <p class="text-on-surface text-xs leading-relaxed">{{ $rev->change_summary }}</p>

                        <div
                            class="flex items-center justify-between text-tertiary pt-1 border-t border-border-subtle font-mono text-[11px]">
                            <span>Oleh {{ $rev->user?->name ?? 'Sistem' }}</span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" wire:click="viewDiff({{ $rev->id }})"
                                    class="text-primary hover:underline cursor-pointer">
                                    Diff
                                </button>
                                <span>&bull;</span>
                                <button type="button" wire:click="startRollbackFromSidebar({{ $rev->id }})"
                                    class="text-rose-600 hover:underline cursor-pointer">
                                    Rollback
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-tertiary font-mono py-2 text-center">Belum ada snapshot revisi.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    @if ($showRevisionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-xs">
        <div
            class="w-full max-w-4xl rounded-[4px] bg-white border border-border-subtle shadow-xl flex flex-col max-h-[88vh] overflow-hidden">
            <div class="flex items-center justify-between border-b border-border-subtle px-6 py-4 bg-white">
                <div class="flex items-center gap-3">
                    <div
                        class="size-7 rounded-[4px] bg-primary-tint border border-primary/20 flex items-center justify-center font-mono text-xs font-semibold text-primary">
                        #{{ $selectedRevisionNumber ?? '' }}
                    </div>
                    <div>
                        <h3 class="font-serif text-lg font-light text-on-surface">Inspeksi Perubahan Versi</h3>
                        <p class="font-mono text-xs text-tertiary">{{ $diffStats }}</p>
                    </div>
                </div>
                <button type="button" wire:click="closeDiff"
                    class="size-8 rounded-[4px] hover:bg-surface flex items-center justify-center text-tertiary hover:text-on-surface transition-colors cursor-pointer text-lg leading-none">
                    &times;
                </button>
            </div>

            <div
                class="flex-1 overflow-y-auto p-6 md:p-8 bg-white font-sans text-sm sm:text-base leading-relaxed text-[#242529] whitespace-pre-wrap selection:bg-[#EEF2FD]">
                {!! $diffHtml !!}
            </div>

            <div
                class="flex flex-col sm:flex-row items-center justify-between border-t border-border-subtle px-6 py-3.5 bg-surface gap-3">
                <div class="flex items-center gap-4 font-mono text-xs text-tertiary">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#027A48]"></span> Ditambahkan
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#B42318]"></span> Dihapus
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    @if ($confirmingRestore)
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs text-tertiary">Gantikan konten saat ini?</span>
                        <x-button type="button" wire:click="$set('confirmingRestore', false)" variant="secondary"
                            size="sm">
                            Batal
                        </x-button>
                        <x-button type="button" wire:click="restoreRevision({{ $selectedRevisionId }})"
                            variant="primary" size="sm" class="bg-[#E45757] hover:bg-[#cf4242]">
                            Ya, Pulihkan Versi Ini
                        </x-button>
                    </div>
                    @else
                    <x-button type="button" wire:click="closeDiff" variant="secondary" size="sm">
                        Tutup
                    </x-button>
                    @if ($selectedRevisionId)
                    <x-button type="button" wire:click="$set('confirmingRestore', true)" variant="primary" size="sm">
                        Pulihkan Versi Ini
                    </x-button>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</form>