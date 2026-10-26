<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'DevOps Ceria') }} - Software Engineering & Cloud Notes</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#F8F9FC] text-on-surface antialiased flex flex-col font-sans">
        {{-- Top Navigation --}}
        <header class="border-b border-border-subtle bg-white sticky top-0 z-30 shadow-2xs">
            <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <span class="font-serif text-2xl font-normal text-on-surface tracking-tight group-hover:text-primary transition-colors">
                        DevOps Ceria
                    </span>
                    <span class="hidden sm:inline-block font-mono text-[11px] text-tertiary px-2 py-0.5 rounded-[2px] bg-surface border border-border-subtle uppercase tracking-wider font-semibold">
                        Blog
                    </span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-3">
                            <span class="hidden md:inline font-mono text-xs text-tertiary">
                                Penulis: <strong class="text-on-surface font-semibold">{{ auth()->user()->name }}</strong>
                            </span>
                            <x-button :href="route('dashboard')" variant="primary" size="sm">
                                Dashboard CMS &rarr;
                            </x-button>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="font-mono text-xs text-tertiary hover:text-[#D92D20] transition-colors cursor-pointer px-2.5 py-1.5 rounded-[3px] border border-border-subtle hover:border-[#D92D20]/40 bg-white">
                                    Log out
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-mono uppercase tracking-wider text-on-surface font-medium hover:text-primary px-3 py-1.5 transition-colors">
                            Masuk
                        </a>
                        <x-button :href="route('register')" variant="primary" size="sm">
                            Daftar Writer
                        </x-button>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Hero Header --}}
        <section class="border-b border-border-subtle bg-white py-14 sm:py-16">
            <div class="max-w-4xl mx-auto px-5 text-center">
                <div class="inline-flex items-center gap-2 mb-4">
                    <x-badge variant="primary">Kumpulan Artikel & Tutorial</x-badge>
                </div>
                <h1 class="font-serif text-4xl sm:text-5xl font-light text-on-surface tracking-tight leading-tight">
                    DevOps, Cloud & System Architecture
                </h1>
                <p class="mt-3 text-base sm:text-lg text-tertiary max-w-2xl mx-auto leading-relaxed">
                    Eksplorasi teknis praktis seputar cloud native, Kubernetes, CI/CD pipelines, dan software craftsmanship yang ditulis langsung oleh para engineer.
                </p>

                {{-- Search Bar --}}
                <form method="GET" action="{{ route('home') }}" class="mt-8 max-w-lg mx-auto flex items-center gap-2">
                    @if ($selectedCategorySlug)
                        <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
                    @endif
                    <div class="relative flex-1">
                        <input
                            type="search"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari artikel berdasarkan judul atau topik..."
                            class="w-full bg-surface text-on-surface font-sans text-sm rounded-[3px] pl-3.5 pr-8 py-2.5 border border-border-subtle focus:outline-hidden focus:border-primary focus:bg-white focus:ring-1 focus:ring-primary placeholder:text-tertiary transition-colors"
                        />
                        @if ($search)
                            <a
                                href="{{ route('home', $selectedCategorySlug ? ['category' => $selectedCategorySlug] : []) }}"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-tertiary hover:text-on-surface font-mono text-xs"
                                title="Hapus pencarian"
                            >
                                &times;
                            </a>
                        @endif
                    </div>
                    <x-button type="submit" variant="primary" size="md">
                        Cari
                    </x-button>
                </form>

                {{-- Category Filter Chips --}}
                @if ($categories->isNotEmpty())
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                        <a
                            href="{{ route('home', $search ? ['search' => $search] : []) }}"
                            class="inline-flex items-center font-mono text-xs px-3 py-1 rounded-full border transition-colors {{ ! $selectedCategorySlug ? 'bg-primary text-white border-primary font-semibold' : 'bg-white text-on-surface border-border-subtle hover:border-primary hover:text-primary font-medium' }}"
                        >
                            Semua Topik
                        </a>
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('home', array_filter(['category' => $category->slug, 'search' => $search])) }}"
                                class="inline-flex items-center gap-1.5 font-mono text-xs px-3 py-1 rounded-full border transition-colors {{ $selectedCategorySlug === $category->slug ? 'bg-primary text-white border-primary font-semibold' : 'bg-white text-on-surface border-border-subtle hover:border-primary hover:text-primary font-medium' }}"
                            >
                                <span>{{ $category->name }}</span>
                                <span class="{{ $selectedCategorySlug === $category->slug ? 'text-white/80' : 'text-tertiary' }} text-[11px]">({{ $category->posts_count }})</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Main Content: Kumpulan Blog --}}
        <main class="flex-1 max-w-6xl w-full mx-auto px-5 py-10 sm:py-12">
            {{-- Header Feed Status --}}
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-border-subtle">
                <div class="flex items-center gap-2">
                    <h2 class="font-serif text-2xl font-light text-on-surface">
                        @if ($search)
                            Hasil pencarian: <span class="font-normal text-primary">&ldquo;{{ $search }}&rdquo;</span>
                        @elseif ($selectedCategorySlug)
                            Kategori: <span class="font-normal text-primary">{{ $categories->firstWhere('slug', $selectedCategorySlug)?->name ?? $selectedCategorySlug }}</span>
                        @else
                            Semua Artikel Blog
                        @endif
                    </h2>
                    <span class="font-mono text-xs text-tertiary">
                        ({{ $posts->total() }} artikel)
                    </span>
                </div>

                @if ($search || $selectedCategorySlug)
                    <a href="{{ route('home') }}" class="font-mono text-xs text-primary hover:underline">
                        &larr; Reset Filter
                    </a>
                @endif
            </div>

            {{-- Articles Grid --}}
            @if ($posts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <x-article-card :post="$post" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($posts->hasPages())
                    <div class="mt-12 pt-8 border-t border-border-subtle">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <div class="py-16 text-center bg-white border border-border-subtle rounded-[2px] p-8">
                    <div class="font-serif text-2xl font-light text-on-surface mb-2">Belum ada artikel ditemukan.</div>
                    <p class="text-sm text-tertiary max-w-md mx-auto mb-6">
                        @if ($search)
                            Tidak ada artikel yang cocok dengan kata kunci "{{ $search }}". Silakan gunakan kata kunci lain.
                        @else
                            Belum ada artikel yang dipublikasikan pada kategori ini.
                        @endif
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        @if ($search || $selectedCategorySlug)
                            <x-button :href="route('home')" variant="secondary">
                                Tampilkan Semua Artikel
                            </x-button>
                        @endif
                        @auth
                            <x-button :href="route('posts.create')" variant="primary">
                                Tulis Artikel Baru &rarr;
                            </x-button>
                        @endauth
                    </div>
                </div>
            @endif
        </main>

        {{-- Footer --}}
        <footer class="border-t border-border-subtle bg-white py-10 mt-auto">
            <div class="max-w-6xl mx-auto px-5 flex flex-col sm:flex-row items-center justify-between gap-4 font-mono text-xs text-tertiary">
                <div class="flex items-center gap-3">
                    <span class="font-serif text-base font-normal text-on-surface">DevOps Ceria</span>
                    <span>&bull;</span>
                    <span>Editor-forward Tech Publication</span>
                </div>
                <div>
                    &copy; {{ date('Y') }} DevOps Ceria. All content licensed under Zed Dev System.
                </div>
            </div>
        </footer>
    </body>
</html>
