<x-layouts.app :title="'Kelola Pengguna'">
    <div class="mb-5">
        <h1 class="text-2xl font-bold dark:text-white">Kelola Pengguna</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Atur peran, status aktif, dan blokir akun siswa/guru.</p>
    </div>

    {{-- Ringkasan --}}
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach ([
            ['Semua', $ringkas['semua'], 'user-group', 'semua'],
            ['Siswa', $ringkas['siswa'], 'academic-cap', 'siswa'],
            ['Guru', $ringkas['guru'], 'user', 'guru'],
            ['Admin', $ringkas['admin'], 'star', 'admin'],
            ['Diblokir', $ringkas['diblokir'], 'no-symbol', 'diblokir'],
        ] as [$label, $n, $ikon, $f])
            <a href="{{ route('admin.pengguna.kelola', $f === 'semua' ? [] : ['filter' => $f]) }}"
               class="card p-4 text-center transition hover:shadow-md {{ $filter === $f ? 'ring-2 ring-brand-500' : '' }}">
                <x-ikon :name="$ikon" class="mx-auto h-6 w-6 text-brand-500 dark:text-brand-400" />
                <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $n }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    {{-- Pencarian + filter --}}
    <form method="GET" action="{{ route('admin.pengguna.kelola') }}" class="card mb-4 p-3">
        <div class="flex flex-wrap gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama, email, atau NIS..."
                   class="input flex-1 min-w-[200px]">
            <select name="filter" class="input w-auto">
                <option value="">— Semua peran —</option>
                @foreach (['siswa', 'guru', 'admin', 'aktif', 'nonaktif', 'diblokir'] as $f)
                    <option value="{{ $f }}" @selected($filter === $f)>{{ ucfirst($f) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Terapkan</button>
        </div>
    </form>

    @if ($pengguna->isEmpty())
        <div class="card p-12 text-center">
            <x-ikon name="user" class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" />
            <h2 class="mt-4 text-lg font-bold dark:text-white">Tidak ada pengguna</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Coba ubah kata kunci atau filter.</p>
        </div>
    @else
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Peran</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($pengguna as $u)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-750">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $u->avatar_url }}" alt="" class="h-9 w-9 rounded-full object-cover">
                                    <div class="min-w-0">
                                        <p class="font-semibold dark:text-white">{{ $u->name }}</p>
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $u->email }}@if($u->nis) &bull; NIS {{ $u->nis }}@endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if (auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('admin.pengguna.peran', $u) }}" class="flex">
                                        @csrf
                                        <select name="role" onchange="this.form.submit()"
                                                class="input w-28 py-1.5 text-xs {{ $u->role === 'admin' ? 'ring-1 ring-amber-300' : '' }}">
                                            @foreach (['siswa', 'guru', 'admin'] as $r)
                                                <option value="{{ $r }}" @selected($u->role === $r)>{{ ucfirst($r) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @else
                                    <span class="badge
                                        {{ $u->role === 'admin' ? 'bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-200' : ($u->role === 'guru' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300') }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ $u->kelas?->nama ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($u->is_banned)
                                    <span class="badge inline-flex items-center gap-1 bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300">
                                        <x-ikon name="no-symbol" class="h-3 w-3" />
                                        Diblokir
                                    </span>
                                @elseif ($u->is_active)
                                    <span class="badge inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300">
                                        <x-ikon name="check-circle" class="h-3 w-3" />
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge inline-flex items-center gap-1 bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                        <x-ikon name="pause-circle" class="h-3 w-3" />
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1.5">
                                    <form method="POST" action="{{ route('admin.pengguna.status', $u) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                                            <x-ikon :name="$u->is_active ? 'pause-circle' : 'play-circle'" class="h-3.5 w-3.5" />
                                            {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pengguna.blokir', $u) }}"
                                          onsubmit="return confirm('{{ $u->is_banned ? 'Lepas blokir' : 'Blokir' }} {{ $u->name }}?')">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $u->is_banned ? 'text-emerald-600 hover:bg-emerald-50' : 'text-rose-600 hover:bg-rose-50' }} dark:hover:bg-slate-700">
                                            <x-ikon :name="$u->is_banned ? 'lock-open' : 'no-symbol'" class="h-3.5 w-3.5" />
                                            {{ $u->is_banned ? 'Lepas Blokir' : 'Blokir' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pt-4">{{ $pengguna->links() }}</div>
    @endif
</x-layouts.app>
