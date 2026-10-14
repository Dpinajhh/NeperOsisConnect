<x-layouts.app :title="'Berita'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Berita Sekolah</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kegiatan, prestasi, dan liputan seputar SMK Negeri 1 Cirebon.</p>
    </div>

    {{-- ─── Headline ─────────────────────────────────────── --}}
    @php($headline = $berita->where('is_headline', true)->first())

    @if ($headline)
        <a href="{{ route('berita.detail', $headline) }}" class="card mb-4 block overflow-hidden transition hover:shadow-md">
            <div class="grid sm:grid-cols-[1.2fr_1fr]">
                <div class="p-6">
                    <span class="badge bg-rose-100 text-[10px] font-bold text-rose-600 dark:bg-rose-900 dark:text-rose-300">🔥 Headline</span>
                    <h2 class="mt-2 text-xl font-bold leading-snug text-slate-800 dark:text-slate-100">{{ $headline->judul }}</h2>
                    <p class="mt-2 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $headline->ringkasan }}</p>
                    <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                        {{ $headline->kategori?->nama ?? 'Berita' }} &bull; {{ $headline->published_at?->diffForHumans() }}
                    </p>
                </div>
                @if ($headline->gambar_url)
                    <img src="{{ $headline->gambar_url }}" alt="" class="h-44 w-full object-cover sm:h-full">
                @else
                    <div class="flex h-44 items-center justify-center bg-gradient-to-br from-brand-500 to-brand-700 text-4xl text-white sm:h-full">📰</div>
                @endif
            </div>
        </a>
    @endif

    {{-- ─── Daftar berita ────────────────────────────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($berita->where('id', '!=', optional($headline)->id) as $b)
            <article class="card flex flex-col overflow-hidden">
                <a href="{{ route('berita.detail', $b) }}">
                    @if ($b->gambar_url)
                        <img src="{{ $b->gambar_url }}" alt="" class="h-40 w-full object-cover">
                    @else
                        <div class="flex h-40 items-center justify-center bg-gradient-to-br from-brand-500 to-brand-700 text-4xl text-white">📰</div>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-5">
                    <span class="badge w-fit bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">{{ $b->kategori?->nama ?? 'Berita' }}</span>

                    <h2 class="mt-2 text-base font-bold leading-snug text-slate-800 dark:text-slate-100">
                        <a href="{{ route('berita.detail', $b) }}" class="hover:text-brand-600 hover:underline dark:hover:text-brand-400">{{ $b->judul }}</a>
                    </h2>

                    <p class="mt-2 line-clamp-2 flex-1 text-sm text-slate-600 dark:text-slate-300">{{ $b->ringkasan }}</p>

                    <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                        {{ $b->penulis?->name }} &bull; {{ $b->published_at?->diffForHumans() }}
                    </p>
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center">
                <div class="text-4xl">📰</div>
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada berita</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Berita kegiatan akan segera dimuat.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-4">{{ $berita->links() }}</div>
</x-layouts.app>
