<x-layouts.app :title="'Moderasi Laporan'">
    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">Moderasi Laporan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Tinjau konten yang dilaporkan siswa. Hapus bila melanggar aturan platform.
            </p>
        </div>

        <div class="flex flex-wrap gap-1.5">
            @php
                $tombol = fn ($id, $label, $n) => [
                    'aktif' => 'bg-brand-600 text-white',
                    'idle'  => 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300',
                ];
            @endphp
            @foreach ([
                'semua' => 'Semua', 'open' => 'Terbuka', 'diproses' => 'Diproses',
                'selesai' => 'Selesai', 'ditolak' => 'Ditolak',
            ] as $k => $label)
                <a href="{{ route('admin.laporan.kelola', ['status' => $k]) }}"
                   class="rounded-xl px-3.5 py-2 text-sm font-medium transition
                          {{ $filter === $k ? 'bg-brand-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs opacity-70">{{ $ringkas[$k] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    @if ($laporan->isEmpty())
        <div class="card p-12 text-center">
            <div class="text-5xl">🛡️</div>
            <h2 class="mt-4 text-lg font-bold dark:text-white">Tidak ada laporan</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                @if ($filter === 'semua')
                    Belum ada siswa yang melaporkan konten. Tetap pantau feed untuk menjaga kenyamanan sekolah.
                @else
                    Tidak ada laporan dengan status "{{ $filter }}".
                @endif
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($laporan as $lap)
                @php($objek = $lap->laporanable)
                <div class="card p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $lap->pelapor->avatar_url }}" alt="" class="h-10 w-10 rounded-full object-cover">
                            <div>
                                <p class="font-semibold dark:text-white">
                                    {{ $lap->pelapor->name }}
                                    <span class="ml-1 text-xs font-normal text-slate-400">(pelapor)</span>
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $lap->created_at->diffForHumans() }} &bull;
                                    <span class="badge bg-slate-100 text-[10px] text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $lap->objek_label }}</span>
                                </p>
                            </div>
                        </div>

                        @php
                            $warna = ['open' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
                                      'diproses' => 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300',
                                      'selesai' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
                                      'ditolak' => 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300'];
                        @endphp
                        <span class="badge {{ $warna[$lap->status] }}">{{ ucfirst($lap->status) }}</span>
                    </div>

                    {{-- Alasan --}}
                    <div class="mt-4 rounded-xl bg-slate-50 p-3 dark:bg-slate-900">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alasan</p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-300">{{ $lap->alasan }}</p>
                    </div>

                    {{-- Konten yang dilaporkan --}}
                    <div class="mt-3 rounded-xl border border-rose-200 bg-rose-50/50 p-3 dark:border-rose-900 dark:bg-rose-950/30">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-500">Konten yang dilaporkan</p>

                        @if ($objek)
                            @php($penulis = $objek->user ?? null)
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                oleh <strong>{{ $penulis?->name ?? '(pengguna terhapus)' }}</strong>
                            </p>

                            @if ($objek instanceof \App\Models\Komentar)
                                <p class="mt-2 text-sm text-slate-800 dark:text-slate-200">💬 {{ $objek->konten }}</p>
                                @if ($objek->post)
                                    <a href="{{ route('posts.show', $objek->post) }}" class="mt-2 inline-block text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">
                                        Buka post induk →
                                    </a>
                                @endif
                            @elseif ($objek instanceof \App\Models\Post)
                                <p class="mt-2 line-clamp-4 text-sm text-slate-800 dark:text-slate-200">📝 {{ $objek->konten }}</p>
                                <a href="{{ route('posts.show', $objek) }}" class="mt-2 inline-block text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">
                                    Buka postingan →
                                </a>
                            @endif
                        @else
                            <p class="mt-1 text-sm text-slate-500 italic">Konten sudah dihapus.</p>
                        @endif
                    </div>

                    @if ($lap->catatan_admin)
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                            📝 Catatan: <span class="whitespace-pre-line">{{ $lap->catatan_admin }}</span>
                            @if ($lap->ditanganiOleh) &bull; oleh {{ $lap->ditanganiOleh->name }} @endif
                        </p>
                    @endif

                    {{-- Aksi --}}
                    @if ($lap->status !== 'selesai')
                        <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4 dark:border-slate-700">
                            @if ($objek)
                                <form method="POST" action="{{ route('admin.laporan.hapus-konten', $lap) }}"
                                      onsubmit="return confirm('Hapus konten ini secara permanen?')">
                                    @csrf
                                    <button type="submit" class="btn-danger text-xs">🗑️ Hapus Konten</button>
                                </form>
                            @endif

                            @if ($lap->status === 'open')
                                <form method="POST" action="{{ route('admin.laporan.perbarui', $lap) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="diproses">
                                    <button type="submit" class="btn-ghost text-xs">🔄 Tandai Diproses</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.laporan.perbarui', $lap) }}">
                                @csrf
                                <input type="hidden" name="status" value="ditolak">
                                <button type="submit" class="btn-ghost text-xs">✅ Tolak (tidak melanggar)</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pt-4">{{ $laporan->links() }}</div>
    @endif
</x-layouts.app>
