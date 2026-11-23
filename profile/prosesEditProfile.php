<?php
    //  INISIALISASI & CEK LOGIN 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ ."/../config/config.php";
    require_once __DIR__ ."/../config/koneksi.php";

    // Redirect ke login jika belum login
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/auth/login.php'); 
        exit;
    }

    // Hanya terima request POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    //  AMBIL DATA DARI FORM 
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    $bio  = trim($_POST['bio'] ?? '');

    //  VALIDASI INPUT 
    if ($name === '' || $bio === '') {
        $_SESSION['input_error'] = 'nama atau bio wajib di isi';
        header("location:profile.php");
        exit;
    }

    //  UPDATE DATA USER 
    $stmt = mysqli_prepare($connect, "UPDATE users SET name = ?, bio = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'ssi', $name, $bio, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Update session name agar langsung terlihat
        $_SESSION['user_name'] = $name; 
        $_SESSION['success_msg'] = 'Profile anda berhasil di update';
        header("location:profile.php");
        exit;
    }

    mysqli_stmt_close($stmt);
    $_SESSION['input_error'] = 'Gagal menyimpan perubahan, coba lagi ^^';
    header("location:profile.php");
    exit;
?>
