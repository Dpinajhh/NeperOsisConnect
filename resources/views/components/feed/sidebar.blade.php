{{-- Sidebar kanan feed: Trending Topik + info --}}
@props(['trending'])

<div class="card p-4">
    <div class="flex items-center justify-between">
        <h2 class="flex items-center gap-1.5 text-sm font-bold dark:text-white">🔥 Trending Topik</h2>
        <span class="badge bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">7 hari</span>
    </div>

    <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">
        Peringkat berdasarkan aktivitas: postingan + komentar + suka.
    </p>

    @forelse ($trending as $i => $t)
        <a href="{{ route('topik.show', $t) }}"
           class="mt-3 flex items-center gap-3 rounded-xl p-2 transition hover:bg-slate-50 dark:hover:bg-slate-700/60">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold
                {{ $i === 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200'
                    : ($i === 1 ? 'bg-slate-200 text-slate-600 dark:bg-slate-600 dark:text-slate-200'
                    : ($i === 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200'
                    : 'bg-slate-100 text-slate-500 dark:bg-slate-900 dark:text-slate-400')) }}">
                {{ $i + 1 }}
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-semibold text-slate-700 dark:text-slate-200">#{{ $t->nama }}</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">{{ $t->jumlah_post }} postingan</span>
            </span>
            @if ($i < 3)
                <span class="shrink-0 text-xs">🔥</span>
            @endif
        </a>
    @empty
        <p class="mt-4 rounded-xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-400 dark:bg-slate-900 dark:text-slate-500">
            Belum ada topik. Pakai <b>#hashtag</b> saat posting!
        </p>
    @endforelse
</div>

<div class="card p-4">
    <h2 class="text-sm font-bold dark:text-white">Tentang Osis Connect</h2>
    <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
        Ruang digital siswa <strong>SMK Negeri 1 Cirebon</strong> — berbagi cerita, info pengumuman, dan berita kegiatan sekolah.
    </p>
    <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] text-slate-400 dark:text-slate-500">
        <span class="badge bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300">🎓 Neper</span>
        <span class="badge bg-slate-100 dark:bg-slate-700">Berbagi cerita</span>
        <span class="badge bg-slate-100 dark:bg-slate-700">Aman & ter-moderasi</span>
    </div>
</div>

<p class="px-2 text-[10px] leading-relaxed text-slate-400 dark:text-slate-500">
    Osis Connect &bull; SMK Negeri 1 Cirebon<br>
    Jl. Perjuangan–Sunyaragi, Kesambi, Cirebon
</p>
