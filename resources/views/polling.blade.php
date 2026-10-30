<x-layouts.app :title="'Polling Siswa'">
    {{-- ─── Hero ─────────────────────────────────────────────── --}}
    <section class="mb-5 card overflow-hidden bg-gradient-to-br from-violet-500 to-brand-700 p-6 text-white sm:p-8">
        <span class="badge inline-flex items-center gap-1 bg-white/15">
            <x-ikon name="chart-bar" class="h-3.5 w-3.5" />
            Polling Siswa
        </span>
        <h1 class="mt-3 text-2xl font-bold sm:text-3xl">Suarakan Pilihanmu</h1>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/80">
            Ikuti polling kegiatan sekolah. Satu pengguna satu suara — hasil tampil setelah kamu memilih.
        </p>
    </section>

    <div class="grid gap-4 lg:grid-cols-2">
        @forelse ($polling as $p)
            <article class="card flex flex-col p-5">
                <h2 class="text-base font-bold leading-snug text-slate-800 dark:text-slate-100">
                    <a href="{{ route('polling.detail', $p) }}" class="hover:text-violet-600 hover:underline dark:hover:text-violet-400">
                        {{ $p->pertanyaan }}
                    </a>
                </h2>

                @if ($p->deskripsi)
                    <p class="mt-2 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $p->deskripsi }}</p>
                @endif

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1">
                        <x-ikon name="chart-bar" class="h-3.5 w-3.5" />
                        {{ $p->suara_count }} suara masuk
                    </span>
                    <a href="{{ route('polling.detail', $p) }}" class="font-semibold text-violet-600 hover:underline dark:text-violet-400">
                        @if ($p->sudahMemilih(auth()->id())) Lihat hasil → @else Ikut polling → @endif
                    </a>
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center">
                <x-ikon name="chart-bar" class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" />
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada polling aktif</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengurus OSIS akan membuka polling segera.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
