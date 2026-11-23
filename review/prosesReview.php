<?php
    session_start();
    require_once __DIR__.'/../config/config.php';
    require_once __DIR__.'/../config/koneksi.php';

    // Redirect ke login jika belum login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }

    // Hanya terima request POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: review.php');
        exit;
    }

    $userId  = (int)$_SESSION['user_id'];
    $bookId  = (int)($_POST['book_id'] ?? 0);
    $rating  = (int)($_POST['rating'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    //  VALIDASI INPUT 
    if ($bookId <= 0 || $rating < 1 || $rating > 5) {
        $_SESSION['review_error'] = 'Pilih buku dan rating 1-5 dulu ya.';
        header('Location: review.php');
        exit;
    }

    //  CEK BUKU EXIST DI DATABASE 
    $cek = mysqli_prepare($connect, "SELECT id FROM books WHERE id = ?");
    mysqli_stmt_bind_param($cek, 'i', $bookId);
    mysqli_stmt_execute($cek);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        $_SESSION['review_error'] = 'Buku tidak ditemukan.';
        header('Location: review.php');
        exit;
    }

    //  LOGIKA EDIT ATAU INSERT REVIEW 
    $reviewId = (int)($_POST['review_id'] ?? 0);

    if ($reviewId > 0) {
        // EDIT MODE: Verifikasi review milik user saat ini
        $cekReview = mysqli_prepare($connect, "SELECT id FROM reviews WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($cekReview, 'ii', $reviewId, $userId);
        mysqli_stmt_execute($cekReview);

        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cekReview))) {
            $_SESSION['review_error'] = 'Review tidak ditemukan atau bukan milik kamu.';
            header('Location: review.php');
            exit;
        }

        // Update review
        $update = mysqli_prepare($connect, "UPDATE reviews SET rating = ?, content = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($update, 'isii', $rating, $content, $reviewId, $userId);

        if (mysqli_stmt_execute($update)) {
            $_SESSION['review_success'] = 'Review berhasil diupdate.';
        } else {
            $_SESSION['review_error'] = 'Gagal mengupdate review: ' . mysqli_error($connect);
        }

        header('Location: review.php');
        exit;
    }

    //  INSERT REVIEW BARU 
    // Jika review untuk buku yg sama sudah ada, update; jika belum ada, insert
    $stmt = mysqli_prepare($connect,
        "INSERT INTO reviews (user_id, book_id, rating, content)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE rating = VALUES(rating), content = VALUES(content)");
    mysqli_stmt_bind_param($stmt, 'iiis', $userId, $bookId, $rating, $content);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['review_success'] = 'Review berhasil disimpan.';
    } else {
        $_SESSION['review_error'] = 'Gagal menyimpan review: ' . mysqli_error($connect);
    }

    header('Location: review.php');
    exit;
?>
