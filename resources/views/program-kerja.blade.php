<x-layouts.app :title="'Program Kerja OSIS'">
    {{-- ─── Hero ─────────────────────────────────────────────── --}}
    <section class="mb-5 card overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white sm:p-8">
        <span class="badge bg-white/15">📋 Program Kerja</span>
        <h1 class="mt-3 text-2xl font-bold sm:text-3xl">Program Kerja OSIS</h1>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-white/80">
            Rencana dan aksi nyata pengurus OSIS SMK Negeri 1 Cirebon untuk satu periode kepengurusan.
        </p>
    </section>

    {{-- ─── Filter status ─────────────────────────────── --}}
    @php
        $tab = [
            '' => 'Semua',
            'rencana' => 'Rencana',
            'berjalan' => 'Berjalan',
            'selesai' => 'Selesai',
            'batal' => 'Dibatalkan',
        ];
        $aktif = request('status', '');
    @endphp

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($tab as $nilai => $label)
            <a href="{{ $nilai ? route('program-kerja', ['status' => $nilai]) : route('program-kerja') }}"
               class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition
                      {{ $aktif === $nilai
                          ? 'bg-brand-600 text-white shadow-sm'
                          : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ─── Daftar program ────────────────────────────── --}}
    <div class="grid gap-4 lg:grid-cols-2">
        @forelse ($programs as $program)
            <article class="card flex flex-col p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold leading-snug text-slate-800 dark:text-slate-100">
                            <a href="{{ route('program-kerja.detail', $program) }}" class="hover:text-brand-600 hover:underline dark:hover:text-brand-400">
                                {{ $program->nama }}
                            </a>
                        </h2>
                        @if ($program->bidang)
                            <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Bidang {{ $program->bidang }}</p>
                        @endif
                    </div>
                    <span class="badge shrink-0 {{ $program->status_warna }}">{{ $program->status_label }}</span>
                </div>

                <p class="mt-3 line-clamp-2 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ Str::limit($program->deskripsi, 120) }}
                </p>

                {{-- Progres --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Progres</span>
                        <span>{{ $program->progres }}%</span>
                    </div>
                    <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-600" style="width: {{ $program->progres }}%"></div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500 dark:border-slate-700 dark:text-slate-400">
                    <span>
                        @if ($program->tanggal_mulai)
                            {{ $program->tanggal_mulai->translatedFormat('d M Y') }}
                            @if ($program->tanggal_selesai) — {{ $program->tanggal_selesai->translatedFormat('d M Y') }} @endif
                        @else
                            Jadwal menyusul
                        @endif
                    </span>
                    <span>{{ $program->kegiatan_count }} kegiatan</span>
                </div>
            </article>
        @empty
            <div class="card col-span-full p-10 text-center">
                <div class="text-4xl">📋</div>
                <h2 class="mt-3 text-lg font-bold dark:text-white">Belum ada program kerja</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pengurus OSIS akan menyusunnya segera.</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
