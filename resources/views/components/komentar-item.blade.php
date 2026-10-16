{{-- Satu komentar + balasannya. Dipakai via @include. --}}

<div class="flex gap-2" data-komentar="{{ $komentar->id }}">
    <img src="{{ $komentar->user->avatar_url }}" alt="" class="h-7 w-7 shrink-0 rounded-full object-cover">

    <div class="min-w-0 flex-1">
        <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900">
            <div class="flex items-center gap-1.5">
                <a href="{{ route('profil.show', $komentar->user) }}" class="text-xs font-semibold text-slate-800 hover:underline dark:text-slate-100">{{ $komentar->user->name }}</a>
                @if ($komentar->user->isPengelola())
                    <span class="badge bg-brand-50 px-1 py-0 text-[9px] text-brand-600 dark:bg-brand-900 dark:text-brand-300">pengelola</span>
                @endif
                <span class="text-[10px] text-slate-400 dark:text-slate-500">&bull; {{ $komentar->created_at->diffForHumans() }}</span>
            </div>
            <p class="mt-0.5 whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">{{ $komentar->konten }}</p>
        </div>

        <div class="mt-1 flex items-center gap-3 pl-1 text-[11px] text-slate-400 dark:text-slate-500">
            @if (! $komentar->parent_id)
                <button type="button" onclick="balasKomentar(event, {{ $komentar->id }}, '{{ addslashes($komentar->user->name) }}')" class="font-semibold hover:text-brand-600">Balas</button>
            @endif
            @if (auth()->id() === $komentar->user_id || auth()->user()->isPengelola())
                <button type="button" onclick="hapusKomentar({{ $postId }}, {{ $komentar->id }})" class="font-semibold hover:text-rose-500">Hapus</button>
            @elseif (auth()->id() !== $komentar->user_id)
                <x-lapor-button tipe="komentar" :id="$komentar->id" />
            @endif
        </div>

        @if (($renderBalasan ?? true) && $komentar->balasan->isNotEmpty())
            <div class="mt-2 space-y-2 border-l-2 border-slate-100 pl-3 dark:border-slate-700">
                @foreach ($komentar->balasan as $balasan)
                    <div class="flex gap-2" data-komentar="{{ $balasan->id }}">
                        <img src="{{ $balasan->user->avatar_url }}" alt="" class="h-6 w-6 shrink-0 rounded-full object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="rounded-2xl bg-slate-50 px-3 py-1.5 dark:bg-slate-900">
                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-100">{{ $balasan->user->name }}</span>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">&bull; {{ $balasan->created_at->diffForHumans() }}</span>
                                <p class="whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">{{ $balasan->konten }}</p>
                            </div>
                            @if (auth()->id() === $balasan->user_id || auth()->user()->isPengelola())
                                <button type="button" onclick="hapusKomentar({{ $postId }}, {{ $balasan->id }})" class="mt-1 pl-1 text-[11px] font-semibold text-slate-400 hover:text-rose-500">Hapus</button>
                            @elseif (auth()->id() !== $balasan->user_id)
                                <span class="ml-1 inline-flex align-middle"><x-lapor-button tipe="komentar" :id="$balasan->id" /></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
