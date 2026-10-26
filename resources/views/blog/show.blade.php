<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $post->title }} - {{ config('app.name', 'DevOps Ceria') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-on-surface antialiased flex flex-col font-sans">
        {{-- Top Navigation --}}
        <header class="border-b border-border-subtle bg-white/90 backdrop-blur-xs sticky top-0 z-30">
            <div class="max-w-4xl mx-auto px-5 h-16 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="font-serif text-xl font-normal text-on-surface tracking-tight group-hover:text-primary transition-colors">
                        &larr; DevOps Ceria
                    </span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <x-button :href="route('dashboard')" variant="secondary" size="sm">
                            Dashboard CMS
                        </x-button>
                    @else
                        <x-button :href="route('login')" variant="secondary" size="sm">
                            Log in
                        </x-button>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Article Header & Body --}}
        <main class="flex-1 max-w-3xl w-full mx-auto px-5 py-12 md:py-16">
            <article class="space-y-8">
                {{-- Metadata Top --}}
                <div class="space-y-4 border-b border-border-subtle pb-8">
                    <div class="flex items-center gap-2 font-mono text-xs text-tertiary">
                        @if ($post->category)
                            <a href="{{ route('home', ['category' => $post->category->slug]) }}">
                                <x-badge variant="primary">{{ $post->category->name }}</x-badge>
                            </a>
                        @endif
                        <span>&bull;</span>
                        <span>{{ $post->reading_time }} menit membaca</span>
                        <span>&bull;</span>
                        <time datetime="{{ $post->published_at?->toDateString() }}">
                            {{ $post->published_at?->format('d F Y') }}
                        </time>
                    </div>

                    <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-light text-on-surface tracking-tight leading-tight">
                        {{ $post->title }}
                    </h1>

                    <div class="flex items-center gap-3 pt-2">
                        <div class="size-8 rounded-full bg-primary-tint border border-primary/20 flex items-center justify-center font-mono text-xs text-primary font-medium">
                            {{ $post->user->initials() }}
                        </div>
                        <div>
                            <div class="font-medium text-sm text-on-surface">{{ $post->user->name }}</div>
                            <div class="font-mono text-xs text-tertiary">Penulis Konten &bull; {{ ucfirst($post->user->role) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Excerpt / Subheading --}}
                @if ($post->excerpt)
                    <div class="border-l-2 border-primary pl-4 font-serif text-lg text-on-surface/90 italic">
                        {{ $post->excerpt }}
                    </div>
                @endif

                {{-- Rendered Markdown Body --}}
                <div class="prose max-w-none text-base leading-relaxed text-on-surface">
                    {!! $post->html_content !!}
                </div>

                {{-- Article Footer --}}
                <div class="pt-10 mt-12 border-t border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4 font-mono text-xs text-tertiary">
                    <a href="{{ route('home') }}" class="text-primary hover:underline">
                        &larr; Kembali ke daftar artikel
                    </a>
                    <div>
                        Dipublikasikan pada {{ $post->published_at?->format('d M Y') }}
                    </div>
                </div>
            </article>
        </main>

        {{-- Minimal Footer --}}
        <footer class="border-t border-border-subtle bg-surface py-8 mt-auto">
            <div class="max-w-4xl mx-auto px-5 text-center font-mono text-xs text-tertiary">
                &copy; {{ date('Y') }} DevOps Ceria. All content licensed for technical education.
            </div>
        </footer>
    </body>
</html>
