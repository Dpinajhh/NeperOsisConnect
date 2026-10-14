<x-layouts.app :title="'Kelola Polling'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Kelola Polling</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buat poll, lihat hasil, dan tutup polling yang sudah selesai.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_1.2fr]">
        {{-- ─── Form buat / edit ───────────────────────────────── --}}
        @php($edit = request('edit') ? \App\Models\Polling::with('pilihan')->find(request('edit')) : null)
        <form method="POST"
              action="{{ $edit ? route('admin.polling.update', $edit) : route('admin.polling.store') }}"
              class="card space-y-3 p-5">
            @csrf
            @if ($edit) @method('PUT') @endif

            <h2 class="text-sm font-bold dark:text-white">{{ $edit ? 'Edit: '.$edit->pertanyaan : 'Buat polling baru' }}</h2>

            @if ($edit && $edit->totalSuara() > 0)
                <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                    Polling ini sudah punya {{ $edit->totalSuara() }} suara — pilihan tidak bisa diubah.
                </p>
            @endif

            <div>
                <label class="label" for="pertanyaan">Pertanyaan</label>
                <input id="pertanyaan" name="pertanyaan" class="input" value="{{ old('pertanyaan', $edit->pertanyaan ?? '') }}" required>
            </div>

            <div>
                <label class="label" for="deskripsi">Deskripsi <span class="text-slate-400">(opsional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="2" class="input">{{ old('deskripsi', $edit->deskripsi ?? '') }}</textarea>
            </div>

            {{-- Pilihan --}}
            <div class="space-y-2" x-data="pilihanPolling({{ count($edit?->pilihan ?? [1, 2, 3, 4]) }})">
                <div class="flex items-center justify-between">
                    <label class="label mb-0">Pilihan Jawaban</label>
                    <button type="button" @click="tambah()" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">+ Tambah pilihan</button>
                </div>

                <template x-for="(nilai, i) in daftar" :key="i">
                    <div class="flex items-center gap-2">
                        <input :name="'pilihan['+i+']'" x-model="daftar[i]" class="input" placeholder="Tulis pilihan…" required>
                        <button type="button" x-show="daftar.length > 2" @click="hapus(i)" class="rounded-lg p-2 text-slate-400 hover:text-rose-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <div>
                <label class="label" for="status">Status</label>
                <select id="status" name="status" class="input">
                    <option value="terbit" @selected(old('status', $edit->status ?? 'terbit') === 'terbit')>Terbit (berlangsung)</option>
                    <option value="draft" @selected(old('status', $edit->status ?? 'terbit') === 'draft')>Draft</option>
                    <option value="ditutup" @selected(old('status', $edit->status ?? 'terbit') === 'ditutup')>Ditutup</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">{{ $edit ? 'Simpan' : 'Buat Polling' }}</button>
                @if ($edit)
                    <a href="{{ route('admin.polling.kelola') }}" class="btn-ghost">Batal</a>
                @endif
            </div>
        </form>

        {{-- ─── Daftar polling ──────────────────────────────────── --}}
        <div class="space-y-4">
            @forelse ($polling as $p)
                <div class="card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $p->pertanyaan }}</h2>
                            <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                                {{ $p->pilihan_count ?? $p->pilihan()->count() }} pilihan &bull; {{ $p->suara_count }} suara
                            </p>
                        </div>
                        <span class="badge shrink-0 {{ $p->status === 'terbit'
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200'
                            : ($p->status === 'draft'
                                ? 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300'
                                : 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-200') }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </div>

                    {{-- Hasil ringkas --}}
                    <div class="mt-3 space-y-2">
                        @foreach ($p->hasil() as $baris)
                            <div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-600 dark:text-slate-300">{{ $baris['teks'] }}</span>
                                    <span class="font-semibold text-slate-500 dark:text-slate-400">{{ $baris['persen'] }}%</span>
                                </div>
                                <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                                    <div class="h-full rounded-full bg-violet-500" style="width: {{ $baris['persen'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-3 text-xs dark:border-slate-700">
                        <a href="{{ route('admin.polling.kelola', ['edit' => $p->id]) }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">Edit</a>

                        <form method="POST" action="{{ route('admin.polling.tutup', $p) }}">
                            @csrf
                            <button type="submit" class="font-semibold text-amber-600 hover:underline dark:text-amber-400">
                                {{ $p->status === 'ditutup' ? 'Buka lagi' : 'Tutup voting' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.polling.destroy', $p) }}"
                              onsubmit="return confirm('Hapus polling ini beserta suaranya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="font-semibold text-rose-600 hover:underline dark:text-rose-400">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="card p-10 text-center text-sm text-slate-400">Belum ada polling.</div>
            @endforelse
        </div>
    </div>

    @push('skrip')
    <script>
        function pilihanPolling(jumlah) {
            const awal = {{ \Illuminate\Support\Js::from($edit?->pilihan->pluck('teks') ?? ['', '']) }};
            return {
                daftar: awal.length >= 2 ? awal : Array(Math.max(2, jumlah || 2)).fill(''),
                tambah() { if (this.daftar.length < 8) this.daftar.push(''); },
                hapus(i) { if (this.daftar.length > 2) this.daftar.splice(i, 1); }
            }
        }
    </script>
    @endpush
</x-layouts.app>
