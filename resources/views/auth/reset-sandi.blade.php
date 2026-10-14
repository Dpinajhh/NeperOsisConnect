<x-layouts.auth :title="'Reset Kata Sandi'">
    <h2 class="mb-1 text-xl font-bold dark:text-white">Buat kata sandi baru</h2>
    <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">Untuk akun <strong>{{ $email }}</strong></p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <label for="password" class="label">Kata Sandi Baru</label>
            <input id="password" name="password" type="password" class="input" placeholder="Minimal 8 karakter" required autofocus>
            @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Ulangi Kata Sandi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="input" placeholder="Ulangi kata sandi" required>
        </div>

        <button type="submit" class="btn-primary w-full">Simpan Kata Sandi Baru</button>
    </form>
</x-layouts.auth>
