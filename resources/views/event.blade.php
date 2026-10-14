<x-layouts.app :title="'Event & Kegiatan'">
    {{-- ─── Hero ─────────────────────────────────────────────── --}}
    <section class="mb-5 card overflow-hidden bg-gradient-to-br from-rose-500 to-brand-700 p-6 text-white sm:p-8">
        <span class="badge bg-white/15">📅 Event & Kegiatan</span>
        <h1 class="mt-3 text-2xl font-bold sm:text-3xl">Agenda Kegiatan Sekolah</h1>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/80">
            Ikuti lomba, pentas, seminar, dan aksi sosial dari OSIS SMK Negeri 1 Cirebon.
        </p>
    </section>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            <article class="card flex flex-col overflow-hidden">
                {{-- Poster / kepala warna --}}
                <a href="{{ route('event.detail', $event) }}" class="block">
                    @if ($event->poster_url)
                        <img src="{{ $event->poster_url }}" alt="" class="h-40 w-full object-cover">
                    @else
                        <div class="flex h-40 items-center justify-center bg-gradient-to-br from-brand-500 to-brand-700 text-4xl text-white">
                            {{ Str::startsWith($event->nama, 'Lomba') || Str::contains($event->nama, 'Kompetisi') ? '🏆' : (Str::contains($event->nama, 'Seminar') ? '🎤' : (Str::contains($event->nama, 'Pentas') ? '🎭' : '📅')) }}
                        </div>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-5">
                    <h2 class="text-base font-bold leading-snug text-slate-800 dark:text-slate-100">
                        <a href="{{ route('event.detail', $event) }}" class="hover:text-brand-600 hover:underline dark:hover:text-brand-400">
                            {{ $event->nama }}
                        </a>
                    </h2>

                    <p class="mt-2 line-clamp-2 flex-1 text-sm text-slate-600 dark:text-slate-300">
                        {{ Str::limit($event->deskripsi, 90) }}
                    </p>

                    <div class="mt-3 space-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <p>🕐 {{ $event->waktu_mulai->translatedFormat('d M Y, H:i') }} WIB</p>
                        @if ($event->lokasi)
                            <p>📍 {{ $event->lokasi }}</p>
                        @endif
                        <p>
                            👥 {{ $event->peserta_count }}
                            @if ($event->kuota) / {{ $event->kuota }} peserta @else peserta @endif
                            @if ($event->penuh()) <span class="font-semibold text-rose-500">(penuh)</span> @endif
                        </p>
                    </div>

                    <a href="{{ route('event.detail', $event) }}" class="btn-primary mt-4 text-xs">Lihat detail</a>
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center">
                <div class="text-4xl">📅</div>
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada kegiatan</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Agenda kegiatan akan diumumkan di sini.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
