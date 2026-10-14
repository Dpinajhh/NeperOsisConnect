<x-layouts.app :title="'Dashboard'">
    {{-- ─── Sapaan ────────────────────────────────────── --}}
    <section class="card p-6">
        <h1 class="text-2xl font-bold dark:text-white">
            Hai, {{ auth()->user()->name }}! 👋
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
                    ['label' => 'Siswa', 'nilai' => $statistik['siswa'], 'ikon' => '🎒'],
                    ['label' => 'Post', 'nilai' => $statistik['post'], 'ikon' => '💬'],
                    ['label' => 'Pengumuman', 'nilai' => $statistik['pengumuman'], 'ikon' => '📢'],
                    ['label' => 'Berita', 'nilai' => $statistik['berita'], 'ikon' => '📰'],
                    ['label' => 'Laporan terbuka', 'nilai' => $statistik['laporan'], 'ikon' => '⚠️'],
                ];
            @endphp

            @foreach ($kartu as $k)
                <div class="card p-4 text-center">
                    <div class="text-xl">{{ $k['ikon'] }}</div>
                    <p class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-400">{{ $k['nilai'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $k['label'] }}</p>
                </div>
            @endforeach
        </section>
    @endif

    {{-- ─── Menu cepat ───────────────────────────────── --}}
    <section class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('feed') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <div class="text-2xl">💬</div>
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Feed</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Lihat & bagikan cerita siswa.</p>
        </a>

        <a href="{{ route('pengumuman.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <div class="text-2xl">📢</div>
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Pengumuman</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Info resmi sekolah & kelasmu.</p>
        </a>

        <a href="{{ route('berita.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <div class="text-2xl">📰</div>
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Berita</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kegiatan & prestasi sekolah.</p>
        </a>

        <a href="{{ route('notifikasi.index') }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
            <div class="text-2xl">🔔</div>
            <h2 class="mt-2 text-sm font-semibold dark:text-white">Notifikasi</h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Info & interaksi terkini.</p>
        </a>
    </section>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        {{-- ─── Pengumuman terbaru ────────────────────── --}}
        <section class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold dark:text-white">📢 Pengumuman terbaru</h2>
                <a href="{{ route('pengumuman.index') }}" class="text-xs font-semibold text-brand-600 hover:underline dark:text-brand-400">Lihat semua</a>
            </div>

            <ul class="mt-3 divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($pengumumanTerbaru as $p)
                    <li class="flex items-start gap-2 py-2.5">
                        @if ($p->is_pinned)
                            <span class="mt-0.5 text-xs" title="Disematkan">📌</span>
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
                <h2 class="text-sm font-bold dark:text-white">📰 Berita terbaru</h2>
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
                <span class="text-2xl">⚙️</span>
                <div>
                    <h2 class="text-sm font-semibold dark:text-white">Panel Pengelola</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kelola pengumuman, berita, moderasi, dan pengguna.</p>
                </div>
            </a>
        </section>
    @endif
</x-layouts.app>
