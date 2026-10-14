<x-layouts.app :title="'Kelola Profil OSIS'">
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">Kelola Profil OSIS</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Perbarui visi, misi, sambutuan, dan daftar pengurus.</p>
        </div>
        <a href="{{ route('profil-osis') }}" class="btn-ghost text-xs">Lihat halaman →</a>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1.5fr_1fr]">
        {{-- ─── Form profil ──────────────────────────────────────── --}}
        <form method="POST" action="{{ route('admin.profil-osis.update') }}" class="card space-y-4 p-6">
            @csrf @method('PUT')

            <div>
                <label class="label" for="periode">Periode Kepengurusan</label>
                <input id="periode" name="periode" class="input" value="{{ old('periode', $profil->periode) }}" required>
            </div>

            <div>
                <label class="label" for="visi">Visi</label>
                <textarea id="visi" name="visi" rows="3" class="input" required>{{ old('visi', $profil->visi) }}</textarea>
            </div>

            <div>
                <label class="label" for="misi">Misi</label>
                <textarea id="misi" name="misi" rows="5" class="input" required>{{ old('misi', $profil->misi) }}</textarea>
            </div>

            <div>
                <label class="label" for="sejarah">Sejarah Singkat</label>
                <textarea id="sejarah" name="sejarah" rows="3" class="input">{{ old('sejarah', $profil->sejarah) }}</textarea>
            </div>

            <div>
                <label class="label" for="sambutuan">Sambutan</label>
                <textarea id="sambutuan" name="sambutuan" rows="4" class="input">{{ old('sambutuan', $profil->sambutuan) }}</textarea>
            </div>

            <div>
                <label class="label" for="sambutuan_pengirim">Pengirim Sambutan</label>
                <input id="sambutuan_pengirim" name="sambutuan_pengirim" class="input" value="{{ old('sambutuan_pengirim', $profil->sambutuan_pengirim) }}">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="alamat">Alamat Sekolah</label>
                    <input id="alamat" name="alamat" class="input" value="{{ old('alamat', $profil->alamat) }}">
                </div>
                <div>
                    <label class="label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="input" value="{{ old('email', $profil->email) }}">
                </div>
                <div>
                    <label class="label" for="telepon">Telepon</label>
                    <input id="telepon" name="telepon" class="input" value="{{ old('telepon', $profil->telepon) }}">
                </div>
                <div>
                    <label class="label" for="instagram">Instagram</label>
                    <input id="instagram" name="instagram" class="input" value="{{ old('instagram', $profil->instagram) }}">
                </div>
            </div>

            <button type="submit" class="btn-primary">Simpan Profil</button>
        </form>

        {{-- ─── Kolom kanan: pengurus ────────────────────────── --}}
        <div class="space-y-4">
            <form method="POST" action="{{ route('admin.kepengurusan.store') }}" enctype="multipart/form-data" class="card space-y-3 p-5">
                @csrf

                <h2 class="text-sm font-bold dark:text-white">Tambah Pengurus</h2>

                <div>
                    <label class="label" for="nama">Nama</label>
                    <input id="nama" name="nama" class="input" value="{{ old('nama') }}" required>
                </div>
                <div>
                    <label class="label" for="jabatan">Jabatan</label>
                    <input id="jabatan" name="jabatan" class="input" value="{{ old('jabatan') }}" required>
                </div>
                <div>
                    <label class="label" for="bidang">Bidang <span class="text-slate-400">(opsional)</span></label>
                    <input id="bidang" name="bidang" class="input" value="{{ old('bidang') }}">
                </div>
                <div>
                    <label class="label" for="foto">Foto <span class="text-slate-400">(opsional)</span></label>
                    <input id="foto" name="foto" type="file" accept="image/*" class="input">
                </div>

                <button type="submit" class="btn-primary w-full">Tambah</button>
            </form>

            <div class="card p-5">
                <h2 class="text-sm font-bold dark:text-white">Daftar Pengurus ({{ $pengurus->count() }})</h2>

                <ul class="mt-3 divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($pengurus as $p)
                        <li class="flex items-center gap-3 py-2.5">
                            <img src="{{ $p->foto_url }}" alt="" class="h-8 w-8 rounded-lg object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $p->nama }}</p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $p->jabatan }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.kepengurusan.hapus', $p) }}"
                                  onsubmit="return confirm('Hapus ' + {{ json_encode($p->nama) }} + ' dari daftar?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="py-3 text-center text-xs text-slate-400">Belum ada pengurus.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-layouts.app>
