<x-layouts.app :title="'Notifikasi'">
    <div class="mx-auto max-w-2xl space-y-4">

        {{-- Kepala halaman --}}
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold dark:text-white">Notifikasi</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    @if ($jumlahBelumDibaca > 0)
                        Ada <span class="font-semibold text-brand-600 dark:text-brand-400">{{ $jumlahBelumDibaca }}</span>
                        notifikasi yang belum dibaca.
                    @else
                        Semua notifikasi sudah dibaca.
                    @endif
                </p>
            </div>

            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="btn-ghost text-xs">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach (['semua' => 'Semua', 'belum' => 'Belum dibaca'] as $kunci => $label)
                <a href="{{ route('notifikasi.index', ['filter' => $kunci]) }}"
                   class="rounded-xl px-3 py-1.5 text-xs font-semibold transition {{ $filter === $kunci
                        ? 'bg-brand-600 text-white'
                        : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                    {{ $label }}
                    @if ($kunci === 'belum' && $jumlahBelumDibaca > 0)
                        <span class="ml-1 rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ $jumlahBelumDibaca }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Daftar notifikasi --}}
        @if ($notifikasi->isEmpty())
            <div class="card p-10 text-center">
                <x-ikon name="bell" class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-600" />
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                    {{ $filter === 'belum'
                        ? 'Tidak ada notifikasi yang belum dibaca.'
                        : 'Belum ada notifikasi untukmu. Suka & komentar dari teman akan muncul di sini.' }}
                </p>
            </div>
        @else
            <div class="card divide-y divide-slate-100 overflow-hidden dark:divide-slate-700">
                @foreach ($notifikasi as $n)
                    {{-- Dikirim lewat form agar klik = tandai dibaca + antar ke tujuan --}}
                    <form method="POST" action="{{ route('notifikasi.baca', $n) }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-start gap-3 px-4 py-3.5 text-left transition hover:bg-slate-50 dark:hover:bg-slate-700/40 {{ $n->dibaca_at ? '' : 'bg-brand-50/60 dark:bg-brand-950/40' }}">
                            @php
                                $ikonNotifikasi = match ($n->tipe) {
                                    'suka' => 'heart',
                                    'komentar' => 'chat-bubble-left',
                                    'pengumuman' => 'megaphone',
                                    'berita' => 'newspaper',
                                    default => 'bell',
                                };
                            @endphp
                            <x-ikon :name="$ikonNotifikasi" class="mt-0.5 h-5 w-5 shrink-0" />

                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-2">
                                    <span class="text-sm font-semibold dark:text-white">{{ $n->judul }}</span>
                                    @if (! $n->dibaca_at)
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand-600 dark:bg-brand-400"></span>
                                    @endif
                                </span>

                                <span class="mt-0.5 block text-sm text-slate-500 dark:text-slate-400">{{ $n->pesan }}</span>
                                <span class="mt-1 block text-xs text-slate-400 dark:text-slate-500">{{ $n->created_at->diffForHumans() }}</span>
                            </span>
                        </button>
                    </form>
                @endforeach
            </div>

            <div class="pt-2">{{ $notifikasi->links() }}</div>
        @endif
    </div>
</x-layouts.app>
