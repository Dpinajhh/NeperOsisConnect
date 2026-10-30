{{-- Skrip interaksi feed: suka, komentar, balas, hapus, bagikan. Dipush oleh halaman feed/topik/detail. --}}

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const headerJson = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    };

    function postInteraksi(postId, disukaiAwal, jumlahAwal) {
        return {
            disukai: disukaiAwal,
            jumlah: jumlahAwal,
            teks: '',
            ledakan: false,

            async suka() {
                try {
                    const res = await fetch(`/posts/${postId}/suka`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                    });
                    if (!res.ok) return;

                    const data = await res.json();
                    const baruMenyukai = data.disukai && !this.disukai;

                    this.disukai = data.disukai;
                    this.jumlah = data.jumlah_suka;

                    if (baruMenyukai) {
                        this.ledakan = true;
                        setTimeout(() => (this.ledakan = false), 900);
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            async kirim() {
                if (!this.teks.trim()) return;

                const kartu = this.$refs.form.closest('[data-post]');
                const parentId = kartu.querySelector('input[name="parent_id"]').value;

                try {
                    const res = await fetch(`/posts/${postId}/komentar`, {
                        method: 'POST',
                        headers: headerJson,
                        body: JSON.stringify({ konten: this.teks.trim(), parent_id: parentId || null }),
                    });
                    if (!res.ok) {
                        const err = await res.json().catch(() => null);
                        alert(err?.message?.[0] || 'Komentar gagal dikirim.');
                        return;
                    }

                    const k = await res.json();
                    this.teks = '';
                    kartu.querySelector('input[name="parent_id"]').value = '';
                    sembunyikanTagBalas(kartu);

                    // Sisipkan komentar baru di urutan teratas daftar komentar
                    const daftar = kartu.querySelector('[data-daftar-komentar]');
                    daftar?.insertAdjacentHTML('afterbegin', this.kartuKomentar(k));
                    this.tambahCounter(kartu, k.parent_id ? 1 : 1, k.parent_id);
                } catch (e) {
                    alert('Komentar gagal dikirim. Periksa koneksi kamu.');
                    console.error(e);
                }
            },

            kartuKomentar(k) {
                const label = k.parent_id ? '' : '';
                return `
                    <div class="flex gap-2" data-komentar="${k.id}">
                        <img src="${k.avatar}" alt="" class="h-7 w-7 shrink-0 rounded-full object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-900">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-100">${k.nama}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">&bull; baru saja</span>
                                </div>
                                <p class="mt-0.5 whitespace-pre-line text-sm text-slate-700 dark:text-slate-200">${this.aman(k.konten)}</p>
                            </div>
                        </div>
                    </div>`;
            },

            aman(teks) {
                const div = document.createElement('div');
                div.textContent = teks;
                return div.innerHTML;
            },

            tambahCounter(kartu, jumlah, parentId) {
                if (parentId) {
                    // balasan disisipkan di bawah komentar induknya
                }
                const el = kartu.querySelector('[data-komentar-total]');
                if (el) el.textContent = (parseInt(el.textContent || '0', 10) + jumlah).toString();
            },

            bagikan() {
                const url = `${window.location.origin}/posts/${postId}`;
                navigator.clipboard.writeText(url).then(() => {
                    const tombol = this.$el.querySelector('button:last-child');
                    const asli = tombol ? tombol.textContent : '';
                    if (tombol) tombol.textContent = 'Tautan disalin!';
                    setTimeout(() => { if (tombol) tombol.textContent = asli; }, 1500);
                });
            },
        };
    }

    // ─── Balas & hapus komentar (fungsi global) ──────────
    function balasKomentar(evt, komentarId, nama) {
        const kartu = evt.target.closest('[data-post]');
        if (!kartu) return;

        kartu.querySelector('input[name="parent_id"]').value = komentarId;

        let tag = kartu.querySelector('[data-balas-tag]');
        tag.classList.remove('hidden');
        tag.classList.add('flex');
        tag.innerHTML = `
            <span>Membalas <b>${nama}</b></span>
            <button type="button" class="font-bold hover:text-brand-800" onclick="batalBalas(this)" aria-label="Batal membalas">
                <svg class="inline h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>`;

        kartu.querySelector('input[x-ref="input"]')?.focus();
    }

    function batalBalas(tombol) {
        const kartu = tombol.closest('[data-post]');
        kartu.querySelector('input[name="parent_id"]').value = '';
        const tag = kartu.querySelector('[data-balas-tag]');
        tag.classList.add('hidden');
        tag.classList.remove('flex');
        tag.innerHTML = '';
    }

    function sembunyikanTagBalas(kartu) {
        const tag = kartu?.querySelector('[data-balas-tag]');
        if (tag) {
            tag.classList.add('hidden');
            tag.classList.remove('flex');
            tag.innerHTML = '';
        }
    }

    async function hapusKomentar(postId, komentarId) {
        if (!confirm('Hapus komentar ini?')) return;

        try {
            const res = await fetch(`/posts/${postId}/komentar/${komentarId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
            });
            if (!res.ok) return;

            const data = await res.json();
            const kartu = document.querySelector(`[data-post="${postId}"]`);
            const el = kartu?.querySelector(`[data-komentar="${komentarId}"]`);

            // Hitung komentar yang hilang (utama + balasan yang ikut terhapus)
            const hilang = el ? el.querySelectorAll('[data-komentar]').length + 1 : data.jumlah_hilang || 1;
            el?.remove();

            const total = kartu?.querySelector('[data-komentar-total]');
            if (total) total.textContent = Math.max(0, parseInt(total.textContent || '0', 10) - hilang).toString();
        } catch (e) {
            console.error(e);
        }
    }
</script>
