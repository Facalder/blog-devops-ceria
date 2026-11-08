<section class="mt-16 pt-12 border-t border-border-subtle" id="comments">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="font-serif text-2xl font-light text-on-surface flex items-center gap-2">
                <span>Diskusi & Komentar</span>
                <span class="font-mono text-xs text-primary bg-primary-tint border border-primary/20 px-2 py-0.5 rounded-[2px] font-semibold">
                    {{ $this->comments->count() }}
                </span>
            </h2>
            <p class="font-mono text-xs text-tertiary mt-1">
                Tulis tanggapan atau pertanyaan teknis seputar materi artikel ini.
            </p>
        </div>
    </div>

    @if ($feedbackMessage)
        <div class="mb-6 p-3 bg-primary-tint border border-primary/25 rounded-[3px] flex items-center justify-between text-xs font-mono text-primary">
            <span>{{ $feedbackMessage }}</span>
            <button type="button" wire:click="dismissFeedback" class="text-primary hover:text-primary-active font-bold ml-4 cursor-pointer">
                &times;
            </button>
        </div>
    @endif

    <form wire:submit="save" class="bg-surface border border-border-subtle rounded-[3px] p-5 sm:p-6 mb-10 space-y-4">
        @auth
            <div class="font-mono text-xs text-tertiary flex items-center gap-2 pb-2 border-b border-border-subtle">
                <span>Berkomentar sebagai:</span>
                <strong class="text-on-surface font-semibold">{{ auth()->user()->name }}</strong>
                <span class="text-[11px] text-tertiary">({{ auth()->user()->email }})</span>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label for="authorName" class="block font-mono text-xs text-tertiary font-medium">
                        Nama Lengkap <span class="text-[#E45757]">*</span>
                    </label>
                    <x-input
                        id="authorName"
                        type="text"
                        wire:model="authorName"
                        placeholder="Nama Anda"
                        :error="$errors->first('authorName')"
                    />
                    @error('authorName')
                        <p class="font-mono text-xs text-[#E45757]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="authorEmail" class="block font-mono text-xs text-tertiary font-medium">
                        Alamat Email <span class="text-[#E45757]">*</span>
                    </label>
                    <x-input
                        id="authorEmail"
                        type="email"
                        wire:model="authorEmail"
                        placeholder="nama@email.com"
                        :error="$errors->first('authorEmail')"
                    />
                    @error('authorEmail')
                        <p class="font-mono text-xs text-[#E45757]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endauth

        <div class="space-y-1">
            <label for="commentContent" class="block font-mono text-xs text-tertiary font-medium">
                Komentar <span class="text-[#E45757]">*</span>
            </label>
            <x-textarea
                id="commentContent"
                wire:model="content"
                rows="4"
                placeholder="Tuliskan pandangan, pertanyaan teknis, atau tanggapan Anda..."
                :error="$errors->first('content')"
            />
            @error('content')
                <p class="font-mono text-xs text-[#E45757]">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-2">
            <span class="font-mono text-[11px] text-tertiary">
                Mendukung teks santun & diskusi profesional.
            </span>
            <x-button type="submit" variant="primary" size="md" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Kirim Komentar</span>
                <span wire:loading wire:target="save">Mengirim...</span>
            </x-button>
        </div>
    </form>

    <div class="space-y-4">
        @forelse ($this->comments as $comment)
            <div class="bg-white border border-border-subtle rounded-[3px] p-5 transition-colors hover:border-primary/40 flex flex-col justify-between" id="comment-{{ $comment->id }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="size-8 rounded-full bg-primary-tint border border-primary/20 flex items-center justify-center font-mono text-xs text-primary font-bold">
                            {{ $comment->initials() }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm text-on-surface">{{ $comment->author_name }}</span>
                                @if ($comment->user_id && $comment->user_id === $post->user_id)
                                    <x-badge variant="primary">Penulis Artikel</x-badge>
                                @elseif ($comment->user?->isAdmin())
                                    <x-badge variant="neutral">Admin</x-badge>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 font-mono text-xs text-tertiary">
                                <time datetime="{{ $comment->created_at?->toIso8601String() }}">
                                    {{ $comment->created_at?->diffForHumans() }}
                                </time>
                                @if ($comment->updated_at && $comment->updated_at->gt($comment->created_at))
                                    <span>&bull;</span>
                                    <span class="italic text-[11px]">(diedit)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if ($editingCommentId === $comment->id)
                    <form wire:submit="update" class="mt-4 space-y-3">
                        <div class="space-y-1">
                            <x-textarea
                                id="editComment-{{ $comment->id }}"
                                wire:model="editingContent"
                                rows="3"
                                placeholder="Perbarui komentar Anda..."
                                :error="$errors->first('editingContent')"
                            />
                            @error('editingContent')
                                <p class="font-mono text-xs text-[#E45757]">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-1">
                            <x-button type="button" wire:click="cancelEdit" variant="secondary" size="sm">
                                Batal
                            </x-button>
                            <x-button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                                <span wire:loading wire:target="update">Menyimpan...</span>
                            </x-button>
                        </div>
                    </form>
                @else
                    <div class="mt-4 text-sm text-on-surface leading-relaxed whitespace-pre-line font-sans pl-11">
                        {{ $comment->content }}
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-2 font-mono text-xs">
                        <button
                            type="button"
                            wire:click="edit({{ $comment->id }})"
                            class="inline-flex items-center gap-1.5 text-tertiary hover:text-primary transition-colors cursor-pointer px-2.5 py-1 rounded-[2px] border border-border-subtle hover:border-primary/40 bg-surface"
                            title="Edit komentar ini"
                        >
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            <span>Edit</span>
                        </button>

                        <button
                            type="button"
                            wire:click="delete({{ $comment->id }})"
                            wire:confirm="Apakah Anda yakin ingin menghapus komentar ini?"
                            class="inline-flex items-center gap-1.5 text-tertiary hover:text-[#E45757] transition-colors cursor-pointer px-2.5 py-1 rounded-[2px] border border-border-subtle hover:border-[#E45757]/40 bg-surface"
                            title="Hapus komentar ini"
                        >
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="py-12 text-center bg-surface border border-border-subtle rounded-[3px] p-6">
                <p class="font-serif text-lg font-light text-on-surface">Belum ada diskusi pada artikel ini.</p>
                <p class="font-mono text-xs text-tertiary mt-1">Jadilah yang pertama untuk meninggalkan komentar.</p>
            </div>
        @endforelse
    </div>
</section>
