<x-layouts.auth :title="'Masuk'">
    <h2 class="mb-1 text-xl font-bold dark:text-white">Masuk ke akunmu</h2>
    <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">Pakai email atau NIS untuk masuk.</p>

    @if (session('sukses'))
        <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('sukses') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="login" class="label">Email atau NIS</label>
            <input id="login" name="login" type="text" value="{{ old('login') }}"
                   placeholder="siswa@osisconnect.test / SW001"
                   class="input @error('login') border-rose-500 @enderror" required autofocus>
            @error('login')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label">Kata Sandi</label>
            <input id="password" name="password" type="password" class="input" placeholder="••••••••" required>
            @error('password')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">Lupa sandi?</a>
        </div>

        <button type="submit" class="btn-primary w-full">Masuk</button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500 dark:text-slate-400">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">Daftar sekarang</a>
    </p>

    <div class="mt-5 rounded-xl bg-slate-50 p-3 text-xs text-slate-500 dark:bg-slate-900 dark:text-slate-400">
        <p class="mb-1 font-semibold">Akun uji (password: <code class="rounded bg-slate-200 px-1 dark:bg-slate-700">password</code>)</p>
        <p>Admin: <code>admin@osisconnect.test</code></p>
        <p>Guru: <code>guru@osisconnect.test</code></p>
        <p>Siswa: <code>siswa@osisconnect.test</code> atau NIS <code>SW001</code></p>
    </div>
</x-layouts.auth>
