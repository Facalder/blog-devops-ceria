<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-surface text-on-surface antialiased flex flex-col items-center justify-center p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 group" wire:navigate>
                <span class="font-serif text-2xl font-light text-on-surface tracking-tight group-hover:text-primary transition-colors">
                    {{ config('app.name', 'Laravel') }}
                </span>
                <span class="font-mono text-xs text-tertiary">DevOps & Cloud Publishing</span>
            </a>

            <div class="rounded-[2px] border border-border-subtle bg-white p-6 sm:p-8 shadow-xs">
                {{ $slot }}
            </div>

            <div class="text-center font-mono text-xs text-tertiary">
                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
