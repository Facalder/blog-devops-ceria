<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-5">
        <div class="text-center">
            <h1 class="font-serif text-2xl font-light text-on-surface">Daftar Akun Writer</h1>
            <p class="mt-1 text-xs text-tertiary">Mulai menulis dan publikasikan artikel blog</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                    Nama Lengkap
                </label>
                <x-input
                    id="name"
                    name="name"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nama Penulis"
                    :error="$errors->first('name')"
                />
                @error('name')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

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
                <label for="password" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                    Password
                </label>
                <x-input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    :error="$errors->first('password')"
                />
                @error('password')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-mono uppercase tracking-wider text-tertiary mb-1">
                    Konfirmasi Password
                </label>
                <x-input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    :error="$errors->first('password_confirmation')"
                />
                @error('password_confirmation')
                    <p class="mt-1 text-xs text-[#E45757]">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <x-button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    Daftar Sebagai Writer
                </x-button>
            </div>
        </form>

        <div class="text-center text-xs text-tertiary pt-2 border-t border-border-subtle">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" wire:navigate class="text-primary font-medium hover:underline ml-1">
                Masuk ke CMS
            </a>
        </div>
    </div>
</x-layouts::auth>
