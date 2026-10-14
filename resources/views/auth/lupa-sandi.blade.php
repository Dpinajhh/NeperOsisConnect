<x-layouts.auth :title="'Lupa Kata Sandi'">
    <h2 class="mb-1 text-xl font-bold dark:text-white">Lupa kata sandi?</h2>
    <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">
        Masukkan email akunmu. Kami akan mengirim tautan untuk membuat kata sandi baru.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" placeholder="nama@email.com" required autofocus>
            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-primary w-full">Kirim Tautan Reset</button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500 dark:text-slate-400">
        Ingat passwordnya?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">Kembali ke login</a>
    </p>
</x-layouts.auth>
