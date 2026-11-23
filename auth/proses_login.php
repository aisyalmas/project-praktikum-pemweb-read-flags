<?php
    // INISIALISASI & CEK REQUEST METHOD
    session_start();
    
    require_once __DIR__ . '/../config/koneksi.php';
    
    // Hanya terima request POST
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header("location:login.php");
        exit;
    }

    // AMBIL & VALIDASI INPUT
    $email=trim($_POST['email'] ?? '');
    $password=$_POST['password'] ?? '';
    $errors = [];

    if($email === ''){
        $errors['email'] = 'Email wajib diisi.';
    }
    if($password === ''){
        $errors['password'] = 'Password wajib diisi.';
    }

    // Simpan email lama agar form tidak kosong saat error
    $_SESSION['login_old_email'] = $email;

    // JIKA ADA ERROR, REDIRECT KE LOGIN
    if($errors){
        $_SESSION['login_errors'] = $errors;
        header("location:login.php");
        exit;
    }

    // CARI USER DI DATABASE BERDASARKAN EMAIL
    $stmt = mysqli_prepare($connect,"SELECT id,name,email, password from users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's',$email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user=mysqli_fetch_assoc($result);

    // VERIFIKASI PASSWORD MENGGUNAKAN PASSWORD_VERIFY
    if($user && password_verify($password,$user['password'])){
        // Set session untuk user yang login
        $_SESSION['user_id']= $user['id'];
        $_SESSION['user_name']= $user['name'];
        $_SESSION['user_email']= $user['email'];

        header("location:../dashboard/dashboard.php");
        exit;
    };

    // JIKA LOGIN GAGAL, SET ERROR MESSAGE
    if(!$user){
        $_SESSION['login_errors'] = ['email' => 'Email tidak terdaftar.'];
    }else{
        $_SESSION['login_errors'] = ['password' => 'Password salah.'];
    }
    
    header("location:login.php");
    exit; 
   
?>
