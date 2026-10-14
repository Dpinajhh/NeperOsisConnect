<x-layouts.app :title="'Beranda'">
    {{-- ─── Hero ─────────────────────────────────────── --}}
    <section class="card overflow-hidden">
        <div class="grid gap-8 p-6 sm:p-10 lg:grid-cols-[1.5fr_1fr] lg:items-center">
            <div>
                <span class="badge bg-brand-50 text-brand-700 dark:bg-brand-900 dark:text-brand-200">
                    🎓 SMK Negeri 1 Cirebon
                </span>

                <h1 class="mt-4 text-3xl font-bold leading-tight sm:text-4xl dark:text-white">
                    Osis <span class="text-brand-600 dark:text-brand-400">Connect</span>
                </h1>

                <p class="mt-3 max-w-xl leading-relaxed text-slate-600 dark:text-slate-300">
                    Ruang digital untuk warga <strong>SMK Negeri 1 Cirebon</strong> — berbagi cerita,
                    pantau pengumuman sekolah, dan baca berita kegiatan OSIS dalam satu tempat.
                    <span class="mt-1 block text-sm italic text-slate-500 dark:text-slate-400">
                        Sampurasun, Wargi Neper! 👋
                    </span>
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">Buka Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary">Daftar sekarang</a>
                        <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                    @endauth
                </div>
            </div>

            {{-- Statistik singkat --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-2xl bg-slate-50 p-4 text-center dark:bg-slate-900">
                    <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $statistik['siswa'] }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Siswa terdaftar</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 text-center dark:bg-slate-900">
                    <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $statistik['pengumuman'] }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pengumuman</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4 text-center dark:bg-slate-900">
                    <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $statistik['berita'] }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Berita</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Apa yang bisa dilakukan ─────────────────── --}}
    <section class="mt-6">
        <h2 class="mb-3 text-lg font-bold dark:text-white">Apa yang bisa kamu lakukan?</h2>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="card p-5">
                <div class="text-2xl">💬</div>
                <h3 class="mt-2 text-sm font-semibold dark:text-white">Feed Siswa</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                    Bagikan cerita, kegiatan kelas, dan pendapat. Bisa untuk seluruh sekolah, satu jurusan, atau satu kelas.
                </p>
            </div>

            <div class="card p-5">
                <div class="text-2xl">📢</div>
                <h3 class="mt-2 text-sm font-semibold dark:text-white">Pengumuman</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                    Info resmi dari pengurus OSIS &amp; guru — dari Jadwal ujian sampai pengumpulan tugas.
                </p>
            </div>

            <div class="card p-5">
                <div class="text-2xl">📰</div>
                <h3 class="mt-2 text-sm font-semibold dark:text-white">Berita Sekolah</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                    Liputan kegiatan, prestasi, dan opini seputar SMK Negeri 1 Cirebon.
                </p>
            </div>

            <div class="card p-5">
                <div class="text-2xl">🛡️</div>
                <h3 class="mt-2 text-sm font-semibold dark:text-white">Aman & Ter-moderasi</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                    Bisa melapor konten yang tidak pantas. Tanpa hoaks, tanpa bullying.
                </p>
            </div>
        </div>
    </section>

    {{-- ─── Pengumuman terbaru ──────────────────────── --}}
    @if ($pengumumanTerbaru->isNotEmpty())
        <section class="mt-6">
            <h2 class="mb-3 text-lg font-bold dark:text-white">Pengumuman terbaru</h2>

            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($pengumumanTerbaru as $p)
                    <div class="card p-5">
                        <p class="text-xs text-slate-400 dark:text-slate-500">
                            {{ $p->published_at?->translatedFormat('d M Y') }}
                        </p>
                        <h3 class="mt-1 text-sm font-semibold leading-snug dark:text-white">
                            {{ $p->judul }}
                        </h3>
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">
                                Masuk untuk membaca selengkapnya →
                            </a>
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ─── Info sekolah ────────────────────────────── --}}
    <section class="mt-6 card p-6">
        <h2 class="text-lg font-bold dark:text-white">SMK Negeri 1 Cirebon</h2>

        <dl class="mt-3 grid gap-3 text-sm text-slate-600 sm:grid-cols-2 dark:text-slate-300">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</dt>
                <dd>Jl. Perjuangan–Sunyaragi, Karyamulya, Kesambi, Kota Cirebon, Jawa Barat</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Julukan siswa</dt>
                <dd>Neper — SMK tertua di Kota Cirebon (berdiri sejak 1955)</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Website resmi</dt>
                <dd>
                    <a href="https://www.smkn1-cirebon.sch.id" target="_blank" rel="noopener"
                       class="font-medium text-brand-600 hover:underline dark:text-brand-400">
                        smkn1-cirebon.sch.id
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dikelola oleh</dt>
                <dd>Pengurus OSIS &amp; Pembina OSIS SMK Negeri 1 Cirebon</dd>
            </div>
        </dl>
    </section>
</x-layouts.app>
