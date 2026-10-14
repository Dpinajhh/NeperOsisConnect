<x-layouts.app :title="'Kelola Pengumuman'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Kelola Pengumuman</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buat informasi untuk seluruh sekolah, satu jurusan, atau satu kelas.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.3fr]">
        {{-- ─── Form ───────────────────────────────────────────── --}}
        <form method="POST"
              action="{{ $edit ? route('admin.pengumuman.update', $edit) : route('admin.pengumuman.store') }}"
              enctype="multipart/form-data"
              class="card space-y-3 p-5">
            @csrf
            @if ($edit) @method('PUT') @endif

            <h2 class="text-sm font-bold dark:text-white">{{ $edit ? 'Edit pengumuman' : 'Tulis pengumuman baru' }}</h2>

            <div>
                <label class="label" for="judul">Judul</label>
                <input id="judul" name="judul" class="input" value="{{ old('judul', $edit->judul ?? '') }}" required>
            </div>

            <div>
                <label class="label" for="konten">Isi Pengumuman</label>
                <textarea id="konten" name="konten" rows="6" class="input" required>{{ old('konten', $edit->konten ?? '') }}</textarea>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label" for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id" class="input">
                        <option value="">— Tanpa kategori —</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" @selected(old('kategori_id', $edit->kategori_id ?? '') == $k->id)>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="cover">Cover <span class="text-slate-400">(opsional)</span></label>
                    <input id="cover" name="cover" type="file" accept="image/*" class="input">
                </div>
                <div>
                    <label class="label" for="target">Tujuan</label>
                    <select id="target" name="target" class="input">
                        <option value="semua" @selected(old('target', $edit->target ?? 'semua') === 'semua')>Seluruh sekolah</option>
                        <option value="jurusan" @selected(old('target', $edit->target ?? 'semua') === 'jurusan')>Satu jurusan</option>
                        <option value="kelas" @selected(old('target', $edit->target ?? 'semua') === 'kelas')>Satu kelas</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="jurusan_id">Jurusan (jika bertarget jurusan)</label>
                    <select id="jurusan_id" name="jurusan_id" class="input">
                        <option value="">—</option>
                        @foreach ($jurusan as $j)
                            <option value="{{ $j->id }}" @selected(old('jurusan_id', $edit->jurusan_id ?? '') == $j->id)>{{ $j->kode }} — {{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="kelas_id">Kelas (jika bertarget kelas)</label>
                    <select id="kelas_id" name="kelas_id" class="input">
                        <option value="">—</option>
                        @foreach ($kelas as $kl)
                            <option value="{{ $kl->id }}" @selected(old('kelas_id', $edit->kelas_id ?? '') == $kl->id)>{{ $kl->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $edit->is_pinned ?? false)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Sematkan di atas
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $edit ? 'Simpan' : 'Terbitkan' }}</button>
                @if ($edit)
                    <a href="{{ route('admin.pengumuman.kelola') }}" class="btn-ghost">Batal</a>
                @endif
            </div>
        </form>

        {{-- ─── Daftar ─────────────────────────────────────────── --}}
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Judul</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Dilihat</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($pengumuman as $p)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $p->judul }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    @if ($p->target === 'semua') Seluruh sekolah
                                    @elseif ($p->target === 'kelas') Kelas {{ $p->kelas?->nama }}
                                    @else Jurusan {{ $p->jurusan?->kode }}
                                    @endif
                                    @if ($p->is_pinned) &bull; 📌 sematkan @endif
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                                @if ($p->published_at?->isFuture())
                                    <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200">Terjadwal</span>
                                @else
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200">Terbit</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $p->dilihat }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.pengumuman.kelola', ['edit' => $p->id]) }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Edit</a>

                                <form method="POST" action="{{ route('admin.pengumuman.sematkan', $p) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="ml-2 text-xs font-semibold text-amber-600 hover:underline dark:text-amber-400">
                                        {{ $p->is_pinned ? 'Lepas' : 'Sematkan' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.pengumuman.destroy', $p) }}" class="inline"
                                      onsubmit="return confirm('Hapus pengumuman ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-2 text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada pengumuman.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-4 py-3">{{ $pengumuman->links() }}</div>
        </div>
    </div>
</x-layouts.app>
