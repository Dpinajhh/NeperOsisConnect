<x-layouts.app :title="'Kelola Program Kerja'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Kelola Program Kerja</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tambah, ubah, dan hapus program kerja OSIS.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.4fr]">
        {{-- ─── Form tambah / edit ───────────────────────────────── --}}
        <form method="POST"
              action="{{ $edit ? route('admin.program-kerja.update', $edit) : route('admin.program-kerja.store') }}"
              class="card space-y-3 p-5">
            @csrf
            @if ($edit) @method('PUT') @endif

            <h2 class="text-sm font-bold dark:text-white">{{ $edit ? 'Edit: '.$edit->nama : 'Tambah program kerja' }}</h2>

            <div>
                <label class="label" for="nama">Nama Program</label>
                <input id="nama" name="nama" class="input" value="{{ old('nama', $edit->nama ?? '') }}" required>
            </div>

            <div>
                <label class="label" for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="input" required>{{ old('deskripsi', $edit->deskripsi ?? '') }}</textarea>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="label" for="bidang">Bidang</label>
                    <input id="bidang" name="bidang" class="input" value="{{ old('bidang', $edit->bidang ?? '') }}">
                </div>
                <div>
                    <label class="label" for="penanggung_jawab">Penanggung Jawab</label>
                    <input id="penanggung_jawab" name="penanggung_jawab" class="input" value="{{ old('penanggung_jawab', $edit->penanggung_jawab ?? '') }}">
                </div>
                <div>
                    <label class="label" for="tanggal_mulai">Tanggal Mulai</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" class="input"
                           value="{{ old('tanggal_mulai', $edit?->tanggal_mulai?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="label" for="tanggal_selesai">Tanggal Selesai</label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" class="input"
                           value="{{ old('tanggal_selesai', $edit?->tanggal_selesai?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="label" for="status">Status</label>
                    <select id="status" name="status" class="input">
                        @foreach (['rencana' => 'Rencana', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai', 'batal' => 'Dibatalkan'] as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('status', $edit->status ?? 'rencana') === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="progres">Progres (%)</label>
                    <input id="progres" name="progres" type="number" min="0" max="100" class="input"
                           value="{{ old('progres', $edit->progres ?? 0) }}">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $edit ? 'Simpan Perubahan' : 'Tambah' }}</button>
                @if ($edit)
                    <a href="{{ route('admin.program-kerja.kelola') }}" class="btn-ghost">Batal</a>
                @endif
            </div>
        </form>

        {{-- ─── Daftar ───────────────────────────────────────────── --}}
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Program</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Progres</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($programs as $program)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $program->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $program->bidang ?? 'Tanpa bidang' }} &bull; {{ $program->kegiatan_count }} kegiatan</p>
                            </td>
                            <td class="px-4 py-3"><span class="badge {{ $program->status_warna }}">{{ $program->status_label }}</span></td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $program->progres }}%</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.program-kerja.kelola', ['edit' => $program->id]) }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Edit</a>
                                <form method="POST" action="{{ route('admin.program-kerja.destroy', $program) }}" class="inline"
                                      onsubmit="return confirm('Hapus program kerja ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-2 text-xs font-semibold text-rose-600 hover:underline dark:text-rose-400">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada program kerja.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
