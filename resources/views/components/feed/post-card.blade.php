{{-- Kartu satu postingan — gaya feed sosial --}}
@props(['post', 'ringkas' => true])

@php
    $saya = auth()->user();
    $komentarUtama = $post->komentar;
    $totalKomentar = $post->jumlah_komentar;
    $gambarUrl = $post->gambar ? asset('storage/'.$post->gambar) : null;
@endphp

<article class="card overflow-hidden" x-data="postInteraksi({{ $post->id }}, {{ json_encode($post->sudah_disukai) }}, {{ $post->jumlah_suka }})" data-post="{{ $post->id }}">

    {{-- ─── Kepala kartu ───────────────────────────────── --}}
    <header class="flex items-center gap-3 p-4 pb-3">
        <img src="{{ $post->user->avatar_url }}" alt="" class="h-10 w-10 rounded-full object-cover">

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-x-1.5 text-sm">
                <a href="{{ route('profil.show', $post->user) }}" class="font-semibold text-slate-800 hover:underline dark:text-slate-100">
                    {{ $post->user->name }}
                </a>
                @if ($post->user->isPengelola())
                    <span class="badge bg-brand-50 px-1.5 py-0 text-[10px] text-brand-600 dark:bg-brand-900 dark:text-brand-300">pengelola</span>
                @endif
            </div>
            <p class="mt-0.5 flex flex-wrap items-center gap-1 text-[11px] text-slate-400 dark:text-slate-500">
                @if ($post->user->kelas)
                    <span>{{ $post->user->kelas->nama }}</span>
                    <span>&bull;</span>
                @endif
                <span>{{ $post->created_at->diffForHumans() }}</span>
                <span>&bull;</span>
                <span>
                    @if ($post->visibilitas === 'sekolah') 🌐 Seluruh sekolah
                    @elseif ($post->visibilitas === 'kelas') 👥 Kelas {{ $post->user->kelas?->nama }}
                    @else 🏫 Jurusan {{ $post->user->kelas?->jurusan?->kode }}
                    @endif
                </span>
            </p>
        </div>

        @if ($saya->id === $post->user_id || $saya->isPengelola())
            <form method="POST" action="{{ route('posts.destroy', $post) }}"
                  onsubmit="return confirm('Hapus postingan ini?')" class="shrink-0">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950" title="Hapus">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.02.166m-1.02-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.02-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </form>
        @endif
    </header>

    {{-- ─── Gambar (klik ganda = suka) ───────────────── --}}
    @if ($gambarUrl)
        <div class="relative bg-slate-100 dark:bg-slate-900" @dblclick="suka()">
            <img src="{{ $gambarUrl }}" alt="Gambar postingan" class="max-h-[520px] w-full object-cover">

            {{-- Animasi hati --}}
            <div x-show="ledakan" x-cloak x-transition.opacity.duration.700ms
                 class="pointer-events-none absolute inset-0 flex items-center justify-center">
                <svg class="h-24 w-24 text-white drop-shadow-lg" viewBox="0 0 24 24" fill="currentColor"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/></svg>
            </div>
        </div>
    @endif

    {{-- ─── Isi & aksi ─────────────────────────────────── --}}
    <div class="px-4 pt-3">
        <p class="whitespace-pre-line text-sm leading-relaxed text-slate-800 dark:text-slate-100">
            {!! $post->konten_html !!}
        </p>

        @if ($post->topik->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach ($post->topik as $t)
                    <a href="{{ route('topik.show', $t) }}"
                       class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-brand-600 transition hover:bg-brand-50 dark:bg-slate-700 dark:text-brand-300 dark:hover:bg-slate-600">
                        #{{ $t->nama }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Tombol aksi --}}
        <div class="mt-3 flex items-center gap-1 border-t border-slate-100 pt-3 dark:border-slate-700">
            <button type="button" @click="suka()" class="group flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-semibold transition"
                    :class="disukai ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 hover:text-rose-500 dark:text-slate-400'">
                <svg class="h-5 w-5 transition-transform group-active:scale-125" :fill="disukai ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C6.1 3.75 4 5.765 4 8.25c0 7.22 8 11.25 8 11.25s8-4.03 8-11.25Z"/></svg>
                <span x-text="jumlah"></span>
            </button>

            <button type="button" @click="$refs.input.focus()" class="flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-500 transition hover:text-brand-600 dark:text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226A8.75 8.75 0 0 1 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                <span data-komentar-total>{{ $totalKomentar }}</span>
            </button>

            <button type="button" @click="bagikan()" class="flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-xs font-semibold text-slate-500 transition hover:text-brand-600 dark:text-slate-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
                Bagikan
            </button>

            @if (auth()->id() !== $post->user_id)
                <x-lapor-button tipe="post" :id="$post->id" />
            @endif

            @if ($totalKomentar > 3 && $ringkas)
                <a href="{{ route('posts.show', $post) }}" class="ml-auto text-[11px] font-semibold text-brand-600 hover:underline dark:text-brand-400">
                    Lihat semua {{ $totalKomentar }} komentar →
                </a>
            @endif
        </div>

        {{-- ─── Komentar ──────────────────────────────── --}}
        <div class="mt-2 space-y-2.5 pb-2" data-daftar-komentar>
            @foreach ($komentarUtama->take($ringkas ? 2 : 20) as $komentar)
                @include('components.komentar-item', ['komentar' => $komentar, 'postId' => $post->id, 'renderBalasan' => $ringkas])
            @endforeach
        </div>

        {{-- Penanda sedang membalas --}}
        <div data-balas-tag class="hidden items-center justify-between rounded-xl bg-brand-50 px-3 py-1.5 text-xs text-brand-700 dark:bg-brand-900 dark:text-brand-200"></div>

        {{-- Form komentar --}}
        <form class="flex items-center gap-2 border-t border-slate-100 py-3 dark:border-slate-700"
              x-ref="form" @submit.prevent="kirim()">
            <input type="hidden" name="parent_id" value="">
            <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-7 w-7 shrink-0 rounded-full object-cover">
            <input type="text" x-ref="input" x-model="teks" placeholder="Tulis komentar…"
                   class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm placeholder:text-slate-400 focus:ring-0 dark:text-slate-100">
            <button type="submit" class="text-xs font-bold text-brand-600 transition hover:text-brand-700 disabled:opacity-40 dark:text-brand-400" :disabled="!teks.trim()">
                Kirim
            </button>
        </form>
    </div>
</article>
