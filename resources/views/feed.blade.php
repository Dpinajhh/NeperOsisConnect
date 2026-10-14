<x-layouts.app :title="'Feed'">
    <div class="mx-auto flex max-w-6xl gap-6">

        {{-- ─── Kolom utama ─────────────────────────────────── --}}
        <div class="mx-auto w-full max-w-[600px] space-y-4">

            {{-- Trending versi mobile --}}
            @if ($trendingTopik->isNotEmpty())
                <div class="lg:hidden">
                    <p class="mb-2 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-slate-400">
                        🔥 Trending Topik
                    </p>
                    <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1">
                        @foreach ($trendingTopik as $i => $t)
                            <a href="{{ route('topik.show', $t) }}"
                               class="flex shrink-0 items-center gap-1 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
                                <span class="{{ $i === 0 ? 'text-rose-500' : 'text-slate-400' }}">#{{ $i + 1 }}</span>
                                {{ $t->nama }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Kotak buat post --}}
            <x-feed.composer />

            {{-- Daftar postingan --}}
            @forelse ($posts as $post)
                <x-feed.post-card :post="$post" />
            @empty
                <div class="card p-10 text-center">
                    <div class="text-4xl">🌱</div>
                    <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada postingan</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Jadilah yang pertama berbagi cerita di feed siswa!
                    </p>
                </div>
            @endforelse

            <div class="pt-2">
                {{ $posts->links() }}
            </div>
        </div>

        {{-- ─── Sidebar (desktop) ────────────────────────────── --}}
        <aside class="hidden w-72 shrink-0 lg:block">
            <div class="sticky top-20 space-y-4">
                <x-feed.sidebar :trending="$trendingTopik" />
            </div>
        </aside>
    </div>

    @push('skrip')
        @include('components.feed.skrip')
    @endpush
</x-layouts.app>
