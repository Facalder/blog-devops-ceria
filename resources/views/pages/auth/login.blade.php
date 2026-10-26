<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-5">
        <div class="text-center">
            <h1 class="font-serif text-2xl font-light text-on-surface">Masuk ke CMS</h1>
            <p class="mt-1 text-xs text-tertiary">Kelola dan publikasikan konten blog Anda</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                    Email Address
                </label>
                <x-input
                    id="email"
                    name="email"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="nama@domain.com"
                    :error="$errors->first('email')"
                />
                @error('email')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-mono uppercase tracking-wider text-tertiary">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request')" wire:navigate class="text-xs font-mono text-primary hover:underline">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <x-input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    :error="$errors->first('password')"
                />
                @error('password')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <label class="flex items-center gap-2 text-xs text-tertiary cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="remember"
                        class="rounded-[2px] border-border-subtle text-primary focus:ring-primary"
                        {{ old('remember') ? 'checked' : '' }}
                    />
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <div class="pt-2">
                <x-button variant="primary" type="submit" class="w-full" data-test="login-button">
                    Masuk
                </x-button>
            </div>
        </form>

        <div class="text-center text-xs text-tertiary pt-2 border-t border-border-subtle">
            Belum punya akun?
            <a href="{{ route('register') }}" wire:navigate class="text-primary font-medium hover:underline ml-1">
                Daftar Writer
            </a>
        </div>
    </div>
</x-layouts::auth>
