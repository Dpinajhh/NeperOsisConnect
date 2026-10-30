<x-layouts.app :title="'Dashboard'">
    {{-- ─── Sapaan ────────────────────────────────────── --}}
    <section class="card p-6">
        <h1 class="flex flex-wrap items-center gap-2 text-2xl font-bold dark:text-white">
            Hai, {{ auth()->user()->name }}!
            <x-ikon name="hand-raised" class="h-6 w-6 text-amber-500" />
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            @if (auth()->user()->kelas)
                {{ auth()->user()->kelas->nama }}
                @if (auth()->user()->kelas->jurusan)
                    &bull; {{ auth()->user()->kelas->jurusan->nama }}
                @endif
                &bull;
            @endif
            {{ ucfirst(auth()->user()->role) }} &bull; SMK Negeri 1 Cirebon
        </p>
    </section>

    {{-- ─── Statistik pengelola ──────────────────────── --}}
    @if (! empty($statistik))
        <section class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @php
                $kartu = [
                    ['label' => 'Siswa', 'nilai' => $statistik['siswa'], 'ikon' => 'academic-cap'],
                    ['label' => 'Post', 'nilai' => $statistik['post'], 'ikon' => 'chat-bubble-left-right'],
                    ['label' => 'Pengumuman', 'nilai' => $statistik['pengumuman'], 'ikon' => 'megaphone'],
                    ['label' => 'Berita', 'nilai' => $statistik['berita'], 'ikon' => 'newspaper'],
                    ['label' => 'Laporan terbuka', 'nilai' => $statistik['laporan'], 'ikon' => 'exclamation-triangle'],
                ];
            @endphp

            @foreach ($kartu as $k)
                <div class="card p-4 text-center">
                    <x-ikon :name="$k['ikon']" class="mx-auto h-6 w-6 text-brand-500 dark:text-brand-400" />
                    <p class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-400">{{ $k['nilai'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $k['label'] }}</p>
                </div>
            @endforeach
        </section>
    @endif

    {{-- ─── Menu cepat ───────────────────────────────── --}}
    <section class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('feed') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <x-ikon name="chat-bubble-left-right" class="h-7 w-7 text-brand-600 dark:text-brand-400" />
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Feed</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Lihat & bagikan cerita siswa.</p>
        </a>

        <a href="{{ route('pengumuman.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <x-ikon name="megaphone" class="h-7 w-7 text-brand-600 dark:text-brand-400" />
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Pengumuman</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Info resmi sekolah & kelasmu.</p>
        </a>

        <a href="{{ route('berita.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <x-ikon name="newspaper" class="h-7 w-7 text-brand-600 dark:text-brand-400" />
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Berita</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kegiatan & prestasi sekolah.</p>
        </a>

        <a href="{{ route('notifikasi.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <x-ikon name="bell" class="h-7 w-7 text-brand-600 dark:text-brand-400" />
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Notifikasi</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Info & interaksi terkini.</p>
        </a>
    </section>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        {{-- ─── Pengumuman terbaru ────────────────────── --}}
        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-1.5 text-sm font-bold dark:text-white">
                    <x-ikon name="megaphone" class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                    Pengumuman terbaru
                </h2>
                <a href="{{ route('pengumuman.index') }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Lihat semua</a>
            </div>

            <ul class="mt-3 divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($pengumumanTerbaru as $p)
                    <li class="flex items-start gap-2 py-2.5">
                        @if ($p->is_pinned)
                            <x-ikon name="bookmark" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-amber-500" title="Disematkan" />
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-700 dark:text-slate-200">{{ $p->judul }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">
                                {{ $p->penulis?->name }} &bull; {{ $p->published_at?->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-center text-sm text-slate-400 dark:text-slate-500">
                        Belum ada pengumuman.
                    </li>
                @endforelse
            </ul>
        </section>

        {{-- ─── Berita terbaru ────────────────────────── --}}
        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-1.5 text-sm font-bold dark:text-white">
                    <x-ikon name="newspaper" class="h-4 w-4 text-brand-600 dark:text-brand-400" />
                    Berita terbaru
                </h2>
                <a href="{{ route('berita.index') }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Lihat semua</a>
            </div>

            <ul class="mt-3 divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($beritaTerbaru as $b)
                    <li class="py-2.5">
                        <p class="truncate text-sm font-medium text-slate-700 dark:text-slate-200">{{ $b->judul }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $b->kategori?->nama ?? 'Tanpa kategori' }} &bull; {{ $b->published_at?->diffForHumans() }}
                        </p>
                    </li>
                @empty
                    <li class="py-4 text-center text-sm text-slate-400 dark:text-slate-500">
                        Belum ada berita terbit.
                    </li>
                @endforelse
            </ul>
        </section>
    </div>

    {{-- ─── Pintasan pengelola ──────────────────────── --}}
    @if (auth()->user()->isPengelola())
        <section class="mt-4">
            <a href="{{ route('admin.beranda') }}" class="card flex items-center gap-3 p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
                <x-ikon name="cog-6-tooth" class="h-7 w-7 shrink-0 text-brand-600 dark:text-brand-400" />
                <div>
                    <h2 class="text-sm font-semibold dark:text-white">Panel Pengelola</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kelola pengumuman, berita, moderasi, dan pengguna.</p>
                </div>
            </a>
        </section>
    @endif
</x-layouts.app>
