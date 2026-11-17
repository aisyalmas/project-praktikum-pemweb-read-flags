let dash = null;
let libBuku = [];
let libGrup = { series: [], collections: [], genres: [] };
let rakFilter = 'tbr';
let pilihKoleksi = [];
let pilihKoleksiBaru = [];
let logBukuId = null;
const COVER_KOSONG = '../assets/images/cover-babel.jpg';
const LABEL_RAK = { tbr: 'up next', reading: 'reading', wishlist: 'wishlist' };

function tampilModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
}

function tanggalLokal(tanggal) {
    const bulan = String(tanggal.getMonth() + 1).padStart(2, '0');
    const hari = String(tanggal.getDate()).padStart(2, '0');
    return tanggal.getFullYear() + '-' + bulan + '-' + hari;
}

function formatTgl(teks) {
    if (!teks) return '-';
    return new Date(teks.substring(0, 10) + 'T00:00:00').toLocaleDateString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric'
    });
}

function tanggalObjek(teks) {
    return new Date(teks.substring(0, 10) + 'T00:00:00');
}

function hariBaca(b) {
    if (!b.started_at || !b.finished_at) return 0;
    return Math.round((tanggalObjek(b.finished_at) - tanggalObjek(b.started_at)) / 86400000) + 1;
}

function nilaiRating(r) {
    return r ? Number(r).toFixed(1) : '-';
}

async function kirimPOST(url, data) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const hasil = await res.json();

        if (!res.ok) {
            tampilToast(hasil.error || 'Failed to save.', false);
            return false;
        }
        return true;
    } catch (error) {
        console.error(error);
        tampilToast('Connection problem, try again.', false);
        return false;
    }
}

async function muatDashboard() {
    try {
        const [resDash, resBuku, resGrup] = await Promise.all([
            fetch('get_dashboard.php'),
            fetch('../library/get_books.php'),
            fetch('../library/get_groups.php')
        ]);

        dash = await resDash.json();
        libBuku = await resBuku.json();
        libGrup = await resGrup.json();

        gambarSemua();
    } catch (error) {
        console.error(error);
        tampilToast('Failed to load dashboard.', false);
    }
}

function gambarSemua() {
    gambarNowReading();
    gambarStreak();
    gambarTugas();
    gambarChallenge();
    gambarRak();
}

function gambarNowReading() {
    const n = dash.now_reading;
    const persen = n ? n.percent : 0;

    document.getElementById('n-cover').src = (n && n.cover_url) || COVER_KOSONG;
    document.getElementById('n-judul').textContent = n ? n.title : 'no book being read yet';
    document.getElementById('n-penulis').textContent = n ? (n.authors || '-') : 'books with status reading show here';
    document.getElementById('n-halaman').textContent = 'p. ' + (n ? n.current_page : 0);
    document.getElementById('n-total').textContent = 'of ' + (n ? n.page_count : 0);
    document.getElementById('n-persen').textContent = persen + '%';
    document.getElementById('n-bar').style.width = persen + '%';

    document.getElementById('n-hari').textContent = dash.hari_ini.halaman;
    document.getElementById('n-menit').textContent = dash.hari_ini.menit + ' m';
    document.getElementById('n-pace').textContent = dash.pace;

    document.getElementById('badge-baca').textContent = persen + '% read';
    document.getElementById('badge-sisa').textContent = (100 - persen) + '% left';

    let perkiraan = '-';
    if (n && dash.pace > 0) {
        const sisa = Math.max(0, n.page_count - n.current_page);
        perkiraan = Math.ceil(sisa / dash.pace) + ' days';
    }
    document.getElementById('n-finish').textContent = perkiraan;

    document.getElementById('btn-log-now').dataset.id = n ? n.id : '';
}

function gambarStreak() {
    document.getElementById('n-streak').textContent = dash.streak;

    const peta = {};
    dash.logs.forEach(function (l) { peta[l.tanggal] = l; });

    const titik = [];
    const sekarang = new Date();
    for (let i = 27; i >= 0; i--) {
        const d = new Date();
        d.setDate(sekarang.getDate() - i);

        const log = peta[tanggalLokal(d)];
        let kelas = 'dot dot-empty';

        if (i === 0) {
            kelas = 'dot-today d-inline-block';
        } else if (log && (log.menit >= 30 || log.halaman >= 20)) {
            kelas = 'dot dot-read';
        } else if (log) {
            kelas = 'dot dot-light';
        }

        titik.push({ kelas: kelas, tanggal: d });
    }

    let html = '';
    for (let r = 0; r < 4; r++) {
        const potongan = titik.slice(r * 7, r * 7 + 7);
        const awal = potongan[0].tanggal.getDate();
        const garis = r < 3 ? 'timeline-line solid' : 'timeline-line';

        html += `
            <div class="timeline-row${r === 3 ? ' mb-0' : ''}">
                <div class="${garis}"></div>
                <div class="d-flex align-items-center">
                    <span class="small me-3" style="min-width: 35px; font-size: 0.8rem;">${awal}</span>
                    <div class="dots-container flex-grow-1">
                        ${potongan.map(p => `<span class="${p.kelas}"></span>`).join('')}
                    </div>
                </div>
            </div>`;
    }
    document.getElementById('streak-baris').innerHTML = html;
}

function isiStreakModal() {
    const baris = dash.logs.slice(0, 30).map(function (l) {
        return `
            <div class="d-flex justify-content-between border-bottom py-2 fs-13">
                <span>${formatTgl(l.tanggal)}</span>
                <span>${l.halaman} p · ${l.menit} m</span>
            </div>`;
    }).join('');

    document.getElementById('isi-streak').innerHTML = `
        <p class="fs-13 mb-3">Current streak is <strong>${dash.streak} days</strong>. If you haven't read today, your streak is still counted from yesterday.</p>
        <h6 class="fw-bold mb-2">Last 30 days</h6>
        ${baris || pesanKosong('No reading records yet.')}`;
}

function gambarTugas() {
    const daftar = dash.tasks;
    const selesai = daftar.filter(t => t.is_done).length;

    document.getElementById('n-tugas-ringkas').textContent = selesai + ' of ' + daftar.length + ' done';

    const jumlahSegmen = Math.min(daftar.length, 10);
    let segmen = '';
    for (let i = 0; i < jumlahSegmen; i++) {
        const aktif = i < Math.round(selesai / daftar.length * jumlahSegmen);
        segmen += `<div class="seg-bar ${aktif ? 'active' : ''}"></div>`;
    }
    document.getElementById('tugas-segmen').innerHTML = segmen;

    document.getElementById('tugas-preview').innerHTML = daftar.slice(0, 2).map(t => `
        <div class="task-item d-flex align-items-center gap-2 py-2" role="button" data-task-centang="${t.id}">
            <span class="task-check ${t.is_done ? 'checked' : ''}"></span>
            <span class="flex-grow-1 fs-13 ${t.is_done ? 'text-decoration-line-through text-gray' : ''}">${aman(t.title)}</span>
        </div>`).join('') || pesanKosong('No tasks yet.');

    gambarDaftarTugas();
}

function gambarDaftarTugas() {
    document.getElementById('tugas-daftar').innerHTML = dash.tasks.map(t => `
        <div class="d-flex align-items-center gap-2 border-bottom py-2">
            <button type="button" class="btn btn-sm p-0" data-task-centang="${t.id}">
                <span class="task-check ${t.is_done ? 'checked' : ''}"></span>
            </button>
            <span class="flex-grow-1 fs-13 ${t.is_done ? 'text-decoration-line-through text-gray' : ''}">${aman(t.title)}</span>
            <button type="button" class="btn btn-sm btn-link text-danger p-0" data-task-hapus="${t.id}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>`).join('') || pesanKosong('No tasks yet. Add one above first.');
}

async function centangTugas(id) {
    const tugas = dash.tasks.find(t => Number(t.id) === Number(id));
    if (!tugas) return;

    tugas.is_done = !tugas.is_done;
    gambarTugas();

    const ok = await kirimPOST('get_dashboard.php', { action: 'task-centang', id: id });

    if (!ok) {
        tugas.is_done = !tugas.is_done;
        gambarTugas();
    }
}

function gambarChallenge() {
    const c = dash.challenge;

    if (!c) {
        document.getElementById('n-chal-nama').textContent = 'no challenge yet';
        document.getElementById('n-chal-selesai').textContent = '0';
        document.getElementById('n-chal-target').textContent = '/ 0';
        document.getElementById('n-chal-info').innerHTML = '<i class="bi bi-bullseye"></i> click edit to create';
        return;
    }

    document.getElementById('n-chal-nama').textContent = c.name;
    document.getElementById('n-chal-selesai').textContent = c.completed;
    document.getElementById('n-chal-target').textContent = '/ ' + c.target;
}

function gambarRak() {
    const tbr = libBuku.filter(b => b.status === 'tbr');
    const selesai = libBuku.filter(b => b.status === 'read').length;

    document.getElementById('n-rak-ringkas').textContent = tbr.length + ' in TBR • ' + selesai + ' finished';

    document.querySelectorAll('#chip-rak [data-filter]').forEach(function (chip) {
        const status = chip.dataset.filter;
        const jumlah = libBuku.filter(b => b.status === status).length;
        chip.textContent = LABEL_RAK[status] + ' (' + jumlah + ')';
        chip.classList.toggle('chip-active', status === rakFilter);
    });

    const daftar = libBuku.filter(b => b.status === rakFilter).slice(0, 6);
    document.getElementById('shelf-buku').innerHTML = daftar.length
        ? daftar.map(b => `
            <div class="book-item position-relative" role="button" data-buka-buku="${b.id}">
                <img src="${b.cover_url || COVER_KOSONG}" alt="${aman(b.title)}" class="cover-book-shelves">
            </div>`).join('')
        : '<p class="text-gray fs-13 mb-0">no books here yet.</p>';

    const kandidat = libBuku.filter(b => b.status === 'wishlist' || b.status === 'tbr');
    const kartu = document.getElementById('curated-card');

    if (kandidat.length === 0) {
        kartu.classList.add('d-none');
        return;
    }

    kartu.classList.remove('d-none');
    const hari = Math.floor(Date.now() / 86400000);
    const c = kandidat[hari % kandidat.length];

    document.getElementById('c-cover').src = c.cover_url || COVER_KOSONG;
    document.getElementById('c-judul').textContent = c.title;
    document.getElementById('c-penulis').textContent = (c.authors || '-') + ' • ' + (c.page_count || 0) + ' pages';
    document.getElementById('btn-curated').dataset.id = c.id;
}

function bukaModalBuku(id, panel = 'overview') {
    const buku = libBuku.find(b => Number(b.id) === Number(id));
    if (!buku) return;

    document.getElementById('form-buku').dataset.id = buku.id;

    document.getElementById('m-cover').src = buku.cover_url || COVER_KOSONG;
    document.getElementById('modal-buku-judul').textContent = buku.title;
    document.getElementById('modal-buku-penulis').textContent = buku.authors || '-';
    document.getElementById('m-halaman').textContent = buku.page_count || 0;

    isiOverview(buku);

    document.getElementById('f-deskripsi').value = buku.description || '';
    isiDropdownGenre(buku.genre_id);
    isiDropdownSeri(buku);

    pilihKoleksi = (buku.collection_ids || []).map(Number);
    pilihKoleksiBaru = [];
    document.getElementById('f-koleksi-baru').value = '';
    gambarPillKoleksi();

    document.getElementById('f-status').value = buku.status;
    document.getElementById('f-rating').value = buku.rating || 0;
    document.getElementById('f-total').value = buku.page_count || 0;
    document.getElementById('f-halaman-baca').value = buku.current_page || 0;
    document.getElementById('f-mulai').value = buku.started_at || '';
    document.getElementById('f-selesai').value = buku.finished_at || '';

    pilihPanel(panel);
    tampilModal('modal-buku');
}

function isiOverview(buku) {
    document.getElementById('o-status').textContent = buku.status;
    document.getElementById('o-rating').textContent = nilaiRating(buku.rating);
    document.getElementById('o-halaman').textContent = (buku.current_page || 0) + '/' + (buku.page_count || 0);

    document.getElementById('o-deskripsi').textContent = buku.description || 'No description yet.';
    document.getElementById('o-halaman-total').textContent = buku.page_count || '-';
    document.getElementById('o-penerbit').textContent = buku.publisher || '-';
    document.getElementById('o-tahun').textContent = buku.published_year || '-';
    document.getElementById('o-isbn').textContent = buku.isbn || '-';

    document.getElementById('o-genre').textContent = buku.genre_name || 'Other';
    document.getElementById('o-tanggal').textContent = tulisTanggalBaca(buku);
    document.getElementById('o-ditambahkan').textContent = formatTgl(buku.added_at);
}

function tulisTanggalBaca(buku) {
    if (!buku.started_at) return 'Not started yet';

    const mulai = formatTgl(buku.started_at);
    if (!buku.finished_at) return 'Started ' + mulai + ' · reading';

    return mulai + ' – ' + formatTgl(buku.finished_at) + ' · ' + hariBaca(buku) + ' days';
}

function isiDropdownGenre(genreId) {
    const pilihan = document.getElementById('f-genre');
    pilihan.innerHTML = libGrup.genres.map(g =>
        `<option value="${g.id}">${aman(g.name)}</option>`
    ).join('');
    pilihan.value = genreId;
}

function isiDropdownSeri(buku) {
    const pilihan = document.getElementById('f-seri');

    let opsi = '<option value="">no series</option>';
    opsi += libGrup.series.map(s => `<option value="${s.id}">${aman(s.name)}</option>`).join('');
    opsi += '<option value="__baru__">+ add new series</option>';

    pilihan.innerHTML = opsi;
    pilihan.value = buku.series_id || '';
    document.getElementById('f-seri-baru').value = '';
    document.getElementById('f-urutan').value = buku.series_order || '';
    cekSeriBaru();
}

function cekSeriBaru() {
    const baru = document.getElementById('f-seri').value === '__baru__';
    document.getElementById('blok-seri-baru').classList.toggle('d-none', !baru);
}

function gambarPillKoleksi() {
    const wadah = document.getElementById('f-koleksi-pills');

    let html = libGrup.collections.map(k => {
        const aktif = pilihKoleksi.includes(Number(k.id));
        return `<button type="button" class="btn btn-sm rounded-pill pill-koleksi ${aktif ? 'btn-dark' : 'btn-outline-dark'}" data-id="${k.id}">${aman(k.name)}</button>`;
    }).join('');

    html += pilihKoleksiBaru.map((nama, i) =>
        `<button type="button" class="btn btn-sm rounded-pill btn-dark pill-koleksi-baru" data-index="${i}">${aman(nama)} ✕</button>`
    ).join('');

    wadah.innerHTML = html || '<p class="fs-13 text-gray mb-0">no collections yet.</p>';
}

function pilihPanel(nama) {
    document.querySelectorAll('#modal-buku .modal-tab').forEach(function (tab) {
        tab.classList.toggle('active', tab.dataset.panel === nama);
    });
    document.querySelectorAll('#modal-buku .modal-panel').forEach(function (panel) {
        panel.classList.toggle('d-none', panel.id !== 'panel-' + nama);
    });
}

async function simpanModalBuku(e) {
    e.preventDefault();

    const seriDipilih = document.getElementById('f-seri').value;

    const dataBaru = {
        book_id: this.dataset.id,
        status: document.getElementById('f-status').value,
        rating: Number(document.getElementById('f-rating').value),
        page_count: Number(document.getElementById('f-total').value),
        current_page: Number(document.getElementById('f-halaman-baca').value),
        started_at: document.getElementById('f-mulai').value,
        finished_at: document.getElementById('f-selesai').value,
        description: document.getElementById('f-deskripsi').value,
        genre_id: Number(document.getElementById('f-genre').value),
        series_id: (seriDipilih === '__baru__' || seriDipilih === '') ? null : Number(seriDipilih),
        series_baru: seriDipilih === '__baru__' ? document.getElementById('f-seri-baru').value.trim() : '',
        series_order: document.getElementById('f-urutan').value,
        collection_ids: pilihKoleksi,
        collection_baru: pilihKoleksiBaru
    };

    const ok = await kirimPOST('../library/update_book.php', dataBaru);
    if (ok) {
        bootstrap.Modal.getInstance(document.getElementById('modal-buku')).hide();
        tampilToast('Book changes saved.');
        await muatDashboard();
    }
}

function bukaModalLog(id) {
    const buku = libBuku.find(b => Number(b.id) === Number(id));
    if (!buku) return;

    logBukuId = buku.id;

    document.getElementById('log-cover').src = buku.cover_url || COVER_KOSONG;
    document.getElementById('log-tanggal').textContent = 'today · ' + formatTgl(tanggalLokal(new Date()));
    document.getElementById('log-judul').textContent = buku.title;
    document.getElementById('log-penulis').textContent = buku.authors || '-';
    document.getElementById('log-sekarang').textContent = buku.current_page + ' of ' + buku.page_count;

    document.getElementById('log-halaman').value = 0;
    document.getElementById('log-menit').value = 0;
    updateLogPreview();

    tampilModal('modal-log');
}

function updateLogPreview() {
    const buku = libBuku.find(b => Number(b.id) === Number(logBukuId));
    if (!buku) return;

    const tambah = Number(document.getElementById('log-halaman').value || 0);
    const total = Number(buku.page_count || 0);

    let setelah = Number(buku.current_page || 0) + tambah;
    if (total > 0) {
        setelah = Math.min(setelah, total);
    }

    document.getElementById('log-setelah').textContent = setelah + ' of ' + total;
}

document.getElementById('btn-log-now').addEventListener('click', function () {
    if (this.dataset.id) bukaModalLog(this.dataset.id);
});

document.getElementById('btn-streak').addEventListener('click', function () {
    isiStreakModal();
    tampilModal('modal-streak');
});

document.getElementById('btn-tugas-semua').addEventListener('click', function () {
    gambarDaftarTugas();
    tampilModal('modal-tugas');
});

document.getElementById('form-tugas').addEventListener('submit', async function (e) {
    e.preventDefault();
    const judul = document.getElementById('tugas-input').value.trim();
    if (judul === '') return;

    const ok = await kirimPOST('get_dashboard.php', { action: 'task-tambah', title: judul });
    if (ok) {
        document.getElementById('tugas-input').value = '';
        await muatDashboard();
    }
});

document.getElementById('tugas-daftar').addEventListener('click', async function (e) {
    const centang = e.target.closest('[data-task-centang]');
    if (centang) {
        await centangTugas(centang.dataset.taskCentang);
        return;
    }

    const hapus = e.target.closest('[data-task-hapus]');
    if (hapus) {
        if (!confirm('Delete this task?')) return;
        const ok = await kirimPOST('get_dashboard.php', { action: 'task-hapus', id: hapus.dataset.taskHapus });
        if (ok) {
            tampilToast('Task deleted.');
            await muatDashboard();
        }
    }
});

document.getElementById('tugas-preview').addEventListener('click', function (e) {
    const baris = e.target.closest('[data-task-centang]');
    if (baris) centangTugas(baris.dataset.taskCentang);
});

document.getElementById('btn-challenge').addEventListener('click', function () {
    const c = dash.challenge;
    document.getElementById('chal-nama').value = c ? c.name : '';
    document.getElementById('chal-target').value = c ? c.target : '';
    tampilModal('modal-challenge');
});

document.getElementById('form-challenge').addEventListener('submit', async function (e) {
    e.preventDefault();

    const ok = await kirimPOST('get_dashboard.php', {
        action: 'challenge-simpan',
        name: document.getElementById('chal-nama').value.trim(),
        target: Number(document.getElementById('chal-target').value)
    });

    if (ok) {
        bootstrap.Modal.getInstance(document.getElementById('modal-challenge')).hide();
        tampilToast('Challenge saved.');
        await muatDashboard();
    }
});

document.getElementById('chip-rak').addEventListener('click', function (e) {
    const chip = e.target.closest('[data-filter]');
    if (!chip) return;

    rakFilter = chip.dataset.filter;
    gambarRak();
});

document.getElementById('btn-pick').addEventListener('click', function () {
    const tbr = libBuku.filter(b => b.status === 'tbr');
    if (tbr.length === 0) {
        tampilToast('No TBR books yet.', false);
        return;
    }
    const acak = tbr[Math.floor(Math.random() * tbr.length)];
    bukaModalBuku(acak.id, 'overview');
});

document.getElementById('shelf-buku').addEventListener('click', function (e) {
    const cover = e.target.closest('[data-buka-buku]');
    if (cover) bukaModalBuku(cover.dataset.bukaBuku, 'overview');
});

document.getElementById('btn-curated').addEventListener('click', function () {
    if (this.dataset.id) bukaModalBuku(this.dataset.id, 'overview');
});

document.getElementById('form-buku').addEventListener('submit', simpanModalBuku);

document.querySelectorAll('#modal-buku .modal-tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
        pilihPanel(this.dataset.panel);
    });
});

document.getElementById('f-seri').addEventListener('change', cekSeriBaru);

document.getElementById('f-koleksi-pills').addEventListener('click', function (e) {
    const tombol = e.target.closest('button');
    if (!tombol) return;

    if (tombol.classList.contains('pill-koleksi')) {
        const id = Number(tombol.dataset.id);
        if (pilihKoleksi.includes(id)) {
            pilihKoleksi = pilihKoleksi.filter(x => x !== id);
        } else {
            pilihKoleksi.push(id);
        }
    } else if (tombol.classList.contains('pill-koleksi-baru')) {
        pilihKoleksiBaru.splice(Number(tombol.dataset.index), 1);
    }

    gambarPillKoleksi();
});

function buatKoleksiBaru() {
    const input = document.getElementById('f-koleksi-baru');
    const nama = input.value.trim();
    if (nama === '') return;

    const ada = libGrup.collections.find(k => k.name.toLowerCase() === nama.toLowerCase());
    if (ada) {
        if (!pilihKoleksi.includes(Number(ada.id))) {
            pilihKoleksi.push(Number(ada.id));
        }
    } else {
        pilihKoleksiBaru.push(nama);
    }

    input.value = '';
    gambarPillKoleksi();
}

document.getElementById('btn-buat-koleksi').addEventListener('click', buatKoleksiBaru);

document.getElementById('f-koleksi-baru').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        buatKoleksiBaru();
    }
});

document.querySelectorAll('#modal-buku [data-step]').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        let nilai = Math.max(0, Number(input.value || 0) + Number(this.dataset.step));

        if (this.dataset.target === 'f-halaman-baca') {
            const total = Number(document.getElementById('f-total').value);
            if (total > 0) {
                nilai = Math.min(nilai, total);
            }
        }

        input.value = nilai;
    });
});

document.getElementById('btn-hapus-buku').addEventListener('click', async function () {
    const id = document.getElementById('form-buku').dataset.id;
    if (!confirm('Remove this book from your shelf? The book data will stay in the database.')) return;

    const ok = await kirimPOST('../library/update_book.php', { action: 'hapus', book_id: id });
    if (ok) {
        bootstrap.Modal.getInstance(document.getElementById('modal-buku')).hide();
        tampilToast('Book removed from shelf.');
        await muatDashboard();
    }
});

document.querySelectorAll('#modal-log [data-step]').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        input.value = Math.max(0, Number(input.value || 0) + Number(this.dataset.step));
        updateLogPreview();
    });
});

document.getElementById('log-halaman').addEventListener('input', updateLogPreview);

document.getElementById('form-log').addEventListener('submit', async function (e) {
    e.preventDefault();

    const halaman = Number(document.getElementById('log-halaman').value || 0);
    const menit = Number(document.getElementById('log-menit').value || 0);

    if (halaman <= 0 && menit <= 0) {
        tampilToast('Enter pages or minutes first.', false);
        return;
    }

    const tombol = this.querySelector('button[type="submit"]');
    setLoading(tombol, true);

    const ok = await kirimPOST('../library/action_book.php', {
        action: 'log-harian',
        book_id: logBukuId,
        pages: halaman,
        minutes: menit
    });

    setLoading(tombol, false);

    if (ok) {
        bootstrap.Modal.getInstance(document.getElementById('modal-log')).hide();
        tampilToast(halaman + ' pages and ' + menit + ' minutes saved.');
        await muatDashboard();
    }
});

function setTime() {
    const d = new Date();
    const tanggal = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    const jam = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    document.getElementById('tanggal-hari').textContent = (tanggal + ' • ' + jam).toLowerCase();
}

setTime();
setInterval(setTime, 60000);

document.getElementById('judul-chal').textContent = new Date().getFullYear() + ' challenge';

muatDashboard();