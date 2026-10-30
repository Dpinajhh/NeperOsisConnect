{{-- Halaman placeholder generik untuk fitur yang masih dibangun.
     Variabel: $ikon, $judul, $deskripsi --}}
<x-layouts.app :title="$judul">
    <div class="card mx-auto max-w-2xl p-8 text-center sm:p-12">
        <div class="text-5xl">{{ $ikon }}</div>

        <h1 class="mt-4 text-2xl font-bold dark:text-white">{{ $judul }}</h1>

        <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-slate-500 dark:text-slate-400">
            {{ $deskripsi }}
        </p>

        <div class="mt-5 inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300">
            <x-ikon name="wrench-screwdriver" class="h-3.5 w-3.5" />
            Sedang dikembangkan
        </div>

        <div class="mt-7">
            <a href="{{ route('dashboard') }}" class="btn-ghost">← Kembali ke Dashboard</a>
        </div>
    </div>
</x-layouts.app>
