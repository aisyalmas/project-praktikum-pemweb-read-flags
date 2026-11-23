<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Flags</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
</head>

<body>
    <?php include './navigation/sidebar.php'; ?>

    <main class="main-content">
        <div class="container p-0">
            <p class="mb-0 text-gray fs-13">Monday, november 2 · 11:07 am</p>
            <h2 class="mb-3">Good morning, reader</h2>

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
                                    <div class="col-6">
                                        <span class="badge rounded-pill text-bg-active fs-11"><span class="circle-active"></span>100%</span>
                                        <span class="badge rounded-pill text-bg-custom fs-11"><span class="circle"></span>75%</span>
                                        <span class="badge rounded-pill text-bg-custom fs-11"><span class="circle"></span>75%</span>
                                    </div>
                                </div>
                                <div class="row justify-content-between mt-3">
                                    <div class="col-3">
                                        <div class="w-100">
                                            <img src="./assets/images/cover-babel.jpg" alt="" class="cover-book">
                                        </div>
                                    </div>
                                    <div class="col-9">
                                        <h3 class="text-white">Babel: An Arcane History</h3>
                                        <p class="text-gray fs-11">R.F. Kuang • 544 pages</p>

                                        <div class="row justify-content-between">
                                            <div class="col page d-flex align-items-center gap-1">
                                                <h6 class="text-white mb-0">p. 402</h6>
                                                <p class="text-gray fs-11 mb-0">of 544</p>
                                            </div>
                                            <div class="col-3 text-end">
                                                <h6 class="text-pink mb-0">74%</h6>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-3 p-1">
                                                <div class="status-reading text-center">
                                                    <h6 class="text-white mb-0">48</h6>
                                                    <p class="text-gray fs-10 mb-0">pages today</p>
                                                </div>
                                            </div>
                                            <div class="col-3 p-1">
                                                <div class="status-reading text-center">
                                                    <h6 class="text-white mb-0">48</h6>
                                                    <p class="text-gray fs-10 mb-0">pages today</p>
                                                </div>
                                            </div>
                                            <div class="col-3 p-1">
                                                <div class="status-reading text-center">
                                                    <h6 class="text-white mb-0">48</h6>
                                                    <p class="text-gray fs-10 mb-0">pages today</p>
                                                </div>
                                            </div>
                                            <div class="col-3 p-1">
                                                <div class="status-reading text-center">
                                                    <h6 class="text-white mb-0">48</h6>
                                                    <p class="text-gray fs-10 mb-0">pages today</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 px-2">
                    <div class="card h-100">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">streak & cadence</h4>
                                <a href="#" class="link-more">november ›</a>
                            </div>

                            <div class="d-flex align-items-end gap-3 mb-4">
                                <h1 class="streak-number mb-0">33</h1>
                                <div class="pb-1">
                                    <p class="mb-0 fw-bold fs-13">day streak</p>
                                    <p class="mb-0 text-gray fs-11">longest: 91 days • 0 missed</p>
                                </div>
                                <div class="freeze-box text-center ms-auto">
                                    <h6 class="mb-0">2</h6>
                                    <p class="mb-0 text-gray fs-10">freezes left</p>
                                </div>
                            </div>

                            <!-- minggu ini -->
                            <p class="section-label mb-2">this week</p>
                            <div class="d-flex justify-content-between">
                                <div class="day-dot done">M</div>
                                <div class="day-dot done">T</div>
                                <div class="day-dot done">W</div>
                                <div class="day-dot done">T</div>
                                <div class="day-dot today">F</div>
                                <div class="day-dot">S</div>
                                <div class="day-dot">S</div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- JOURNAL + TASKS -->
                <div class="col-md-3 px-2">

                    <!-- reading journal -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="section-label mb-3">reading journal</p>

                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="journal-cover"><span>notes &amp; quotes</span></div>
                                <div>
                                    <h5 class="mb-1">Autumn Musings</h5>
                                    <p class="mb-0 text-gray fs-11">28 entries • last edited 2h ago</p>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-pill-dark flex-fill">write entry</button>
                                <button class="btn btn-pill-outline flex-fill">open book</button>
                            </div>
                        </div>
                    </div>

                    <!-- reading tasks -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">reading tasks</h5>
                                <a href="#" class="link-more">all 12 ›</a>
                            </div>
                            <p class="section-label mt-1 mb-2">3 of 5 done this week</p>

                            <div class="d-flex gap-2 mb-3">
                                <div class="seg-bar active"></div>
                                <div class="seg-bar active"></div>
                                <div class="seg-bar"></div>
                            </div>

                            <div class="task-item d-flex align-items-center gap-2 py-2">
                                <span class="task-check checked"></span>
                                <span class="flex-grow-1 fs-13">ARC Review: Yellowface</span>
                                <span class="tag-pink fs-11">tomorrow</span>
                            </div>

                            <div class="task-item d-flex align-items-center gap-2 py-2">
                                <span class="task-check"></span>
                                <span class="flex-grow-1 fs-13">Book Club Qs: Ch. 12–18</span>
                                <span class="text-gray fs-11">friday</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ROW 2 -->
            <div class="row mt-3">

                <!-- CHALLENGE -->
                <div class="col-md-3 px-2">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">

                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">2026 challenge</h4>
                                <a href="#" class="link-more">edit ›</a>
                            </div>

                            <div class="challenge-box mt-3 p-3">
                                <h1 class="mb-0">28 <span class="challenge-total">/ 35</span></h1>
                                <p class="mb-2 text-white fs-11">books completed</p>
                                <span class="chip-light fs-11">
                                    <i class="bi bi-bullseye"></i> 3 books ahead of schedule
                                </span>
                            </div>

                            <p class="section-label mt-4 mb-2">top genres this year</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="chip fs-11"><span class="dot-pink"></span>Literary (14)</span>
                                <span class="chip fs-11"><span class="dot-light"></span>Fantasy (9)</span>
                                <span class="chip fs-11"><span class="dot-gray"></span>Memoir (5)</span>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- SHELVES -->
                <div class="col-md-9 px-2">
                    <div class="card h-100">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div>
                                    <h4 class="mb-0">on your shelves</h4>
                                    <p class="mb-0 text-gray fs-11">8 in TBR • 48 finished</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-pill-outline">browse tbr</button>
                                    <button class="btn btn-pill-dark">✦ pick for me</button>
                                </div>
                            </div>

                            <!-- filter -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="chip chip-active fs-11">up next (4)</span>
                                <span class="chip fs-11">priorities</span>
                                <span class="chip fs-11">book club</span>
                                <span class="chip fs-11">spicy reads</span>
                            </div>

                            <!-- rak buku -->
                            <div class="shelf-wrap">
                                <div class="shelf-books d-flex align-items-end gap-3">
                                    <div class="book book-1"><span>TOMORROW</span><small>G. Zevin</small></div>
                                    <div class="book book-2"><span>THE SECRET HISTORY</span><small>D. Tartt</small></div>
                                    <div class="book book-3"><span>INTERMEZZO</span><small>Sally Rooney</small></div>
                                    <div class="book book-4"><span>BUNNY</span><small>Mona Awad</small></div>
                                </div>
                                <div class="shelf-board"></div>
                            </div>

                            <!-- curated -->
                            <div class="curated-card d-flex gap-3 p-3 mt-3">
                                <div class="curated-cover"><span>EMILY HENRY</span></div>
                                <div>
                                    <span class="tag-pink fs-10">curated for you</span>
                                    <h5 class="mt-1 mb-0">Funny Story</h5>
                                    <p class="mb-1 text-gray fs-11">Emily Henry • 400 pages</p>
                                    <p class="mb-2 fs-11 text-pink">Matches your cozy autumn mood</p>
                                    <button class="btn btn-pill-dark btn-sm">start reading</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>