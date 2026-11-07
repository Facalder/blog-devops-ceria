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
            <div class="bg-white border border-border-subtle rounded-[3px] p-5 transition-colors hover:border-primary/40">
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
                            <time datetime="{{ $comment->created_at?->toIso8601String() }}" class="font-mono text-xs text-tertiary">
                                {{ $comment->created_at?->diffForHumans() }}
                            </time>
                        </div>
                    </div>

                    @auth
                        @if (auth()->user()->isAdmin() || auth()->id() === $post->user_id || auth()->id() === $comment->user_id)
                            <button
                                type="button"
                                wire:click="delete({{ $comment->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus komentar ini?"
                                class="font-mono text-xs text-tertiary hover:text-[#E45757] transition-colors cursor-pointer px-2 py-1 rounded-[2px] border border-border-subtle hover:border-[#E45757]/40 bg-surface"
                                title="Hapus komentar"
                            >
                                Hapus
                            </button>
                        @endif
                    @endauth
                </div>

                <div class="mt-4 text-sm text-on-surface leading-relaxed whitespace-pre-line font-sans pl-11">
                    {{ $comment->content }}
                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-surface border border-border-subtle rounded-[3px] p-6">
                <p class="font-serif text-lg font-light text-on-surface">Belum ada diskusi pada artikel ini.</p>
                <p class="font-mono text-xs text-tertiary mt-1">Jadilah yang pertama untuk meninggalkan komentar.</p>
            </div>
        @endforelse
    </div>
</section>
