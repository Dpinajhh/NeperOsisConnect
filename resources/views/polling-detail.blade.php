<x-layouts.app :title="$polling->pertanyaan">
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('polling') }}" class="mb-3 inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            ← Semua polling
        </a>

        <div class="card p-6">
            <span class="badge inline-flex items-center gap-1 bg-violet-100 text-violet-700 dark:bg-violet-900 dark:text-violet-200">
                <x-ikon name="chart-bar" class="h-3.5 w-3.5" />
                Polling
            </span>

            <h1 class="mt-3 text-xl font-bold text-slate-800 dark:text-slate-100">{{ $polling->pertanyaan }}</h1>

            @if ($polling->deskripsi)
                <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $polling->deskripsi }}</p>
            @endif

            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">{{ $totalSuara }} suara masuk</p>

            {{-- ─── Form voting ───────────────────────────────── --}}
            @if (! $sudahMemilih)
                @auth
                    <form method="POST" action="{{ route('polling.vote', $polling) }}" class="mt-5 space-y-2">
                        @csrf

                        @foreach ($polling->pilihan as $pilihan)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3.5 transition hover:border-violet-400 hover:bg-violet-50/50 dark:border-slate-700 dark:hover:border-violet-500 dark:hover:bg-violet-950/30">
                                <input type="radio" name="pilihan_id" value="{{ $pilihan->id }}" required
                                       class="h-4 w-4 border-slate-300 text-violet-600 focus:ring-violet-500">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $pilihan->teks }}</span>
                            </label>
                        @endforeach

                        <button type="submit" class="btn-primary mt-3 w-full">Kirim Suara</button>
                    </form>
                @else
                    <div class="mt-5 rounded-xl bg-slate-50 p-5 text-center dark:bg-slate-900">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Masuk untuk memberikan suara.</p>
                        <a href="{{ route('login') }}" class="btn-primary mt-3">Masuk</a>
                    </div>
                @endauth
            @else
                {{-- ─── Hasil ──────────────────────────────────── --}}
                <div class="mt-5 space-y-3">
                    @foreach ($hasil as $baris)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $baris['teks'] }}
                                    @if ($pilihanSaya === $baris['id'])
                                        <span class="ml-1 text-xs font-normal text-violet-600 dark:text-violet-400">(pilihanmu)</span>
                                    @endif
                                </span>
                                <span class="font-bold text-slate-600 dark:text-slate-300">{{ $baris['persen'] }}%</span>
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-brand-600 transition-all" style="width: {{ $baris['persen'] }}%"></div>
                                </div>
                                <span class="w-12 text-right text-xs text-slate-400">{{ $baris['jumlah'] }} suara</span>
                            </div>
                        </div>
                    @endforeach

                    <p class="rounded-xl bg-violet-50 px-4 py-3 text-center text-xs font-semibold text-violet-700 dark:bg-violet-950 dark:text-violet-300">
                        Terima kasih sudah berpartisipasi — satu pengguna satu suara.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
