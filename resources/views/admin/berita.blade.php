<x-layouts.app :title="'Kelola Berita'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Kelola Berita</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tulis liputan kegiatan, simpan sebagai draft, atau langsung terbitkan.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.3fr]">
        {{-- ─── Form ───────────────────────────────────────────── --}}
        <form method="POST"
              action="{{ $edit ? route('admin.berita.update', $edit) : route('admin.berita.store') }}"
              enctype="multipart/form-data"
              class="card space-y-3 p-5">
            @csrf
            @if ($edit) @method('PUT') @endif

            <h2 class="text-sm font-bold dark:text-white">{{ $edit ? 'Edit berita' : 'Tulis berita baru' }}</h2>

            <div>
                <label class="label" for="judul">Judul</label>
                <input id="judul" name="judul" class="input" value="{{ old('judul', $edit->judul ?? '') }}" required>
            </div>

            <div>
                <label class="label" for="ringkasan">Ringkasan</label>
                <textarea id="ringkasan" name="ringkasan" rows="2" class="input" maxlength="300" required>{{ old('ringkasan', $edit->ringkasan ?? '') }}</textarea>
            </div>

            <div>
                <label class="label" for="konten">Isi Berita</label>
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
                    <label class="label" for="status">Status</label>
                    <select id="status" name="status" class="input">
                        <option value="published" @selected(old('status', $edit->status ?? 'published') === 'published')>Terbit</option>
                        <option value="draft" @selected(old('status', $edit->status ?? 'published') === 'draft')>Draft</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="is_headline" value="1" @checked(old('is_headline', $edit->is_headline ?? false)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Jadikan headline
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $edit ? 'Simpan' : 'Simpan' }}</button>
                @if ($edit)
                    <a href="{{ route('admin.berita.kelola') }}" class="btn-ghost">Batal</a>
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
                    @forelse ($berita as $b)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $b->judul }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $b->kategori?->nama ?? 'Tanpa kategori' }}
                                    @if ($b->is_headline) &bull; 🔥 headline @endif
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                @if ($b->status === 'published')
                                    <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200">Terbit</span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $b->dilihat }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.berita.kelola', ['edit' => $b->id]) }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Edit</a>
                                <form method="POST" action="{{ route('admin.berita.destroy', $b) }}" class="inline"
                                      onsubmit="return confirm('Hapus berita ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-2 text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada berita.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-4 py-3">{{ $berita->links() }}</div>
        </div>
    </div>
</x-layouts.app>
