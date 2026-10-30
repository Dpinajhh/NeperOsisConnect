<x-layouts.app :title="'Pengumuman'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Pengumuman</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Info resmi dari pengurus OSIS & guru untukmu.</p>
    </div>

    <div class="space-y-4">
        @forelse ($pengumuman as $p)
            <article class="card p-5">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($p->is_pinned)
                        <span class="badge inline-flex items-center gap-1 bg-amber-100 text-[10px] font-bold text-amber-700 dark:bg-amber-900 dark:text-amber-200">
                            <x-ikon name="bookmark" class="h-3 w-3" />
                            Disematkan
                        </span>
                    @endif
                    @if ($p->kategori)
                        <span class="badge bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">{{ $p->kategori->nama }}</span>
                    @endif
                    <span class="badge inline-flex items-center gap-1 bg-brand-50 text-[10px] text-brand-600 dark:bg-brand-900 dark:text-brand-300">
                        @if ($p->target === 'semua')
                            <x-ikon name="globe-alt" class="h-3 w-3" /> Seluruh sekolah
                        @elseif ($p->target === 'kelas')
                            <x-ikon name="users" class="h-3 w-3" /> Kelas {{ $p->kelas?->nama }}
                        @else
                            <x-ikon name="building-library" class="h-3 w-3" /> Jurusan {{ $p->jurusan?->kode }}
                        @endif
                    </span>
                    @unless ($p->sudah_dibaca)
                        <span class="badge bg-rose-100 text-[10px] font-bold text-rose-600 dark:bg-rose-900 dark:text-rose-300">Baru</span>
                    @endunless
                </div>

                <h2 class="mt-2.5 text-base font-bold leading-snug text-slate-800 dark:text-slate-100">
                    <a href="{{ route('pengumuman.detail', $p) }}" class="hover:text-brand-600 hover:underline dark:hover:text-brand-400">
                        {{ $p->judul }}
                    </a>
                </h2>

                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ Str::limit(strip_tags($p->konten), 140) }}
                </p>

                <p class="mt-3 text-xs text-slate-400 dark:text-slate-500">
                    {{ $p->penulis?->name }} &bull; {{ $p->published_at?->diffForHumans() }} &bull; {{ $p->dilihat }} dilihat
                </p>
            </article>
        @empty
            <div class="card p-10 text-center">
                <x-ikon name="megaphone" class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" />
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada pengumuman</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengumuman untukmu akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    <div class="pt-4">{{ $pengumuman->links() }}</div>
</x-layouts.app>
