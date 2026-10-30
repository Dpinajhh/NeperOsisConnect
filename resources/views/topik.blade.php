<x-layouts.app :title="'#'.$topik->nama">
    <div class="mx-auto max-w-[600px]">
        {{-- ─── Kepala topik ────────────────────────────────── --}}
        <div class="card mb-4 bg-gradient-to-br from-brand-500 to-brand-700 p-6 text-white">
            <p class="text-xs font-semibold uppercase tracking-wide text-white/70">Topik</p>
            <h1 class="mt-1 text-3xl font-bold">#{{ $topik->nama }}</h1>
            <p class="mt-2 text-sm text-white/80">
                {{ $topik->jumlah_post }} postingan membahas topik ini
                @if ($peringkat)
                    &bull; Peringkat <strong>#{{ $peringkat }}</strong> trending saat ini
                    <x-ikon name="fire" class="inline-block h-4 w-4 -translate-y-0.5 text-amber-400" />
                @endif
            </p>
        </div>

        @forelse ($posts as $post)
            <div class="mb-4">
                <x-feed.post-card :post="$post" />
            </div>
        @empty
            <div class="card p-10 text-center">
                <x-ikon name="magnifying-glass" class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" />
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada postingan</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Belum ada yang membahas #{{ $topik->nama }} yang bisa kamu lihat.
                </p>
            </div>
        @endforelse

        <div class="pt-2">
            {{ $posts->links() }}
        </div>
    </div>

    @push('skrip')
        @include('components.feed.skrip')
    @endpush
</x-layouts.app>
