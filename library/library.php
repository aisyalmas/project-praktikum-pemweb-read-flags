<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library | Read Flags</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/library.css">
</head>

<body>
    <?php include '../navigation/sidebar.php'; ?>

    <main class="main-content">
        <div class="container p-0">

            <div class="row align-items-center mb-3">
                <div class="col-md-7">
                    <h2 class="mb-0">Library</h2>
                </div>

                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <!-- search -->
                        <form id="search-form" class="flex-grow-1" role="search">
                            <div class="position-relative">
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
                                <input type="search" id="search-input"
                                    class="form-control rounded-pill ps-5 py-2"
                                    placeholder="Search title, author,...">
                            </div>
                        </form>

                        <button type="button" class="btn btn-dark rounded-pill px-4 text-nowrap"
                            data-bs-toggle="offcanvas" data-bs-target="#filters" aria-controls="filters">
                            <i class="bi bi-sliders me-2"></i>Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="filters" aria-labelledby="filtersLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="filtersLabel">filters</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    ...
                </div>
            </div>

            <ul class="nav nav-underline border-bottom mb-3">
                <li class="nav-item">
                    <a class="nav-link lib-tab active" href="#" data-tab="books">
                        books <span class="badge" data-count="books">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link lib-tab" href="#" data-tab="series">
                        series <span class="badge" data-count="series">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link lib-tab" href="#" data-tab="authors">
                        authors <span class="badge" data-count="authors">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link lib-tab" href="#" data-tab="collections">
                        collections <span class="badge" data-count="collections">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link lib-tab" href="#" data-tab="wishlist">
                        wishlist <span class="badge" data-count="wishlist">0</span>
                    </a>
                </li>
                <li class="nav-item ms-auto d-none d-md-block">
                    <span class="nav-link text-gray fs-13" id="counter">0 books</span>
                </li>
            </ul>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

                <div id="pill-bar" class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm rounded-pill lib-pill" data-status="reading">
                        <span class="fs-13 fw-bold me-1" data-count-status="reading">0</span><span class="fs-11">reading</span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill lib-pill" data-status="read">
                        <span class="fs-13 fw-bold me-1" data-count-status="read">0</span><span class="fs-11">read</span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill lib-pill" data-status="tbr">
                        <span class="fs-13 fw-bold me-1" data-count-status="tbr">0</span><span class="fs-11">tbr</span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill lib-pill" data-status="dnf">
                        <span class="fs-13 fw-bold me-1" data-count-status="dnf">0</span><span class="fs-11">dnf</span>
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill lib-pill" data-status="wishlist">
                        <span class="fs-13 fw-bold me-1" data-count-status="wishlist">0</span><span class="fs-11">wishlist</span>
                    </button>
                </div>

                <div id="kontrol" class="d-flex gap-2 ms-auto">

                    <!-- Dropdown urutkan: diberi id supaya bisa disembunyikan -->
                    <div class="dropdown" id="dropdown-urut">
                        <button class="btn btn-sm rounded-pill btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-sort-down me-1"></i><span id="label-urut">added</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item pilih-urut" href="#" data-urut="added">added</a></li>
                            <li><a class="dropdown-item pilih-urut" href="#" data-urut="rating">rating</a></li>
                            <li><a class="dropdown-item pilih-urut" href="#" data-urut="az">a-z</a></li>
                            <li><a class="dropdown-item pilih-urut" href="#" data-urut="length">length</a></li>
                        </ul>
                    </div>

                    <!-- Tombol grid / list -->
                    <button type="button" id="btn-view" class="btn btn-sm rounded-pill btn-outline-dark" title="Ganti tampilan">
                        <i class="bi bi-list-ul"></i>
                    </button>

                    <!-- Tombol + new: hanya muncul di series dan collections -->
                    <button type="button" id="btn-baru" class="btn btn-sm rounded-pill btn-dark d-none">+ new</button>

                </div>
            </div>

            <div id="book-container"></div>

        </div>
    </main>

<!-- toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-wadah"></div>

    <!-- MODAL BUKU -->
    <div class="modal fade" id="modal-buku" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form id="form-buku" class="modal-content">
                <div class="modal-header text-white d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">cancel</button>
                    <button type="submit" class="btn btn-pink rounded-pill px-4">save</button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <img id="m-cover" src="" alt="Book Cover" class="rounded shadow" style="height: 110px;">
                        </div>
                        <div class="col">
                            <p class="fs-11 text-gray mb-0">editing · <span id="m-halaman">0</span> pages</p>
                            <h3 class="fw-bold mb-0" id="modal-buku-judul">Detail buku</h3>
                            <p class="fs-13 text-gray mb-0" id="modal-buku-penulis">Author</p>
                        </div>
                    </div>

                    <ul class="nav nav-underline border-bottom mb-3">
                        <li class="nav-item">
                            <button type="button" class="nav-link modal-tab active" data-panel="overview">overview</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link modal-tab" data-panel="tentang">about the book</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link modal-tab" data-panel="baca">my reading</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link modal-tab" data-panel="seri">series</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link modal-tab" data-panel="koleksi">collection</button>
                        </li>

                    </ul>

                    <!-- OVERVIEW -->
                    <div class="modal-panel" id="panel-overview">
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="card p-3 h-100">
                                    <h4 class="text-pink-strong" id="o-status">-</h4>
                                    <div class="fs-11 text-gray">status</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card p-3 h-100">
                                    <h4 class="text-pink-strong" id="o-rating" style="height: 28px !important;">-</h4>
                                    <div class="fs-11 text-gray">your rating</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card p-3 h-100">
                                    <h4 class="text-pink-strong" id="o-halaman">0/0</h4>
                                    <div class="fs-11 text-gray">halaman dibaca</div>
                                </div>
                            </div>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-1">about</div>
                            <p class="fs-13 mb-0" id="o-deskripsi">-</p>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-2">details</div>
                            <div class="d-flex justify-content-between fs-13 border-bottom py-1">
                                <span class="text-gray">pages</span><span id="o-halaman-total">-</span>
                            </div>
                            <div class="d-flex justify-content-between fs-13 border-bottom py-1">
                                <span class="text-gray">publisher</span><span id="o-penerbit">-</span>
                            </div>
                            <div class="d-flex justify-content-between fs-13 border-bottom py-1">
                                <span class="text-gray">tahun terbit</span><span id="o-tahun">-</span>
                            </div>
                            <div class="d-flex justify-content-between fs-13 py-1">
                                <span class="text-gray">isbn</span><span id="o-isbn">-</span>
                            </div>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-1">genre</div>
                            <button type="button" class="btn btn-sm rounded-pill btn-dark" id="o-genre" style="width: fit-content; display: inline-flex;">-</button>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-2">dates</div>
                            <p class="fs-13 mb-0" id="o-tanggal">-</p>
                        </div>

                        <div class="card p-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-2">owning it</div>
                            <div class="d-flex justify-content-between fs-13">
                                <span class="text-gray">ditambahkan</span><span id="o-ditambahkan">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- ABOUT THE BOOK -->
                    <div class="modal-panel d-none" id="panel-tentang">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold">Deskripsi</label>
                            <textarea class="form-control" id="f-deskripsi" rows="6"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold">Genre</label>
                            <select class="form-select" id="f-genre"></select>
                        </div>
                    </div>

                    <!-- SERIES -->
                    <div class="modal-panel d-none" id="panel-seri">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold">Series</label>
                            <select class="form-select" id="f-seri"></select>
                        </div>
                        <div class="mb-3 d-none" id="blok-seri-baru">
                            <label class="form-label fs-13 fw-bold">Nama series baru</label>
                            <input type="text" class="form-control" id="f-seri-baru" placeholder="contoh: Dune">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold">Urutan di series</label>
                            <input type="number" step="0.1" min="0" class="form-control" id="f-urutan" placeholder="contoh: 1">
                        </div>
                    </div>

                    <!-- COLLECTION -->
                    <div class="modal-panel d-none" id="panel-koleksi">
                        <p class="fs-13 text-gray mb-2">Klik untuk memilih. Yang berwarna gelap berarti dipilih.</p>
                        <div id="f-koleksi-pills" class="d-flex flex-wrap gap-2 mb-3"></div>

                        <div class="input-group">
                            <input type="text" class="form-control" id="f-koleksi-baru" placeholder="nama koleksi baru">
                            <button type="button" class="btn btn-outline-dark" id="btn-buat-koleksi">+ buat</button>
                        </div>
                    </div>
                    
                    <!-- MY READING -->
                    <div class="modal-panel d-none" id="panel-baca">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold">Status</label>
                            <select class="form-select" id="f-status">
                                <option value="reading">reading</option>
                                <option value="read">read</option>
                                <option value="tbr">tbr</option>
                                <option value="dnf">dnf</option>
                                <option value="wishlist">wishlist</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold">Rating</label>
                            <select class="form-select" id="f-rating">
                                <option value="0">belum dinilai</option>
                                <option value="1">1 bintang</option>
                                <option value="2">2 bintang</option>
                                <option value="3">3 bintang</option>
                                <option value="4">4 bintang</option>
                                <option value="5">5 bintang</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold">Total halaman</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-dark" data-step="-1" data-target="f-total">-</button>
                                    <input type="number" class="form-control text-center" id="f-total" min="0" value="0">
                                    <button type="button" class="btn btn-outline-dark" data-step="1" data-target="f-total">+</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold">Halaman saat ini</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-dark" data-step="-1" data-target="f-halaman-baca">-</button>
                                    <input type="number" class="form-control text-center" id="f-halaman-baca" min="0" value="0">
                                    <button type="button" class="btn btn-outline-dark" data-step="1" data-target="f-halaman-baca">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold">Mulai baca</label>
                                <input type="date" class="form-control" id="f-mulai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold">Selesai baca</label>
                                <input type="date" class="form-control" id="f-selesai">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button type="button" class="btn btn-link text-danger fs-13" id="btn-hapus-buku">delete this book</button>
                    </div>

                </div>

            </form>
        </div>
    </div>

<!-- MODAL SERIES -->
<div class="modal fade" id="modal-series" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">series</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="isi-series"></div>
        </div>
    </div>
</div>

<!-- MODAL AUTHOR -->
<div class="modal fade" id="modal-author" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">author</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="isi-author"></div>
        </div>
    </div>
</div>

<!-- MODAL COLLECTION -->
<div class="modal fade" id="modal-collection" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">collection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="isi-collection"></div>
        </div>
    </div>
</div>

<!-- MODAL CARI / ADD BOOKS -->
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
                        placeholder="cari judul atau author...">
                    <div id="cari-hasil"></div>
                </div>
                <div id="view-tambah" class="d-none"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-baru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="form-baru" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="baru-judul">new</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="baru-jenis">
                <div class="mb-3">
                    <label class="form-label fs-13 fw-bold">Nama</label>
                    <input type="text" class="form-control" id="baru-nama" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fs-13 fw-bold">Deskripsi</label>
                    <textarea class="form-control" id="baru-deskripsi" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn btn-pink rounded-pill px-4">save</button>
            </div>
        </form>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/library.js"></script>
</body>

</html>