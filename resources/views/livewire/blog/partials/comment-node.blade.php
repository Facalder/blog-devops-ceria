@php
    $effectiveDepth = min($depth, 3);

    $cardBg = match ($effectiveDepth) {
        0 => 'bg-white border-border-subtle hover:border-primary/40',
        1 => 'bg-[#FAFBFD] border-border-subtle hover:border-primary/40 border-l-[3px] border-l-primary/35',
        2 => 'bg-[#F2F5FD] border-primary/20 hover:border-primary/40 border-l-[3px] border-l-primary/65',
        default => 'bg-[#EAF0FC] border-primary/30 hover:border-primary/50 border-l-[3px] border-l-primary',
    };

    $cardPadding = match ($effectiveDepth) {
        0 => 'p-3.5 sm:p-5',
        1 => 'p-3 sm:p-4',
        default => 'p-2.5 sm:p-3.5',
    };

    $avatarSize = match ($effectiveDepth) {
        0 => 'size-7.5 sm:size-8 text-xs',
        1 => 'size-6.5 sm:size-7 text-[11px]',
        default => 'size-5.5 sm:size-6 text-[10px]',
    };

    $contentPadding = match ($effectiveDepth) {
        0 => 'pl-0 sm:pl-10',
        1 => 'pl-0 sm:pl-9',
        default => 'pl-0 sm:pl-8',
    };

    $subTreeMargin = match ($effectiveDepth) {
        0 => 'pl-2.5 sm:pl-6 ml-1.5 sm:ml-4 border-l-2 border-primary/20',
        1 => 'pl-2 sm:pl-5 ml-1 sm:ml-3 border-l-2 border-primary/35',
        default => 'pl-1.5 sm:pl-4 ml-0.5 sm:ml-2 border-l-2 border-primary/50',
    };

    $connectorWidth = match ($effectiveDepth) {
        0 => '-left-[11px] sm:-left-[25px] w-2.5 sm:w-5 border-primary/20',
        1 => '-left-[9px] sm:-left-[21px] w-2 sm:w-4 border-primary/35',
        default => '-left-[7px] sm:-left-[17px] w-1.5 sm:w-3 border-primary/50',
    };
@endphp

<div class="space-y-3 relative w-full" id="node-{{ $comment->id }}">
    @if ($depth > 0)
        <div class="absolute {{ $connectorWidth }} top-4 sm:top-5 border-b-2"></div>
    @endif

    <div class="{{ $cardBg }} {{ $cardPadding }} border rounded-[3px] transition-all flex flex-col justify-between scroll-mt-24 target:ring-2 target:ring-primary target:bg-primary-tint/25 shadow-2xs w-full overflow-hidden" id="comment-{{ $comment->id }}">
        <div class="flex items-start justify-between gap-2.5">
            <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                <div class="{{ $avatarSize }} rounded-full bg-primary-tint border border-primary/20 flex items-center justify-center font-mono text-primary font-bold shrink-0">
                    {{ $comment->initials() }}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-medium text-xs sm:text-sm text-on-surface truncate">{{ $comment->author_name }}</span>

                        @if ($comment->user_id && $comment->user_id === $post->user_id)
                            <x-badge variant="primary">Penulis Artikel</x-badge>
                        @elseif ($comment->user?->isAdmin())
                            <x-badge variant="neutral">Admin</x-badge>
                        @endif

                        @if ($comment->reply_to_id && $comment->replyTo)
                            <span class="inline-flex items-center gap-1 font-mono text-[10px] sm:text-[11px] text-tertiary bg-white border border-border-subtle px-1.5 py-0.5 rounded-[2px] shrink-0 max-w-full">
                                <span class="shrink-0">membalas</span>
                                <a href="#comment-{{ $comment->reply_to_id }}" class="text-primary font-semibold hover:underline truncate max-w-[120px] sm:max-w-[200px]" title="{{ $comment->replyTo->author_name }}">
                                    @<span>{{ $comment->replyTo->author_name }}</span>
                                </a>
                            </span>
                        @endif

                        @if ($depth >= 2)
                            <span class="font-mono text-[9px] sm:text-[10px] text-primary bg-primary-tint border border-primary/25 px-1.5 py-0.5 rounded-[2px] font-semibold shrink-0">
                                Level {{ $depth }}
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1.5 font-mono text-[10px] sm:text-[11px] text-tertiary mt-0.5">
                        <time datetime="{{ $comment->created_at?->toIso8601String() }}">
                            {{ $comment->created_at?->diffForHumans() }}
                        </time>
                        @if ($comment->updated_at && $comment->updated_at->gt($comment->created_at))
                            <span>&bull;</span>
                            <span class="italic text-[10px]">(diedit)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($editingCommentId === $comment->id)
            <form wire:submit="update" class="mt-3.5 space-y-3">
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
            <div class="mt-2.5 sm:mt-3 text-xs sm:text-sm text-on-surface leading-relaxed whitespace-pre-line font-sans {{ $contentPadding }} break-words">
                {{ $comment->content }}
            </div>

            <div class="mt-2.5 flex items-center justify-end gap-1 sm:gap-1.5 font-mono text-xs flex-wrap">
                <button
                    type="button"
                    wire:click="startReply({{ $comment->id }})"
                    class="inline-flex items-center gap-1 text-tertiary hover:text-primary transition-colors cursor-pointer px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[2px] border border-border-subtle hover:border-primary/40 bg-white text-[11px]"
                    title="Balas komentar ini"
                >
                    <svg class="size-3 sm:size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    <span>Balas</span>
                </button>

                <button
                    type="button"
                    wire:click="edit({{ $comment->id }})"
                    class="inline-flex items-center gap-1 text-tertiary hover:text-primary transition-colors cursor-pointer px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[2px] border border-border-subtle hover:border-primary/40 bg-white text-[11px]"
                    title="Edit komentar ini"
                >
                    <svg class="size-3 sm:size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    <span>Edit</span>
                </button>

                <button
                    type="button"
                    wire:click="delete({{ $comment->id }})"
                    wire:confirm="Apakah Anda yakin ingin menghapus komentar ini?"
                    class="inline-flex items-center gap-1 text-tertiary hover:text-[#E45757] transition-colors cursor-pointer px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-[2px] border border-border-subtle hover:border-[#E45757]/40 bg-white text-[11px]"
                    title="Hapus komentar ini"
                >
                    <svg class="size-3 sm:size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Hapus</span>
                </button>
            </div>
        @endif

        @if ($replyingToCommentId === $comment->id)
            <form wire:submit="saveReply" class="mt-3.5 p-3 sm:p-4 bg-white border border-border-subtle rounded-[3px] space-y-3">
                <div class="flex items-center justify-between font-mono text-xs text-tertiary pb-2 border-b border-border-subtle">
                    <div class="flex items-center gap-1.5 truncate">
                        <svg class="size-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        <span>Membalas <strong class="text-on-surface">@<span>{{ $comment->author_name }}</span></strong></span>
                    </div>
                    <button type="button" wire:click="cancelReply" class="text-tertiary hover:text-[#E45757] font-mono text-xs cursor-pointer ml-2 shrink-0">
                        &times; Batal
                    </button>
                </div>

                @auth
                    <div class="p-2.5 bg-surface border border-border-subtle rounded-[2px] flex items-center gap-2 text-xs font-mono min-w-0">
                        <span class="text-tertiary text-[11px] shrink-0">Membalas sebagai:</span>
                        <strong class="text-on-surface font-semibold truncate">{{ auth()->user()->name }}</strong>
                        <span class="text-tertiary text-[11px] truncate">({{ auth()->user()->email }})</span>
                    </div>
                @else
                    <div x-show="hasStoredProfile && !showEditProfile" x-cloak class="p-2.5 bg-surface border border-border-subtle rounded-[2px] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 text-xs font-mono min-w-0">
                        <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                            <span class="text-tertiary text-[11px] shrink-0">Membalas sebagai:</span>
                            <strong class="text-on-surface font-semibold truncate max-w-[130px] sm:max-w-[200px]" x-text="storedName"></strong>
                            <span class="text-tertiary text-[11px] truncate max-w-[160px] sm:max-w-none">
                                (<span x-text="storedEmail" class="break-all"></span>)
                            </span>
                        </div>
                        <button type="button" @click="changeProfile" class="self-start sm:self-auto text-primary hover:underline text-[11px] cursor-pointer shrink-0">
                            Ganti Profil
                        </button>
                    </div>

                    <div x-show="!hasStoredProfile || showEditProfile" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="replyAuthorName-{{ $comment->id }}" class="block font-mono text-xs text-tertiary font-medium">
                                Nama Lengkap <span class="text-[#E45757]">*</span>
                            </label>
                            <x-input
                                id="replyAuthorName-{{ $comment->id }}"
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
                            <label for="replyAuthorEmail-{{ $comment->id }}" class="block font-mono text-xs text-tertiary font-medium">
                                Alamat Email <span class="text-[#E45757]">*</span>
                            </label>
                            <x-input
                                id="replyAuthorEmail-{{ $comment->id }}"
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
                    <x-textarea
                        id="replyContent-{{ $comment->id }}"
                        wire:model="replyContent"
                        rows="3"
                        placeholder="Tuliskan balasan Anda di sini..."
                        :error="$errors->first('replyContent')"
                    />
                    @error('replyContent')
                        <p class="font-mono text-xs text-[#E45757]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <x-button type="button" wire:click="cancelReply" variant="secondary" size="sm">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveReply">Kirim Balasan</span>
                        <span wire:loading wire:target="saveReply">Mengirim...</span>
                    </x-button>
                </div>
            </form>
        @endif
    </div>

    @if ($comment->replies->isNotEmpty())
        <div class="mt-3 space-y-3 relative {{ $subTreeMargin }}">
            @foreach ($comment->replies as $childReply)
                @include('livewire.blog.partials.comment-node', [
                    'comment' => $childReply,
                    'depth' => $depth + 1,
                    'post' => $post,
                ])
            @endforeach
        </div>
    @endif
</div>
