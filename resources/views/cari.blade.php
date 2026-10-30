<x-layouts.app :title="'Hasil Pencarian'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Hasil Pencarian</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            @if ($q === '')
                Cari postingan, berita, pengumuman, topik hashtag, atau teman sebaya.
            @else
                Menampilkan <strong class="text-slate-700 dark:text-slate-200">{{ $total }}</strong> hasil untuk
                <span class="rounded-md bg-brand-50 px-2 py-0.5 font-semibold text-brand-700 dark:bg-slate-700 dark:text-brand-300">&ldquo;{{ $q }}&rdquo;</span>
            @endif
        </p>
    </div>

    {{-- Kotak pencarian utama --}}
    <form action="{{ route('cari') }}" method="GET" class="card mb-5 p-3">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="search" name="q" value="{{ $q }}" placeholder="Ketik kata kunci..."
                   class="input py-3 pl-10 pr-24 text-base" autofocus>
            <button type="submit" class="btn-primary absolute right-1.5 top-1/2 -translate-y-1/2 px-4 py-2">Cari</button>
        </div>
    </form>

    @if ($q === '')
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (['pencil-square' => ['Postingan siswa', 'Kata kunci di isi post', 'feed'], 'newspaper' => ['Berita sekolah', 'Kegiatan & prestasi', 'berita.index'], 'megaphone' => ['Pengumuman', 'Informasi resmi OSIS', 'pengumuman.index'], 'tag' => ['Topik hashtag', '#trending di sekolah', null], 'user-group' => ['Siswa & guru', 'Cari nama atau NIS', null]] as $ikon => [$judul, $ket, $rute])
                <div class="card p-4">
                    <x-ikon :name="$ikon" class="h-7 w-7 text-brand-600 dark:text-brand-400" />
                    <p class="mt-2 font-semibold dark:text-white">{{ $judul }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $ket }}</p>
                </div>
            @endforeach
        </div>
    @elseif ($total === 0)
        <div class="card p-12 text-center">
            <x-ikon name="magnifying-glass" class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" />
            <h2 class="mt-4 text-lg font-bold dark:text-white">Tidak ada hasil</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Coba kata kunci lain, misalnya nama kegiatan, kategori berita, atau nama teman.
            </p>
        </div>
    @else
        <div x-data="{ tab: 'semua' }">
            {{-- Tab --}}
            <div class="card mb-5 flex gap-1 overflow-x-auto p-1.5">
                @php
                    $tab = fn (string $id, string $label, int $n) => [
                        'aktif' => 'bg-brand-600 text-white shadow-sm',
                        'idle' => 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700',
                    ];
                @endphp
                <button @click="tab='semua'" :class="tab==='semua' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-medium transition">Semua ({{ $total }})</button>
                <button @click="tab='post'" :class="tab==='post' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-medium transition">Postingan ({{ $posts->count() }})</button>
                <button @click="tab='berita'" :class="tab==='berita' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-medium transition">Berita ({{ $berita->count() }})</button>
                <button @click="tab='pengumuman'" :class="tab==='pengumuman' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-medium transition">Pengumuman ({{ $pengumuman->count() }})</button>
                <button @click="tab='topik'" :class="tab==='topik' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-medium transition">Topik ({{ $topik->count() }})</button>
                <button @click="tab='orang'" :class="tab==='orang' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700'"
                        class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-medium transition">Siswa ({{ $pengguna->count() }})</button>
            </div>

            {{-- ─── Postingan ─────────────────────────────── --}}
            <div x-show="tab==='semua' || tab==='post'" x-cloak>
                @if ($posts->count())
                    <h2 class="mb-2 mt-5 flex items-center gap-1.5 text-sm font-bold uppercase tracking-wide text-slate-400">
                        <x-ikon name="pencil-square" class="h-4 w-4" />
                        Postingan
                    </h2>
                    <div class="space-y-3">
                        @foreach ($posts as $p)
                            <a href="{{ route('posts.show', $p) }}" class="card block p-4 transition hover:shadow-md">
                                <div class="flex items-start gap-3">
                                    <img src="{{ $p->user->avatar_url }}" alt="" class="h-10 w-10 rounded-full object-cover">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-x-2">
                                            <span class="text-sm font-semibold dark:text-white">{{ $p->user->name }}</span>
                                            @if ($p->user->kelas)
                                                <span class="text-xs text-slate-400">• {{ $p->user->kelas->nama }}</span>
                                            @endif
                                            <span class="text-xs text-slate-400">• {{ $p->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="mt-1 line-clamp-3 text-sm text-slate-600 dark:text-slate-300">{{ $p->konten }}</p>
                                        <div class="mt-2 flex gap-4 text-xs text-slate-400">
                                            <span class="inline-flex items-center gap-1">
                                                <x-ikon name="heart" class="h-3.5 w-3.5" />
                                                {{ $p->jumlah_suka }} suka
                                            </span>
                                            <span class="inline-flex items-center gap-1">
                                                <x-ikon name="chat-bubble-left" class="h-3.5 w-3.5" />
                                                {{ $p->jumlah_komentar }} komentar
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ─── Berita ────────────────────────────────── --}}
            <div x-show="tab==='semua' || tab==='berita'" x-cloak>
                @if ($berita->count())
                    <h2 class="mb-2 mt-6 flex items-center gap-1.5 text-sm font-bold uppercase tracking-wide text-slate-400">
                        <x-ikon name="newspaper" class="h-4 w-4" />
                        Berita
                    </h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($berita as $b)
                            <a href="{{ route('berita.detail', $b) }}" class="card block p-4 transition hover:shadow-md">
                                <span class="badge w-fit bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">{{ $b->kategori?->nama ?? 'Berita' }}</span>
                                <h3 class="mt-2 font-bold leading-snug text-slate-800 dark:text-slate-100">{{ $b->judul }}</h3>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ $b->ringkasan }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $b->penulis?->name }} &bull; {{ $b->published_at?->diffForHumans() }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ─── Pengumuman ────────────────────────────── --}}
            <div x-show="tab==='semua' || tab==='pengumuman'" x-cloak>
                @if ($pengumuman->count())
                    <h2 class="mb-2 mt-6 flex items-center gap-1.5 text-sm font-bold uppercase tracking-wide text-slate-400">
                        <x-ikon name="megaphone" class="h-4 w-4" />
                        Pengumuman
                    </h2>
                    <div class="space-y-3">
                        @foreach ($pengumuman as $pg)
                            <a href="{{ route('pengumuman.detail', $pg) }}" class="card block p-4 transition hover:shadow-md">
                                <div class="flex items-center gap-2">
                                    @if ($pg->is_pinned)
                                        <span class="badge inline-flex items-center gap-1 bg-amber-100 text-[10px] text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                            <x-ikon name="bookmark" class="h-3 w-3" />
                                            Disematkan
                                        </span>
                                    @endif
                                    <span class="badge bg-brand-50 text-[10px] text-brand-700 dark:bg-slate-700 dark:text-brand-300">{{ $pg->kategori?->nama ?? 'Pengumuman' }}</span>
                                    <span class="text-xs text-slate-400">• {{ $pg->target }}</span>
                                </div>
                                <h3 class="mt-2 font-bold text-slate-800 dark:text-slate-100">{{ $pg->judul }}</h3>
                                <p class="mt-1 line-clamp-2 text-sm text-slate-600 dark:text-slate-300">{{ \Illuminate\Support\Str::limit(strip_tags($pg->konten), 140) }}</p>
                                <p class="mt-2 text-xs text-slate-400">{{ $pg->penulis?->name }} &bull; {{ $pg->published_at?->diffForHumans() }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ─── Topik ─────────────────────────────────── --}}
            <div x-show="tab==='semua' || tab==='topik'" x-cloak>
                @if ($topik->count())
                    <h2 class="mb-2 mt-6 flex items-center gap-1.5 text-sm font-bold uppercase tracking-wide text-slate-400">
                        <x-ikon name="hashtag" class="h-4 w-4" />
                        Topik
                    </h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($topik as $t)
                            <a href="{{ route('topik.show', $t) }}" class="card block p-4 transition hover:shadow-md">
                                <p class="text-lg font-bold text-brand-600 dark:text-brand-400">#{{ $t->nama }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $t->jumlah_post }} postingan</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ─── Pengguna ──────────────────────────────── --}}
            <div x-show="tab==='semua' || tab==='orang'" x-cloak>
                @if ($pengguna->count())
                    <h2 class="mb-2 mt-6 flex items-center gap-1.5 text-sm font-bold uppercase tracking-wide text-slate-400">
                        <x-ikon name="user-group" class="h-4 w-4" />
                        Siswa &amp; Guru
                    </h2>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($pengguna as $u)
                            <a href="{{ route('profil.show', $u) }}" class="card flex items-center gap-3 p-4 transition hover:shadow-md">
                                <img src="{{ $u->avatar_url }}" alt="" class="h-11 w-11 rounded-full object-cover">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold dark:text-white">{{ $u->name }}</p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $u->kelas?->nama ?? ucfirst($u->role) }}@if($u->nis) &bull; NIS {{ $u->nis }}@endif
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-layouts.app>
