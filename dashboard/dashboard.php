<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Read Flags</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
</head>

<body>
    <?php include '../navigation/sidebar.php'; ?>

    <main class="main-content">
        <div class="container p-0">
            <p class="mb-0 text-gray fs-13" id="tanggal-hari"></p>
            <h2 class="mb-3">good morning, reader <span class="text-pink">✦</span></h2>

            <div class="container-card">
                <div class="row">
                    <!-- now reading -->
                    <div class="col-md-5 px-2">
                        <div class="card card-dark h-100">
                            <div class="card-body">
                                <div class="row justify-content-between">
                                    <div class="col-4 d-flex align-items-center">
                                        <p class="text-pink fs-11 mb-0">NOW READING</p>
                                    </div>
                                    <div class="col-5">
                                        <span class="badge rounded-pill text-bg-active fs-11"><span class="circle-active"></span><span id="badge-baca">0% read</span></span>
                                        <span class="badge rounded-pill text-bg-custom fs-11"><span class="circle"></span><span id="badge-sisa">100% left</span></span>
                                    </div>
                                </div>
                                <div class="row justify-content-between mt-3">
                                    <div class="col-3">
                                        <img id="n-cover" src="../assets/images/nocover.jpg" alt="" class="cover-book">
                                    </div>
                                    <div class="col-9">
                                        <h3 class="text-white" id="n-judul">-</h3>
                                        <p class="text-gray fs-11" id="n-penulis">-</p>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="page d-flex align-items-center gap-1">
                                                <h6 class="text-white mb-0" id="n-halaman">p. 0</h6>
                                                <p class="text-gray fs-11 mb-0" id="n-total">of 0</p>
                                            </div>
                                            <h6 class="text-pink mb-0" id="n-persen">0%</h6>
                                        </div>

                                        <div class="progress mt-2">
                                            <div class="progress-bar bg-custom" id="n-bar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3 px-2">
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-white mb-0" id="n-hari">0</h6>
                                            <p class="text-gray fs-10 mb-0">pages today</p>
                                        </div>
                                    </div>
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-white mb-0" id="n-menit">0 m</h6>
                                            <p class="text-gray fs-10 mb-0">reading time</p>
                                        </div>
                                    </div>
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-white mb-0" id="n-pace">0</h6>
                                            <p class="text-gray fs-10 mb-0">daily pace</p>
                                        </div>
                                    </div>
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-pink mb-0" id="n-finish">-</h6>
                                            <p class="text-gray fs-10 mb-0">est. finish</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <button type="button" id="btn-log-now" class="btn btn-pink fs-13 fw-semibold"><i class="bi bi-check2" style="font-size: 20px !important; padding-right: 5px;"></i> log pages</button>
                                        <button type="button" class="btn btn-outline fs-13 fw-semibold"><i class="bi bi-plus" style="font-size: 20px !important; padding-right: 2px;"></i> add review</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- streak -->
                    <div class="col-md-4 px-2">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">streak & cadence</h4>
                                    <button type="button" class="btn btn-link link-more p-0" id="btn-streak">detail ›</button>
                                </div>

                                <div class="d-flex align-items-end gap-3 mb-4">
                                    <h1 class="streak-number mb-0" id="n-streak">0</h1>
                                    <div class="pb-1">
                                        <p class="mb-0 fw-bold fs-13">day streak</p>
                                        <p class="mb-0 text-gray fs-11">days read in a row</p>
                                    </div>
                                </div>

                                <div class="p-2" id="streak-baris"></div>

                                <div class="d-flex justify-content-center align-items-center gap-4 pt-3 border-top border-light" style="font-size: 0.8rem;">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="legend-dot dot-read"></span>
                                        <span class="text-gray fs-11">read</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="legend-dot dot-light"></span>
                                        <span class="text-gray fs-11">light read</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="dot-today" style="width: 10px; height: 10px;"></span>
                                        <span class="text-gray fs-11">today</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 px-2">

                        <!-- challenge -->
                        <div class="card mb-3" style="height: 41%;">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0" id="judul-chal">challenge</h5>
                                    <button type="button" class="btn btn-link link-more p-0" id="btn-challenge">edit ›</button>
                                </div>
                                <p class="text-gray fs-11 mt-1 mb-2" id="n-chal-nama">belum ada challenge</p>

                                <div class="challenge-box p-2">
                                    <h3 class="mb-0"><span id="n-chal-selesai">0</span> <span class="challenge-total" id="n-chal-target">/ 0</span></h3>
                                    <p class="mb-1 text-white fs-11">books completed</p>
                                    <!-- <span class="chip-light fs-11" id="n-chal-info"><i class="bi bi-bullseye"></i> -</span> -->
                                </div>
                            </div>
                        </div>

                        <!-- reading tasks -->
                        <div class="card" style="height: 55%;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">reading tasks</h5>
                                    <button type="button" class="btn btn-link link-more p-0" id="btn-tugas-semua">all ›</button>
                                </div>
                                <p class="section-label mt-1 mb-2" id="n-tugas-ringkas">0 of 0 done</p>

                                <div class="d-flex gap-2 mb-3" id="tugas-segmen"></div>
                                <div id="tugas-preview"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- shelves -->
                <div class="row mt-3">
                    <div class="col-12 px-2">
                        <div class="card card-custom h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-baseline gap-2">
                                        <h4 class="font-serif mb-0 fw-bold">on your shelves</h4>
                                        <span class="text-muted small" id="n-rak-ringkas">0 in TBR • 0 finished</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="../library/library.php?tab=books&pill=tbr" class="btn btn-outline-cream px-3">browse tbr</a>
                                        <button type="button" class="btn btn-dark px-3" id="btn-pick">✦ pick for me</button>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-2" id="chip-rak">
                                    <span class="chip chip-active" role="button" data-filter="tbr">up next</span>
                                    <span class="chip" role="button" data-filter="reading">reading</span>
                                    <span class="chip" role="button" data-filter="wishlist">wishlist</span>
                                </div>

                                <div class="shelf-wrap position-relative">
                                    <div class="d-flex align-items-end justify-content-between gap-3">
                                        <div class="shelf-books d-flex align-items-end gap-3" id="shelf-buku" style="min-height: 150px;"></div>

                                        <div class="curated-card d-flex align-items-center gap-3 p-3 mb-3" id="curated-card">
                                            <img src="../assets/images/nocover.jpg" alt="" class="curated-cover" id="c-cover">
                                            <div>
                                                <span class="tag-pink fs-9">CURATED FOR YOU</span>
                                                <h6 class="font-serif fw-bold mt-1 mb-0" id="c-judul">-</h6>
                                                <p class="mb-1 text-gray fs-11" id="c-penulis">-</p>
                                                <p class="mb-1 text-pink fs-11 fw-medium">today's picks from your shelf</p>
                                                <button type="button" class="btn btn-dark px-3" style="padding: 5px 20px !important;" id="btn-curated" data-id="">start reading</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shelf-board"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

     <!-- modal streak -->
    <div class="modal fade" id="modal-streak" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">streak detail</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="isi-streak"></div>
            </div>
        </div>
    </div>

     <!-- modal task -->
    <div class="modal fade" id="modal-tugas" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">reading tasks</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-tugas" class="input-group mb-3">
                        <input type="text" class="form-control" id="tugas-input" placeholder="write a new task...">
                        <button type="submit" class="btn btn-dark">+ add</button>
                    </form>
                    <div id="tugas-daftar"></div>
                </div>
            </div>
        </div>
    </div>

     <!-- modal challege -->
    <div class="modal fade" id="modal-challenge" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form id="form-challenge" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">challenge this year</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-bold">challenge name</label>
                        <input type="text" class="form-control" id="chal-nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-bold">target number of books</label>
                        <input type="number" min="1" class="form-control" id="chal-target" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">cancel</button>
                    <button type="submit" class="btn btn-pink rounded-pill px-4">save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- modal buku (sama kek di library) -->
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
                        <li class="nav-item"><button type="button" class="nav-link modal-tab active" data-panel="overview">overview</button></li>
                        <li class="nav-item"><button type="button" class="nav-link modal-tab" data-panel="tentang">about the book</button></li>
                        <li class="nav-item"><button type="button" class="nav-link modal-tab" data-panel="baca">my reading</button></li>
                        <li class="nav-item"><button type="button" class="nav-link modal-tab" data-panel="seri">series</button></li>
                        <li class="nav-item"><button type="button" class="nav-link modal-tab" data-panel="koleksi">collection</button></li>
                    </ul>

                    <!-- OVERVIEW -->
                    <div class="modal-panel" id="panel-overview">
                        <div class="row g-2 mb-3">
                            <div class="col-4"><div class="card p-3"><h4 class="text-pink-strong" id="o-status">-</h4><div class="fs-11 text-gray">status</div></div></div>
                            <div class="col-4"><div class="card p-3"><h4 class="text-pink-strong" id="o-rating">-</h4><div class="fs-11 text-gray">your rating</div></div></div>
                            <div class="col-4"><div class="card p-3"><h4 class="text-pink-strong" id="o-halaman">0/0</h4><div class="fs-11 text-gray">halaman dibaca</div></div></div>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-1">about</div>
                            <p class="fs-13 mb-0" id="o-deskripsi">-</p>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-2">details</div>
                            <div class="d-flex justify-content-between fs-13 border-bottom py-1"><span class="text-gray">pages</span><span id="o-halaman-total">-</span></div>
                            <div class="d-flex justify-content-between fs-13 border-bottom py-1"><span class="text-gray">publisher</span><span id="o-penerbit">-</span></div>
                            <div class="d-flex justify-content-between fs-13 border-bottom py-1"><span class="text-gray">publication year</span><span id="o-tahun">-</span></div>
                            <div class="d-flex justify-content-between fs-13 py-1"><span class="text-gray">isbn</span><span id="o-isbn">-</span></div>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-1">genre</div>
                            <button type="button" class="btn btn-sm rounded-pill btn-dark" id="o-genre" style="width: fit-content;">-</button>
                        </div>

                        <div class="card p-3 mb-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-2">dates</div>
                            <p class="fs-13 mb-0" id="o-tanggal">-</p>
                        </div>

                        <div class="card p-3">
                            <div class="fs-11 text-pink-strong fw-bold mb-2">owning it</div>
                            <div class="d-flex justify-content-between fs-13"><span class="text-gray">added</span><span id="o-ditambahkan">-</span></div>
                        </div>
                    </div>

                    <!-- ABOUT THE BOOK -->
                    <div class="modal-panel d-none" id="panel-tentang">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-pink-strong">description</label>
                            <textarea class="form-control" id="f-deskripsi" rows="6"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-pink-strong">genre</label>
                            <select class="form-select" id="f-genre"></select>
                        </div>
                    </div>

                    <!-- MY READING -->
                    <div class="modal-panel d-none" id="panel-baca">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-pink-strong">status</label>
                            <select class="form-select" id="f-status">
                                <option value="reading">reading</option>
                                <option value="read">read</option>
                                <option value="tbr">tbr</option>
                                <option value="dnf">dnf</option>
                                <option value="wishlist">wishlist</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-pink-strong">rating</label>
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
                                <label class="form-label fs-13 fw-bold text-pink-strong">total pages</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-dark" data-step="-1" data-target="f-total">-</button>
                                    <input type="number" class="form-control text-center" id="f-total" min="0" value="0">
                                    <button type="button" class="btn btn-outline-dark" data-step="1" data-target="f-total">+</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold text-pink-strong">current page</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-dark" data-step="-1" data-target="f-halaman-baca">-</button>
                                    <input type="number" class="form-control text-center" id="f-halaman-baca" min="0" value="0">
                                    <button type="button" class="btn btn-outline-dark" data-step="1" data-target="f-halaman-baca">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold text-pink-strong">start date</label>
                                <input type="date" class="form-control" id="f-mulai">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13 fw-bold text-pink-strong">finish date</label>
                                <input type="date" class="form-control" id="f-selesai">
                            </div>
                        </div>
                    </div>

                    <!-- SERIES -->
                    <div class="modal-panel d-none" id="panel-seri">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-pink-strong">series</label>
                            <select class="form-select" id="f-seri"></select>
                        </div>
                        <div class="mb-3 d-none" id="blok-seri-baru">
                            <label class="form-label fs-13 fw-bold text-pink-strong">new series name</label>
                            <input type="text" class="form-control" id="f-seri-baru" placeholder="contoh: Dune">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-pink-strong">series order</label>
                            <input type="number" step="0.1" min="0" class="form-control" id="f-urutan" placeholder="contoh: 1">
                        </div>
                    </div>

                    <!-- COLLECTION -->
                    <div class="modal-panel d-none" id="panel-koleksi">
                        <p class="fs-13 text-gray mb-2">click to select. highlighted items are selected.</p>
                        <div id="f-koleksi-pills" class="d-flex flex-wrap gap-2 mb-3"></div>
                        <div class="input-group">
                            <input type="text" class="form-control" id="f-koleksi-baru" placeholder="new collection name">
                            <button type="button" class="btn btn-outline-dark" id="btn-buat-koleksi">+ create</button>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <button type="button" class="btn btn-link text-danger fs-13" id="btn-hapus-buku">delete this book</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- modal log pages -->
    <div class="modal fade" id="modal-log" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="form-log" class="modal-content">

                <div class="modal-header text-white d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">cancel</button>
                    <button type="submit" class="btn btn-pink rounded-pill px-4">save</button>
                </div>
                
                <div class="modal-body" style="border-radius: 25px;">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <img id="log-cover" src="" alt="" class="rounded shadow" style="height: 110px;">
                        </div>
                        <div class="col">
                            <p class="fs-11 text-gray mb-0" id="log-tanggal">today</p>
                            <h3 class="fw-bold mb-0" id="log-judul">-</h3>
                            <p class="fs-13 text-gray mb-4" id="log-penulis">-</p>
                        </div>
                    </div>


                    <label class="form-label fs-13 fw-bold">Pages read today</label>
                    <div class="input-group mb-3">
                        <button type="button" class="btn btn-outline-dark" data-step="-1" data-target="log-halaman">-</button>
                        <input type="number" class="form-control text-center" id="log-halaman" min="0" value="0">
                        <button type="button" class="btn btn-outline-dark" data-step="1" data-target="log-halaman">+</button>
                    </div>

                    <label class="form-label fs-13 fw-bold">Minutes read today</label>
                    <div class="input-group mb-4">
                        <button type="button" class="btn btn-outline-dark" data-step="-5" data-target="log-menit">-5</button>
                        <input type="number" class="form-control text-center" id="log-menit" min="0" value="0">
                        <button type="button" class="btn btn-outline-dark" data-step="5" data-target="log-menit">+5</button>
                    </div>

                    <div class="card p-3">
                        <div class="d-flex justify-content-between fs-13 border-bottom py-1">
                            <span class="text-gray">current page</span>
                            <span id="log-sekarang">0 of 0</span>
                        </div>
                        <div class="d-flex justify-content-between fs-13 py-1">
                            <span class="text-gray">after saving</span>
                            <span id="log-setelah">0 of 0</span>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>