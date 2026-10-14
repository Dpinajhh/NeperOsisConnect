<x-layouts.app :title="'Postingan'">
    <div class="mx-auto max-w-[600px]">
        <a href="{{ url()->previous() }}" class="mb-3 inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-brand-600 dark:text-slate-400">
            ← Kembali
        </a>

        <x-feed.post-card :post="$post" :ringkas="false" />
    </div>

    @push('skrip')
        @include('components.feed.skrip')
    @endpush
</x-layouts.app>
