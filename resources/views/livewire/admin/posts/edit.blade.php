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
                <label for="title" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                    Judul Artikel *
                </label>
                <input
                    id="title"
                    type="text"
                    wire:model.live.debounce.400ms="title"
                    placeholder="Masukkan judul artikel..."
                    class="w-full font-serif text-2xl font-light bg-white text-on-surface border border-border-subtle rounded-[2px] px-3.5 py-2.5 focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary placeholder:text-tertiary"
                />
                @error('title')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                    Slug URL *
                </label>
                <div class="flex items-center rounded-[2px] border border-border-subtle bg-surface px-3 py-1.5">
                    <span class="font-mono text-xs text-tertiary mr-1">/blog/</span>
                    <input
                        id="slug"
                        type="text"
                        wire:model="slug"
                        placeholder="judul-artikel"
                        class="w-full font-mono text-xs text-on-surface bg-transparent focus:outline-hidden"
                    />
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
                    <h2 class="text-sm font-mono uppercase tracking-wider text-on-surface font-semibold">Pengaturan Publikasi</h2>
                </div>

                <div>
                    <span id="status-label" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                        Status Artikel
                    </span>
                    <div class="space-y-2" role="radiogroup" aria-labelledby="status-label">
                        <label class="flex items-center gap-2 text-sm text-on-surface cursor-pointer">
                            <input type="radio" wire:model.live="status" value="draft" class="text-primary focus:ring-primary" />
                            <span>Simpan sebagai Draft</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-on-surface cursor-pointer">
                            <input type="radio" wire:model.live="status" value="published" class="text-primary focus:ring-primary" />
                            <span>Publikasikan Sekarang</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-on-surface cursor-pointer">
                            <input type="radio" wire:model.live="status" value="scheduled" class="text-primary focus:ring-primary" />
                            <span>Jadwalkan Publikasi</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                    @enderror
                </div>

                @if ($status === 'scheduled')
                    <div>
                        <label for="scheduled_at" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                            Waktu Publikasi ({{ config('app.timezone') }}) *
                        </label>
                        <input
                            id="scheduled_at"
                            type="datetime-local"
                            wire:model="scheduled_at"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            class="w-full rounded-[2px] border border-border-subtle bg-white px-3 py-2 font-mono text-xs text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                        />
                        @error('scheduled_at')
                            <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @if ($post->status === 'scheduled')
                    <button
                        type="button"
                        wire:click="cancelSchedule"
                        wire:confirm="Batalkan jadwal publikasi dan ubah artikel menjadi draft?"
                        class="w-full h-9 rounded-[2px] border border-border-subtle bg-white text-xs font-medium text-[#E45757] hover:bg-red-50 transition-colors cursor-pointer"
                    >
                        Batalkan Jadwal
                    </button>
                @endif

                <div>
                    <label for="category_id" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1.5">
                        Kategori Artikel
                    </label>
                    <select
                        id="category_id"
                        wire:model="category_id"
                        class="w-full rounded-[2px] border border-border-subtle bg-white px-3 py-2 text-sm text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                    >
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
        </div>
    </div>
</form>