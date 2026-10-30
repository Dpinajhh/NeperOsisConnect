<x-layouts.app :title="'Event & Kegiatan'">
    {{-- ─── Hero ─────────────────────────────────────────────── --}}
    <section class="mb-5 card overflow-hidden bg-gradient-to-br from-rose-500 to-brand-700 p-6 text-white sm:p-8">
        <span class="badge inline-flex items-center gap-1 bg-white/15">
            <x-ikon name="calendar-days" class="h-3.5 w-3.5" />
            Event &amp; Kegiatan
        </span>
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
                        <div class="flex h-40 items-center justify-center bg-gradient-to-br from-brand-500 to-brand-700 text-white">
                            <x-ikon :name="Str::startsWith($event->nama, 'Lomba') || Str::contains($event->nama, 'Kompetisi') ? 'trophy' : (Str::contains($event->nama, 'Seminar') ? 'microphone' : (Str::contains($event->nama, 'Pentas') ? 'musical-note' : 'calendar-days'))" class="h-12 w-12" />
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
                        <p class="inline-flex items-center gap-1.5">
                            <x-ikon name="clock" class="h-3.5 w-3.5 shrink-0" />
                            {{ $event->waktu_mulai->translatedFormat('d M Y, H:i') }} WIB
                        </p>
                        @if ($event->lokasi)
                            <p class="inline-flex items-center gap-1.5">
                                <x-ikon name="map-pin" class="h-3.5 w-3.5 shrink-0" />
                                {{ $event->lokasi }}
                            </p>
                        @endif
                        <p class="inline-flex items-center gap-1.5">
                            <x-ikon name="users" class="h-3.5 w-3.5 shrink-0" />
                            <span>
                                {{ $event->peserta_count }}
                                @if ($event->kuota) / {{ $event->kuota }} peserta @else peserta @endif
                                @if ($event->penuh()) <span class="font-semibold text-rose-500">(penuh)</span> @endif
                            </span>
                        </p>
                    </div>

                    <a href="{{ route('event.detail', $event) }}" class="btn-primary mt-4 text-xs">Lihat detail</a>
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center">
                <x-ikon name="calendar-days" class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" />
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada kegiatan</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Agenda kegiatan akan diumumkan di sini.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
