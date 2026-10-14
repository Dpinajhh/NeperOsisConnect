<x-layouts.app :title="$event->nama">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('event') }}" class="mb-3 inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            ← Semua kegiatan
        </a>

        <div class="card overflow-hidden">
            @if ($event->poster_url)
                <img src="{{ $event->poster_url }}" alt="" class="max-h-72 w-full object-cover">
            @else
                <div class="flex h-40 items-center justify-center bg-gradient-to-br from-brand-500 to-brand-700 text-5xl text-white">📅</div>
            @endif

            <div class="p-6">
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">{{ $event->nama }}</h1>

                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Waktu</dt>
                        <dd class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ $event->waktu_mulai->translatedFormat('d F Y, H:i') }} WIB
                            @if ($event->waktu_selesai)
                                <span class="block text-xs font-normal text-slate-400">s.d. {{ $event->waktu_selesai->translatedFormat('d F Y, H:i') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lokasi</dt>
                        <dd class="mt-1 font-semibold text-slate-700 dark:text-slate-200">{{ $event->lokasi ?? 'Akan diinformasikan' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Peserta</dt>
                        <dd class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            {{ $event->peserta_count }} terdaftar
                            @if ($event->kuota) <span class="text-xs font-normal text-slate-400">dari kuota {{ $event->kuota }}</span> @endif
                        </dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Program kerja</dt>
                        <dd class="mt-1 font-semibold text-slate-700 dark:text-slate-200">
                            @if ($event->programKerja)
                                <a href="{{ route('program-kerja.detail', $event->programKerja) }}" class="text-brand-600 hover:underline dark:text-brand-400">{{ $event->programKerja->nama }}</a>
                            @else
                                Umum
                            @endif
                        </dd>
                    </div>
                </dl>

                <h2 class="mt-6 text-sm font-bold dark:text-white">Deskripsi Kegiatan</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ $event->deskripsi }}
                </p>

                {{-- Tombol daftar --}}
                <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-700">
                    @auth
                        @if ($sudahDaftar)
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="badge bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200">✅ Kamu sudah terdaftar</span>
                                <form method="POST" action="{{ route('event.batal', $event) }}"
                                      onsubmit="return confirm('Batalkan pendaftaran kegiatan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost text-xs">Batalkan</button>
                                </form>
                            </div>
                        @elseif ($event->penuh())
                            <span class="badge bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-200">Kuota penuh</span>
                        @else
                            <form method="POST" action="{{ route('event.daftar', $event) }}">
                                @csrf
                                <button type="submit" class="btn-primary">Daftar sebagai peserta</button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Masuk untuk mendaftar</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- ─── Daftar peserta ───────────────────────────────────── --}}
        @if ($event->peserta->isNotEmpty())
            <section class="mt-4 card p-6">
                <h2 class="text-sm font-bold dark:text-white">Peserta terbaru</h2>
                <div class="mt-3 flex flex-wrap gap-3">
                    @foreach ($event->peserta as $user)
                        <div class="flex items-center gap-2 rounded-full bg-slate-50 py-1 pl-1 pr-3 dark:bg-slate-900">
                            <img src="{{ $user->avatar_url }}" alt="" class="h-7 w-7 rounded-full object-cover">
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
