<!DOCTYPE html>
<html lang="id" x-data="temaOsis()" :class="dark ? 'dark' : ''" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Masuk' }} | Osis Connect</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">

    <script>
        (function () {
            const tersimpan = localStorage.getItem('osis-tema');
            const sistem = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (tersimpan === 'dark' || (!tersimpan && sistem)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="flex min-h-screen flex-col bg-gradient-to-br from-brand-50 via-white to-slate-100 font-sans text-slate-800 antialiased dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 dark:text-slate-100">

    <div class="flex flex-1 items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="mb-6 text-center">
                <a href="{{ route('beranda') }}" class="inline-flex items-center gap-2.5">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-2xl font-bold text-white shadow-lg">O</span>
                </a>
                <h1 class="mt-3 text-2xl font-bold dark:text-white">Osis <span class="text-brand-600 dark:text-brand-400">Connect</span></h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Platform siswa SMK Negeri 1 Cirebon</p>
            </div>

            {{-- Kartu form --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl dark:border-slate-700 dark:bg-slate-800">
                {{ $slot }}
            </div>

            <p class="mt-4 text-center text-xs text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} Osis Connect &bull; SMK Negeri 1 Cirebon
            </p>
        </div>
    </div>

    <button @click="tukarTema()" type="button" title="Ganti tema"
            class="fixed right-4 top-4 rounded-lg p-2 text-slate-400 transition hover:bg-white hover:text-brand-600 dark:hover:bg-slate-800">
        <svg x-show="!dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/></svg>
        <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
    </button>

    @if($errors->any())
        <div class="pointer-events-none fixed inset-x-0 top-4 z-50 flex justify-center px-4"
             x-data="{ tampil: true }" x-show="tampil" x-init="setTimeout(() => tampil = false, 6000)">
            <div class="pointer-events-auto rounded-xl bg-rose-600 px-4 py-3 text-sm font-medium text-white shadow-lg">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

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
</body>
</html>
