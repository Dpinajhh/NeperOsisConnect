{{-- Kotak buat post — gaya feed sosial --}}
@props(['kelas'])

<div class="card p-4" x-data="composer()">
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" x-ref="form">
        @csrf

        <div class="flex gap-3">
            <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-10 w-10 shrink-0 rounded-full object-cover">

            <div class="min-w-0 flex-1">
                <textarea name="konten" x-model="teks" x-ref="teks" rows="2"
                          maxlength="2000" placeholder="Bagikan ceritamu, {{ auth()->user()->name }}…"
                          class="w-full resize-none border-0 bg-transparent p-0 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-0 dark:text-slate-100"></textarea>

                {{-- Preview gambar --}}
                <template x-if="preview">
                    <div class="relative mt-2 overflow-hidden rounded-xl">
                        <img :src="preview" alt="" class="max-h-72 w-full object-cover">
                        <button type="button" @click="bersih()" class="absolute right-2 top-2 rounded-full bg-black/60 p-1.5 text-white hover:bg-black/80">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>

                {{-- Opsi --}}
                <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-700">
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                        Foto
                        <input type="file" name="gambar" accept="image/*" class="hidden"
                               x-ref="gambar" @change="pilih($event)">
                    </label>

                    <select name="visibilitas" class="rounded-lg border-0 bg-slate-100 py-1.5 pl-2.5 pr-8 text-xs font-semibold text-slate-600 focus:ring-brand-500 dark:bg-slate-700 dark:text-slate-300">
                        <option value="sekolah">🌐 Seluruh sekolah</option>
                        <option value="jurusan" @disabled(! auth()->user()->kelas?->jurusan_id)>🏫 Jurusan</option>
                        <option value="kelas" @disabled(! auth()->user()->kelas_id)>👥 Kelas</option>
                    </select>

                    <span class="ml-auto text-[11px] font-medium text-slate-400" x-text="sisa()"></span>

                    <button type="submit" class="btn-primary px-4 py-1.5 text-xs" :disabled="kosong">
                        Bagikan
                    </button>
                </div>

                <p class="mt-2 text-[11px] text-slate-400 dark:text-slate-500">
                    Gunakan <span class="font-semibold text-brand-500">#hashtag</span> agar postinganmu masuk hitungan Trending Topik 🔥
                </p>
            </div>
        </div>
    </form>
</div>

@push('skrip')
<script>
    function composer() {
        return {
            teks: '', preview: null,
            get kosong() { return this.teks.trim().length === 0 && !this.preview; },
            sisa() { return (2000 - this.teks.length) + ' karakter'; },
            pilih(e) {
                const file = e.target.files[0];
                if (file) this.preview = URL.createObjectURL(file);
            },
            bersih() {
                this.preview = null;
                this.$refs.gambar.value = '';
            }
        }
    }
</script>
@endpush
