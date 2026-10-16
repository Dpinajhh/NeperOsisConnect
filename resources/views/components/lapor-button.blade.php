{{--
    Tombol "Laporkan" untuk post / komentar.
    Pemakaian: <x-lapor-button tipe="post" :id="$post->id" />
--}}
@props(['tipe', 'id'])

<div class="relative" x-data="{ buka: false, alasan: '' }" @click.outside="buka = false">
    <button type="button" @click="buka = !buka" title="Laporkan konten ini"
            class="rounded-lg p-1.5 text-slate-300 transition hover:bg-rose-50 hover:text-rose-500 dark:text-slate-500 dark:hover:bg-rose-950">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"/></svg>
    </button>

    <div x-show="buka" x-cloak x-transition
         class="absolute right-0 z-30 mt-1 w-72 rounded-xl border border-slate-200 bg-white p-3 shadow-xl dark:border-slate-700 dark:bg-slate-800">
        <p class="text-sm font-bold dark:text-white">Laporkan konten</p>
        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Beri tahu pengurus jika konten melanggar aturan.</p>

        <form method="POST" action="{{ route('lapor.store') }}" class="mt-2.5 space-y-2">
            @csrf
            <input type="hidden" name="tipe" value="{{ $tipe }}">
            <input type="hidden" name="id" value="{{ $id }}">

            <textarea name="alasan" x-model="alasan" rows="3" required maxlength="500"
                      placeholder="Jelaskan mengapa konten ini tidak pantas..."
                      class="input text-sm"></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" @click="buka = false"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                    Batal
                </button>
                <button type="submit" :disabled="!alasan.trim()"
                        class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-700 disabled:opacity-40">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>
