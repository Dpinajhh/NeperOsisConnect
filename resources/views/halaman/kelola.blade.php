<x-layouts.app :title="'Panel Pengelola'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold dark:text-white">Panel Pengelola</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Pusat kendali pengurus OSIS &amp; guru untuk mengelola konten Osis Connect.
        </p>
    </div>

    {{-- Ringkasan angka --}}
    @php
        $statistik = [
            ['label' => 'Siswa', 'nilai' => \App\Models\User::where('role', 'siswa')->count(), 'ikon' => '🎒'],
            ['label' => 'Post', 'nilai' => \App\Models\Post::count(), 'ikon' => '💬'],
            ['label' => 'Pengumuman', 'nilai' => \App\Models\Pengumuman::count(), 'ikon' => '📢'],
            ['label' => 'Berita', 'nilai' => \App\Models\Berita::count(), 'ikon' => '📰'],
            ['label' => 'Laporan terbuka', 'nilai' => \App\Models\Laporan::where('status', 'open')->count(), 'ikon' => '⚠️'],
        ];
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($statistik as $s)
            <div class="card p-4 text-center">
                <div class="text-2xl">{{ $s['ikon'] }}</div>
                <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $s['nilai'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Menu pengelolaan --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $menu = [
                ['nama' => 'Kelola Pengumuman', 'rute' => 'pengumuman.index', 'ikon' => '📢', 'keterangan' => 'Buat & atur pengumuman untuk sekolah/jurusan/kelas'],
                ['nama' => 'Kelola Berita', 'rute' => 'berita.index', 'ikon' => '📰', 'keterangan' => 'Tulis berita kegiatan, draft, dan headline'],
                ['nama' => 'Moderasi Laporan', 'rute' => 'feed', 'ikon' => '⚠️', 'keterangan' => 'Tangani laporan post & komentar dari siswa'],
                ['nama' => 'Kelola Pengguna', 'rute' => 'feed', 'ikon' => '🎒', 'keterangan' => 'Aktif/nonaktifkan akun & atur peran'],
                ['nama' => 'Feed Siswa', 'rute' => 'feed', 'ikon' => '💬', 'keterangan' => 'Pantau aktivitas siswa'],
            ];
        @endphp

        @foreach ($menu as $m)
            <a href="{{ route($m['rute']) }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
                <div class="text-2xl">{{ $m['ikon'] }}</div>
                <h2 class="mt-2 text-sm font-semibold dark:text-white">{{ $m['nama'] }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ $m['keterangan'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 rounded-xl border border-dashed border-slate-300 p-4 text-center text-xs text-slate-500 dark:border-slate-600 dark:text-slate-400">
        Menu pengelolaan penuh akan aktif bertahap seiring modusnya dibangun.
    </div>
</x-layouts.app>
