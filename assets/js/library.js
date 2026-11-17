let semuaBuku = [];
let dataGrup = { series: [], authors: [], collections: [], genres: [] };
let tabAktif = 'books';
let pillAktif = null;
let kataCari = '';
let urutan = 'added';
let tampilanGrid = true;
let pilihKoleksi = [];
let pilihKoleksiBaru = [];
let detailAktif = null;
let dataDetail = null;

const COVER_KOSONG = '../assets/images/cover-babel.jpg';

const WARNA = {
    reading: 'success',
    read: 'primary',
    tbr: 'warning',
    dnf: 'danger',
    wishlist: 'info'
};

const LABEL_URUTAN = {
    added: 'added',
    rating: 'rating',
    az: 'a-z',
    length: 'length'
};

const PLACEHOLDER_SEARCH = {
    books: 'Search title, author,...',
    wishlist: 'search wishlist...',
    series: 'search series...',
    authors: 'search author...',
    collections: 'search collection...'
};

const container = document.getElementById('book-container');
const pillBar = document.getElementById('pill-bar');
const kontrol = document.getElementById('kontrol');
const searchInput = document.getElementById('search-input');
const searchForm = document.getElementById('search-form');
const counter = document.getElementById('counter');
const labelUrut = document.getElementById('label-urut');
const btnView = document.getElementById('btn-view');
const dropdownUrut = document.getElementById('dropdown-urut');
const btnBaru = document.getElementById('btn-baru');

async function kirimJSON(url, data) {
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

async function ambilData() {
    try {
        const [resBuku, resGrup] = await Promise.all([
            fetch('get_books.php'),
            fetch('get_groups.php')
        ]);

        const dataBuku = await resBuku.json();
        const dataGrupBaru = await resGrup.json();

        if (!resBuku.ok || !resGrup.ok) {
            container.innerHTML = kotakKosong(dataBuku.error || dataGrupBaru.error || 'Failed to load data.');
            return;
        }

        semuaBuku = dataBuku;
        dataGrup = dataGrupBaru;
        tampilkan();
    } catch (error) {
        container.innerHTML = kotakKosong('Failed to load data.');
        console.error(error);
    }
}

function tampilModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
}

function sembunyiModal(id) {
    bootstrap.Modal.getInstance(document.getElementById(id)).hide();
}

function bukuYangCocok() {
    const hasil = semuaBuku.filter(function (buku) {
        if (tabAktif === 'wishlist' && buku.status !== 'wishlist') {
            return false;
        }

        if (tabAktif === 'books' && pillAktif !== null && buku.status !== pillAktif) {
            return false;
        }

        if (kataCari !== '') {
            const teks = (
                buku.title + ' ' +
                (buku.authors || '') + ' ' +
                (buku.series_name || '') + ' ' +
                buku.collections.join(' ')
            ).toLowerCase();

            if (!teks.includes(kataCari)) {
                return false;
            }
        }

        return true;
    });

    return urutkan(hasil);
}

function urutkan(daftar) {
    const salinan = daftar.slice();

    if (urutan === 'added') {
        salinan.sort((a, b) => b.added_at.localeCompare(a.added_at));
    }
    if (urutan === 'rating') {
        salinan.sort((a, b) => (b.rating || 0) - (a.rating || 0));
    }
    if (urutan === 'az') {
        salinan.sort((a, b) => a.title.localeCompare(b.title));
    }
    if (urutan === 'length') {
        salinan.sort((a, b) => (b.page_count || 0) - (a.page_count || 0));
    }

    return salinan;
}

function updatePlaceholder() {
    searchInput.placeholder = PLACEHOLDER_SEARCH[tabAktif] || 'search...';
}

function tampilkan() {
    const adalahKartu = (tabAktif === 'books' || tabAktif === 'wishlist');
    const adalahBaru = (tabAktif === 'series' || tabAktif === 'collections');

    pillBar.classList.toggle('d-none', tabAktif !== 'books');
    kontrol.classList.toggle('d-none', tabAktif === 'authors');
    dropdownUrut.classList.toggle('d-none', !adalahKartu);
    btnView.classList.toggle('d-none', !adalahKartu);
    btnBaru.classList.toggle('d-none', !adalahBaru);

    updatePlaceholder();

    if (adalahKartu) {
        const daftar = bukuYangCocok();
        tampilkanKartu(daftar);
        updateCounter(daftar.length);
    } else {
        const daftar = grupYangCocok();
        tampilkanGrup(daftar);
        updateCounter(daftar.length);
    }

    updateBadgeTab();
    updateCountPill();
}

function grupYangCocok() {
    const daftar = dataGrup[tabAktif];

    if (kataCari === '') {
        return daftar;
    }

    return daftar.filter(function (item) {
        const teks = [item.name, item.description, item.bio, item.nationality]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();
        return teks.includes(kataCari);
    });
}

function tampilkanGrup(daftar) {
    if (daftar.length === 0) {
        container.innerHTML = kotakKosong(
            kataCari !== '' ? 'No results match your search.' : 'Nothing in this category yet.'
        );
        return;
    }

    if (tabAktif === 'series') {
        container.innerHTML =
            '<div class="row row-cols-1 row-cols-md-2 g-4">' +
            daftar.map(kartuSeries).join('') +
            '</div>';
    }

    if (tabAktif === 'authors') {
        container.innerHTML =
            '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">' +
            daftar.map(kartuAuthor).join('') +
            '</div>';
    }

    if (tabAktif === 'collections') {
        container.innerHTML =
            '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">' +
            daftar.map(kartuCollection).join('') +
            '</div>';
    }
}

function tampilkanKartu(daftar) {
    if (daftar.length === 0) {
        container.innerHTML = kotakKosong('No books in this category yet.');
        return;
    }

    if (tampilanGrid) {
        container.innerHTML =
            '<div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-4">' +
            daftar.map(buatKartu).join('') +
            '</div>';
    } else {
        container.innerHTML = daftar.map(buatBaris).join('');
    }
}

function kartuSeries(s) {
    const covers = s.covers ? s.covers.split('||').filter(Boolean) : [];

    const gambar = covers.length
        ? covers.map(url => `<img src="${url}" alt="" class="rounded" style="height:100px;width:auto">`).join('')
        : `<img src="${COVER_KOSONG}" alt="" class="rounded" style="height:100px">`;

    return `
        <div class="col">
            <div class="card h-100 p-3 card-klik" data-tipe="series" data-id="${s.id}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">${aman(s.name)}</h6>
                    <span class="badge rounded-pill bg-dark">${s.dimiliki}/${s.total_buku}</span>
                </div>
                <p class="text-gray fs-13 mb-2">${aman(s.description || '')}</p>
                <div class="d-flex gap-2 overflow-auto pb-1">${gambar}</div>
            </div>
        </div>`;
}

function kartuAuthor(a) {
    return `
        <div class="col">
            <div class="card h-100 p-3 card-klik" data-tipe="author" data-id="${a.id}">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="fw-bold mb-0">${aman(a.name)}</h6>
                    <span class="badge rounded-pill bg-dark">${a.dimiliki}/${a.total_buku} books</span>
                </div>
                <p class="text-gray fs-13 mb-2">
                    ${aman(a.nationality || '-')} · ${a.birth_year || '-'}
                </p>
                <p class="fs-13 mb-0">${aman(a.bio || 'No bio yet.')}</p>
            </div>
        </div>`;
}

function kartuCollection(c) {
    const total = Number(c.total_buku);
    const terbaca = Number(c.terbaca);
    const persen = total > 0 ? Math.round((terbaca / total) * 100) : 0;

    return `
        <div class="col">
            <div class="card h-100 p-3 card-klik" data-tipe="collection" data-id="${c.id}">
                <h6 class="fw-bold mb-1">${aman(c.name)}</h6>
                <p class="text-gray fs-13 mb-2">${aman(c.description || '')}</p>
                <p class="fs-13 mb-2">${total} books · ${persen}% read</p>
                <div class="progress" style="height:6px">
                    <div class="progress-bar bg-success" role="progressbar" style="width:${persen}%"></div>
                </div>
            </div>
        </div>`;
}

function buatKartu(buku) {
    const cover = buku.cover_url || COVER_KOSONG;
    const w = WARNA[buku.status];

    return `
        <div class="col">
            <div class="card h-100 p-3 card-klik" data-tipe="buku" data-id="${buku.id}">
                <div class="mb-2">
                    <span class="badge rounded-pill bg-${w}-subtle text-${w}-emphasis fs-11">
                        <i class="bi bi-circle-fill me-1 lib-dot"></i>${aman(buku.status.toUpperCase())}
                    </span>
                </div>
                <img src="${cover}" alt="" class="grid-book-cover">
                <h6 class="mt-3 fw-bold mb-0">${aman(buku.title)}</h6>
                <p class="text-gray fs-13 mb-0">${aman(buku.authors || '-')}</p>
                <div class="rating fs-11">
                    ${bintang(buku.rating)}
                    <span class="ms-1">${skorRating(buku.rating)}</span>
                </div>
            </div>
        </div>`;
}

function buatBaris(buku) {
    const cover = buku.cover_url || COVER_KOSONG;
    const w = WARNA[buku.status];

    return `
        <div class="card mb-2 card-klik" data-tipe="buku" data-id="${buku.id}">
            <div class="card-body d-flex align-items-center gap-3 py-2">
                <img src="${cover}" alt="" class="list-book-cover">
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold">${aman(buku.title)}</h6>
                    <p class="text-gray fs-13 mb-0">${aman(buku.authors || '-')}</p>
                </div>
                <span class="badge rounded-pill bg-${w}-subtle text-${w}-emphasis fs-11 d-none d-md-inline">
                    ${aman(buku.status.toUpperCase())}
                </span>
                <div class="rating fs-13 d-none d-md-block">
                    ${bintang(buku.rating)}
                    <span class="ms-1">${skorRating(buku.rating)}</span>
                </div>
                <p class="text-gray fs-13 mb-0 d-none d-md-block">${buku.page_count || 0} p</p>
            </div>
        </div>`;
}

function bintang(nilai) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        html += (i <= (nilai || 0)) ? '<i class="bi bi-star-fill"></i>' : '';
    }
    return html;
}

function skorRating(nilai) {
    if (!nilai) return '';
    return Number(nilai).toFixed(1);
}

function kotakKosong(pesan) {
    return `
        <div class="alert alert-light" role="alert">
            <p class="text-gray fs-13 mb-0">${aman(pesan)}</p>
        </div>`;
}

function updateCounter(jumlah) {
    if (tabAktif === 'books' || tabAktif === 'wishlist') {
        counter.textContent = jumlah + ' books · sorted by ' + LABEL_URUTAN[urutan];
    } else {
        counter.textContent = jumlah + ' ' + tabAktif;
    }
}

function updateBadgeTab() {
    document.querySelector('[data-count="books"]').textContent = semuaBuku.length;
    document.querySelector('[data-count="series"]').textContent = dataGrup.series.length;
    document.querySelector('[data-count="authors"]').textContent = dataGrup.authors.length;
    document.querySelector('[data-count="collections"]').textContent = dataGrup.collections.length;
    document.querySelector('[data-count="wishlist"]').textContent =
        semuaBuku.filter(b => b.status === 'wishlist').length;
}

function updateCountPill() {
    document.querySelectorAll('[data-count-status]').forEach(function (el) {
        const status = el.dataset.countStatus;
        el.textContent = semuaBuku.filter(b => b.status === status).length;
    });
}

function updatePillAktif() {
    document.querySelectorAll('.lib-pill').forEach(function (pill) {
        pill.classList.toggle('active', pill.dataset.status === pillAktif);
    });
}

function aman(teks) {
    const div = document.createElement('div');
    div.textContent = teks ?? '';
    return div.innerHTML;
}

function gantiPill(status) {
    pillAktif = (pillAktif === status) ? null : status;
    updatePillAktif();
    tampilkan();
}

btnBaru.addEventListener('click', function () {
    const jenis = tabAktif === 'series' ? 'series' : 'collection';
    document.getElementById('baru-jenis').value = jenis;
    document.getElementById('baru-judul').textContent = 'new ' + jenis;
    document.getElementById('baru-nama').value = '';
    document.getElementById('baru-deskripsi').value = '';
    tampilModal('modal-baru');
});

document.getElementById('form-baru').addEventListener('submit', async function (e) {
    e.preventDefault();

    const jenis = document.getElementById('baru-jenis').value;
    const aksi = jenis === 'series' ? 'series-baru' : 'koleksi-baru';

    const ok = await kirimAksiTanpaDetail({
        action: aksi,
        name: document.getElementById('baru-nama').value.trim(),
        description: document.getElementById('baru-deskripsi').value.trim()
    }, 'Created new ' + jenis + '.');

    if (ok) {
        sembunyiModal('modal-baru');
    }
});

async function kirimAksiTanpaDetail(data, pesanSukses) {
    const ok = await kirimJSON('action_book.php', data);
    if (ok) {
        tampilToast(pesanSukses);
        await ambilData();
    }
    return ok;
}

document.querySelectorAll('.lib-tab').forEach(function (tab) {
    tab.addEventListener('click', function (e) {
        e.preventDefault();

        document.querySelectorAll('.lib-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        tabAktif = this.dataset.tab;
        tampilkan();
    });
});

document.querySelectorAll('.lib-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
        gantiPill(this.dataset.status);
    });
});

document.querySelectorAll('.pilih-urut').forEach(function (item) {
    item.addEventListener('click', function (e) {
        e.preventDefault();
        urutan = this.dataset.urut;
        labelUrut.textContent = LABEL_URUTAN[urutan];
        tampilkan();
    });
});

btnView.addEventListener('click', function () {
    tampilanGrid = !tampilanGrid;
    this.innerHTML = tampilanGrid
        ? '<i class="bi bi-list-ul"></i>'
        : '<i class="bi bi-grid-3x3-gap"></i>';
    tampilkan();
});

searchInput.addEventListener('input', function () {
    kataCari = this.value.trim().toLowerCase();
    tampilkan();
});

searchForm.addEventListener('submit', function (e) {
    e.preventDefault();
});

container.addEventListener('click', function (e) {
    const kartu = e.target.closest('.card-klik');
    if (!kartu) return;

    bukaDetail(kartu.dataset.tipe, kartu.dataset.id);
});

document.querySelectorAll('.modal-tab').forEach(function (tab) {
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
        pilihKoleksi = pilihKoleksi.includes(id)
            ? pilihKoleksi.filter(x => x !== id)
            : [...pilihKoleksi, id];
    } else if (tombol.classList.contains('pill-koleksi-baru')) {
        pilihKoleksiBaru.splice(Number(tombol.dataset.index), 1);
    }

    gambarPillKoleksi();
});

function buatKoleksiBaru() {
    const input = document.getElementById('f-koleksi-baru');
    const nama = input.value.trim();
    if (nama === '') return;

    const ada = dataGrup.collections.find(k => k.name.toLowerCase() === nama.toLowerCase());
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

document.querySelectorAll('[data-step]').forEach(function (tombol) {
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

document.getElementById('form-buku').addEventListener('submit', async function (e) {
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

    const ok = await kirimJSON('update_book.php', dataBaru);
    if (ok) {
        sembunyiModal('modal-buku');
        tampilToast('Book changes saved.');
        await ambilData();
    }
});

window.addEventListener('buku-berubah', async function () {
    await ambilData();
});

window.addEventListener('cari-ditutup', async function (e) {
    const idKoleksi = e.detail.collectionId;
    await ambilData();

    if (idKoleksi === null || !detailAktif) return;

    await ambilDetail();
    tampilDetail();
    tampilModal('modal-' + detailAktif.tipe);
});

['series', 'author', 'collection'].forEach(function (tipe) {
    document.getElementById('isi-' + tipe).addEventListener('click', klikDiModal);
});

document.getElementById('btn-hapus-buku').addEventListener('click', async function () {
    const bukuId = document.getElementById('form-buku').dataset.id;
    if (!confirm('Remove this book from your shelf? The book data will stay in the database.')) return;

    const ok = await kirimJSON('update_book.php', { action: 'hapus', book_id: bukuId });
    if (ok) {
        sembunyiModal('modal-buku');
        tampilToast('Book removed from shelf.');
        await ambilData();
    }
});

function bukaDetail(tipe, id) {
    if (tipe === 'buku') {
        bukaModalBuku(id);
    } else {
        bukaModalDetail(tipe, id);
    }
}

function bukaModalBuku(id, panel = 'overview') {
    const buku = semuaBuku.find(b => b.id == id);
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
    document.getElementById('o-rating').textContent = skorRating(buku.rating) || '-';
    document.getElementById('o-halaman').textContent = (buku.current_page || 0) + '/' + (buku.page_count || 0);

    document.getElementById('o-deskripsi').textContent = buku.description || 'No description yet.';
    document.getElementById('o-halaman-total').textContent = buku.page_count || '-';
    document.getElementById('o-penerbit').textContent = buku.publisher || '-';
    document.getElementById('o-tahun').textContent = buku.published_year || '-';
    document.getElementById('o-isbn').textContent = buku.isbn || '-';

    document.getElementById('o-genre').textContent = buku.genre_name || 'Other';
    document.getElementById('o-tanggal').textContent = tulisTanggalBaca(buku);
    document.getElementById('o-ditambahkan').textContent = formatTanggal(buku.added_at);
}

function tulisTanggalBaca(buku) {
    if (!buku.started_at) return 'Not started yet';

    const mulai = formatTanggal(buku.started_at);
    if (!buku.finished_at) return 'Started ' + mulai + ' · reading';

    const selesai = formatTanggal(buku.finished_at);
    return mulai + ' – ' + selesai + ' · ' + hariBaca(buku) + ' days';
}

function tanggalObjek(teks) {
    return new Date(teks.substring(0, 10) + 'T00:00:00');
}

function formatTanggal(teks) {
    if (!teks) return '-';
    return tanggalObjek(teks).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function hariBaca(b) {
    if (!b.started_at || !b.finished_at) return 0;
    return Math.round((tanggalObjek(b.finished_at) - tanggalObjek(b.started_at)) / 86400000) + 1;
}

function isiDropdownGenre(genreId) {
    const pilihan = document.getElementById('f-genre');
    pilihan.innerHTML = dataGrup.genres.map(g =>
        `<option value="${g.id}">${aman(g.name)}</option>`
    ).join('');
    pilihan.value = genreId;
}

function isiDropdownSeri(buku) {
    const pilihan = document.getElementById('f-seri');

    let opsi = '<option value="">no series</option>';
    opsi += dataGrup.series.map(s =>
        `<option value="${s.id}">${aman(s.name)}</option>`
    ).join('');
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

    let html = dataGrup.collections.map(k => {
        const aktif = pilihKoleksi.includes(Number(k.id));
        return `<button type="button" class="btn btn-sm rounded-pill pill-koleksi ${aktif ? 'btn-dark' : 'btn-outline-dark'}" data-id="${k.id}">${aman(k.name)}</button>`;
    }).join('');

    html += pilihKoleksiBaru.map((nama, i) =>
        `<button type="button" class="btn btn-sm rounded-pill btn-dark pill-koleksi-baru" data-index="${i}">${aman(nama)} ✕</button>`
    ).join('');

    wadah.innerHTML = html || '<p class="fs-13 text-gray mb-0">no collections yet.</p>';
}

function pilihPanel(nama) {
    document.querySelectorAll('.modal-tab').forEach(function (tab) {
        tab.classList.toggle('active', tab.dataset.panel === nama);
    });
    document.querySelectorAll('.modal-panel').forEach(function (panel) {
        panel.classList.toggle('d-none', panel.id !== 'panel-' + nama);
    });
}

async function bukaModalDetail(tipe, id) {
    detailAktif = { tipe: tipe, id: id };

    const modalEl = document.getElementById('modal-' + tipe);
    document.getElementById('isi-' + tipe).innerHTML = '<p class="text-gray">Loading...</p>';
    tampilModal('modal-' + tipe);

    const ok = await ambilDetail();
    if (!ok) {
        bootstrap.Modal.getInstance(modalEl).hide();
        return;
    }
    tampilDetail();
}

async function ambilDetail() {
    const res = await fetch(`get_detail.php?tipe=${detailAktif.tipe}&id=${detailAktif.id}`);
    const hasil = await res.json();

    if (!res.ok) {
        tampilToast(hasil.error || 'Failed to load details.', false);
        return false;
    }

    dataDetail = hasil;
    return true;
}

function tampilDetail() {
    if (!dataDetail || !detailAktif) return;

    const isi = document.getElementById('isi-' + detailAktif.tipe);

    if (detailAktif.tipe === 'series') {
        isi.innerHTML = htmlSeries();
    } else if (detailAktif.tipe === 'author') {
        isi.innerHTML = htmlAuthor();
    } else if (detailAktif.tipe === 'collection') {
        isi.innerHTML = htmlCollection();
    }
}

function htmlSeries() {
    const s = dataDetail.info;
    const buku = dataDetail.books;
    const terbaca = buku.filter(b => b.status === 'read').length;
    const persen = buku.length ? Math.round((terbaca / buku.length) * 100) : 0;

    const daftar = buku.map(b =>
        barisBuku(b, b.series_order ? '#' + Number(b.series_order) : '', tombolRak(b))
    ).join('');

    return `
        <h3 class="fw-bold mb-1">${aman(s.name)}</h3>
        <p class="fs-13 text-gray mb-3">${aman(s.description || 'No description yet.')}</p>

        <div class="d-flex justify-content-between fs-13 mb-1">
            <span>${terbaca}/${buku.length} read</span>
            <span>${persen}%</span>
        </div>
        <div class="progress mb-4" style="height:8px">
            <div class="progress-bar bg-success" style="width:${persen}%"></div>
        </div>

        <h6 class="fw-bold mb-2">books in series</h6>
        ${daftar || kotakKosong('No books in this series yet.')}

        <div class="row g-2 mt-3">
            <div class="col-6">
                <div class="card p-3">
                    <div class="fs-11 text-gray">total pages</div>
                    <h4 class="text-pink-strong mb-0">${totalHalaman(buku)}</h4>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3">
                    <div class="fs-11 text-gray">total days</div>
                    <h4 class="text-pink-strong mb-0">${totalHari(buku)}</h4>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-link text-danger fs-13" data-aksi="series-hapus" data-id="${s.id}">delete this series</button>
        </div>`;
}

function htmlAuthor() {
    const a = dataDetail.info;
    const buku = dataDetail.books;
    const dimiliki = buku.filter(b => b.status !== null).length;

    const daftar = buku.map(b => barisBuku(b, '', tombolRak(b))).join('');

    return `
        <h3 class="fw-bold mb-1">${aman(a.name)}</h3>
        <p class="fs-13 text-gray mb-2">${aman(a.nationality || '-')} · ${a.birth_year || '-'}</p>
        <p class="fs-13 mb-3">${aman(a.bio || 'No bio yet.')}</p>

        <div class="card p-3 mb-4">
            <div class="fs-11 text-gray">books owned</div>
            <h4 class="text-pink-strong mb-0">${dimiliki}/${buku.length}</h4>
        </div>

        <h6 class="fw-bold mb-2">books by ${aman(a.name)}</h6>
        ${daftar || kotakKosong('No books from this author yet.')}`;
}

function htmlCollection() {
    const c = dataDetail.info;
    const buku = dataDetail.books;

    const daftar = buku.map(b =>
        barisBuku(b, '', `<button type="button" class="btn btn-sm btn-outline-dark rounded-pill" data-aksi="koleksi-hapus" data-book="${b.id}">✕</button>`)
    ).join('');

    return `
        <h3 class="fw-bold mb-1">${aman(c.name)}</h3>
        <p class="fs-13 text-gray mb-3">${aman(c.description || 'No description yet.')}</p>

        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="card p-3">
                    <div class="fs-11 text-gray">total books</div>
                    <h4 class="text-pink-strong mb-0">${buku.length}</h4>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-3">
                    <div class="fs-11 text-gray">total pages</div>
                    <h4 class="text-pink-strong mb-0">${totalHalaman(buku)}</h4>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">books</h6>
            <button type="button" class="btn btn-sm btn-pink rounded-pill px-3" data-aksi="buka-cari">+ add books</button>
        </div>

        ${daftar || kotakKosong('No books in this collection yet.')}

        <div class="text-center mt-4">
            <button type="button" class="btn btn-link text-danger fs-13" data-aksi="koleksi-hapus-semua" data-id="${c.id}">delete this collection</button>
        </div>`;
}

function barisBuku(b, nomor, tombol) {
    const cover = b.cover_url || COVER_KOSONG;
    const punyaRak = b.status !== null;
    const w = WARNA[b.status] || 'secondary';

    return `
        <div class="card mb-2 ${punyaRak ? '' : 'opacity-50'}">
            <div class="card-body d-flex align-items-center gap-3 py-2">
                ${nomor ? `<span class="text-gray fs-13">${aman(nomor)}</span>` : ''}
                <img src="${cover}" alt="" class="list-book-cover">

                <div class="flex-grow-1" ${punyaRak ? `data-buku="${b.id}" style="cursor:pointer"` : ''}>
                    <h6 class="mb-0 fw-bold">${aman(b.title)}</h6>
                    <p class="text-gray fs-13 mb-0">${aman(b.authors || '-')}</p>
                </div>

                ${punyaRak ? `<span class="badge rounded-pill bg-${w}-subtle text-${w}-emphasis fs-11">${aman(b.status.toUpperCase())}</span>` : ''}
                ${punyaRak && b.rating ? `<div class="rating fs-13 d-none d-md-block">${bintang(b.rating)}</div>` : ''}
                <span class="text-gray fs-13">${b.page_count || 0} p</span>

                <div class="d-flex gap-1">${tombol}</div>
            </div>
        </div>`;
}

function tombolRak(b) {
    if (b.status === null) {
        return `
            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" data-aksi="rak" data-book="${b.id}" data-status="wishlist">+ wishlist</button>
            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" data-aksi="rak" data-book="${b.id}" data-status="tbr">+ to read</button>`;
    }
    return `<button type="button" class="btn btn-sm btn-dark rounded-pill" data-aksi="log" data-book="${b.id}">log pages</button>`;
}

function totalHalaman(daftar) {
    return daftar.reduce((total, b) => total + Number(b.page_count || 0), 0);
}

function totalHari(daftar) {
    return daftar.reduce((total, b) => total + hariBaca(b), 0);
}

async function kirimAksi(data, tombol = null, pesanSukses = 'Saved.') {
    setLoading(tombol, true);
    const ok = await kirimJSON('action_book.php', data);
    setLoading(tombol, false);

    if (!ok) return false;

    tampilToast(pesanSukses);
    await ambilData();
    await ambilDetail();
    tampilDetail();
    return true;
}

async function klikDiModal(e) {
    const tombol = e.target.closest('[data-aksi]');

    if (tombol) {
        await jalankanAksi(tombol);
        return;
    }

    const judul = e.target.closest('[data-buku]');
    if (judul) {
        const bukuId = judul.dataset.buku;
        gantiModal('modal-' + detailAktif.tipe, () => bukaModalBuku(bukuId));
    }
}

async function jalankanAksi(tombol) {
    const aksi = tombol.dataset.aksi;
    const bukuId = tombol.dataset.book;
    const idModal = 'modal-' + detailAktif.tipe;

    switch (aksi) {
        case 'buka-cari':
            gantiModal(idModal, () => bukaModalCari(detailAktif.id));
            break;

        case 'rak': {
            const label = tombol.dataset.status === 'wishlist' ? 'wishlist' : 'to read';
            await kirimAksi(
                { action: 'rak', book_id: bukuId, status: tombol.dataset.status },
                tombol,
                'Book added to ' + label + '.'
            );
            break;
        }

        case 'log':
            gantiModal(idModal, () => bukaModalBuku(bukuId, 'baca'));
            break;

        case 'koleksi-hapus':
            await kirimAksi(
                { action: 'koleksi-hapus', collection_id: detailAktif.id, book_id: bukuId },
                tombol,
                'Book removed from collection.'
            );
            break;

        case 'series-hapus':
            if (!confirm('Delete this series?')) return;
            if (await kirimAksiTanpaDetail({ action: 'series-hapus', series_id: detailAktif.id }, 'Series deleted.')) {
                sembunyiModal('modal-series');
            }
            break;

        case 'koleksi-hapus-semua':
            if (!confirm('Delete this collection and its contents? Books will stay on your shelf.')) return;
            if (await kirimAksiTanpaDetail({ action: 'koleksi-hapus-semua', collection_id: detailAktif.id }, 'Collection deleted.')) {
                sembunyiModal('modal-collection');
            }
            break;
    }
}

function gantiModal(idModalLama, fungsiBerikutnya) {
    const el = document.getElementById(idModalLama);
    el.addEventListener('hidden.bs.modal', fungsiBerikutnya, { once: true });
    bootstrap.Modal.getOrCreateInstance(el).hide();
}

const parameter = new URLSearchParams(window.location.search);

if (parameter.get('tab')) {
    tabAktif = parameter.get('tab');
    document.querySelectorAll('.lib-tab').forEach(function (tab) {
        tab.classList.toggle('active', tab.dataset.tab === tabAktif);
    });
}

if (parameter.get('pill')) {
    pillAktif = parameter.get('pill');
}

updatePillAktif();
ambilData();