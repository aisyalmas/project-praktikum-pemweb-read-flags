<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Flags</title>
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
            <p class="mb-0 text-gray fs-13">monday, november 2 • 11:07 am</p>
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
                                    <div class="col-6">
                                        <span class="badge rounded-pill text-bg-active fs-11"><span class="circle-active"></span>100%</span>
                                        <span class="badge rounded-pill text-bg-custom fs-11"><span class="circle"></span>75%</span>
                                        <span class="badge rounded-pill text-bg-custom fs-11"><span class="circle"></span>75%</span>
                                    </div>
                                </div>
                                <div class="row justify-content-between mt-3">
                                    <div class="col-4">
                                            <img src="../assets/images/cover-babel.jpg" alt="" class="cover-book">
                                    </div>
                                    <div class="col-8">
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

                                        <div class="row mt-2 col">
                                            <div class="col">
                                                <div class="progress" role="progressbar" aria-label="Reading progress" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar bg-custom" style="width: 50%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3 px-2">
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-white mb-0">48</h6>
                                            <p class="text-gray fs-10 mb-0">pages today</p>
                                        </div>
                                    </div>
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-white mb-0">48 d</h6>
                                            <p class="text-gray fs-10 mb-0">reading time</p>
                                        </div>
                                    </div>
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-white mb-0">48 p</h6>
                                            <p class="text-gray fs-10 mb-0">daily pace</p>
                                        </div>
                                    </div>
                                    <div class="col-3 p-1">
                                        <div class="status-reading text-center">
                                            <h6 class="text-pink mb-0">4 days</h6>
                                            <p class="text-gray fs-10 mb-0">est. finish</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <button type="button" class="btn btn-pink fs-13 fw-semibold"><i class="bi bi-check2" style="font-size: 20px !important; padding-right: 5px;"></i> log pages</button>
                                        <button type="button" class="btn btn-outline fs-13 fw-semibold"><i class="bi bi-plus" style="font-size: 20px !important; padding-right: 2px;"></i> add review</button>
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

                            <div class="p-2">
                                <!-- Baris 1: 1-7 -->
                                <div class="timeline-row">
                                    <div class="timeline-line solid"></div>
                                    <div class="d-flex align-items-center">
                                        <span class="small me-3" style="min-width: 35px; font-size: 0.8rem;">1–7</span>
                                        <div class="dots-container flex-grow-1">
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-light"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Baris 2: 8-14 -->
                                <div class="timeline-row">
                                    <div class="timeline-line solid"></div>
                                    <div class="d-flex align-items-center">
                                        <span class="small me-3" style="min-width: 35px; font-size: 0.8rem;">8–14</span>
                                        <div class="dots-container flex-grow-1">
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-light"></span>
                                            <span class="dot dot-read"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Baris 3: 15-21 (Today) -->
                                <div class="timeline-row">
                                    <div class="timeline-line solid"></div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold text-dark small me-3" style="min-width: 35px; font-size: 0.8rem;">15–21</span>
                                        <div class="dots-container flex-grow-1">
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <span class="dot dot-read"></span>
                                            <!-- Hari ini (Today) -->
                                            <span class="dot-today d-inline-block"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Baris 4: 22-28 -->
                                <div class="timeline-row mb-0">
                                    <div class="timeline-line"></div>
                                    <div class="d-flex align-items-center">
                                        <span class="small me-3" style="min-width: 35px; font-size: 0.8rem;">22–28</span>
                                        <div class="dots-container flex-grow-1">
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                            <span class="dot dot-empty"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center align-items-center gap-4 mt-4 pt-3 border-top border-light" style="font-size: 0.8rem;">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="legend-dot dot-read"></span>
                                    <span class="text-gray fs-11">read (30+ min)</span>
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

                <!-- JOURNAL + TASKS -->
                <div class="col-md-3 px-2">

                    <!-- reading journal -->
                    <div class="card mb-3" style="height: 41%;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="journal-cover"><span>notes &amp; quotes</span></div>
                                <div>
                                    <p class="text-gray mb-1 fs-11">READING JOURNAL</p>
                                    <h5 class="mb-1">Autumn Musings</h5>
                                    <p class="mb-0 text-gray fs-11">28 entries • last edited 2h ago</p>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-dark flex-fill">write entry</button>
                                <button class="btn btn-outline-cream flex-fill">open book</button>
                            </div>
                        </div>
                    </div>

                    <!-- reading tasks -->
                    <div class="card" style="height: 55%;">
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

                            <p class="text-gray fs-11 fw-semibold mt-3 mb-2">TOP-GENRE THIS YEAR</p>
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
                    <div class="card card-custom h-100">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <div class="d-flex align-items-baseline gap-2">
                                    <h4 class="font-serif mb-0 fw-bold">on your shelves</h4>
                                    <span class="text-muted small">8 in TBR • 48 finished</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-cream px-3">browse tbr</button>
                                    <button class="btn btn-dark px-3">✦ pick for me</button>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-1">
                                <span class="chip chip-active">up next (4)</span>
                                <span class="chip">priorities</span>
                                <span class="chip">book club</span>
                                <span class="chip">spicy reads</span>
                            </div>

                            <div class="shelf-wrap position-relative">
                                <div class="d-flex align-items-end justify-content-between gap-3 pb-0">
                                    
                                    <div class="shelf-books d-flex align-items-end gap-3">
                                        <div class="book-item position-relative">
                                            <img src="../assets/images/cover-babel.jpg" alt="Tomorrow" class="cover-book-shelves">
                                        </div>
                                        <div class="book-item position-relative">
                                            <img src="../assets/images/cover-babel.jpg" alt="The Secret History" class="cover-book-shelves">
                                        </div>
                                        <div class="book-item position-relative">
                                            <img src="../assets/images/cover-babel.jpg" alt="Intermezzo" class="cover-book-shelves">
                                        </div>
                                        <div class="book-item position-relative">
                                            <img src="../assets/images/cover-babel.jpg" alt="Bunny" class="cover-book-shelves">
                                        </div>
                                    </div>

                                    <div class="curated-card d-flex align-items-center gap-3 p-3 mb-3">
                                        <img src="../assets/images/cover-babel.jpg" alt="Funny Story" class="curated-cover">
                                        <div>
                                            <span class="tag-pink fs-9">CURATED FOR YOU</span>
                                            <h6 class="font-serif fw-bold mt-1 mb-0">Funny Story</h6>
                                            <p class="mb-0 text-gray fs-11">Emily Henry • 400 pages</p>
                                            <p class="mb-1 text-pink fs-11 fw-medium">Matches your cozy autumn mood</p>
                                            <button class="btn btn-dark px-3" style="padding: 5px 20px !important;">start reading</button>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebar.js"></script>
</body>

</html>