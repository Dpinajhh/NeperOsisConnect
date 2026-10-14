<x-layouts.app :title="$program->nama">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('program-kerja') }}" class="mb-3 inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            ← Semua program kerja
        </a>

        {{-- ─── Kepala ─────────────────────────────────────────── --}}
        <div class="card overflow-hidden">
            <div class="bg-gradient-to-br from-brand-600 to-brand-800 p-6 text-white">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="badge bg-white/15">{{ $program->status_label }}</span>
                    @if ($program->bidang)
                        <span class="badge bg-white/15">Bidang {{ $program->bidang }}</span>
                    @endif
                </div>
                <h1 class="mt-3 text-2xl font-bold">{{ $program->nama }}</h1>
                @if ($program->tanggal_mulai)
                    <p class="mt-2 text-sm text-white/80">
                        {{ $program->tanggal_mulai->translatedFormat('d F Y') }}
                        @if ($program->tanggal_selesai) — {{ $program->tanggal_selesai->translatedFormat('d F Y') }} @endif
                    </p>
                @endif
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between text-sm font-semibold">
                    <span class="text-slate-500 dark:text-slate-400">Progres pelaksanaan</span>
                    <span class="text-brand-600 dark:text-brand-400">{{ $program->progres }}%</span>
                </div>
                <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                    <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600" style="width: {{ $program->progres }}%"></div>
                </div>

                <h2 class="mt-6 text-sm font-bold dark:text-white">Deskripsi</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ $program->deskripsi }}
                </p>

                @if ($program->penanggung_jawab)
                    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-semibold">Penanggung jawab:</span> {{ $program->penanggung_jawab }}
                    </p>
                @endif
            </div>
        </div>

        {{-- ─── Kegiatan terkait ────────────────────────────────── --}}
        <section class="mt-4 card p-6">
            <h2 class="text-sm font-bold dark:text-white">Kegiatan terkait</h2>

            @forelse ($program->kegiatan as $event)
                <a href="{{ route('event.detail', $event) }}"
                   class="mt-3 flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-brand-300 dark:border-slate-700 dark:hover:border-brand-600">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300">📅</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $event->nama }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $event->waktu_mulai->translatedFormat('d M Y, H:i') }}
                            @if ($event->lokasi) &bull; {{ $event->lokasi }} @endif
                        </span>
                    </span>
                </a>
            @empty
                <p class="mt-3 rounded-xl bg-slate-50 px-4 py-4 text-center text-xs text-slate-400 dark:bg-slate-900 dark:text-slate-500">
                    Belum ada kegiatan terjadwal untuk program ini.
                </p>
            @endforelse
        </section>
    </div>
</x-layouts.app>
