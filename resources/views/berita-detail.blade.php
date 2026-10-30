<x-layouts.app :title="$berita->judul">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('berita.index') }}" class="mb-3 inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            ← Semua berita
        </a>

        <article class="card p-6 sm:p-8">
            @if ($berita->gambar_url)
                <img src="{{ $berita->gambar_url }}" alt="" class="mb-5 max-h-80 w-full rounded-xl object-cover">
            @endif

            <span class="badge bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">
                {{ $berita->kategori?->nama ?? 'Berita' }}
            </span>

            <h1 class="mt-3 text-2xl font-bold leading-tight text-slate-800 dark:text-slate-100">{{ $berita->judul }}</h1>

            <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">
                {{ $berita->penulis?->name }} &bull; {{ $berita->published_at?->translatedFormat('d F Y, H:i') }} &bull; {{ $berita->dilihat }} dilihat
            </p>

            <p class="mt-5 rounded-xl bg-slate-50 px-4 py-3 text-sm font-medium leading-relaxed text-slate-600 dark:bg-slate-900 dark:text-slate-300">
                {{ $berita->ringkasan }}
            </p>

            <div class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                {{ $berita->konten }}
            </div>
        </article>

        {{-- ─── Berita lain ────────────────────────────────── --}}
        @if ($lainnya->isNotEmpty())
            <section class="mt-4 card p-5">
                <h2 class="text-sm font-bold dark:text-white">Berita lainnya</h2>
                <ul class="mt-2 divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($lainnya as $b)
                        <li class="flex items-center gap-3 py-2.5">
                            <x-ikon name="newspaper" class="h-5 w-5 shrink-0 text-slate-400" />
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('berita.detail', $b) }}" class="block truncate text-sm font-semibold text-slate-700 hover:text-brand-600 dark:text-slate-200">{{ $b->judul }}</a>
                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $b->published_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
</x-layouts.app>
