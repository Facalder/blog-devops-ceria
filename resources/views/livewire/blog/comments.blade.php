<section
    x-data="{
        storedName: '',
        storedEmail: '',
        hasStoredProfile: false,
        showEditProfile: false,
        init() {
            try {
                const profile = JSON.parse(localStorage.getItem('devops_ceria_reader') || '{}');
                if (profile && profile.name && profile.email) {
                    this.storedName = profile.name;
                    this.storedEmail = profile.email;
                    this.hasStoredProfile = true;
                    if (! $wire.authorName) {
                        $wire.set('authorName', profile.name);
                    }
                    if (! $wire.authorEmail) {
                        $wire.set('authorEmail', profile.email);
                    }
                }
            } catch (e) {}

            window.addEventListener('profile-saved', (event) => {
                const detail = event.detail;
                if (detail && detail.name && detail.email) {
                    this.storedName = detail.name;
                    this.storedEmail = detail.email;
                    this.hasStoredProfile = true;
                    this.showEditProfile = false;
                    localStorage.setItem('devops_ceria_reader', JSON.stringify({
                        name: detail.name,
                        email: detail.email
                    }));
                }
            });
        },
        changeProfile() {
            this.showEditProfile = true;
        }
    }"
    class="mt-16 pt-12 border-t border-border-subtle w-full min-w-0"
    id="comments"
>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="font-serif text-2xl font-light text-on-surface flex items-center gap-2 flex-wrap">
                <span>Diskusi & Komentar</span>
                <span class="font-mono text-xs text-primary bg-primary-tint border border-primary/20 px-2 py-0.5 rounded-xs font-semibold">
                    {{ $this->totalCommentsCount }}
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

    <form wire:submit="save" class="bg-surface border border-border-subtle rounded-[3px] p-4 sm:p-6 mb-10 space-y-4 shadow-2xs w-full min-w-0 overflow-hidden">
        @auth
            <div class="p-3 bg-white border border-border-subtle rounded-[2px] flex items-center justify-between gap-2 text-xs font-mono min-w-0">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="size-6 rounded-full bg-primary-tint border border-primary/20 flex items-center justify-center font-bold text-primary text-[10px] shrink-0">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="min-w-0 flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5">
                        <span class="text-tertiary text-[11px] shrink-0">Berkomentar sebagai:</span>
                        <strong class="text-on-surface font-semibold truncate">{{ auth()->user()->name }}</strong>
                        <span class="text-tertiary text-[11px] truncate">({{ auth()->user()->email }})</span>
                    </div>
                </div>
            </div>
        @else
            <div x-show="hasStoredProfile && !showEditProfile" x-cloak class="p-3 bg-white border border-border-subtle rounded-[2px] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs font-mono min-w-0">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="size-6 rounded-full bg-primary-tint border border-primary/20 flex items-center justify-center font-bold text-primary text-[10px] shrink-0">
                        <span x-text="storedName ? storedName.charAt(0).toUpperCase() : '?'"></span>
                    </div>
                    <div class="min-w-0 flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5">
                        <span class="text-tertiary text-[11px] shrink-0">Berkomentar sebagai:</span>
                        <strong class="text-on-surface font-semibold truncate max-w-[140px] sm:max-w-[220px]" x-text="storedName"></strong>
                        <span class="text-tertiary text-[11px] truncate max-w-[180px] sm:max-w-none">
                            (<span x-text="storedEmail" class="break-all"></span>)
                        </span>
                    </div>
                </div>
                <button
                    type="button"
                    @click="changeProfile"
                    class="self-start sm:self-auto text-primary hover:underline text-[11px] font-mono cursor-pointer shrink-0"
                >
                    Ganti Profil
                </button>
            </div>

            <div x-show="!hasStoredProfile || showEditProfile" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
            <span class="font-mono text-[11px] text-tertiary leading-relaxed">
                Mendukung teks santun & diskusi profesional.
            </span>
            <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto shrink-0" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Kirim Komentar</span>
                <span wire:loading wire:target="save">Mengirim...</span>
            </x-button>
        </div>
    </form>

    <div class="space-y-6">
        @forelse ($this->comments as $comment)
            @include('livewire.blog.partials.comment-node', [
                'comment' => $comment,
                'depth' => 0,
                'post' => $post,
            ])
        @empty
            <div class="py-12 text-center bg-surface border border-border-subtle rounded-[3px] p-6">
                <p class="font-serif text-lg font-light text-on-surface">Belum ada diskusi pada artikel ini.</p>
                <p class="font-mono text-xs text-tertiary mt-1">Jadilah yang pertama untuk meninggalkan komentar.</p>
            </div>
        @endforelse
    </div>
</section>
