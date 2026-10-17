<x-layouts.app :title="'Profil Saya'">
    <div class="mx-auto max-w-2xl space-y-4">

        <div>
            <h1 class="text-2xl font-bold dark:text-white">Profil Saya</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perbarui data profil dan kata sandi akunmu.</p>
        </div>

        {{-- ─── Data diri ─────────────────────────────────── --}}
        <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="card space-y-5 p-5">
            @csrf
            @method('PUT')

            <div class="flex flex-wrap items-center gap-4">
                <img id="pratinjau-avatar" src="{{ $user->avatar_url }}" alt=""
                     class="h-16 w-16 shrink-0 rounded-2xl object-cover shadow-sm">

                <div class="min-w-0 flex-1">
                    <label for="avatar" class="label">Foto profil</label>
                    <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp"
                           class="block w-full cursor-pointer text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:text-slate-400 dark:file:bg-slate-700 dark:file:text-slate-200">
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                    @error('avatar')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="name" class="label">Nama lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="input" required>
                @error('name')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="bio" class="label">Bio</label>
                <textarea id="bio" name="bio" rows="3" maxlength="255" class="input"
                          placeholder="Ceritakan sedikit tentang dirimu...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="telepon" class="label">No. WhatsApp <span class="text-slate-400">(opsional)</span></label>
                <input id="telepon" name="telepon" type="text" value="{{ old('telepon', $user->telepon) }}"
                       class="input" placeholder="08xxxxxxxxxx">
                @error('telepon')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>

            {{-- Identitas: hanya pengelola yang boleh mengubah --}}
            <div class="grid gap-3 rounded-xl bg-slate-50 p-4 text-xs sm:grid-cols-3 dark:bg-slate-900">
                <div>
                    <p class="font-semibold uppercase tracking-wide text-slate-400">Email</p>
                    <p class="mt-1 truncate text-slate-600 dark:text-slate-300">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="font-semibold uppercase tracking-wide text-slate-400">NIS</p>
                    <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $user->nis ?: '—' }}</p>
                </div>
                <div>
                    <p class="font-semibold uppercase tracking-wide text-slate-400">Kelas</p>
                    <p class="mt-1 text-slate-600 dark:text-slate-300">
                        {{ $user->kelas?->nama ?? '—' }}@if ($user->kelas?->jurusan) &bull; {{ $user->kelas->jurusan->kode }}@endif
                    </p>
                </div>
                <p class="text-slate-400 sm:col-span-3 dark:text-slate-500">
                    Email, NIS, dan kelas hanya bisa diubah oleh pengelola OSIS.
                </p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>

        {{-- ─── Ganti kata sandi ──────────────────────────── --}}
        <form method="POST" action="{{ route('profil.sandi') }}" class="card space-y-4 p-5">
            @csrf
            @method('PUT')

            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Ganti Kata Sandi</h2>

            <div>
                <label for="sandi_lama" class="label">Kata sandi sekarang</label>
                <input id="sandi_lama" name="sandi_lama" type="password" class="input"
                       autocomplete="current-password" required>
                @error('sandi_lama')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="sandi" class="label">
                        Kata sandi baru <span class="font-normal text-slate-400">(min. 8 karakter)</span>
                    </label>
                    <input id="sandi" name="sandi" type="password" class="input" autocomplete="new-password" required>
                    @error('sandi')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="sandi_confirmation" class="label">Ulangi kata sandi baru</label>
                    <input id="sandi_confirmation" name="sandi_confirmation" type="password" class="input"
                           autocomplete="new-password" required>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Ganti Kata Sandi</button>
            </div>
        </form>
    </div>

    <script>
        // Pratinjau avatar sebelum berkas diunggah
        document.addEventListener('DOMContentLoaded', function () {
            const masukan = document.getElementById('avatar');
            const pratinjau = document.getElementById('pratinjau-avatar');
            if (!masukan || !pratinjau) return;

            masukan.addEventListener('change', function (e) {
                const berkas = e.target.files && e.target.files[0];
                if (berkas) pratinjau.src = URL.createObjectURL(berkas);
            });
        });
    </script>
</x-layouts.app>
