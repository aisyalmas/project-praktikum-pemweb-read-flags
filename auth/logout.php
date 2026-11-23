<?php
    //  LOGOUT: HAPUS SESSION & REDIRECT KE LOGIN 
    session_start();

    // Kosongkan semua data session
    $_SESSION = [];
    
    session_destroy();

    require_once __DIR__ . '/../config/config.php';
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
