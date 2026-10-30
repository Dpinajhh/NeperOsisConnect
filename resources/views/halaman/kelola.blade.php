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
            ['label' => 'Siswa', 'nilai' => \App\Models\User::where('role', 'siswa')->count(), 'ikon' => 'academic-cap'],
            ['label' => 'Post', 'nilai' => \App\Models\Post::count(), 'ikon' => 'chat-bubble-left-right'],
            ['label' => 'Pengumuman', 'nilai' => \App\Models\Pengumuman::count(), 'ikon' => 'megaphone'],
            ['label' => 'Berita', 'nilai' => \App\Models\Berita::count(), 'ikon' => 'newspaper'],
            ['label' => 'Laporan terbuka', 'nilai' => \App\Models\Laporan::where('status', 'open')->count(), 'ikon' => 'exclamation-triangle'],
        ];
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($statistik as $s)
            <div class="card p-4 text-center">
                <x-ikon :name="$s['ikon']" class="mx-auto h-7 w-7 text-brand-500 dark:text-brand-400" />
                <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $s['nilai'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $s['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Menu pengelolaan --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @php
            $menu = [
                ['nama' => 'Kelola Pengumuman', 'rute' => 'admin.pengumuman.kelola', 'ikon' => 'megaphone', 'keterangan' => 'Buat & atur pengumuman untuk sekolah/jurusan/kelas'],
                ['nama' => 'Kelola Berita', 'rute' => 'admin.berita.kelola', 'ikon' => 'newspaper', 'keterangan' => 'Tulis berita kegiatan, draft, dan headline'],
                ['nama' => 'Program Kerja', 'rute' => 'admin.program-kerja.kelola', 'ikon' => 'clipboard-document-list', 'keterangan' => 'Tambah, ubah, dan hapus program kerja OSIS'],
                ['nama' => 'Event & Kegiatan', 'rute' => 'admin.event.kelola', 'ikon' => 'calendar-days', 'keterangan' => 'Atur agenda kegiatan dan pantau peserta'],
                ['nama' => 'Profil OSIS', 'rute' => 'admin.profil-osis.edit', 'ikon' => 'building-library', 'keterangan' => 'Visi, misi, sambutan, dan kepengurusan'],
                ['nama' => 'Moderasi Laporan', 'rute' => 'admin.laporan.kelola', 'ikon' => 'exclamation-triangle', 'keterangan' => 'Tangani laporan post & komentar dari siswa'],
                ['nama' => 'Kelola Pengguna', 'rute' => 'admin.pengguna.kelola', 'ikon' => 'academic-cap', 'keterangan' => 'Aktif/nonaktifkan akun & atur peran'],
                ['nama' => 'Feed Siswa', 'rute' => 'feed', 'ikon' => 'chat-bubble-left-right', 'keterangan' => 'Pantau aktivitas siswa'],
            ];
        @endphp

        @foreach ($menu as $m)
            @continue($m['rute'] === 'admin.pengguna.kelola' && auth()->user()->role !== 'admin')
            <a href="{{ route($m['rute']) }}" class="card p-5 transition hover:border-brand-300 hover:shadow-md dark:hover:border-brand-600">
                <x-ikon :name="$m['ikon']" class="h-7 w-7 text-brand-600 dark:text-brand-400" />
                <h2 class="mt-2 text-sm font-semibold dark:text-white">{{ $m['nama'] }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ $m['keterangan'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 rounded-xl border border-dashed border-slate-300 p-4 text-center text-xs text-slate-500 dark:border-slate-600 dark:text-slate-400">
        Menu pengelolaan penuh akan aktif bertahap seiring modusnya dibangun.
    </div>
</x-layouts.app>
