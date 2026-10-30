<x-layouts.app :title="'Profil ' . $user->name">
    <div class="mx-auto max-w-2xl space-y-4">
        {{-- Kartu identitas --}}
        <div class="card p-6">
            <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-start">
                <img src="{{ $user->avatar_url }}" alt=""
                     class="h-20 w-20 rounded-2xl object-cover shadow-sm">

                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-xl font-bold dark:text-white">{{ $user->name }}</h1>

                    <div class="mt-2 flex flex-wrap justify-center gap-2 sm:justify-start">
                        <span class="badge {{ $user->isAdmin() ? 'bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-200' : ($user->isGuru() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300') }}">
                            {{ ucfirst($user->role) }}
                        </span>

                        @if ($user->kelas)
                            <span class="badge bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                {{ $user->kelas->nama }}
                                @if ($user->kelas->jurusan)
                                    &bull; {{ $user->kelas->jurusan->kode }}
                                @endif
                            </span>
                        @endif
                    </div>

                    <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                        {{ $user->bio ?: 'Belum ada bio.' }}
                    </p>

                    @auth
                        @if (auth()->id() === $user->id)
                            <a href="{{ route('profil.edit') }}" class="btn-ghost mt-4 inline-flex items-center gap-1.5 text-xs">
                                <x-ikon name="pencil-square" class="h-3.5 w-3.5" />
                                Edit Profil
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- Aktivitas --}}
        <div class="card p-6">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Aktivitas</h2>
            <p class="mt-3 grid grid-cols-3 gap-3 text-center">
                <span class="rounded-xl bg-slate-50 py-3 dark:bg-slate-900">
                    <span class="block text-lg font-bold text-brand-600 dark:text-brand-400">{{ $jumlahPost }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Post</span>
                </span>
                <span class="rounded-xl bg-slate-50 py-3 dark:bg-slate-900">
                    <span class="block text-lg font-bold text-brand-600 dark:text-brand-400">{{ $jumlahKomentar }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Komentar</span>
                </span>
                @if (auth()->id() === $user->id || auth()->user()->isPengelola())
                    {{-- Waktu login hanya untuk diri sendiri & pengelola, bukan publik --}}
                    <span class="rounded-xl bg-slate-50 py-3 dark:bg-slate-900">
                        <span class="block text-lg font-bold text-brand-600 dark:text-brand-400">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">Login terakhir</span>
                    </span>
                @else
                    <span class="rounded-xl bg-slate-50 py-3 dark:bg-slate-900">
                        <span class="block text-lg font-bold text-brand-600 dark:text-brand-400">{{ $user->created_at?->translatedFormat('M Y') ?? '—' }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">Bergabung</span>
                    </span>
                @endif
            </p>
        </div>
    </div>
</x-layouts.app>
