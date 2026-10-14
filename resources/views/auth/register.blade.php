<x-layouts.auth :title="'Daftar'">
    <h2 class="mb-1 text-xl font-bold dark:text-white">Buat akun siswa</h2>
    <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">Daftar untuk ikut berbagi cerita & dapat info sekolah.</p>

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="label">Nama Lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" class="input" placeholder="Nama kamu" required>
            @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="nis" class="label">NIS</label>
                <input id="nis" name="nis" type="text" value="{{ old('nis') }}" class="input" placeholder="SW001" required>
                @error('nis')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="kelas_id" class="label">Kelas</label>
                <select id="kelas_id" name="kelas_id" class="input" required>
                    <option value="">— Pilih kelas —</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected(old('kelas_id') == $kelas->id)>
                            {{ $kelas->nama }} ({{ $kelas->jurusan->kode }})
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" placeholder="nama@email.com" required>
            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="telepon" class="label">No. WhatsApp <span class="text-slate-400">(opsional)</span></label>
            <input id="telepon" name="telepon" type="text" value="{{ old('telepon') }}" class="input" placeholder="08xxxxxxxxxx">
            @error('telepon')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="password" class="label">Kata Sandi</label>
                <input id="password" name="password" type="password" class="input" placeholder="Minimal 8 karakter" required>
                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Ulangi Kata Sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" placeholder="Ulangi kata sandi" required>
            </div>
        </div>

        <label class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
            <input type="checkbox" name="syarat" value="1" class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-900" required>
            <span>Saya berjanji menjaga keramahan, tidak menyebar hoaks, dan tidak melakukan bullying.</span>
        </label>
        @error('syarat')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror

        <button type="submit" class="btn-primary w-full">Daftar Sekarang</button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-500 dark:text-slate-400">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">Masuk di sini</a>
    </p>
</x-layouts.auth>
