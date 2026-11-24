<?php
    session_start();
    require_once __DIR__.'/../config/config.php';
    require_once __DIR__.'/../config/koneksi.php';

    // CEK STATUS LOGIN USER 
    $isLoggedIn = isset($_SESSION['user_id']);
    $currentUserId = $isLoggedIn ? (int)$_SESSION['user_id'] : 0;

    // FUNGSI HELPER: GENERATE INISIAL NAMA 
    function reviewInitials($name) {
        $parts = preg_split('/\s+/', trim((string)$name));
        $ini = '';
        foreach ($parts as $p) {
            if ($p !== '') $ini .= strtoupper(substr($p, 0, 1));
        }
        return $ini !== '' ? substr($ini, 0, 2) : '?';
    }

    // PALET WARNA UNTUK COVER BUKU 
    $coverPalette = ['#7590C2','#325240','#E3CBA8','#432A33','#D4597D','#1A1A1A','#7A8B5C','#B5651D'];

    // AMBIL PARAMETER PENCARIAN & FILTER DARI URL 
    $q = trim($_GET['q'] ?? '');
    $rating = (int)($_GET['rating'] ?? 0);
    $bookSearch = trim($_GET['book_search'] ?? '');
    $like = '%' . $q . '%';

    // QUERY REVIEWS DENGAN FILTER SEARCH & RATING 
    $stmt = mysqli_prepare($connect, "
        SELECT r.id, r.user_id, r.rating, r.content, r.created_at,
               u.name AS user_name,
               b.id AS book_id, b.title, b.cover_url,
               GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors
        FROM reviews r
        JOIN users u ON u.id = r.user_id
        JOIN books b ON b.id = r.book_id
        LEFT JOIN book_authors ba ON ba.book_id = b.id
        LEFT JOIN authors a ON a.id = ba.author_id
        WHERE (? = 0 OR r.rating = ?)
        GROUP BY r.id
        HAVING CONCAT_WS(' ', b.title, u.name, authors) LIKE ?
        ORDER BY r.created_at DESC
    ");
    mysqli_stmt_bind_param($stmt, 'iis', $rating, $rating, $like);
    mysqli_stmt_execute($stmt);
    $reviews = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    //query semua bukunya buat pilih buku
    $books = mysqli_fetch_all(mysqli_query($connect, "
        SELECT b.id, b.title, b.cover_url,
               GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors
        FROM books b
        LEFT JOIN book_authors ba ON ba.book_id = b.id
        LEFT JOIN authors a ON a.id = ba.author_id
        GROUP BY b.id
        ORDER BY b.title ASC
    "), MYSQLI_ASSOC);

    // FILTER BUKU BERDASARKAN PENCARIAN  
    if ($bookSearch) {
        $books = array_filter($books, function($b) use ($bookSearch) {
            $searchLower = strtolower($bookSearch);
            $titleMatch = strpos(strtolower($b['title']), $searchLower) !== false;
            $authorsMatch = strpos(strtolower($b['authors'] ?? ''), $searchLower) !== false;
            return $titleMatch || $authorsMatch;
        });
    }

    
    $successMsg = $_SESSION['review_success'] ?? null;
    $errorMsg   = $_SESSION['review_error'] ?? null;
    unset($_SESSION['review_success'], $_SESSION['review_error']);

    //  HANDLE MODAL ACTIONS: ADD, EDIT, DELETE 
    $action = $_GET['action'] ?? '';
    $editReview = null;
    $selectedBook = null;

    // Logika Edit: Ambil data review dari database untuk prefill modal
    if ($action === 'edit' && isset($_GET['review_id'])) {
        $rid = (int)$_GET['review_id'];
        $stmt = mysqli_prepare($connect, "
            SELECT r.id, r.user_id, r.rating, r.content, r.book_id, b.title, b.cover_url,
                   GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors
            FROM reviews r
            JOIN books b ON b.id = r.book_id
            LEFT JOIN book_authors ba ON ba.book_id = b.id
            LEFT JOIN authors a ON a.id = ba.author_id
            WHERE r.id = ? AND r.user_id = ?
            GROUP BY r.id
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $rid, $currentUserId);
        mysqli_stmt_execute($stmt);
        $editReview = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    // Logika Add: Ambil data buku yang dipilih user dari modal step 1
    if ($action === 'add' && isset($_GET['book_id'])) {
        $bid = (int)$_GET['book_id'];
        $stmt = mysqli_prepare($connect, "
            SELECT b.id, b.title, b.cover_url,
                   GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors
            FROM books b
            LEFT JOIN book_authors ba ON ba.book_id = b.id
            LEFT JOIN authors a ON a.id = ba.author_id
            WHERE b.id = ?
            GROUP BY b.id
        ");
        mysqli_stmt_bind_param($stmt, 'i', $bid);
        mysqli_stmt_execute($stmt);
        $selectedBook = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    // Logika Delete: Ambil data review untuk konfirmasi hapus
    $deleteReview = null;
    if ($action === 'delete' && isset($_GET['review_id'])) {
        $rid = (int)$_GET['review_id'];
        $stmt = mysqli_prepare($connect, "
            SELECT r.id, r.book_id, b.title
            FROM reviews r
            JOIN books b ON b.id = r.book_id
            WHERE r.id = ? AND r.user_id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $rid, $currentUserId);
        mysqli_stmt_execute($stmt);
        $deleteReview = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Book Reviews | Read Flags</title>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Fraunces:opsz,wght@9..144,700;9..144,900&family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <link rel="stylesheet" href="../assets/css/review.css">
    </head>
    <body>
        <?php include '../navigation/sidebar.php';?>
        <main class="main-content vh-100 overflow-auto">
            <div class="review-page">

                <!--  ALERT PESAN SUKSES/ERROR  -->
                <?php if ($successMsg): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                        <?= htmlspecialchars($successMsg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($errorMsg): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                        <?= htmlspecialchars($errorMsg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- HEADER HALAMAN -->
                <header class="review-header">
                    <div>
                        <p class="review-eyebrow font-bricolage">community  &amp; critic feed</p>
                        <h1 class="font-bricolage review-title">Book reviews <span class="review-title-star" style="display: inline-block; transform: rotate(90deg); font-size: 0.5em; position:relative; top:-18px; left:-5px;"><i class="bi bi-bookmark-fill"></i></span></h1>
                    </div>
                    <?php if ($isLoggedIn): ?>
                        <a href="?action=add" class="write-review btn btn-dark rounded-pill px-4 font-bricolage">
                            <i class="bi bi-plus-lg"></i> write review
                        </a>
                    <?php else: ?>
                        <a class="btn btn-dark rounded-pill px-4" href="<?= BASE_URL ?>/auth/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> login untuk review
                        </a>
                    <?php endif; ?>
                </header>

                <!--  TOOLBAR: PENCARIAN & FILTER RATING  -->
                <div class="review-toolbar">
                    <form method="get" class="review-search roboto-mono d-flex align-items-center gap-2">
                        <div class="position-relative flex-grow-1">
                            <i class="bi bi-search-heart-fill"></i>
                            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search reviews by title, author, user...">
                        </div>
                        <?php if ($rating): ?>
                            <input type="hidden" name="rating" value="<?= $rating ?>">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-dark rounded-circle" style="width:38px;height:38px;" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                    <!-- Filter Rating Bintang -->
                    <div class="review-filters">
                        <a class="filter-btn roboto-mono <?= $rating === 0 ? 'active' : '' ?>" href="?q=<?= urlencode($q) ?>">All</a>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <a class="filter-btn roboto-mono <?= $rating === $i ? 'active' : '' ?>" href="?q=<?= urlencode($q) ?>&rating=<?= $i ?>">
                                <?= $i ?> <i class="bi bi-star-fill"></i>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>

                <!--  GRID DAFTAR REVIEW  -->
                <div class="row g-4" id="reviewGrid">
                    <?php if (!$reviews): ?>
                        <div class="col-12"><p class="review-empty">Belum ada review. Jadi yang pertama!</p></div>
                    <?php endif; ?>

                    <?php foreach ($reviews as $r):
                        $authors = $r['authors'] ?? '';
                        $coverColor = $coverPalette[$r['book_id'] % count($coverPalette)];
                        $isOwner = $isLoggedIn && (int)$r['user_id'] === $currentUserId;
                    ?>
                    <!-- Review Card Item -->
                    <div class="col-12 col-lg-6">
                        <article class="review-card">
                            <div class="review-card-head">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="review-avatar"><?= htmlspecialchars(reviewInitials($r['user_name'])) ?></div>
                                    <div>
                                        <div class="review-user font-bricolage"><?= htmlspecialchars($r['user_name']) ?></div>
                                        <div class="review-date roboto-mono"><?= date('M j, Y', strtotime($r['created_at'])) ?></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Rating Bintang -->
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi <?= $i <= (int)$r['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <!-- Tombol Edit & Hapus (Hanya untuk pemilik review) -->
                                    <?php if ($isOwner): ?>
                                    <a href="?action=edit&review_id=<?= (int)$r['id'] ?>" class="review-action-btn text-decoration-none" title="Edit review">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?action=delete&review_id=<?= (int)$r['id'] ?>" class="review-action-btn text-decoration-none" title="Hapus review">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="review-card-body">
                                <!-- Cover Buku -->
                                <div class="review-cover" style="background-color: <?= htmlspecialchars($coverColor) ?>">
                                    <?php if (!empty($r['cover_url'])): ?>
                                        <img src="<?= htmlspecialchars($r['cover_url']) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
                                    <?php else: ?>
                                        <span><?= htmlspecialchars($r['title']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <!-- Info Review -->
                                <div class="review-info">
                                    <h4 class="review-book-title font-bricolage"><?= htmlspecialchars($r['title']) ?></h4>
                                    <p class="review-book-author roboto-mono"><?= htmlspecialchars($authors) ?></p>
                                    <p class="review-text roboto-mono"><?= nl2br(htmlspecialchars($r['content'] ?? '')) ?></p>
                                </div>
                            </div>
                        </article>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>

        <!--  MODAL WRITE/EDIT REVIEW  -->
        <?php $showModal = ($action === 'add' || $action === 'edit') && $isLoggedIn; ?>
        <div class="modal <?= $showModal ? 'show d-block' : 'fade' ?>" id="writeReviewModal" tabindex="-1" aria-hidden="<?= $showModal ? 'false' : 'true' ?>" <?= $showModal ? 'style="background-color: rgba(0,0,0,0.5);"' : '' ?>>
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content review-modal">

                    <!-- Step 1: Cari & Pilih Buku -->
                    <div id="reviewStep1" class="<?= ($action === 'edit' || $selectedBook) ? 'd-none' : '' ?>">
                        <div class="modal-header border-0 d-flex align-items-center gap-2">
                            <a href="review.php" class="btn btn-light rounded-circle text-decoration-none" aria-label="Close">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                            <h2 class="review-modal-title mb-0 mx-auto font-bricolage">find a book</h2>
                            <span style="width:38px"></span>
                        </div>
                        <div class="modal-body pt-0">
                            <!-- Form Pencarian Buku -->
                            <form method="get" class="review-search mb-3 roboto-mono d-flex align-items-center gap-2">
                                <input type="hidden" name="action" value="add">
                                <div class="position-relative flex-grow-1">
                                    <i class="bi bi-search-heart-fill"></i>
                                    <input type="text" name="book_search" value="<?= htmlspecialchars($bookSearch) ?>" placeholder="search title or author...">
                                </div>
                                <button type="submit" class="btn btn-dark rounded-circle" style="width:38px;height:38px;" title="Search">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                            <!-- Daftar Buku -->
                            <div class="book-list" id="bookList">
                                <?php foreach ($books as $b):
                                    $bookColor = $coverPalette[$b['id'] % count($coverPalette)];
                                ?>
                                <a href="?action=add&book_id=<?= (int)$b['id'] ?>" class="book-option text-decoration-none d-flex align-items-center gap-3 p-2 rounded">
                                    <span class="book-option-cover" style="background-color: <?= htmlspecialchars($bookColor) ?>">
                                        <?php if (!empty($b['cover_url'])): ?>
                                            <img src="<?= htmlspecialchars($b['cover_url']) ?>" alt="">
                                        <?php else: ?>
                                            <?= htmlspecialchars(substr($b['title'], 0, 2)) ?>
                                        <?php endif; ?>
                                    </span>
                                    <span class="book-option-info flex-grow-1">
                                        <span class="book-option-title font-bricolage d-block"><?= htmlspecialchars($b['title']) ?></span>
                                        <span class="book-option-author roboto-mono d-block"><?= htmlspecialchars($b['authors'] ?? '') ?></span>
                                    </span>
                                    <span class="book-option-plus"><i class="bi bi-plus-lg"></i></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Form Rating & Review -->
                    <div id="reviewStep2" class="<?= ($action === 'edit' || $selectedBook) ? '' : 'd-none' ?>">
                        <form action="<?= BASE_URL ?>/review/prosesReview.php" method="POST" id="reviewForm">
                            <div class="modal-header border-0 d-flex align-items-center gap-2">
                                <a href="?action=add" class="btn btn-light rounded-circle text-decoration-none" aria-label="Back">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                                <h2 class="review-modal-title mb-0 mx-auto font-bricolage">write review</h2>
                                <span style="width:38px"></span>
                            </div>
                            <div class="modal-body pt-0">
                                <div class="row g-4">
                                    <!-- Cover & Info Buku yang Dipilih -->
                                    <div class="col-12 col-md-4 text-center">
                                        <div class="review-modal-cover mx-auto" style="background-color: <?= htmlspecialchars(($selectedBook ? $coverPalette[$selectedBook['id'] % count($coverPalette)] : ($editReview ? $coverPalette[$editReview['book_id'] % count($coverPalette)] : '#eaddcd'))) ?>">
                                            <?php if ($selectedBook && !empty($selectedBook['cover_url'])): ?>
                                                <img src="<?= htmlspecialchars($selectedBook['cover_url']) ?>" alt="">
                                            <?php elseif ($editReview && !empty($editReview['cover_url'])): ?>
                                                <img src="<?= htmlspecialchars($editReview['cover_url']) ?>" alt="">
                                            <?php elseif ($selectedBook): ?>
                                                <span><?= htmlspecialchars($selectedBook['title']) ?></span>
                                            <?php elseif ($editReview): ?>
                                                <span><?= htmlspecialchars($editReview['title']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="review-modal-book-title font-bricolage"><?= htmlspecialchars($selectedBook ? $selectedBook['title'] : ($editReview ? $editReview['title'] : '')) ?></h3>
                                        <p class="review-modal-book-author roboto-mono"><?= htmlspecialchars($selectedBook ? ($selectedBook['authors'] ?? '') : ($editReview ? ($editReview['authors'] ?? '') : '')) ?></p>
                                    </div>
                                    <!-- Form Input Rating & Review -->
                                    <div class="col-12 col-md-8">
                                        <!-- Rating Bintang (CSS-only Radio Button) -->
                                        <label class="review-label roboto-mono">Rating</label>
                                        <div class="star-input" id="starInput">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" <?= ($editReview && (int)$editReview['rating'] === $i) ? 'checked' : '' ?> required>
                                                <label for="star<?= $i ?>"><i class="bi"></i></label>
                                            <?php endfor; ?>
                                        </div>

                                        <input type="hidden" name="review_id" value="<?= $editReview ? (int)$editReview['id'] : '' ?>">
                                        <input type="hidden" name="book_id" value="<?= $selectedBook ? (int)$selectedBook['id'] : ($editReview ? (int)$editReview['book_id'] : '') ?>">

                                        <!-- Textarea Review -->
                                        <label class="review-label mt-3 roboto-mono">Review</label>
                                        <textarea name="content" rows="4" class="form-control" placeholder="Tulis review kamu..."><?= $editReview ? htmlspecialchars($editReview['content'] ?? '') : '' ?></textarea>

                                        <button type="submit" class="btn btn-post btn-dark w-100 rounded-pill mt-4 roboto-mono">post review</button>
                                        <a href="review.php" class="btn btn-cancel btn-dark w-100 rounded-pill mt-4 roboto-mono text-decoration-none text-center">cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!--  MODAL KONFIRMASI HAPUS REVIEW  -->
        <?php $showDeleteModal = ($action === 'delete' && $deleteReview && $isLoggedIn); ?>
        <div class="modal <?= $showDeleteModal ? 'show d-block' : 'fade' ?>" id="deleteReviewModal" tabindex="-1" aria-hidden="<?= $showDeleteModal ? 'false' : 'true' ?>" <?= $showDeleteModal ? 'style="background-color: rgba(0,0,0,0.5);"' : '' ?>>
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content review-modal text-center p-4">
                    <h2 class="review-modal-title mb-2">hapus review?</h2>
                    <p class="mb-4">Review untuk <strong><?= $deleteReview ? htmlspecialchars($deleteReview['title']) : 'buku ini' ?></strong> akan dihapus.</p>
                    <form action="<?= BASE_URL ?>/review/prosesHapusReview.php" method="POST">
                        <input type="hidden" name="review_id" value="<?= $deleteReview ? (int)$deleteReview['id'] : '' ?>">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill mb-2">hapus</button>
                        <a href="review.php" class="btn btn-link w-100 text-muted text-decoration-none">cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js" integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu" crossorigin="anonymous"></script>
        <script src="../assets/js/sidebar.js"></script>
    </body>
</html>  