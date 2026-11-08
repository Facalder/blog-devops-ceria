@php
    $currentYear = now()->year;

    $monthlyStatistics = \App\Support\MonthlyPublicationStatistics::countByMonth(
        \App\Models\Post::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->get(['status', 'published_at'])
            ->map(fn ($post) => [
                'status' => $post->status,
                'published_at' => $post->published_at,
            ])
            ->all(),
        $currentYear
    );
    $postStatuses = \App\Support\PostStatusStatistics::countByStatus(
        \App\Models\Post::query()->get(['status'])
            ->map(fn ($post) => ['status' => $post->status])
            ->all()
    );
    $totalPosts = $postStatuses['total'];
    $publishedPosts = $postStatuses['published'];
    $draftPosts = $postStatuses['draft'];
    $totalCategories = \App\Models\Category::count();
    $totalUsers = \App\Models\User::count();
    $recentPosts = \App\Models\Post::with(['user', 'category'])->latest()->take(5)->get();
    $publicationRate = \App\Support\PublicationStatistics::percentage(
        $publishedPosts,
        $totalPosts
    );
    $publicationStatus = \App\Support\PublicationStatistics::status(
        $publicationRate
    );
@endphp

<x-layouts::app :title="__('Dashboard Overview')">
    <div class="space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border-subtle pb-6">
            <div>
                <h1 class="font-serif text-3xl font-light text-on-surface">Dashboard CMS</h1>
                <p class="mt-1 text-sm text-tertiary">
                    Selamat datang kembali, <span class="text-on-surface font-medium">{{ auth()->user()->name }}</span>. Status role: <span class="font-mono text-xs uppercase text-primary font-semibold">{{ auth()->user()->role }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-button :href="route('posts.create')" wire:navigate variant="primary">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tulis Artikel Baru</span>
                </x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-card class="space-y-1">
                <div class="font-mono text-xs uppercase tracking-wider text-tertiary">Total Artikel</div>
                <div class="font-serif text-3xl font-light text-on-surface">{{ $totalPosts }}</div>
                <div class="text-xs text-tertiary">Semua konten dibuat</div>
            </x-card>

            <x-card class="space-y-1">
                <div class="font-mono text-xs uppercase tracking-wider text-tertiary">Artikel Terbit</div>
                <div class="font-serif text-3xl font-light text-primary">{{ $publishedPosts }}</div>
                <div class="text-xs text-tertiary">Tampil di halaman global</div>
            </x-card>

            <x-card class="space-y-1">
                <div class="font-mono text-xs uppercase tracking-wider text-tertiary">Draf</div>
                <div class="font-serif text-3xl font-light text-on-surface">{{ $draftPosts }}</div>
                <div class="text-xs text-tertiary">Belum dipublikasikan</div>
            </x-card>

            <x-card class="space-y-1">
                <div class="font-mono text-xs uppercase tracking-wider text-tertiary">Kategori</div>
                <div class="font-serif text-3xl font-light text-on-surface">{{ $totalCategories }}</div>
                <div class="text-xs text-tertiary">Kategori aktif</div>
            </x-card>
        </div>

        <div class="space-y-4">
            <div>
                <h2 class="font-serif text-xl font-light text-on-surface">
                    Monitoring Publikasi
                </h2>
                
            <div class="space-y-4">
                <h2 class="font-serif text-xl font-light text-on-surface">
                    Statistik Publikasi Bulanan {{ $currentYear }}
                </h2>

                <x-card>
                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
                        @foreach ($monthlyStatistics as $month => $count)
                            <div class="rounded border border-gray-200 p-4 text-center">
                                <p class="text-xs text-tertiary">
                                    {{ \Carbon\Carbon::create($currentYear, $month, 1)->locale('id')->translatedFormat('M') }}
                                </p>

                                <p class="mt-2 text-2xl font-semibold text-primary">
                                    {{ $count }}
                                </p>

                                <p class="text-xs text-tertiary">
                                    Artikel
                                </p>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

                <p class="text-xs text-tertiary">
                    Statistik publikasi artikel pada CMS
                </p>
            </div>

            <x-card class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-tertiary">
                        Tingkat Publikasi Artikel
                    </span>

                    <span class="text-2xl font-semibold text-primary">
                        {{ $publicationRate }}%
                    </span>
                </div>

                
                <div class="w-full rounded-full overflow-hidden"
                    style="height: 12px; background-color: #E5E7EB;">

                    <div class="rounded-full"
                        style="width: {{ $publicationRate }}%;
                                height: 100%;
                                background-color: #16A34A;
                                transition: width 0.5s ease;">
                    </div>

                </div>

                <div class="flex justify-between text-xs text-tertiary">
                    <span>
                        {{ $publishedPosts }} artikel diterbitkan
                    </span>

                    <span>
                        {{ $totalPosts - $publishedPosts }} artikel belum diterbitkan
                    </span>
                </div>
            </x-card>
            <p class="text-sm text-tertiary">
                Status Publikasi:
                <span class="font-semibold text-primary">
                    {{ $publicationStatus }}
                </span>
            </p>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-xl font-light text-on-surface">Artikel Terbaru</h2>
                    <p class="text-xs text-tertiary">5 konten blog terakhir yang diperbarui</p>
                </div>
                <a href="{{ route('posts.index') }}" wire:navigate class="text-xs font-mono text-primary hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-hidden rounded-[2px] border border-border-subtle bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border-subtle bg-[#F2F4F7] text-xs font-mono uppercase tracking-wider text-[#344054] font-semibold">
                            <tr>
                                <th class="px-4 py-3">Judul Artikel</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Penulis</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle">
                            @forelse ($recentPosts as $post)
                                <tr class="hover:bg-[#FBFBFC] transition-colors">
                                    <td class="px-4 py-3.5">
                                        <div class="font-serif text-sm font-normal text-on-surface">
                                            {{ $post->title }}
                                        </div>
                                        <div class="font-mono text-xs text-tertiary">
                                            /{{ $post->slug }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        @if ($post->category)
                                            <x-badge variant="neutral">{{ $post->category->name }}</x-badge>
                                        @else
                                            <span class="text-xs text-tertiary">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-on-surface">
                                        {{ $post->user->name }}
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
                                        <x-button :href="route('posts.edit', $post)" wire:navigate size="sm" variant="secondary">
                                            Edit
                                        </x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-tertiary">
                                        Belum ada artikel. Klik tombol "Tulis Artikel Baru" di atas untuk memulai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-card :href="route('posts.index')" wire:navigate hoverable="true" class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs uppercase tracking-wider text-tertiary">Modul</span>
                    <span class="text-xs font-mono text-primary">&rarr;</span>
                </div>
                <div class="font-serif text-lg font-light text-on-surface">Management Blog</div>
                <p class="text-xs text-tertiary">Daftar lengkap artikel, filter status publikasi, dan kelola draf.</p>
            </x-card>

            <x-card :href="route('categories.index')" wire:navigate hoverable="true" class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs uppercase tracking-wider text-tertiary">Modul</span>
                    <span class="text-xs font-mono text-primary">&rarr;</span>
                </div>
                <div class="font-serif text-lg font-light text-on-surface">Management Kategori</div>
                <p class="text-xs text-tertiary">Tambah atau ubah taksonomi kategori konten blog.</p>
            </x-card>

            <x-card :href="route('users.index')" wire:navigate hoverable="true" class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs uppercase tracking-wider text-tertiary">Modul</span>
                    <span class="text-xs font-mono text-primary">&rarr;</span>
                </div>
                <div class="font-serif text-lg font-light text-on-surface">Management User</div>
                <p class="text-xs text-tertiary">Daftar akun pengguna dan verifikasi role kontributor.</p>
            </x-card>
        </div>
    </div>
</x-layouts::app>
