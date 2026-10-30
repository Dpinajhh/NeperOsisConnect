<x-layouts.app :title="'Ubah Postingan'">
    <div class="mx-auto max-w-2xl space-y-4">
        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            &larr; Kembali ke postingan
        </a>

        <div>
            <h1 class="text-2xl font-bold dark:text-white">Ubah Postingan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Perbaiki tulisan atau ubah siapa yang boleh melihatnya.
            </p>
        </div>

        <form method="POST" action="{{ route('posts.update', $post) }}" class="card space-y-4 p-5">
            @csrf
            @method('PUT')

            <div>
                <label for="konten" class="label">Isi postingan</label>
                <textarea id="konten" name="konten" rows="6" maxlength="2000" required
                          class="input" placeholder="Tulis sesuatu...">{{ old('konten', $post->konten) }}</textarea>
                @error('konten')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="visibilitas" class="label">Siapa yang boleh melihat?</label>
                <select id="visibilitas" name="visibilitas" class="input" required>
                    <option value="sekolah" @selected(old('visibilitas', $post->visibilitas) === 'sekolah')>Seluruh sekolah</option>
                    <option value="jurusan" @selected(old('visibilitas', $post->visibilitas) === 'jurusan') @disabled(! auth()->user()->kelas?->jurusan_id)>
                        Jurusan {{ auth()->user()->kelas?->jurusan?->kode }}
                    </option>
                    <option value="kelas" @selected(old('visibilitas', $post->visibilitas) === 'kelas') @disabled(! auth()->user()->kelas_id)>
                        Kelas {{ auth()->user()->kelas?->nama }}
                    </option>
                </select>
                @error('visibilitas')<p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $message }}</p>@enderror
            </div>

            @if ($post->gambar)
                <div class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Gambar saat ini</p>
                    <img src="{{ asset('storage/'.$post->gambar) }}" alt="Gambar postingan" class="max-h-56 w-full rounded-lg object-cover">
                    <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">Gambar tidak diubah dari halaman ini.</p>
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <a href="{{ route('posts.show', $post) }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layouts.app>
