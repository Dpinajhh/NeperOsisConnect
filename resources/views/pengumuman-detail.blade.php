<x-layouts.app :title="$pengumuman->judul">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('pengumuman.index') }}" class="mb-3 inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            ← Semua pengumuman
        </a>

        <article class="card p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                @if ($pengumuman->is_pinned)
                    <span class="badge inline-flex items-center gap-1 bg-amber-100 text-[10px] font-bold text-amber-700 dark:bg-amber-900 dark:text-amber-200">
                        <x-ikon name="bookmark" class="h-3 w-3" />
                        Disematkan
                    </span>
                @endif
                @if ($pengumuman->kategori)
                    <span class="badge bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">{{ $pengumuman->kategori->nama }}</span>
                @endif
                <span class="badge inline-flex items-center gap-1 bg-brand-50 text-[10px] text-brand-600 dark:bg-brand-900 dark:text-brand-300">
                    @if ($pengumuman->target === 'semua')
                        <x-ikon name="globe-alt" class="h-3 w-3" /> Seluruh sekolah
                    @elseif ($pengumuman->target === 'kelas')
                        <x-ikon name="users" class="h-3 w-3" /> Kelas {{ $pengumuman->kelas?->nama }}
                    @else
                        <x-ikon name="building-library" class="h-3 w-3" /> Jurusan {{ $pengumuman->jurusan?->kode }}
                    @endif
                </span>
            </div>

            <h1 class="mt-3 text-2xl font-bold leading-tight text-slate-800 dark:text-slate-100">{{ $pengumuman->judul }}</h1>

            <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">
                {{ $pengumuman->penulis?->name }} &bull; {{ $pengumuman->published_at?->translatedFormat('d F Y, H:i') }} &bull; {{ $pengumuman->dilihat }} dilihat
            </p>

            @if ($pengumuman->cover)
                <img src="{{ asset('storage/'.$pengumuman->cover) }}" alt="" class="mt-4 max-h-72 w-full rounded-xl object-cover">
            @endif

            <div class="mt-5 whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                {{ $pengumuman->konten }}
            </div>
        </article>
    </div>
</x-layouts.app>
