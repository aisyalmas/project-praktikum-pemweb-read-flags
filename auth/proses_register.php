<?php
    //  INISIALISASI & CEK REQUEST METHOD 
    session_start(); 

    require_once __DIR__ . '/../config/koneksi.php';

    // Hanya terima request POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: register.php');
        exit;
    }

    //  AMBIL INPUT DARI FORM 
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Simpan input lama agar form tidak kosong saat error
    $_SESSION['register_old_name']  = $name;
    $_SESSION['register_old_email'] = $email;

    //  VALIDASI SEMUA FIELD SEKALIGUS 
    $errors = [];

    if ($name === '') {
        $errors['name'] = 'Nama wajib diisi.';
    }

    if ($email === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if ($password === '') {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    if ($confirm === '') {
        $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
    } elseif ($password !== '' && $password !== $confirm) {
        $errors['confirm_password'] = 'Konfirmasi password tidak cocok.';
    }

    //  JIKA ADA ERROR, REDIRECT KE REGISTER 
    if ($errors) {
        $_SESSION['register_errors'] = $errors;
        header('Location: register.php');
        exit;
    }

    //  CEK EMAIL SUDAH TERDAFTAR ATAU BELUM 
    $stmt = mysqli_prepare($connect, 'SELECT id FROM users WHERE email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        $_SESSION['register_errors'] = ['email' => 'Email sudah terdaftar. Silakan login.'];
        header('Location: register.php');
        exit;
    }
    mysqli_stmt_close($stmt);

    //  HASH PASSWORD UNTUK KEAMANAN 
    $hash = password_hash($password, PASSWORD_DEFAULT);

    //  INSERT USER BARU KE DATABASE 
    $stmt = mysqli_prepare($connect, 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hash);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Hapus data sementara setelah berhasil
        unset($_SESSION['register_old_name'], $_SESSION['register_old_email'], $_SESSION['register_errors']);
        // Set pesan sukses untuk ditampilkan di login.php
        $_SESSION['register_success'] = 'Registrasi berhasil. Silakan login.';
        header('Location: login.php');
        exit;
    }

    //  JIKA REGISTRASI GAGAL 
    $_SESSION['register_error'] = 'Registrasi gagal, coba lagi.';
    header('Location: register.php');
    exit;
?>
