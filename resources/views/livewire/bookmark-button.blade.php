<div>
    <x-button
        :variant="$bookmarked ? 'primary' : 'secondary'"
        size="sm"
        wire:click="toggle"
        aria-pressed="{{ $bookmarked ? 'true' : 'false' }}"
    >
        <svg class="size-4" fill="{{ $bookmarked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
        </svg>
        <span>{{ $bookmarked ? 'Tersimpan' : 'Simpan' }}</span>
    </x-button>
</div>