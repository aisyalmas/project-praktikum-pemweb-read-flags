<?php
    session_start();
    require_once __DIR__ .'/../config/koneksi.php';
    require_once __DIR__ .'/../config/config.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = mysqli_prepare($connect, "SELECT * FROM users WHERE id = ? LIMIT 1");

    mysqli_stmt_bind_param($stmt,'i',$user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user=mysqli_fetch_assoc($result);

    //  HITUNG BUKU PER STATUS 
    // Query menghitung jumlah buku berdasarkan status (read, reading, tbr, dnf, wishlist)
    $stmtBooks = mysqli_prepare($connect, " SELECT status, COUNT(*) AS total
    FROM user_books
    WHERE user_id = ?
    GROUP BY status");

    mysqli_stmt_bind_param($stmtBooks,'s',$user_id);
    mysqli_stmt_execute($stmtBooks);
    $resultBooks=mysqli_stmt_get_result($stmtBooks);

    // Inisialisasi counter buku
    $bookCount = [
        'read' => 0,
        'reading' => 0,
        'tbr' => 0,
        'dnf' => 0,
        'wishlist' => 0
    ];

    // Loop hasil query ke dalam array
    while($row=mysqli_fetch_assoc($resultBooks)){
        $status=$row['status'];
        $total=$row['total'];

        $bookCount[$status]=$total;
    }

    //  AMBIL BUKU TERBARU (READING & READ) 
    // Prioritaskan "reading" dulu, lalu "read" berdasarkan tanggal terakhir
    $stmtRecent = mysqli_prepare($connect, "
        SELECT b.title, b.cover_url, b.page_count, ub.status, ub.current_page,
               ub.finished_at, ub.last_read_at, ub.added_at,
               GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors
        FROM user_books ub
        JOIN books b ON b.id = ub.book_id
        LEFT JOIN book_authors ba ON ba.book_id = b.id
        LEFT JOIN authors a ON a.id = ba.author_id
        WHERE ub.user_id = ? AND ub.status IN ('reading', 'read')
        GROUP BY ub.id
        ORDER BY CASE WHEN ub.status = 'reading' THEN 0 ELSE 1 END,
                 COALESCE(ub.finished_at, ub.last_read_at, ub.added_at) DESC
        LIMIT 10");
    mysqli_stmt_bind_param($stmtRecent, 'i', $user_id);
    mysqli_stmt_execute($stmtRecent);
    $recentBooks = mysqli_fetch_all(mysqli_stmt_get_result($stmtRecent), MYSQLI_ASSOC);

    //  GENERATE INISIAL NAMA UNTUK AVATAR 
    $name = $user['name'];
    $nameParts = explode(' ', trim($name));
    $initial = '';

    foreach ($nameParts as $part) {
        $initial .= strtoupper(substr($part, 0, 1));
    }
    $initial = substr($initial, 0, 2);

    //  AMBIL PESAN SUKSES/ERROR DARI SESSION 
    $successMsg = $_SESSION['success_msg'] ?? null;
    $inputError = $_SESSION['input_error'] ?? null;
    unset($_SESSION['success_msg'], $_SESSION['input_error']);
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Read Flags</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/profile.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <script src="../assets/js/sidebar.js"></script>
    <body>
        <?php include '../navigation/sidebar.php'; ?>

        <main class="main-content vh-100 overflow-auto">
            <div class="bg-primary d-flex flex-column profile-bg rounded-4">

                <!--  ALERT PESAN SUKSES/ERROR  -->
                <?php if ($successMsg): ?>
                    <div class="alert alert-success alert-dismissible fade show profile-alert mx-4 mt-4 mb-0" role="alert">
                        <?= htmlspecialchars($successMsg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if ($inputError): ?>
                    <div class="alert alert-danger alert-dismissible fade show profile-alert mx-4 mt-4 mb-0" role="alert">
                        <?= htmlspecialchars($inputError) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- ===== HEADER HALAMAN (JUDUL & TOMBOL BACK) ===== -->
                <div class="head-container p-3 d-flex flex-row flex-nowrap">
                    <div class="arrow-coontainer order-1 p-2">
                        <a href="../index.php">
                            <i class="bi bi-arrow-left" style="font-size: 30px; color:var(--pf-ink)"></i>
                        </a>
                    </div>
                    <div class="order-2 p-3">
                        <h1 class="profile-tag fs-1">Profile</h1>
                    </div>
                </div>

                <!--  PROFILE CARD   -->
                <div class="p-2">
                    <div class="account-card bg-success shadow p-3 mx-4 mb-5 bg-body-tertiary rounded">
                        
                        <!-- User Info Section -->
                        <div class="inner-card d-flex align-items-center p-3 mx-4 mb-5">
                            <!-- Avatar dengan Inisial -->
                            <div class="avatar rounded-circle d-flex justify-content-center align-items-center fw-bold flex-shrink-0" style="width: 130px; height: 130px; font-size: 28px;">
                                <?= htmlspecialchars($initial) ?>
                            </div>
                            <!-- Nama, Email, Bio -->
                            <div class="p-2 ms-4">
                                <div class="displayName" name="displayName"><?= htmlspecialchars($user['name'] ?? '') ?></div>
                                
                                <div class="emailProfile" name="emailProfile">
                                    <span class="email-icon rounded"><i class="bi bi-envelope"></i></span>
                                    <span class="email-text"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                                </div>
                                <div class="bioProfile" name="bioProfile"><?= htmlspecialchars($user['bio'] ?? '') ?></div>
                            </div>
                            <!-- Tombol Edit Profile -->
                            <div class="ms-auto p-2">
                                <a href="#editProfile" class="btn btn-sm rounded-circle edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfile" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" title="Edit profile">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>

                        <!-- ===== STATISTIK BUKU (READ, READING, WISHLIST) ===== -->
                        <div class="inner-card d-flex justify-content-evenly shadow p-3 mx-4 mb-5 bg-body-tertiary rounded">
                            <!-- Buku Sudah Dibaca -->
                            <div class="stat-item">
                                <span class="stat-icon rounded-circle"><i class="bi bi-bookmark-check-fill"></i></span>
                                <div class="stat-value"><?= $bookCount['read'] ?></div>
                                <div class="stat-label roboto-mono">read</div>
                            </div>

                            <span class="border-end border-3"></span>
                            <!-- Buku Sedang Dibaca -->
                            <div class="stat-item">
                                <span class="stat-icon rounded-circle"><i class="bi bi-book-half"></i></span>
                                <div class="stat-value"><?= $bookCount['reading'] ?></div>
                                <div class="stat-label roboto-mono">currently</div>
                            </div>

                            <span class="border-end border-3"></span>
                            <!-- Wishlist Buku -->
                            <div class="stat-item">
                                <span class="stat-icon rounded-circle"><i class="bi bi-heart-fill"></i></span>
                                <div class="stat-value"><?= $bookCount['wishlist'] ?></div>
                                <div class="stat-label roboto-mono">want to read</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  SECTION BUKU TERBARU (READING & READ)  -->
                <div class="p-2">
                    <section class="recent-section">
                        <div class="d-flex justify-content-between align-items-end mb-3 px-2">
                            <div>
                                <p class="recent-eyebrow">your shelf</p>
                                <h2 class="recent-title">recently read</h2>
                            </div>
                            <?php if ($recentBooks): ?>
                                <span class="recent-count"><?= count($recentBooks) ?> books</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($recentBooks): ?>
                            <!-- Daftar Buku Terbaru -->
                            <div class="recent-strip">
                                <?php foreach ($recentBooks as $rb):
                                    $isReading = $rb['status'] === 'reading';
                                    $percent = null;
                                    if ($isReading && (int)$rb['page_count'] > 0) {
                                        $percent = min(100, (int)round(((int)$rb['current_page'] / (int)$rb['page_count']) * 100));
                                    }
                                    if ($isReading) {
                                        $meta = 'reading' . ($percent !== null ? ' · ' . $percent . '%' : '');
                                    } else {
                                        $meta = !empty($rb['finished_at']) ? 'finished ' . date('M j, Y', strtotime($rb['finished_at'])) : 'finished';
                                    }
                                ?>
                                <article class="recent-card">
                                    <div class="recent-cover">
                                        <?php if (!empty($rb['cover_url'])): ?>
                                            <img src="<?= htmlspecialchars($rb['cover_url']) ?>" alt="<?= htmlspecialchars($rb['title']) ?>">
                                        <?php else: ?>
                                            <span class="recent-cover-title"><?= htmlspecialchars($rb['title']) ?></span>
                                        <?php endif; ?>
                                        <!-- Status Buku (Reading/Finished) -->
                                        <span class="recent-status <?= $isReading ? 'is-reading' : 'is-finished' ?>"><?= htmlspecialchars($meta) ?></span>
                                    </div>
                                    <h3 class="recent-book-title"><?= htmlspecialchars($rb['title']) ?></h3>
                                    <p class="recent-book-author"><?= htmlspecialchars($rb['authors'] ?? '') ?></p>
                                </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="recent-empty">Belum ada buku di rak kamu.</p>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
        </main>

        <?php require __DIR__ . '/editProfile.php'; ?>

        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
</html>