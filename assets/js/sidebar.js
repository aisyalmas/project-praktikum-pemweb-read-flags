const desktopMq = window.matchMedia('(min-width: 992px)');

function applySidebarIcon() {
    const collapsed = document.body.classList.contains('sb-collapsed');
    const icon = document.querySelector('.toggle-btn i');
    if (!icon) return;

    icon.classList.toggle('bi-chevron-right', collapsed);
    icon.classList.toggle('bi-chevron-left', !collapsed);
}

function setSidebarState(open) {
    document.body.classList.toggle('sb-collapsed', !open);
    applySidebarIcon();
}

function toggleSidebar() {
    document.body.classList.toggle('sb-collapsed');
    applySidebarIcon();
}

desktopMq.addEventListener('change', (e) => setSidebarState(e.matches));

document.addEventListener('DOMContentLoaded', () => {
    setSidebarState(desktopMq.matches);
});

/**
 * MODAL BUKU
 */
let cariCollectionId = null;
let hasilCari = [];
let bukuDipilih = null;
let timerCari = null;
let modalCariSudahDipasang = false;
const STATUS_LIST = ['reading', 'read', 'tbr', 'dnf', 'wishlist'];

function aman(teks) {
    const div = document.createElement('div');
    div.textContent = teks ?? '';
    return div.innerHTML;
}

function sampul(b) {
    return b.cover_url || '../assets/images/nocover.jpg';
}

function pesanKosong(pesan) {
    return `<div class="alert alert-light" role="alert"><p class="text-gray fs-13 mb-0">${aman(pesan)}</p></div>`;
}

function tampilToast(pesan, sukses = true) {
    let wadah = document.getElementById('toast-wadah');
    if (!wadah) {
        document.body.insertAdjacentHTML('beforeend',
            '<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-wadah"></div>');
        wadah = document.getElementById('toast-wadah');
    }

    const el = document.createElement('div');
    el.className = `toast align-items-center text-white border-0 bg-${sukses ? 'success' : 'danger'}`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${aman(pesan)}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;

    wadah.appendChild(el);
    el.addEventListener('hidden.bs.toast', () => el.remove());
    new bootstrap.Toast(el, { delay: 2500 }).show();
}

function setLoading(tombol, sedang) {
    if (!tombol) return;

    if (sedang) {
        tombol.dataset.teksAwal = tombol.innerHTML;
        tombol.disabled = true;
        tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>processing...';
    } else {
        tombol.disabled = false;
        tombol.innerHTML = tombol.dataset.teksAwal;
    }
}

function pastikanModalCari() {
    if (modalCariSudahDipasang) return;

    if (!document.getElementById('modal-cari')) {
        document.body.insertAdjacentHTML('beforeend', `
            <div class="modal fade" id="modal-cari" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-light btn-sm rounded-pill d-none" id="cari-kembali">back</button>
                            <h5 class="modal-title" id="cari-judul">add books</h5>
                            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="view-cari">
                                <input type="search" id="cari-input" class="form-control rounded-pill mb-3"
                                    placeholder="search title or author...">
                                <div id="cari-hasil"></div>
                            </div>
                            <div id="view-tambah" class="d-none"></div>
                        </div>
                    </div>
                </div>
            </div>`);
    }

    pasangEventModalCari();
    modalCariSudahDipasang = true;
}

function bukaModalCari(collectionId = null) {
    pastikanModalCari();

    cariCollectionId = collectionId;
    document.getElementById('cari-input').value = '';
    tampilView('cari');
    hasilCari = [];
    gambarHasilCari();

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-cari')).show();
}

function tampilView(view) {
    const cari = view === 'cari';
    document.getElementById('view-cari').classList.toggle('d-none', !cari);
    document.getElementById('view-tambah').classList.toggle('d-none', cari);
    document.getElementById('cari-kembali').classList.toggle('d-none', cari);
    document.getElementById('cari-judul').textContent = cari ? 'add books' : 'add a book';
}

async function cariBuku(q) {
    if (q === '') {
        hasilCari = [];
        gambarHasilCari();
        return;
    }

    try {
        const res = await fetch(`../library/api.php?data=search&q=${encodeURIComponent(q)}`);
        hasilCari = res.ok ? await res.json() : [];
        if (!res.ok) {
            tampilToast('Failed to search books.', false);
        }
    } catch (error) {
        console.error(error);
        tampilToast('Connection problem, try again.', false);
        hasilCari = [];
    }
    gambarHasilCari();
}

function gambarHasilCari() {
    const wadah = document.getElementById('cari-hasil');
    const sudahMengetik = document.getElementById('cari-input').value.trim() !== '';

    if (!sudahMengetik) {
        wadah.innerHTML = '';
        return;
    }

    if (hasilCari.length === 0) {
        wadah.innerHTML = pesanKosong('No books found.');
        return;
    }

    wadah.innerHTML = hasilCari.map(b => `
        <div class="card mb-2">
            <div class="card-body d-flex align-items-center gap-3 py-2">
                <img src="${sampul(b)}" alt="" class="list-book-cover">
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold">${aman(b.title)}</h6>
                    <p class="text-gray fs-13 mb-0">${aman(b.authors || '-')}</p>
                </div>
                <span class="text-gray fs-13 d-none d-md-inline">${b.published_year || '-'}</span>
                <span class="text-gray fs-13">${b.page_count || 0} p</span>
                <button type="button" class="btn btn-sm btn-dark rounded-pill" data-tambah="${b.id}">+</button>
            </div>
        </div>`).join('');
}

function tampilTambah(bukuId) {
    bukuDipilih = hasilCari.find(b => b.id == bukuId);
    if (!bukuDipilih) return;

    gambarTambah();
    tampilView('tambah');
}

function gambarTambah() {
    const b = bukuDipilih;

    const badges = STATUS_LIST.map(s => `
        <button type="button"
            class="btn btn-sm rounded-pill ${b.status === s ? 'btn-dark' : 'btn-outline-dark'}"
            data-status="${s}">${s}</button>`).join('');

    document.getElementById('view-tambah').innerHTML = `
        <div class="row g-3">
            <div class="col-auto">
                <img src="${sampul(b)}" alt="" class="rounded shadow" style="height:180px">
            </div>
            <div class="col">
                <h3 class="fw-bold mb-1">${aman(b.title)}</h3>
                <p class="fs-13 text-gray mb-2">${aman(b.authors || '-')}</p>
                <p class="fs-13 mb-1"><strong>Year:</strong> ${b.published_year || '-'}</p>
                <p class="fs-13 mb-1"><strong>Pages:</strong> ${b.page_count || '-'}</p>
                <p class="fs-13 mb-1"><strong>Publisher:</strong> ${aman(b.publisher || '-')}</p>
                <p class="fs-13 mb-0"><strong>ISBN:</strong> ${aman(b.isbn || '-')}</p>
            </div>
        </div>
        <p class="fs-13 mt-3">${aman(b.description || 'No description yet.')}</p>
        <h6 class="fw-bold mb-2">choose status</h6>
        <div class="d-flex flex-wrap gap-2">${badges}</div>`;
}

async function simpanStatus(tombol, status) {
    const res = await fetch('../library/api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'status',
            book_id: bukuDipilih.id,
            status: status,
            collection_id: cariCollectionId
        })
    });
    const hasil = await res.json();

    if (!res.ok) {
        tampilToast(hasil.error || 'Failed to save.', false);
        gambarTambah();
        return;
    }

    bukuDipilih.status = status;
    tampilToast('Status changed to ' + status + '.');
    gambarTambah();
    window.dispatchEvent(new Event('buku-berubah'));
}

function pasangEventModalCari() {
    document.getElementById('cari-input').addEventListener('input', function () {
        clearTimeout(timerCari);
        const q = this.value.trim();
        timerCari = setTimeout(() => cariBuku(q), 300);
    });

    document.getElementById('cari-hasil').addEventListener('click', function (e) {
        const tombol = e.target.closest('[data-tambah]');
        if (tombol) tampilTambah(tombol.dataset.tambah);
    });

    document.getElementById('cari-kembali').addEventListener('click', function () {
        tampilView('cari');
        cariBuku(document.getElementById('cari-input').value.trim());
    });

    document.getElementById('view-tambah').addEventListener('click', async function (e) {
        const tombol = e.target.closest('[data-status]');
        if (!tombol || tombol.disabled) return;

        setLoading(tombol, true);
        this.querySelectorAll('[data-status]').forEach(b => b.disabled = true);

        try {
            await simpanStatus(tombol, tombol.dataset.status);
        } catch (error) {
            console.error(error);
            tampilToast('Connection problem, try again.', false);
            gambarTambah();
        }
    });

    document.getElementById('modal-cari').addEventListener('hidden.bs.modal', function () {
        window.dispatchEvent(new CustomEvent('cari-ditutup', {
            detail: { collectionId: cariCollectionId }
        }));
    });
}

document.addEventListener('click', function (e) {
    if (e.target.closest('#btn-tambah-buku')) {
        e.preventDefault();
        bukaModalCari(null);
    }
});

window.bukaModalCari = bukaModalCari;