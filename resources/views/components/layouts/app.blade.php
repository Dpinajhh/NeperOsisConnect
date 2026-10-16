<!DOCTYPE html>
<html lang="id" x-data="temaOsis()" :class="dark ? 'dark' : ''" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Beranda' }} | Osis Connect</title>
    <meta name="description" content="Osis Connect — platform siswa SMK Negeri 1 Cirebon untuk berbagi cerita, pengumuman, dan berita sekolah.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- PWA: bisa dipasang di HP seperti aplikasi --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Osis Connect">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">

    <script>
        // Terapkan tema sebelum render agar tidak berkedip
        (function () {
            const tersimpan = localStorage.getItem('osis-tema');
            const sistem = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (tersimpan === 'dark' || (!tersimpan && sistem)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased dark:bg-slate-900 dark:text-slate-100">

    {{-- ─── Navbar ─────────────────────────────────────── --}}
    <header x-data="{ menu: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-700 dark:bg-slate-800/90">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-3 px-4">
            {{-- Logo --}}
            <a href="{{ auth()->check() ? route('dashboard') : route('beranda') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white shadow-sm">O</span>
                <span class="hidden text-base font-bold sm:block dark:text-white">Osis <span class="text-brand-600 dark:text-brand-400">Connect</span></span>
            </a>

            {{-- Menu tengah (desktop) --}}
            @auth
                <nav class="hidden flex-wrap items-center gap-1 md:flex">
                    @php
                        $link = 'rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-brand-600 dark:text-slate-300 dark:hover:bg-slate-700';
                        $aktif = 'bg-brand-50 text-brand-700 dark:bg-slate-700 dark:text-brand-300';
                    @endphp
                    <a href="{{ route('dashboard') }}" class="{{ $link }} {{ request()->routeIs('dashboard') ? $aktif : '' }}">Beranda</a>
                    <a href="{{ route('feed') }}" class="{{ $link }} {{ request()->routeIs('feed*') ? $aktif : '' }}">Feed</a>
                    <a href="{{ route('profil-osis') }}" class="{{ $link }} {{ request()->routeIs('profil-osis') ? $aktif : '' }}">OSIS</a>
                    <a href="{{ route('program-kerja') }}" class="{{ $link }} {{ request()->routeIs('program-kerja*') ? $aktif : '' }}">Program</a>
                    <a href="{{ route('event') }}" class="{{ $link }} {{ request()->routeIs('event*') ? $aktif : '' }}">Event</a>
                    <a href="{{ route('polling') }}" class="{{ $link }} {{ request()->routeIs('polling*') ? $aktif : '' }}">Polling</a>
                    <a href="{{ route('pengumuman.index') }}" class="{{ $link }} {{ request()->routeIs('pengumuman*') ? $aktif : '' }}">Pengumuman</a>
                    <a href="{{ route('berita.index') }}" class="{{ $link }} {{ request()->routeIs('berita*') ? $aktif : '' }}">Berita</a>
                    @if(auth()->user()->isPengelola())
                        <a href="{{ route('admin.beranda') }}" class="{{ $link }} {{ request()->routeIs('admin.*') ? $aktif : '' }}">Kelola</a>
                    @endif
                </nav>
            @endauth

            {{-- Kanan --}}
            <div class="flex items-center gap-2">
                @auth
                    {{-- Pencarian --}}
                    <form action="{{ route('cari') }}" method="GET" class="hidden md:block">
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari...  (tekan /)"
                                   class="input w-44 py-2 pl-9 focus:w-64 transition-all lg:w-56">
                        </div>
                    </form>

                    {{-- Pencarian (mobile) --}}
                    <a href="{{ route('cari') }}" title="Cari"
                       class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 md:hidden dark:text-slate-400 dark:hover:bg-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </a>
                @endauth

                <button @click="tukarTema()" type="button" title="Ganti tema"
                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                    <svg x-show="!dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/></svg>
                    <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                </button>

                @auth
                    {{-- Notifikasi --}}
                    <a href="{{ route('notifikasi.index') }}" class="relative rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                        <?php $unread = auth()->user()->notifikasi()->belumDibaca()->count(); ?>
                        @if($unread > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $unread > 9 ? '9+' : $unread }}</span>
                        @endif
                    </a>

                    {{-- Dropdown user --}}
                    <div class="relative" x-data="{ buka: false }" @click.outside="buka = false">
                        <button @click="buka = !buka" class="flex items-center gap-2 rounded-xl p-1 pr-2 transition hover:bg-slate-100 dark:hover:bg-slate-700">
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            <span class="hidden text-sm font-medium sm:block dark:text-slate-200">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>

                        <div x-show="buka" x-cloak x-transition
                             class="absolute right-0 mt-2 w-60 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800">
                            <div class="border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                                <p class="text-sm font-semibold dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                                <span class="mt-1.5 inline-block rounded-md px-2 py-0.5 text-[11px] font-semibold uppercase
                                    {{ auth()->user()->isAdmin() ? 'bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-200' : (auth()->user()->isGuru() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300') }}">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <a href="{{ route('profil.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700">✏️ Profil Saya</a>
                            <a href="{{ route('profil.show', auth()->user()) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700">👤 Lihat Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 text-left text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950">🚪 Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('profil-osis') }}" class="hidden text-xs font-semibold text-slate-500 hover:text-brand-600 lg:block dark:text-slate-400">Profil OSIS</a>
                    <a href="{{ route('program-kerja') }}" class="hidden text-xs font-semibold text-slate-500 hover:text-brand-600 lg:block dark:text-slate-400">Program Kerja</a>
                    <a href="{{ route('event') }}" class="hidden text-xs font-semibold text-slate-500 hover:text-brand-600 lg:block dark:text-slate-400">Event</a>
                    <a href="{{ route('login') }}" class="btn-ghost text-xs sm:text-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary text-xs sm:text-sm">Daftar</a>
                @endauth
            </div>
        </div>

        {{-- Menu bawah (mobile) --}}
        @auth
            <nav class="flex justify-around border-t border-slate-200 py-1.5 md:hidden dark:border-slate-700">
                @php
                    $m = 'flex flex-1 flex-col items-center gap-0.5 rounded-lg py-1.5 text-[11px] font-medium text-slate-500 transition dark:text-slate-400';
                    $mA = 'text-brand-600 dark:text-brand-400';
                @endphp
                <a href="{{ route('dashboard') }}" class="{{ $m }} {{ request()->routeIs('dashboard') ? $mA : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                    Beranda
                </a>
                <a href="{{ route('feed') }}" class="{{ $m }} {{ request()->routeIs('feed*') ? $mA : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                    Feed
                </a>
                <a href="{{ route('pengumuman.index') }}" class="{{ $m }} {{ request()->routeIs('pengumuman*') ? $mA : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73"/></svg>
                    Info
                </a>
                <a href="{{ route('berita.index') }}" class="{{ $m }} {{ request()->routeIs('berita*') ? $mA : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/></svg>
                    Berita
                </a>
                <a href="{{ route('profil.edit') }}" class="{{ $m }} {{ request()->routeIs('profil*') ? $mA : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    Profil
                </a>
            </nav>
        @endauth
    </header>

    {{-- ─── Flash message ────────────────────────────── --}}
    @if(session('sukses') || session('error') || $errors->any())
        <div class="pointer-events-none fixed inset-x-0 top-20 z-50 flex justify-center px-4"
             x-data="{ tampil: true }" x-show="tampil" x-init="setTimeout(() => tampil = false, 5000)" x-transition>
            <div class="pointer-events-auto max-w-md rounded-xl px-4 py-3 text-sm font-medium shadow-lg
                {{ session('sukses') ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                {{ session('sukses') ?? session('error') ?? 'Ada kesalahan pada data yang Anda masukkan.' }}
            </div>
        </div>
    @endif

    {{-- ─── Konten ───────────────────────────────────── --}}
    <main class="mx-auto max-w-6xl px-4 py-6 pb-24 md:pb-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 py-6 text-center text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
        <p><strong>Osis Connect</strong> — Platform siswa <strong>SMK Negeri 1 Cirebon</strong> &bull; <em>Sampurasun, Wargi Neper!</em></p>
        <p class="mt-1">Jl. Perjuangan–Sunyaragi, Karyamulya, Kesambi, Kota Cirebon &bull; Dibuat oleh pengurus OSIS &bull; Laravel {{ app()->version() }}</p>
    </footer>

<script>
    function temaOsis() {
        return {
            dark: document.documentElement.classList.contains('dark'),
            tukarTema() {
                this.dark = !this.dark;
                document.documentElement.classList.toggle('dark', this.dark);
                localStorage.setItem('osis-tema', this.dark ? 'dark' : 'terang');
            }
        };
    }
</script>

@stack('skrip')
</body>
</html>
