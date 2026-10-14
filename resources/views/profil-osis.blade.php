<x-layouts.app :title="'Profil OSIS'">
    {{-- ─── Hero ─────────────────────────────────────────────── --}}
    <section class="card overflow-hidden bg-gradient-to-br from-brand-600 to-brand-800 p-6 text-white sm:p-10">
        <span class="badge bg-white/15 text-white/90">🏫 Organisasi Siswa</span>

        <h1 class="mt-4 text-3xl font-bold sm:text-4xl">OSIS SMK Negeri 1 Cirebon</h1>
        <p class="mt-2 max-w-2xl leading-relaxed text-white/80">
            Sampurasun, Wargi Neper! Pengurus OSIS periode <strong>{{ $profil->periode }}</strong>
            hadir berkomitmen menjadikan kegiatan sekolah lebih hidup, kreatif, dan membanggakan.
        </p>

        <div class="mt-5 flex flex-wrap gap-2">
            <span class="badge bg-white/15">🎓 {{ $pengurus->count() }} pengurus</span>
            <span class="badge bg-white/15">🏷️ {{ $pengurus->pluck('bidang')->unique()->count() }} bidang</span>
            <span class="badge bg-white/15">📅 Periode {{ $profil->periode }}</span>
            @auth
                @if (auth()->user()->isPengelola())
                    <a href="{{ route('admin.profil-osis.edit') }}" class="badge bg-white text-brand-700 hover:bg-white/90">✏️ Kelola profil</a>
                @endif
            @endauth
        </div>
    </section>

    {{-- ─── Sambutan ────────────────────────────────────────── --}}
    @if ($profil->sambutuan)
        <section class="mt-4 card p-6">
            <h2 class="text-lg font-bold dark:text-white">Sambutan Pengurus</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $profil->sambutuan }}</p>
            @if ($profil->sambutuan_pengirim)
                <p class="mt-3 text-sm font-semibold text-brand-600 dark:text-brand-400">— {{ $profil->sambutuan_pengirim }}</p>
            @endif
        </section>
    @endif

    {{-- ─── Visi & Misi ─────────────────────────────────────── --}}
    <section class="mt-4 grid gap-4 lg:grid-cols-2">
        <div class="card p-6">
            <div class="flex items-center gap-2">
                <span class="text-xl">🎯</span>
                <h2 class="text-lg font-bold dark:text-white">Visi</h2>
            </div>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                {{ $profil->visi ?? 'Belum ditetapkan.' }}
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-2">
                <span class="text-xl">📋</span>
                <h2 class="text-lg font-bold dark:text-white">Misi</h2>
            </div>
            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                {{ $profil->misi ?? 'Belum ditetapkan.' }}
            </p>
        </div>
    </section>

    {{-- ─── Sejarah ─────────────────────────────────────────── --}}
    @if ($profil->sejarah)
        <section class="mt-4 card p-6">
            <div class="flex items-center gap-2">
                <span class="text-xl">📜</span>
                <h2 class="text-lg font-bold dark:text-white">Sejarah Singkat</h2>
            </div>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $profil->sejarah }}</p>
        </section>
    @endif

    {{-- ─── Kepengurusan ────────────────────────────────────── --}}
    <section class="mt-4">
        <h2 class="mb-3 text-lg font-bold dark:text-white">Pengurus Harian</h2>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($pengurus->take(4) as $p)
                <div class="card p-5 text-center">
                    <img src="{{ $p->foto_url }}" alt=""
                         class="mx-auto h-20 w-20 rounded-2xl object-cover shadow-sm">
                    <h3 class="mt-3 text-sm font-bold text-slate-800 dark:text-slate-100">{{ $p->nama }}</h3>
                    <p class="mt-0.5 text-xs font-semibold text-brand-600 dark:text-brand-400">{{ $p->jabatan }}</p>
                    @if ($p->bidang)
                        <span class="badge mt-2 bg-slate-100 text-[10px] text-slate-500 dark:bg-slate-700 dark:text-slate-400">{{ $p->bidang }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($pengurus->count() > 4)
            <h2 class="mb-3 mt-6 text-lg font-bold dark:text-white">Koordinator & Anggota</h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($pengurus->skip(4) as $p)
                    <div class="card flex items-center gap-3 p-4">
                        <img src="{{ $p->foto_url }}" alt="" class="h-12 w-12 rounded-xl object-cover">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $p->nama }}</h3>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $p->jabatan }}</p>
                            @if ($p->bidang)
                                <p class="truncate text-[11px] text-brand-600 dark:text-brand-400">{{ $p->bidang }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ─── Kontak ──────────────────────────────────────────── --}}
    <section class="mt-4 card p-6">
        <h2 class="text-lg font-bold dark:text-white">Hubungi Kami</h2>

        <div class="mt-3 grid gap-4 text-sm sm:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat Sekolah</p>
                <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $profil->alamat ?? 'SMK Negeri 1 Cirebon' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Media Sosial</p>
                <p class="mt-1 text-slate-600 dark:text-slate-300">
                    @if ($profil->instagram)
                        Instagram: <span class="font-semibold">{{ $profil->instagram }}</span><br>
                    @endif
                    @if ($profil->email)
                        Email: <span class="font-semibold">{{ $profil->email }}</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Telepon</p>
                <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $profil->telepon ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Website Resmi</p>
                <p class="mt-1">
                    <a href="https://www.smkn1-cirebon.sch.id" target="_blank" rel="noopener"
                       class="font-semibold text-brand-600 hover:underline dark:text-brand-400">smkn1-cirebon.sch.id</a>
                </p>
            </div>
        </div>
    </section>
</x-layouts.app>
