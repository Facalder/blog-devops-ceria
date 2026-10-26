<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border-subtle pb-5">
        <div>
            <h1 class="font-serif text-3xl font-light text-on-surface">Management User</h1>
            <p class="mt-1 text-sm text-tertiary">Daftar pengguna terdaftar dan role kontributor blog.</p>
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
                placeholder="Cari nama atau email..."
            />
        </div>

        <div>
            <select
                wire:model.live="roleFilter"
                class="rounded-[2px] border border-border-subtle bg-white px-3 py-2 text-sm text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
            >
                <option value="">Semua Role</option>
                <option value="writer">Writer</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="overflow-hidden rounded-[2px] border border-border-subtle bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border-subtle bg-[#F2F4F7] text-xs font-mono uppercase tracking-wider text-[#344054] font-semibold">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Artikel Dibuat</th>
                        <th class="px-4 py-3">Terdaftar Sejak</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($this->users as $user)
                        <tr class="hover:bg-[#FBFBFC] transition-colors">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex size-7 items-center justify-center rounded-full bg-primary-tint font-mono text-xs font-medium text-primary">
                                        {{ $user->initials() }}
                                    </div>
                                    <span class="font-medium text-on-surface">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs text-tertiary whitespace-nowrap">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($user->role === 'writer')
                                    <x-badge variant="primary">Writer</x-badge>
                                @elseif ($user->role === 'admin')
                                    <x-badge variant="warning">Admin</x-badge>
                                @else
                                    <x-badge variant="neutral">{{ ucfirst($user->role) }}</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-mono text-xs text-tertiary">{{ $user->posts_count }} artikel</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-mono text-xs text-tertiary">
                                {{ $user->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <x-button type="button" wire:click="editRole({{ $user->id }})" size="sm" variant="secondary">
                                    Ubah Role
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-tertiary">
                                Tidak ada user yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->users->hasPages())
            <div class="border-t border-border-subtle p-4">
                {{ $this->users->links() }}
            </div>
        @endif
    </div>

    {{-- Edit Role Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-sm rounded-[2px] border border-border-subtle bg-white p-6 shadow-lg">
                <div class="flex items-center justify-between border-b border-border-subtle pb-3">
                    <h2 class="font-serif text-lg font-light text-on-surface">Ubah Role User</h2>
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="text-tertiary hover:text-on-surface cursor-pointer text-lg leading-none"
                    >
                        &times;
                    </button>
                </div>

                <form wire:submit="updateRole" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                            Role Kontributor
                        </label>
                        <select
                            wire:model="selectedRole"
                            class="w-full rounded-[2px] border border-border-subtle bg-white px-3 py-2 text-sm text-on-surface focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                        >
                            <option value="writer">Writer (Penulis Konten Blog)</option>
                            <option value="admin">Admin</option>
                            <option value="user">User Biasa</option>
                        </select>
                        @error('selectedRole')
                            <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-border-subtle">
                        <x-button type="button" wire:click="$set('showModal', false)" variant="secondary" size="sm">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" size="sm">
                            Simpan Perubahan
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
