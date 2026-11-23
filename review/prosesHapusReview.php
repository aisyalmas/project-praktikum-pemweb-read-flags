<?php
    // ===== INISIALISASI & CEK LOGIN =====
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

    // ===== AMBIL DATA DARI FORM =====
    $userId   = (int)$_SESSION['user_id'];
    $reviewId = (int)($_POST['review_id'] ?? 0);

    // ===== VALIDASI INPUT =====
    if ($reviewId <= 0) {
        $_SESSION['review_error'] = 'Review tidak valid.';
        header('Location: review.php');
        exit;
    }

    // ===== HAPUS REVIEW (HANYA MILIK USER) =====
    // WHERE clause memastikan user hanya bisa hapus review miliknya sendiri
    $stmt = mysqli_prepare($connect, "DELETE FROM reviews WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $reviewId, $userId);
    mysqli_stmt_execute($stmt);

    // ===== CEK HASIL EKSEKUSI =====
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['review_success'] = 'Review berhasil dihapus.';
    } else {
        $_SESSION['review_error'] = 'Review tidak ditemukan atau bukan milik kamu.';
    }

    header('Location: review.php');
    exit;
?>
