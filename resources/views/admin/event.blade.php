<x-layouts.app :title="'Kelola Event'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Kelola Event & Kegiatan</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buat agenda kegiatan sekolah dan pantau pendaftar.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.4fr]">
        {{-- ─── Form tambah / edit ───────────────────────────────── --}}
        <form method="POST"
              action="{{ $edit ? route('admin.event.update', $edit) : route('admin.event.store') }}"
              enctype="multipart/form-data"
              class="card space-y-3 p-5">
            @csrf
            @if ($edit) @method('PUT') @endif

            <h2 class="text-sm font-bold dark:text-white">{{ $edit ? 'Edit: '.$edit->nama : 'Tambah kegiatan' }}</h2>

            <div>
                <label class="label" for="nama">Nama Kegiatan</label>
                <input id="nama" name="nama" class="input" value="{{ old('nama', $edit->nama ?? '') }}" required>
            </div>

            <div>
                <label class="label" for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="input" required>{{ old('deskripsi', $edit->deskripsi ?? '') }}</textarea>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label" for="waktu_mulai">Waktu Mulai</label>
                    <input id="waktu_mulai" name="waktu_mulai" type="datetime-local" class="input"
                           value="{{ old('waktu_mulai', $edit?->waktu_mulai?->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div>
                    <label class="label" for="waktu_selesai">Waktu Selesai</label>
                    <input id="waktu_selesai" name="waktu_selesai" type="datetime-local" class="input"
                           value="{{ old('waktu_selesai', $edit?->waktu_selesai?->format('Y-m-d\TH:i')) }}">
                </div>
                <div>
                    <label class="label" for="lokasi">Lokasi</label>
                    <input id="lokasi" name="lokasi" class="input" value="{{ old('lokasi', $edit->lokasi ?? '') }}">
                </div>
                <div>
                    <label class="label" for="kuota">Kuota <span class="text-slate-400">(kosong = tak terbatas)</span></label>
                    <input id="kuota" name="kuota" type="number" min="1" class="input" value="{{ old('kuota', $edit->kuota ?? '') }}">
                </div>
                <div>
                    <label class="label" for="status">Status</label>
                    <select id="status" name="status" class="input">
                        <option value="terbit" @selected(old('status', $edit->status ?? 'terbit') === 'terbit')>Terbit</option>
                        <option value="draft" @selected(old('status', $edit->status ?? 'terbit') === 'draft')>Draft</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="program_kerja_id">Program Kerja</label>
                    <select id="program_kerja_id" name="program_kerja_id" class="input">
                        <option value="">— Tidak terkait —</option>
                        @foreach ($programs as $prog)
                            <option value="{{ $prog->id }}" @selected(old('program_kerja_id', $edit->program_kerja_id ?? '') == $prog->id)>{{ $prog->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="label" for="poster">Poster <span class="text-slate-400">(opsional)</span></label>
                <input id="poster" name="poster" type="file" accept="image/*" class="input">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $edit ? 'Simpan Perubahan' : 'Tambah' }}</button>
                @if ($edit)
                    <a href="{{ route('admin.event.kelola') }}" class="btn-ghost">Batal</a>
                @endif
            </div>
        </form>

        {{-- ─── Daftar ───────────────────────────────────────────── --}}
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Kegiatan</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Peserta</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($events as $event)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $event->lokasi ?? 'Tanpa lokasi' }}</p>
                                @if ($event->status === 'draft')
                                    <span class="badge mt-1 bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
                                {{ $event->waktu_mulai->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ $event->peserta_count }}@if ($event->kuota)/{{ $event->kuota }}@endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.event.kelola', ['edit' => $event->id]) }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Edit</a>
                                <form method="POST" action="{{ route('admin.event.destroy', $event) }}" class="inline"
                                      onsubmit="return confirm('Hapus kegiatan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-2 text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada kegiatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
